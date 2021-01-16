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
            :src="'http://banhang.test/storage/featured_image/' + item.featured_image"
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
          <v-btn
            color="primary"
            icon
            @click="addGift(item)"
          >
            <v-icon>mdi-cart-plus</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>
<script>
// import debounce from "lodash/debounce";
export default {
  // middleware: "auth",
  // meta: {
  //   auth: {
  //     permission: "brand.read"
  //   }
  // },
  async asyncData({ app, params }) {
    let user = await app.$axios.$get('/user/' + params.id);
    return {
      user: user
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
      loading: true,
      options: {},
      selected: [],
      headers: [
        // { text: 'ID', align: 'start', value: 'id' },
        { text: 'Ảnh', align: 'start', value: 'image' },
        { text: 'Tên', value: 'name' },
        { text: 'Giá', value: 'price' },
        { text: 'Mua tặng', value: 'actions', sortable: false }
      ],
      user: []
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
    this.fetchData = this.$debounce(this.fetchData, 500);
    this.fetchData()
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
      this.$axios.get("/wishlists/" + this.$route.params.id, config)
      .then(res => {
        if(res.status==200) {
          this.data = res.data.products,
          this.pagination = res.data.pagination
          this.loading = false;
        }
      });
    },
    addGift: function(item) {
      console.log("mua tặng", item)
    },
  },
}
</script>
