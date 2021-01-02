<template>
  <div style="width:100%;min-height:100vh">
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
                />
              </v-col>
            </v-row>
            <v-text-field
              v-model="search"
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
            <v-autocomplete
              v-model="data.category"
              :items="categories"
              label="Danh mục sản phẩm"
              clearable
              item-text="name"
              item-value="id"
              hint="Chọn danh mục sản phẩm bạn muốn"
              persistent-hint
              class="mt-4"
            />
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
  </div>
</template>
<script>
// import debounce from "lodash/debounce";
import simplebar from "simplebar-vue";
import "simplebar/dist/simplebar.min.css";
export default {
  components: {
    simplebar,
  },
  validate({ params }) {
    let allow = ["sale-off", "best-selling", "new", "explore"];
    return allow.includes(params.type);
  },
  async asyncData({app, params}) {
    let string;
    if (params.type == "sale-off") {
      string = "&onsale=true"
    } else if (params.type == "best-selling") {
      string = "orders_count"
    } else if (params.type == "explore") {
      string = "random"
    }
    let { products, pagination } = await app.$axios.$get(`/products?per_page=16&orderBy=${string}`);
    let { brands } = await app.$axios.$get('/brands');
    let { categories } = await app.$axios.$get('/categories');
    return {
      products: products,
      pagination : pagination,
      brands: brands,
      categories: categories
    }
  },
  data() {
    return {
      search: null,
      type: null,
      text: null,
      loading: false,
      products: [],
      brands: [],
      categories: [],
      pagination: {
        current_page: 1,
        per_page: 16,
        total: 0,
        total_pages: 0,
      },
      data: {
        orderBy: null,
        money_range: [0, 10000000],
        rating: null,
        brands: [],
        category: null
      },
      tree: [
        {
          id: 1,
          name: "mot"
        },
        {
          id: 2,
          name: "hai"
        },
        {
          id: 3,
          name: "ba"
        },
        {
          id: 4,
          name: "ba"
        },
        {
          id: 5,
          name: "ba"
        },
        {
          id: 6,
          name: "bon"
        },
        {
          id: 7,
          name: "bon"
        },
        {
          id: 8,
          name: "f"
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
          text: this.text
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
    'data.category': 'reset',
    'data.brands': 'reset',
  },
  beforeMount() {
    this.fetchData = this.$debounce(this.fetchData, 500);
    this.prepareData();
  },
  methods: {
    reset() {
      this.pagination.current_page = 1;
      this.fetchData()
    },
    prepareData() {
      this.type = this.$route.params.type;
      if (this.type == "sale-off") {
        this.data.orderBy = "&onsale=true";
        this.text = "Đang giảm giá";
      } else if (this.type == "best-selling") {
        this.data.orderBy = "orders_count";
        this.text = "Mua nhiều";
      } else if (this.type == "explore") {
        this.data.orderBy = "random";
        this.text = "Khám phá"
      } else {
        this.text = "Sản phẩm mới";
      }
    },
    fetchData() {
      this.loading = true;
      let data = {
        params: {
          per_page: this.pagination.per_page,
          page: this.pagination.current_page,
          orderBy: this.data.orderBy,
          range: this.data.money_range,
          rating: this.data.rating,
          brands: this.data.brands,
          category: this.data.category
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
