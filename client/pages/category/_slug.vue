<template>
  <v-container fill-height>
    <v-overlay v-show="loading">
      <v-progress-circular
        :size="70"
        color="primary"
        indeterminate
      />
    </v-overlay>
    <v-breadcrumbs :items="breaditems">
      <template #divider>
        <v-icon>mdi-chevron-right</v-icon>
      </template>
    </v-breadcrumbs>
    <v-divider />
    <v-row wrap>
      <v-col
        cols="12"
        md="4"
      >
        <v-card>
          <v-card-title>Tìm kiếm sản phẩm</v-card-title>
          <v-card-text>
            <v-range-slider
              v-model="data.money_range"
              label="Lọc theo giá"
              max="10000000"
              min="0"
              step="1000"
              hide-details
            />
            <v-row wrap>
              <v-col
                cols="12"
                md="6"
              >
                <v-text-field
                  v-model="money_range_start"
                  readonly
                  solo-inverted
                  hide-details
                  dense
                  flat
                />
              </v-col>
              <v-col
                cols="12"
                md="6"
              >
                <v-text-field
                  v-model="money_range_end"
                  readonly
                  solo-inverted
                  hide-details
                  dense
                  flat
                />
              </v-col>
            </v-row>
            <v-text-field
              v-model="search"
              class="mt-2"
              label="Lọc theo thương hiệu"
              flat
              solo-inverted
              hide-details
              clearable
              append-icon="mdi-magnify"
              clear-icon="mdi-close-circle-outline"
            />
            <simplebar style="max-height:300px">
              <v-treeview
                v-model="data.brands"
                :search="search"
                selectable
                :items="brands"
              />
            </simplebar>
            <span class="subtitle">Đánh giá</span>
            <div
              v-for="star in [5,4,3,2,1]"
              :key="star"
              class="d-flex flex-wrap align-center"
            >
              <div
                class="d-flex"
                @click="data.rating = star"
              >
                <v-rating
                  :value="star"
                  color="amber"
                  dense
                  half-increments
                  readonly
                  size="30"
                  style="cursor: pointer"
                />
              </div>
              <div
                v-if="star<5"
                class="d-flex"
              >
                <span>Trở lên</span>
                <v-spacer />
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col
        v-if="products.length>0"
        cols="12"
        md="8"
        class="full-page-height"
      >
        <ProductList :products="products" />
        <v-pagination
          v-model="pagination.current_page"
          class="my-4"
          :length="pagination.total_pages"
          :total-visible="7"
          @input="onPageChange"
        />
      </v-col>
      <v-col
        v-else
        cols="12"
        md="8"
        class="full-page-height"
      >
        <v-alert color="warning">
          Không tìm thấy sản phẩm nào.
        </v-alert>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import simplebar from "simplebar-vue";
import "simplebar/dist/simplebar.min.css";
export default {
  components: {
    simplebar,
  },
  async asyncData({ app, params, error }) {
    try {
      let { category } = await app.$axios.$get(`/categories/${params.slug}`);
      const [brands, products] = await Promise.all([
        app.$axios.$get(`/brands`),
        app.$axios.$get(`/products?per_page=16&categories=${category.id}`)
      ])
      return {
        products: products.products,
        pagination : products.pagination,
        brands: brands.brands,
        category: category
      }
    } catch (err) {
      return error({ statusCode: err.response ? err.response.status : 422, message: err.message || 'Có lỗi xảy ra' })
    }
  },
  data() {
    return {
      search: null,
      loading: false,
      products: [],
      brands: [],
      category: {},
      pagination: {
        current_page: 1,
        per_page: 16,
        total: 0,
        total_pages: 0,
      },
      data: {
        money_range: [0, 10000000],
        rating: null,
        brands: [],
      },
    }
  },
  head() {
    return {
      title: 'Sản phẩm theo danh mục ' + this.category.name,
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: this.$strippedContent(this.category.description)
        },
        {
          property: 'og:site_name',
          content: process.env.appName
        },
        {
          property: 'og:title',
          content: 'Sản phẩm theo danh mục ' + this.category.name
        },
        {
          property: 'og:description',
          content: this.$strippedContent(this.category.description)
        },
        {
          property: 'og:image',
          content: ''
        },
        {
          property: 'twitter:site',
          content: process.env.appName
        },
        {
          property: 'twitter:card',
          content: 'summary_large_image'
        },
        {
          property: 'twitter:title',
          content: 'Sản phẩm theo danh mục ' + this.category.name
        },
        {
          property: 'twitter:description',
          content: this.$strippedContent(this.category.description)
        },
        {
          property: 'twitter:image',
          content: ''
        },
      ]
    }
  },
  computed: {
    breaditems() {
      return [
        {
          to: {
            name: "index"
          },
          exact: true,
          text: "Trang chủ",
        },
        {
          to: {
            name: this.$route.name
          },
          disable: true,
          text: 'Sản phẩm thuộc danh mục ' + this.category.name
        }
      ];
    },
    money_range_start() {
      return this.$moneyFormat(this.data.money_range[0]);
    },
    money_range_end() {
      return this.$moneyFormat(this.data.money_range[1]);
    }
  },
  watch: {
    'data.money_range': 'reset',
    'data.rating': 'reset',
    'data.brands': 'reset',
  },
  beforeMount() {
    this.fetchData = this.$debounce(this.fetchData, 500);
  },
  methods: {
    reset() {
      this.pagination.current_page = 1;
      this.fetchData()
    },
    fetchData() {
      this.loading = true;
      let data = {
        params: {
          per_page: this.pagination.per_page,
          page: this.pagination.current_page,
          range: this.data.money_range,
          rating: this.data.rating,
          brands: this.data.brands,
          categories: this.category.id
        }
      };
      this.$axios.get('/products', data).then(res => {
        if (res.status == 200) {
          var data = res.data;
          this.products = data.products;
          this.pagination.total = data.pagination ? data.pagination.total : 0;
          this.pagination.total_pages = data.pagination ? data.pagination.total_pages : 0;
          this.loading = false;
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
    },
  }
}
</script>
