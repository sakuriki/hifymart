<template>
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
        itemsPerPageOptions: [10, 20, 30, 40],
      }"
    >
      <template #[`item.price`]="{ item }">
        <span>{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.price) }}</span>
      </template>
      <template #[`item.orders_count`]="{ item }">
        <span><v-chip>{{ item.orders_count }}</v-chip></span>
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
</template>
<script>
import debounce from "lodash/debounce";
export default {
  layout: "admin",
  middleware: "auth",
  // meta: {
  //   auth: {
  //     permission: "brand.read"
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
      loading: true,
      options: {},
      selected: [],
      headers: [
        // { text: 'ID', align: 'start', value: 'id' },
        { text: 'Tên', align: 'start', value: 'name' },
        { text: 'Giá', value: 'price' },
        { text: 'Số order', value: 'orders_count' },
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
    this.fetchData()
  },
  methods: {
    fetchData: debounce(function() {
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
      this.$axios.get("/admin/products", config)
      .then(res => {
        if(res.status==200 && res.data.code!=401) {
          this.data = res.data.products,
          this.pagination = res.data.pagination
          this.loading = false;
        } else {
          console.log("không đủ quyền")
        }
      });
    }, 500),
    beforeDelete: function(item) {
      console.log("xác nhận xoá", item)
    }
  },
}
</script>
