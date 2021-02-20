<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        Danh sách vai trò
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
        <template #[`item.permissions_count`]="{ item }">
          <span><v-chip color="primary">{{ item.permissions_count }}</v-chip></span>
        </template>
        <template #[`item.actions`]="{ item }">
          <v-btn
            v-if="canUpdate"
            color="success"
            icon
            :to="{ name: 'admin-roles-id',params: { id: item.id }}"
          >
            <v-icon>mdi-pencil-outline</v-icon>
          </v-btn>
          <v-btn
            v-if="canDelete"
            color="error"
            icon
            @click="beforeDelete(item.id)"
          >
            <v-icon>mdi-delete-outline</v-icon>
          </v-btn>
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
      permission: "role.access"
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
        { text: 'Tên', align: 'start', value: 'name' },
        { text: 'Slug', value: 'slug' },
        { text: 'Giới thiệu', value: 'description' },
        { text: 'Số quyền', value: 'permissions_count' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
    }
  },
  computed: {
    canUpdate() {
      return this.$auth.user.permissions.includes("role.update")
    },
    canDelete() {
      return this.$auth.user.permissions.includes("role.delete")
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
        let { roles, pagination } = await this.$axios.$get("/admin/roles", config);
        this.data = roles;
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
      let confirm = await this.$refs.confirm.open('Xoá vai trò', 'Bạn có chắc muốn xoá vai trò này? Đây là hành động vĩnh viễn và không thể thay đổi!', { color: 'red' });
      if (confirm) {
        this.deleteItem(id)
      }
    },
    deleteItem(id) {
      this.$axios.delete("/admin/roles/" + id)
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
