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
          itemsPerPageText: 'Số hàng mỗi trang',
          itemsPerPageOptions: [10, 20, 30, 50],
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
            color="success"
            icon
            :to="{ name: 'admin-products-id',params: { id: item.id }}"
          >
            <v-icon>mdi-pencil-outline</v-icon>
          </v-btn>
          <v-btn
            color="error"
            icon
            @click="beforeDelete"
          >
            <v-icon>mdi-delete-outline</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>
<script>
// import debounce from "lodash/debounce";
export default {
  layout: "admin",
  middleware: "auth",
  meta: {
    auth: {
      permission: "product.access"
    }
  },
  // async asyncData({ app }) {
  //   let { products, pagination } = await app.$axios.$get("/admin/products?per_page=10");
  //   return {
  //     data: products,
  //     pagination: pagination
  //   }
  // },
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
        // { text: 'ID', align: 'start', value: 'id' },
        { text: 'Tên', align: 'start', value: 'name' },
        { text: 'Giá', value: 'price' },
        { text: 'Tồn kho', value: 'quantity' },
        { text: 'Số order', value: 'orders_count' },
        { text: 'Nhãn hiệu', value: 'brand.name', sortable: false },
        { text: 'Danh mục', value: 'category.name', sortable: false },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
    }
  },
  // computed: {
  //   canUpdate() {
  //     return this.$auth.user.permissions.includes("brand.update")
  //   },
  //   canDelete() {
  //     return this.$auth.user.permissions.includes("brand.delete")
  //   }
  // },
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
    // this.fetchData();
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
        console.error('loi fetch: ', err);
      }
    },
    beforeDelete: function(item) {
      console.log("xác nhận xoá", item)
    }
  },
}
</script>
