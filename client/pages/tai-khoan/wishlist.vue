<template>
  <v-card width="100%">
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
      <template #[`item.name`]="{ item }">
        <nuxt-link :to="{ name: 'san-pham-slug', params: { slug: item.slug } }">
          {{ item.name }}
        </nuxt-link>
      </template>
      <template #[`item.image`]="{ item }">
        <v-img
          max-height="200px"
          aspect-ratio="1"
          :src="'http://banhang.test/storage/featured_image/' + item.featured_image"
        />
      </template>
      <template #[`item.price`]="{ item }">
        <span
          class="red--text"
        >
          {{ item.sale_off_price ? moneyFormat(item.sale_off_price) : moneyFormat(item.price) }}
          <span
            v-if="item.sale_off_price"
            class="pl-1 grey--text text-decoration-line-through"
          >{{ moneyFormat(item.price) }}</span>
        </span>
      </template>
      <template #[`item.actions`]="{ item }">
        <v-btn
          color="error"
          icon
          @click="beforeDelete(item)"
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
        { text: 'Ảnh', align: 'start', value: 'image' },
        { text: 'Tên', value: 'name' },
        { text: 'Giá', value: 'price' },
        { text: 'Xoá', value: 'actions', sortable: false }
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
      this.$axios.get("/wishlists/" + this.$auth.user.id, config)
      .then(res => {
        if(res.status==200) {
          this.data = res.data.products,
          this.pagination = res.data.pagination
          this.loading = false;
        }
      });
    }, 500),
    beforeDelete: function(item) {
      console.log("xác nhận xoá", item)
    },
    moneyFormat(number) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number)
    },
  },
}
</script>
