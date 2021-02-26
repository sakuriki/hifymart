<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        Danh sách mã giảm giá
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
        <template #[`item.value`]="{ item }">
          <span>- {{ item.is_percent ? item.value+"%" : $moneyFormat(item.value) }}</span>
        </template>
        <template #[`item.is_percent`]="{ item }">
          <span>{{ item.is_percent ? "Theo phần trăm" : "Giảm cố định" }}</span>
        </template>
        <template #[`item.number`]="{ item }">
          <span><v-chip color="primary">{{ item.number }}</v-chip></span>
        </template>
        <template #[`item.starts_at`]="{ item }">
          <span>{{ humanTime(item.starts_at) }}</span>
        </template>
        <template #[`item.expires_at`]="{ item }">
          <span>{{ humanTime(item.expires_at) }}</span>
        </template>
        <template #[`item.actions`]="{ item }">
          <v-tooltip bottom>
            <template #activator="{ on, attrs }">
              <v-btn
                v-if="canUpdate"
                v-bind="attrs"
                color="success"
                icon
                :to="{ name: 'admin-coupons-id',params: { id: item.id }}"
                v-on="on"
              >
                <v-icon>mdi-pencil-outline</v-icon>
              </v-btn>
            </template>
            <span>Sửa</span>
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
      permission: "coupon.access"
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
        { text: 'Mã', align: 'start', value: 'code' },
        { text: 'Giá trị', value: 'value' },
        { text: 'Loại', value: 'is_percent' },
        { text: 'Giới hạn số lượng', value: 'number' },
        { text: 'Ngày bắt đầu', value: 'starts_at' },
        { text: 'Ngày hết hạn', value: 'expires_at' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
    }
  },
  computed: {
    canUpdate() {
      return this.$auth.user.permissions.includes("coupon.update")
    },
    canDelete() {
      return this.$auth.user.permissions.includes("coupon.delete")
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
        let { coupons, pagination } = await this.$axios.$get("/admin/coupons", config);
        this.data = coupons;
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
    async beforeDelete(id) {
      if (!this.canDelete) {
        this.$notifier.showMessage({
          content: 'Bạn không có quyền thực hiện hành động này!',
          color: 'error',
          right: false
        })
      }
      let confirm = await this.$refs.confirm.open('Xoá mã giảm giá', 'Bạn có chắc muốn xoá mã giảm giá này? Đây là hành động vĩnh viễn và không thể thay đổi!', { color: 'red' });
      if (confirm) {
        this.deleteItem(id)
      }
    },
    deleteItem(id) {
      this.$axios.delete("/admin/coupons/" + id)
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
    humanTime: function(date) {
      if (!date) return;
      let time = new Date(date);
      return time.toLocaleDateString("vi-VN", { day: '2-digit', month: '2-digit', year: 'numeric'})
    }
  },
}
</script>
