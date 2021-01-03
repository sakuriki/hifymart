<template>
  <v-container fill-height>
    <v-carousel
      height="calc(100vw/2.3)"
      class="main-carousel"
    >
      <v-carousel-item
        v-for="(slide, i) in slides"
        :key="i"
        src="http://banhang.test/storage/banners/690x300.png"
      />
    </v-carousel>
    <IndexList
      title="Đang giảm giá"
      :to="{ name: 'browser-type', params: { type: 'sale-off' } }"
      :products="onsaleProducts"
    />
    <IndexList
      title="Sản phẩm hot"
      :to="{ name: 'browser-type', params: { type: 'best-selling' } }"
      :products="hottestProducts"
      :left="true"
    />
    <IndexList
      title="Sản phẩm mới"
      :to="{ name: 'browser-type', params: { type: 'new' } }"
      :products="latestProducts"
    />
    <IndexList
      title="Có thể bạn quan tâm"
      :to="{ name: 'browser-type', params: { type: 'explore' } }"
      :products="randomProducts"
      :left="true"
    />
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex';
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
    ...mapGetters([
      'latestProducts',
      'randomProducts',
      'onsaleProducts',
      'hottestProducts',
    ]),
    // carouselSize() {
    //   return this.$store.getters.browserWidth < 600 ? "152px" : "765px"
    // },
    // hideDelimiters() {
    //   return this.$store.getters.browserWidth < 600 ? true : false
    // }
  }
}
</script>
<style scoped>
@media only screen and (max-width: 599px) {
  .main-carousel >>> .v-carousel__controls {
    display: none!important;
  }
}
.main-carousel, .main-carousel >>> .v-image, .main-carousel >>> .v-window__container {
  max-height: 765px;
  min-height: 152px;
}
</style>
