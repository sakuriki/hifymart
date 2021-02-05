<template>
  <v-row>
    <v-col
      v-for="product in products"
      :key="product.id"
      cols="6"
      md="3"
      sm="4"
    >
      <v-badge
        :content="`-${calSaleOff(product.price, product.sale_off_price)}%`"
        color="red"
        offset-x="20%"
        offset-y="10%"
        :value="product.sale_off_price"
        tile
        left
        style="width:100%;height:100%"
      >
        <v-hover v-slot="{ hover }">
          <v-card
            class="mx-auto"
            height="100%"
            width="100%"
            nuxt
            :to="{ name: 'product-slug', params:{ slug: product.slug } }"
          >
            <v-img
              :src="apiUrl+product.featured_image"
              :aspect-ratio="1/1"
            />

            <v-card-title>
              {{ 47 > product.name.length ? product.name : product.name.substring(0, 47) + "..." }}
            </v-card-title>
            <v-card-subtitle>
              <span
                class="red--text"
              >
                {{ product.sale_off_price ? $moneyFormat(product.sale_off_price) : $moneyFormat(product.price) }}
                <span
                  v-if="product.sale_off_price"
                  class="pl-1 grey--text text-decoration-line-through"
                >{{ $moneyFormat(product.price) }}</span>
              </span>
              <v-rating
                :value="roundRating(product.ratings_average)"
                color="amber"
                dense
                half-increments
                readonly
                size="14"
              />
            </v-card-subtitle>
            <v-overlay
              absolute
              :value="hover"
            >
              <div class="align-self-center">
                <v-tooltip bottom>
                  <template #activator="{ on, attrs }">
                    <v-icon
                      class="mx-2"
                      v-bind="attrs"
                      v-on="on"
                      @click.prevent="addItem({product, add: 1})"
                    >
                      mdi-cart-plus
                    </v-icon>
                  </template>
                  <span>Thêm vào giỏ hàng</span>
                </v-tooltip>
                <v-tooltip bottom>
                  <template #activator="{ on, attrs }">
                    <v-icon
                      class="mx-2"
                      v-bind="attrs"
                      v-on="on"
                    >
                      mdi-heart-plus
                    </v-icon>
                  </template>
                  <span>Thêm vào wishlist</span>
                </v-tooltip>
                <v-tooltip bottom>
                  <template #activator="{ on, attrs }">
                    <v-icon
                      class="mx-2"
                      v-bind="attrs"
                      v-on="on"
                    >
                      mdi-compare
                    </v-icon>
                  </template>
                  <span>So sánh</span>
                </v-tooltip>
              </div>
            </v-overlay>
          </v-card>
        </v-hover>
      </v-badge>
    </v-col>
  </v-row>
</template>
<script>
import { mapActions } from "vuex";
export default {
  props: {
    products: {
      type: Array,
      default: null
    }
  },
  computed: {
    apiUrl() {
      return process.env.apiUrl
    }
  },
  methods: {
    calSaleOff(price, sale_off_price) {
      if(!sale_off_price) return;
      return Math.round(100 - (sale_off_price/price*100));
    },
    roundRating(rating) {
      return Math.round(rating * 10) / 10;
    },
    ...mapActions('cart', ['addItem']),
    // addItem(item) {
    //   return this.$store.dispatch('cart/addItem', { item, count: 1 })
    //     .then(console.log(item))
    // },

  },
}
</script>
<style scoped>
.list-vertical {
  flex-basis: 50%;
  min-width: 50%;
  padding: 3px
}

@media (min-width: 1201px) {
  .list-vertical {
    flex-basis: 25%;
    min-width: 25%;
    max-width: 25%;
  }
}
.v-card__title {
  font-size: 11px;
  line-height: unset!important;
}
@media (min-width: 600px) {
  .v-card__title {
    font-size: 14px;
  }
}
@media (min-width: 1201px) {
  .v-card__title {
    font-size: 17px;
  }
}
</style>
