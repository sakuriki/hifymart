<template>
  <v-main>
    <v-container
      fluid
      class="landing_container pa-0"
    >
      <div class="full-page-height">
        <v-pagination
          v-model="pagination.current_page"
          class="my-4"
          :length="pagination.total_pages"
          :total-visible="7"
          @input="onPageChange"
        />
        <ProductList :products="products" />
        <v-pagination
          v-model="pagination.current_page"
          class="my-4"
          :length="pagination.total_pages"
          :total-visible="7"
          @input="onPageChange"
        />
      </div>
    </v-container>
  </v-main>
</template>
<script>
export default {
  validate({ params }) {
    let allow = ["dang-giam-gia", "mua-nhieu", "hang-moi", "kham-pha"];
    return allow.includes(params.type);
  },
  async asyncData({app, params}) {
    let string;
    if (params.type == "dang-giam-gia") {
      string = "&onsale=true"
    } else if (params.type == "mua-nhieu") {
      string = "orders_count"
    } else if (params.type == "kham-pha") {
      string = "random"
    }
    let { products, pagination } = await app.$axios.$get(`/products?per_page=16&orderBy=${string}`);
    return {
      products: products,
      pagination : pagination
    }
  },
  data() {
    return {
      type: null,
      products: [],
      pagination: {
        current_page: 1,
        per_page: 16,
        total: 0,
        total_pages: 0,
      },
      orderBy: null,
    }
  },
  beforeMount() {
    this.type = this.$route.params.type;
  },
  methods: {
    beforeFetch: function() {
      if (this.type == "dang-giam-gia") {
        this.orderBy = "&onsale=true"
      } else if (this.type == "mua-nhieu") {
        this.orderBy = "orders_count"
      } else if (this.type == "kham-pha") {
        this.orderBy = "random"
      }
      this.fetchData()
    },
    fetchData: function() {
      this.$axios.get(`/products?per_page=${this.pagination.per_page}&page=${this.pagination.current_page}&orderBy=${this.orderBy}`).then(res => {
        if (res.status == 200) {
          var data = res.data;
          this.products = data.products;
          this.pagination.total = data.pagination ? data.pagination.total : 0;
          this.pagination.total_pages = data.pagination ? data.pagination.total_pages : 0;
        }
      })
    },
    onPageChange: function() {
      this.fetchData();
      this.$vuetify.goTo(0, {
        duration: 500,
        offset: 0,
        easing: "easeInOutCubic"
      })
    }
  }
}
</script>
