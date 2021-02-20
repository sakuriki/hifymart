<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        Danh sách đánh giá
        <v-spacer />
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Tìm kiếm"
          single-line
          hide-details
        />
      </v-card-title>
      <v-data-table
        v-model="selected"
        :headers="headers"
        :items="data"
        :options.sync="options"
        :server-items-length="pagination.total"
        :loading="loading"
        show-select
        class="elevation-1"
        :footer-props="{
          itemsPerPageOptions: [10, 20, 30, 50],
          showFirstLastPage: true,
        }"
      >
        <template #[`item.approved`]="{ item }">
          <span><v-btn
            small
            :color="item.approved?'success':'accent'"
          >{{ item.approved ? "Duyệt" : "Chưa" }}</v-btn></span>
        </template>
        <template #[`item.rating`]="{ item }">
          <span>{{ item.rating }}<v-icon
            right
            color="amber"
          >mdi-star</v-icon></span>
        </template>
        <template #[`item.actions`]="{ item }">
          <v-tooltip bottom>
            <template #activator="{ on, attrs }">
              <v-btn
                v-if="canUpdate"
                v-bind="attrs"
                color="success"
                icon
                v-on="on"
                @click="approve(item.id)"
              >
                <v-icon>mdi-check</v-icon>
              </v-btn>
            </template>
            <span>Duyệt</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template #activator="{ on, attrs }">
              <v-btn
                v-if="canDelete"
                color="error"
                v-bind="attrs"
                icon
                v-on="on"
                @click="beforeDelete(item.id)"
              >
                <v-icon>mdi-delete-outline</v-icon>
              </v-btn>
            </template>
            <span>Xoá</span>
          </v-tooltip>
        </template>
      </v-data-table>
    </v-card>
    <ConfirmDialog ref="confirm" />
  </v-container>
</template>
<script>
export default {
  layout: "admin",
  middleware: "authorized",
  meta: {
    auth: {
      permission: "rating.access"
    }
  },
  data () {
    return {
      pagination: {
        total: 0,
        count: 0,
        per_page: 20
      },
      search: "",
      data: [],
      loading: false,
      options: {},
      selected: [],
      headers: [
        { text: 'Người viết', value: 'user.name', sortable: false },
        { text: 'Review', align: 'start', value: 'review', width: '40%' },
        { text: 'Đánh giá', value: 'rating' },
        { text: 'Trạng thái', value: 'approved' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
    }
  },
  computed: {
    canUpdate() {
      return this.$auth.user.permissions.includes("rating.update")
    },
    canDelete() {
      return this.$auth.user.permissions.includes("rating.delete")
    }
  },
  watch: {
    options: {
      handler () {
        this.fetchData()
      },
      deep: true,
    },
    search() {
      if(this.loading == true) return;
      this.fetchData()
    },
  },
  mounted () {
    this.fetchData = this.$debounce(this.fetchData, 500);
  },
  methods: {
    async fetchData() {
      this.loading = true;
      let config = {
        params: {
          q: this.search,
          page: this.options.page,
          per_page: this.options.itemsPerPage,
          sortBy: this.options.sortBy[0],
          sortDesc: this.options.sortDesc[0]
        }
      }
      try {
        let { ratings, pagination } = await this.$axios.$get("/admin/ratings", config);
        this.data = ratings;
        this.pagination = pagination;
        this.loading = false;
        this.$vuetify.goTo(0)
      } catch(err) {
        this.$notifier.showMessage({
          content: 'Có lỗi, vui lòng thử lại',
          color: 'error',
          right: false
        })
      }
    },
    approve(id) {
      let config = {

      };
      this.$axios.post("/admin/ratings/" + id, config)
      .then(() => {
        this.fetchData();
        this.$notifier.showMessage({
          content: 'Xoá thành công',
          color: 'success',
          right: false
        })
      })
      .catch(() => {
        this.$notifier.showMessage({
          content: 'Có lỗi khi xoá, vui lòng thử lại',
          color: 'error',
          right: false
        })
      });
    },
    async beforeDelete(id) {
      let confirm = await this.$refs.confirm.open('Xoá đánh giá', 'Bạn có chắc muốn xoá đánh giá này? Đây là hành động vĩnh viễn và không thể thay đổi!', { color: 'red' });
      if (confirm) {
        this.deleteItem(id)
      }
    },
    deleteItem(id) {
      this.$axios.delete("/admin/ratings/" + id)
      .then(() => {
        this.fetchData();
        this.$notifier.showMessage({
          content: 'Xoá thành công',
          color: 'success',
          right: false
        })
      })
      .catch(() => {
        this.$notifier.showMessage({
          content: 'Có lỗi khi xoá, vui lòng thử lại',
          color: 'error',
          right: false
        })
      });
    },
  },
}
</script>
