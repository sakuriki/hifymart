<template>
  <div>
    <v-carousel
      :height="carouselSize"
      :hide-delimiters="hideDelimiters"
    >
      <v-carousel-item
        v-for="(slide, i) in slides"
        :key="i"
      >
        <v-img
          src="https://via.placeholder.com/690x300"
          :height="carouselSize"
          contain
          aspect-ratio="2.3"
        />
      </v-carousel-item>
    </v-carousel>
    <v-layout
      wrap
      row
    >
      <v-col
        cols="12"
        md="8"
        sm="12"
      >
        <ProductList
          title="Đang giảm giá"
          :to="{ name: 'danh-sach-type', params: { type: 'sale' } }"
          :products="latestProducts"
        />
      </v-col>
      <v-col
        cols="12"
        md="4"
        sm="12"
      >
        <v-img
          height="100%"
          src="https://via.placeholder.com/325x400?text=Banner+d%E1%BB%8Dc"
        />
      </v-col>
    </v-layout>
    <v-layout
      wrap
      row
    >
      <v-col
        cols="12"
        md="4"
        sm="12"
      >
        <v-img
          height="100%"
          src="https://via.placeholder.com/325x400?text=Banner+d%E1%BB%8Dc"
        />
      </v-col>
      <v-col
        cols="12"
        md="8"
        sm="12"
      >
        <ProductList
          title="Sản phẩm hot"
          :to="{ name: 'danh-sach-type', params: { type: 'hot' } }"
          :products="latestProducts"
        />
      </v-col>
    </v-layout>
    <v-layout
      wrap
      row
    >
      <v-col
        cols="12"
        md="8"
        sm="12"
      >
        <ProductList
          title="Sản phẩm mới"
          :to="{ name: 'danh-sach-type', params: { type: 'moi' } }"
          :products="latestProducts"
        />
      </v-col>
      <v-col
        cols="12"
        md="4"
        sm="12"
      >
        <v-img
          height="100%"
          src="https://via.placeholder.com/325x400?text=Banner+d%E1%BB%8Dc"
        />
      </v-col>
    </v-layout>
  </div>
</template>

<script>
export default {
  data() {
    return {
      slides: [
        'First',
        'Second',
        'Third',
        'Fourth',
        'Fifth',
      ],
    }
  },
  computed: {
    latestProducts() {
      return this.$store.getters.latestProducts
    },
    carouselSize() {
      return this.$store.getters.browserWidth < 600 ? "152px" : "765px"
    },
    hideDelimiters() {
      return this.$store.getters.browserWidth < 600 ? true : false
    }
  }
}
</script>
