<template>
  <v-row>
    <v-col
      v-if="left"
      cols="12"
      md="4"
      sm="12"
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
    >
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
        <ProductList :products="products" />
      </div>
    </v-col>
    <v-col
      v-if="!left"
      cols="12"
      md="4"
      sm="12"
    >
      <v-img
        height="100%"
        :src="banner"
      />
    </v-col>
  </v-row>
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
      default: "http://banhang.test/storage/banners/325x400.png"
    }
  }
}
</script>
