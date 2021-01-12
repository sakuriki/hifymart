<template>
  <v-container fill-height>
    <v-row>
      <v-col
        cols="12"
        md="8"
      >
        <v-card>
          <v-card-title>Đánh giá {{ product.name }}</v-card-title>
          <v-card-text>
            <v-row>
              <v-col
                class="d-flex flex-row align-center justify-center"
                cols="12"
                md="4"
              >
                <v-row>
                  <v-col cols="5">
                    <v-img
                      :key="selected_image"
                      :src="appUrl+selected_image"
                      :aspect-ratio="1"
                    />
                  </v-col>
                  <v-col cols="7">
                    <NuxtLink
                      class="text-h6 product-title"
                      :to="{ name: 'product-slug', params: { slug: $route.params.slug } }"
                    >
                      {{ product.name }}
                    </NuxtLink>
                  </v-col>
                </v-row>
              </v-col>
              <v-col
                class="d-flex flex-column align-center justify-center"
                cols="12"
                md="4"
              >
                <template v-if="product.ratings_count>0">
                  <span>Đánh giá trung bình</span>
                  <span class="text-h5 red--text">{{ roundRating + "/5" }}</span>
                  <span>({{ product.ratings_count }} đánh giá)</span>
                </template>
                <span v-else>Chưa có đánh giá, hãy là người đầu tiên</span>
              </v-col>
              <v-col
                cols="12"
                md="4"
              >
                <div
                  v-for="star in [5,4,3,2,1]"
                  :key="star"
                  class="d-flex flex-wrap align-center"
                >
                  <div
                    class="d-flex"
                  >
                    <span>{{ star }}</span>
                    <v-icon
                      small
                      color="amber"
                    >
                      mdi-star
                    </v-icon>
                  </div>
                  <v-flex>
                    <v-progress-linear
                      :value="ratingWithPercentage[star].percentage"
                      height="10"
                      rounded
                      class="my-1"
                    />
                  </v-flex>
                  <div
                    class="d-flex col-1 pa-0 ml-1"
                  >
                    <span>{{ (ratingWithPercentage[star].percentage||0)+"%" }}</span>
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-card-text>
          <v-divider />
          <Rating :ratings="ratings" />
          <v-card-text>
            <v-btn
              v-if="total > (data.offset + data.page_size)"
              color="primary"
              :loading="loading"
              outlined
              block
              @click="fetchReview"
            >
              Tải thêm
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col
        cols="12"
        md="4"
      >
        <v-card>
          <v-card-text>
            Ảnh
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
export default {
  async asyncData({ app, params }) {
    let { product } = await app.$axios.$get("/products/" + params.slug);
    let { ratings, total } = await app.$axios.$get("/ratings/" + product.id + "?page_size=10");
    return {
      product: product,
      total: total,
      ratings: ratings,
      selected_image: product.featured_image,
    }
  },
  data() {
    return {
      data: {
        page_size: 10,
        offset: 0
      },
      loading: false
    }
  },
  computed: {
    appUrl() {
      return process.env.apiUrl
    },
    roundRating() {
      return Math.round(this.product.ratings_average * 10) / 10;
    },
    ratingWithPercentage() {
      let t = this;
      let base = {
        1: {
          percentage: 0,
          total: 0
        },
        2: {
          percentage: 0,
          total: 0
        },
        3: {
          percentage: 0,
          total: 0
        },
        4: {
          percentage: 0,
          total: 0
        },
        5: {
          percentage: 0,
          total: 0
        }
      };
      return Object.values(this.product.ratings).reduce((acc, item) => Object.assign(acc, {
        [item.rating]: {
          percentage: Math.round(item.total / t.product.ratings_count * 100),
          total: item.total
        }
      }), base);
    }
  },
  methods: {
    async fetchReview() {
      this.loading = true;
      this.data.offset += this.data.page_size;
      let config = {
        params: this.data
      }
      let { ratings } = await this.$axios.$get("/ratings/" + this.product.id, config);
      // let a = JSON.parse(JSON.stringify(this.ratings));
      // a.push.apply(a, ratings)
      // console.log(a);
      this.ratings.push.apply(this.ratings, ratings);
      this.loading = false;
    },
  },
}
</script>
<style scoped>
.product-title {
  display: -webkit-box;
  max-width: 200px;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
