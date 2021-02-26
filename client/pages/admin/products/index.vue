<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        Danh sách sản phẩm
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
        <template #[`item.price`]="{ item }">
          <span>{{ $moneyFormat(item.price) }}</span>
        </template>
        <template #[`item.orders_count`]="{ item }">
          <span><v-chip color="primary">{{ item.orders_count }}</v-chip></span>
        </template>
        <template #[`item.quantity`]="{ item }">
          <span><v-chip color="primary">{{ item.quantity }}</v-chip></span>
        </template>
        <template #[`item.views_count`]="{ item }">
          <span><v-chip color="primary">{{ item.views_count }}</v-chip></span>
        </template>
        <template #[`item.actions`]="{ item }">
          <v-tooltip bottom>
            <template #activator="{ on, attrs }">
              <v-btn
                v-if="canUpdate"
                v-bind="attrs"
                color="success"
                icon
                :to="{ name: 'admin-products-id',params: { id: item.id }}"
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
      permission: "product.access"
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
        { text: 'Giá', value: 'price' },
        { text: 'Tồn kho', value: 'quantity' },
        { text: 'Số order', value: 'orders_count' },
        { text: 'Lượt xem', value: 'views_count' },
        { text: 'Nhãn hiệu', value: 'brand.name', sortable: false },
        { text: 'Danh mục', value: 'category.name', sortable: false },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
      confirmItem: null,
      dialog: false
    }
  },
  computed: {
    canUpdate() {
      return this.$auth.user.permissions.includes("product.update")
    },
    canDelete() {
      return this.$auth.user.permissions.includes("product.delete")
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
        let { products, pagination } = await this.$axios.$get("/admin/products", config);
        this.data = products;
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
    }
  },
}
</script>
