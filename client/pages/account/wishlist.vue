<template>
  <v-container fill-height>
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
          itemsPerPageOptions: [10, 20, 30, 40],
        }"
      >
        <template #[`item.name`]="{ item }">
          <NuxtLink
            class="text-decoration-none"
            :to="{ name: 'product-slug', params: { slug: item.slug } }"
          >
            {{ item.name }}
          </NuxtLink>
        </template>
        <template #[`item.image`]="{ item }">
          <v-img
            max-height="200px"
            aspect-ratio="1"
            :src="apiUrl + item.featured_image"
          />
        </template>
        <template #[`item.price`]="{ item }">
          <span
            class="red--text"
          >
            {{ item.sale_off_price ? $moneyFormat(item.sale_off_price) : $moneyFormat(item.price) }}
            <span
              v-if="item.sale_off_price"
              class="pl-1 grey--text text-decoration-line-through"
            >{{ $moneyFormat(item.price) }}</span>
          </span>
        </template>
        <template #[`item.actions`]="{ item }">
          <v-tooltip bottom>
            <template #activator="{ on, attrs }">
              <v-btn
                color="success"
                icon
                v-bind="attrs"
                v-on="on"
                @click="addItem(item.id)"
              >
                <v-icon>mdi-cart-plus</v-icon>
              </v-btn>
            </template>
            <span>Thêm vào giỏ hàng</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template #activator="{ on, attrs }">
              <v-btn
                color="error"
                icon
                v-bind="attrs"
                v-on="on"
                @click="deleteItem(item.id)"
              >
                <v-icon>mdi-delete-outline</v-icon>
              </v-btn>
            </template>
            <span>Xoá khỏi danh sách</span>
          </v-tooltip>
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>
<script>
export default {
  middleware: "authorized",
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
        { text: 'Ảnh', align: 'start', value: 'image' },
        { text: 'Tên', value: 'name' },
        { text: 'Giá', value: 'price' },
        { text: 'Hành động', value: 'actions', sortable: false }
      ],
    }
  },
  computed: {
    apiUrl() {
      return process.env.apiUrl
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
    this.fetchData();
  },
  methods: {
    fetchData() {
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
      this.$axios.get("/wishlists", config)
      .then(res => {
        if(res.status==200) {
          this.data = res.data.products,
          this.pagination = res.data.pagination
          this.loading = false;
        }
      });
    },
    addItem(id) {
      let t = this;
      let index = this.data.findIndex(p => p.id == id);
      if (index > -1) {
        this.$store.dispatch('cart/addItem', {
          product: t.data[index],
          add: 1
        });
      } else {
        this.$notifier.showMessage({
          content: 'Có lỗi khi thêm, vui lòng thử lại',
          color: 'error',
          right: false
        })
      }
    },
    deleteItem(id) {
      this.$axios.delete("/wishlists/" + id)
      .then(() => {
        let index = this.data.findIndex(p => p.id == id);
        if (index > -1) {
          this.data.splice(index, 1);
        }
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
