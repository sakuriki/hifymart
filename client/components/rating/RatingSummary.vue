<template>
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
</template>
<script>
export default {
  props: {
    product: {
      type: Object,
      default: null
    }
  },
  computed: {
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
}
</script>
