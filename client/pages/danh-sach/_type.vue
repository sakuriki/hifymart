<template>
  <div style="width:100%;min-height:100vh">
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
            <v-autocomplete
              :items="[
                'Alabama', 'Alaska', 'American Samoa', 'Arizona',
                'Arkansas', 'California', 'Colorado', 'Connecticut',
                'Delaware', 'District of Columbia', 'Federated States of Micronesia',
                'Florida', 'Georgia', 'Guam', 'Hawaii', 'Idaho',
                'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky',
                'Louisiana', 'Maine', 'Marshall Islands', 'Maryland',
                'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi',
                'Missouri', 'Montana', 'Nebraska', 'Nevada',
                'New Hampshire', 'New Jersey', 'New Mexico', 'New York',
                'North Carolina', 'North Dakota', 'Northern Mariana Islands', 'Ohio',
                'Oklahoma', 'Oregon', 'Palau', 'Pennsylvania', 'Puerto Rico',
                'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee',
                'Texas', 'Utah', 'Vermont', 'Virgin Island', 'Virginia',
                'Washington', 'West Virginia', 'Wisconsin', 'Wyoming',
              ]"
              label="Thương hiệu"
              multiple
              chips
              clearable
              deletable-chips
              hint="Lọc thương hiệu bạn cần tìm"
              persistent-hint
              class="mt-4"
            />
            <v-autocomplete
              :items="[
                'Alabama', 'Alaska', 'American Samoa', 'Arizona',
                'Arkansas', 'California', 'Colorado', 'Connecticut',
                'Delaware', 'District of Columbia', 'Federated States of Micronesia',
                'Florida', 'Georgia', 'Guam', 'Hawaii', 'Idaho',
                'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky',
                'Louisiana', 'Maine', 'Marshall Islands', 'Maryland',
                'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi',
                'Missouri', 'Montana', 'Nebraska', 'Nevada',
                'New Hampshire', 'New Jersey', 'New Mexico', 'New York',
                'North Carolina', 'North Dakota', 'Northern Mariana Islands', 'Ohio',
                'Oklahoma', 'Oregon', 'Palau', 'Pennsylvania', 'Puerto Rico',
                'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee',
                'Texas', 'Utah', 'Vermont', 'Virgin Island', 'Virginia',
                'Washington', 'West Virginia', 'Wisconsin', 'Wyoming',
              ]"
              label="Danh mục sản phẩm"
              clearable
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
import debounce from "lodash/debounce";
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
      text: null,
      products: [],
      pagination: {
        current_page: 1,
        per_page: 16,
        total: 0,
        total_pages: 0,
      },
      data: {
        orderBy: null,
        money_range: [0, 10000000]
      }
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
      return this.moneyFormat(this.data.money_range[0]);
    },
    money_range_end() {
      return this.moneyFormat(this.data.money_range[1]);
    }
  },
  watch: {
    'money_range': 'reset'
  },
  beforeMount() {
    this.prepareData();
  },
  methods: {
    reset() {
      this.pagination.current_page = 1;
      this.fetchData()
    },
    prepareData() {
      this.type = this.$route.params.type;
      if (this.type == "dang-giam-gia") {
        this.data.orderBy = "&onsale=true";
        this.text = "Đang giảm giá";
      } else if (this.type == "mua-nhieu") {
        this.data.orderBy = "orders_count";
        this.text = "Mua nhiều";
      } else if (this.type == "kham-pha") {
        this.data.orderBy = "random";
        this.text = "Khám phá"
      } else {
        this.text = "Sản phẩm mới";
      }
    },
    fetchData: debounce(function() {
      let data = {
        params: {
          per_page: this.pagination.per_page,
          page: this.pagination.current_page,
          orderBy: this.data.orderBy,
          range: this.data.money_range
        }
      };
      this.$axios.get('/products', data).then(res => {
        if (res.status == 200) {
          var data = res.data;
          this.products = data.products;
          this.pagination.total = data.pagination ? data.pagination.total : 0;
          this.pagination.total_pages = data.pagination ? data.pagination.total_pages : 0;
        }
      })
    }, 500),
    onPageChange: function() {
      this.fetchData();
      this.$vuetify.goTo(0, {
        duration: 500,
        offset: 0,
        easing: "easeInOutCubic"
      })
    },
    moneyFormat(number) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number)
    },
  }
}
</script>
