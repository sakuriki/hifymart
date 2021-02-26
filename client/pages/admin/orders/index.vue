<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        Danh sách đơn hàng
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
        <template #[`item.billing_total`]="{ item }">
          <span class="red--text">{{ $moneyFormat(item.billing_total) }}</span>
        </template>
        <template #[`item.billing_shipping_fee`]="{ item }">
          <span class="red--text">{{ $moneyFormat(item.billing_shipping_fee) }}</span>
        </template>
        <template #[`item.billing_tax`]="{ item }">
          <span class="red--text">{{ $moneyFormat(item.billing_tax) }}</span>
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
          <v-tooltip bottom>
            <template #activator="{ on, attrs }">
              <v-btn
                v-if="canUpdate"
                v-bind="attrs"
                color="success"
                icon
                :to="{ name: 'admin-brands-id',params: { id: item.id }}"
                v-on="on"
              >
                <v-icon>mdi-orders-outline</v-icon>
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
        <template #footer>
          <div
            class="pl-2 d-flex align-center"
            style="margin-bottom: -58px;height: 58px;font-size: 0.75rem"
          >
            Đến trang:
            <v-text-field
              placeholder="0"
              type="number"
              style="max-width: 60px;margin: 13px 0 13px 34px;"
              dense
              flat
              hide-details
            />
          </div>
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
      permission: "order.access"
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
        { text: 'Khách hàng', align: 'start', value: 'billing_name' },
        { text: 'Hoá đơn', value: 'billing_total' },
        { text: 'Giỏ hàng', value: 'order_product_count' },
        { text: 'Thuế', value: 'billing_tax' },
        { text: 'Phí ship', value: 'billing_shipping_fee' },
        { text: 'Phương thức thanh toán', value: 'payment_type', sortable: false },
        { text: 'Thanh toán', value: 'is_paid' },
        { text: 'Vận chuyển', value: 'shipped' },
        { text: 'Tình trạng', value: 'status' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
    }
  },
  computed: {
    canUpdate() {
      return this.$auth.user.permissions.includes("order.update")
    },
    canDelete() {
      return this.$auth.user.permissions.includes("order.delete")
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
        let { orders, pagination } = await this.$axios.$get("/admin/orders", config);
        this.data = orders;
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
      let confirm = await this.$refs.confirm.open('Xoá đơn hàng', 'Bạn có chắc muốn xoá đơn hàng này? Đây là hành động vĩnh viễn và không thể thay đổi!', { color: 'red' });
      if (confirm) {
        this.deleteItem(id)
      }
    },
    deleteItem(id) {
      this.$axios.delete("/admin/orders/" + id)
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
    statusText(status) {
      let output;
      switch (status) {
        case 0:
          output = {
            text: "Mới",
            color: "info"
          };
          break;
        case 1:
          output = {
            text: "Đang xử lý",
            color: "accent"
          };
          break;
        case 2:
          output = {
            text: "Đang vận chuyển",
            color: "info"
          };
          break;
        case 3:
          output = {
            text: "Thành công",
            color: "success"
          };
          break;
        default:
          output = {
            text: "Đóng",
            color: "grey"
          };
          break;
      }
      return output;
    }
  },
}
</script>
