<template>
  <v-layout
    wrap
    style="height: unset"
  >
    <v-col
      cols="12"
      md="4"
      sm="12"
      :order="left ? 0 : 1"
    >
      <v-img
        height="100%"
        :src="banner"
      />
    </v-col>
    <v-col
      cols="12"
      md="8"
      sm="12"
      :order="left ? 1 : 0"
    >
      <div class="align-center">
        <v-flex class="d-flex flex flex-row justify-left align-center flex-wrap mb-3">
          <v-flex
            class="h4 text-uppercase flex-column align-left"
          >
            {{ title }}
          </v-flex>
          <v-btn
            dark
            large
            depressed
            outlined
            :to="to"
            color="success"
          >
            Xem tất cả
          </v-btn>
        </v-flex>
        <ProductList :products="products" />
      </div>
    </v-col>
  </v-layout>
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
    },
    left: {
      type: Boolean,
      default: false
    },
    banner: {
      type: String,
      default: "http://via.placeholder.com/325x600.png?text=Banner%20d%E1%BB%8Dc"
    }
  }
}
</script>
