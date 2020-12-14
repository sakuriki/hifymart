<template>
  <div class="align-center">
    <v-flex class="d-flex flex flex-row justify-left align-center flex-wrap mb-3">
      <v-flex
        class="h4 text-uppercase flex-column align-left"
      >
        {{ title }}
      </v-flex>
      <v-btn
        large
        depressed
        outlined
        :to="to"
        color="success"
      >
        Xem tất cả
      </v-btn>
    </v-flex>
    <div class="flex d-flex flex-row flex-wrap">
      <div
        v-for="product in products"
        :key="product.id"
        class="flex d-flex flex-column list-vertical"
      >
        <v-card
          class="mx-auto"
          height="100%"
          width="100%"
          nuxt
          :to="{ name: 'san-pham-slug', params:{ slug: product.slug } }"
        >
          <v-img
            src="https://via.placeholder.com/450x450"
            :aspect-ratio="1/1"
          />

          <v-card-title>
            {{ 47 > product.name.length ? product.name : product.name.substring(0, 47) + "..." }}
          </v-card-title>

          <v-card-subtitle>
            <span>{{ moneyFormat(product.price) }}</span>
            <v-rating
              :value="4.5"
              color="amber"
              dense
              half-increments
              readonly
              size="14"
            />
          </v-card-subtitle>
        </v-card>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    products: {
      type: Array,
      default: null
    },
    title: {
      type: String,
      default: null
    },
    to: {
      type: [String, Object],
      default: null
    }
  },
  methods: {
    moneyFormat(number) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number)
    }
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
