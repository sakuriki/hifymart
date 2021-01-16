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
        }"
      >
        <template #[`item.billing_total`]="{ item }">
          <span class="red--text">{{ $moneyFormat(item.billing_total) }}</span>
        </template>
        <template #[`item.order_product_count`]="{ item }">
          <span><v-chip color="primary">{{ item.order_product_count }}</v-chip></span>
        </template>
        <template #[`item.is_paid`]="{ item }">
          <span><v-btn
            small
            :color="item.is_paid?'success':'accent'"
          >{{ item.is_paid ? "Paid" : "Unpaid" }}</v-btn></span>
        </template>
        <template #[`item.shipped`]="{ item }">
          <span><v-btn
            small
            :color="item.shipped?'success':'accent'"
          >{{ item.shipped ? "Đã chuyển" : "Chưa chuyển" }}</v-btn></span>
        </template>
        <template #[`item.status`]="{ item }">
          <span><v-btn
            small
            :color="statusText(item.status).color"
          >{{ statusText(item.status).text }}</v-btn></span>
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
      permission: "order.access"
    }
  },
  // async asyncData({ app }) {
  //   let { orders, pagination } = await app.$axios.$get("/admin/orders?per_page=10");
  //   return {
  //     data: orders,
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
        { text: 'Khách hàng', align: 'start', value: 'billing_name' },
        { text: 'Hoá đơn', value: 'billing_total' },
        { text: 'Giỏ hàng', value: 'order_product_count' },
        { text: 'Địa chỉ', value: 'billing_address', sortable: false },
        { text: 'Quận/Huyện', value: 'province.name', sortable: false },
        { text: 'Tỉnh/Thành', value: 'district.name', sortable: false },
        { text: 'Thanh toán', value: 'is_paid' },
        { text: 'Vận chuyển', value: 'shipped' },
        { text: 'Tình trạng', value: 'status' },
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
        let { orders, pagination } = await this.$axios.$get("/admin/orders", config);
        this.data = orders;
        this.pagination = pagination;
        this.loading = false;
        this.$vuetify.goTo(0)
      } catch(err) {
        console.error('loi fetch: ', err);
      }
    },
    beforeDelete: function(item) {
      console.log("xác nhận xoá", item)
    },
    statusText(status) {
      let output;
      switch (status) {
        case 0:
          output = {
            text: "New",
            color: "info"
          };
          break;
        case 1:
          output = {
            text: "Processing",
            color: "info"
          };
          break;
        case 2:
          output = {
            text: "Completed",
            color: "success"
          };
          break;
        default:
          output = {
            text: "Lỗi",
            color: "error"
          };
          break;
      }
      return output;
    }
  },
}
</script>
