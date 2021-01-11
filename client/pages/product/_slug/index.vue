<template>
  <v-container fill-height>
    <v-layout wrap>
      <v-col
        cols="12"
        md="8"
      >
        <v-card>
          <v-card-text>
            <v-row>
              <v-col
                cols="12"
                md="4"
              >
                <v-img
                  :key="selected_image"
                  :src="appUrl+selected_image"
                  aspect-ratio="1"
                />
                <v-row class="mx-n1 my-1">
                  <v-col
                    cols="3"
                    md="2"
                    class="pa-1"
                  >
                    <div class="d-flex align-center">
                      <v-img
                        :src="appUrl+product.featured_image"
                        aspect-ratio="1"
                        style="cursor: pointer"
                        :class="{ 'img-highlight': product.featured_image == selected_image }"
                        @mouseover="onImageHover(product.featured_image)"
                      />
                    </div>
                  </v-col>
                  <v-col
                    v-for="image in product.images"
                    :key="image.url"
                    cols="3"
                    md="2"
                    class="pa-1"
                  >
                    <div class="d-flex align-center">
                      <v-img
                        :src="appUrl+image.url"
                        aspect-ratio="1"
                        style="cursor: pointer"
                        :class="{ 'img-highlight': image.url == selected_image }"
                        @mouseover="onImageHover(image.url)"
                      />
                    </div>
                  </v-col>
                </v-row>
              </v-col>
              <v-col
                cols="12"
                md="8"
                style="line-height:2rem"
                class="d-flex flex-column"
              >
                <span class="text-h4">
                  {{ product.name }}
                </span>
                <div class="d-flex flex-wrap align-center">
                  <v-rating
                    class="d-flex"
                    :value="roundRating"
                    color="amber"
                    dense
                    half-increments
                    readonly
                    large
                  />
                  <v-btn
                    text
                    color="primary"
                    class="pl-2 d-flex"
                    @click="scrollToReview"
                  >
                    <span v-if="product.ratings_count>0">Xem {{ product.ratings_count }} đánh giá</span>
                    <span v-else>Làm người đánh giá đầu tiên</span>
                  </v-btn>
                </div>
                <div class="d-flex flex-wrap align-center">
                  <span>Thương hiệu: <NuxtLink
                    class="text-decoration-none"
                    :to="{ name: 'brand-slug', params: { slug: product.brand.slug } }"
                  >{{ product.brand.name }}</NuxtLink></span>
                  <span class="px-2">|</span>
                  <span>Tình trạng: <span class="primary--text">{{ product.quantity > 0 ? "Còn hàng" : "Hết hàng" }}</span></span>
                </div>
                <span
                  class="red--text text-h4"
                >
                  {{ product.sale_off_price ? $moneyFormat(product.sale_off_price) : $moneyFormat(product.price) }}
                  <span
                    v-if="product.sale_off_price"
                    class="pl-1 grey--text text-decoration-line-through"
                  >{{ $moneyFormat(product.price) }}</span>
                </span>
                <div>
                  <span>Số lượng:</span>
                  <v-text-field
                    v-model="data.count"
                    class="shrink text-field-center"
                    style="max-width:150px"
                    type="number"
                    solo-inverted
                    dense
                    append-outer-icon="mdi-plus"
                    prepend-icon="mdi-minus"
                    hide-details
                    @click:append-outer="data.count++"
                    @click:prepend="data.count > 1 && data.count--"
                    @change="onCountChange"
                  />
                </div>
                <div class="mt-2">
                  <span>Giới thiệu:</span>
                  <span>{{ product.description }}</span>
                </div>
                <v-spacer />
                <v-row class="align-center mx-0 mt-2">
                  <v-col
                    cols="12"
                    md="6"
                    class="pa-1"
                  >
                    <v-btn
                      class="d-flex multi-line"
                      color="success"
                      width="100%"
                      x-large
                    >
                      <span class="subtitle-1">Thêm vào giỏ</span>
                      <span class="subtitle-2 text-none">Tiếp tục mua sắm</span>
                    </v-btn>
                  </v-col>
                  <v-col
                    cols="12"
                    md="6"
                    class="pa-1"
                  >
                    <v-btn
                      class="d-flex multi-line"
                      color="info"
                      width="100%"
                      x-large
                    >
                      <span class="subtitle-1">Mua ngay</span>
                      <span class="subtitle-2 text-none">Giao hàng miễn phí tận nơi</span>
                    </v-btn>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
        <v-card class="mt-2">
          <v-card-title>Nhận xét của khách hàng:</v-card-title>
          <v-card-text>
            <v-row>
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
              <v-col
                class="d-flex flex-column align-center justify-center"
                cols="12"
                md="4"
              >
                <v-btn
                  color="red"
                  dark
                >
                  Viết đánh giá của bạn
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
          <v-divider />
          <v-card-text v-if="product.ratings_count<=0">
            Chưa có đánh giá
          </v-card-text>
          <template v-else>
            <Rating :ratings="ratings" />
            <v-btn
              color="primary"
              outlined
              :to="{ name: 'product-slug-reviews', params: { slug: $route.params.slug } }"
              class="ma-2"
            >
              Xem tất cả đánh giá
              <v-icon right>
                mdi-chevron-right
              </v-icon>
            </v-btn>
          </template>
        </v-card>
        <v-card class="mt-2">
          <v-card-title>Hỏi và đáp ({{ total_comment }} Bình luận):</v-card-title>
          <CommentForm />
          <v-card-text>
            <v-textarea
              outlined
              hide-details
              label="Xin mời để lại câu hỏi"
            />
            <div class="d-flex pt-2">
              <v-spacer />
              <v-btn color="primary">
                Đăng bình luận
              </v-btn>
            </div>
          </v-card-text>
          <v-divider />
          <v-card-text v-if="total_comment<=0">
            Để lại câu hỏi đầu tiên
          </v-card-text>
          <template v-else>
            <div
              v-for="comment in comments"
              :key="comment.id"
            >
              <v-card-title>
                <v-avatar
                  size="36"
                  color="indigo"
                >
                  <span class="white--text">{{ comment.user.name.slice(0,1) }}</span>
                </v-avatar>
                <span class="ml-2">{{ comment.user.name }}</span>
              </v-card-title>
              <v-card-text class="pb-0">
                <span>{{ comment.content }}</span>
              </v-card-text>
              <v-card-actions>
                <v-btn
                  text
                  small
                  color="primary"
                >
                  <v-icon left>
                    mdi-reply
                  </v-icon>
                  <span>Trả lời</span>
                </v-btn>
                <v-divider vertical />
                <span class="pl-2 text-caption">{{ cal_time_ago(comment.created_at) }}</span>
              </v-card-actions>
              <div
                v-for="child in comment.replies"
                :key="child.id"
                class="comment-reply"
              >
                <v-divider />
                <v-card-title>
                  <v-avatar
                    size="36"
                    color="indigo"
                  >
                    <span class="white--text">{{ child.user.name.slice(0,1) }}</span>
                  </v-avatar>
                  <span class="ml-2">{{ child.user.name }}</span>
                </v-card-title>
                <v-card-text class="pb-0">
                  <span>{{ child.content }}</span>
                </v-card-text>
                <v-card-actions>
                  <v-btn
                    text
                    small
                    color="primary"
                  >
                    <v-icon left>
                      mdi-reply
                    </v-icon>
                    <span>Trả lời</span>
                  </v-btn>
                  <v-divider vertical />
                  <span class="pl-2 text-caption">{{ cal_time_ago(child.created_at) }}</span>
                </v-card-actions>
              </div>
              <v-divider />
            </div>
            <!-- <v-pagination
              v-model="pagination.current_page"
              :length="pagination.total_pages"
              :total-visible="7"
              :disabled="loading"
            /> -->
          </template>
        </v-card>
      </v-col>
      <v-col
        cols="12"
        md="4"
      >
        <v-card>
          <v-card-text>
            <v-list three-line>
              <v-subheader>Chính sách</v-subheader>
              <v-list-item-group>
                <v-list-item
                  v-for="(item, i) in items"
                  :key="i"
                >
                  <v-list-item-icon>
                    <v-icon v-text="item.icon" />
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title v-text="item.text" />
                    <v-list-item-subtitle v-text="item.subtext" />
                  </v-list-item-content>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-layout>
  </v-container>
</template>
<script>
export default {
  async asyncData({ app, params }) {
    let { product } = await app.$axios.$get("/products/" + params.slug);
    let { ratings, pagination } = await app.$axios.$get("/ratings/" + product.id);
    let { comments, total } = await app.$axios.$get("/comments/" + product.id);
    return {
      product: product,
      pagination: pagination,
      ratings: ratings,
      selected_image: product.featured_image,
      comments: comments,
      total_comment: total
    }
  },
  data() {
    return {
      items: [
        {
          icon: "mdi-truck",
          text: "Giao hàng miễn phí toàn quốc",
          subtext: "cho đơn hàng giá trị từ 300.000đ trở lên"
        },
        {
          icon: "mdi-city",
          text: "Hà Nội",
          subtext: "nhận hàng từ 6 đến 24h sau khi đặt hàng"
        },
        {
          icon: "mdi-map-marker",
          text: "Các tỉnh thành khác",
          subtext: "nhận hàng từ 24 đến 96h sau khi đặt hàng"
        },
      ],
      data: {
        count: 1
      },
      selected_image: null,
      loading: false,
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
  watch: {
    'pagination.current_page': 'fetchReview'
  },
  methods: {
    onCountChange() {
      console.log(this.data.count)
    },
    scrollToReview() {
      this.$vuetify.goTo(0)
    },
    onImageHover(img) {
      this.selected_image = img;
    },
    async fetchReview() {
      this.loading = true;
      let config = {
        params: {
          page: this.pagination.current_page
        }
      }
      let { ratings, pagination } = await this.$axios.$get("/ratings/" + this.product.id, config);
      this.pagination = pagination;
      this.ratings = ratings;
      this.loading = false;
      this.$vuetify.goTo('#user_review')
    },
    a() {
      return
    },
    cal_time_ago: function(time) {
      var timeDiff = new Date() - new Date(time);
      if (timeDiff <= 60*1000)
        return "vài giây trước";
      if (timeDiff <= 60*60*1000) {
        var min = parseInt(timeDiff / (60*1000));
        return min < 2 ? "một phút trước" : "".concat(min, " phút trước")
      }
      if (timeDiff <= 60*60*24*1000) {
        var hour = parseInt(timeDiff / (60*60*1000));
        return hour < 2 ? "một giờ trước" : "".concat(hour, " giờ trước")
      }
      if (timeDiff <= 60*60*24*7*1000) {
        var day = parseInt(timeDiff / (60*60*24*1000));
        return day < 2 ? "hôm qua" : "".concat(day, " ngày trước")
      }
      if (timeDiff <= 60*60*24*30*1000) {
        var week = parseInt(timeDiff / (60*60*24*7*1000));
        return week < 2 ? "tuần trước" : "".concat(week, " tuần trước")
      }
      timeDiff = parseInt(timeDiff / (60*60*24*30*1000));
      return "".concat(timeDiff, " tháng trước")
    }
  },
}
</script>
<style scoped>
.text-field-center >>> input {
  text-align: center;
}
.text-field-center >>> input[type='number']::-webkit-inner-spin-button, input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.comment-reply {
  padding-left: 40px;
}
@media (min-width: 600px) {
  .comment-reply {
    padding-left: 60px;
  }
}
</style>
