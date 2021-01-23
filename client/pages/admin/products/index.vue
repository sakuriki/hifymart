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
        <template #[`item.actions`]="{ item }">
          <v-btn
            v-if="canUpdate"
            color="success"
            icon
            :to="{ name: 'admin-products-id',params: { id: item.id }}"
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
    <v-dialog
      v-model="dialog"
      max-width="290"
    >
      <v-card>
        <v-card-title>
          Xác nhận xoá
        </v-card-title>
        <v-card-text>
          Bạn có chắc muốn xoá sản phẩm này? Đây là hành động vĩnh viễn và không thể thay đổi!
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="green darken-1"
            text
            @click="resetDelete"
          >
            Huỷ
          </v-btn>
          <v-btn
            color="red darken-1"
            text
            @click="deleteItem"
          >
            Xoá
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>
<script>
// import debounce from "lodash/debounce";
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
    beforeDelete(item) {
      this.dialog = true;
      this.confirmItem = item;
    },
    deleteItem() {
      this.$axios.delete("/admin/products/" + this.confirmItem)
      .then(() => {
        let index = this.data.findIndex(p => p.id == this.confirmItem);
        if (index > -1) {
          this.data.splice(index, 1);
        }
        this.pagination.total--;
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
      })
      .then(() => {
        this.resetDelete()
      });
    },
    resetDelete() {
      this.dialog = false;
      this.confirmItem = null
    }
  },
}
</script>
