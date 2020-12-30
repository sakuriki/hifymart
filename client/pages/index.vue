<template>
  <div>
    <ClientOnly placeholder="Đang tải...">
      <v-carousel
        :height="carouselSize"
        :hide-delimiters="hideDelimiters"
      >
        <v-carousel-item
          v-for="(slide, i) in slides"
          :key="i"
        >
          <v-img
            src="http://banhang.test/storage/banners/690x300.png"
            :height="carouselSize"
            contain
            aspect-ratio="2.3"
          />
        </v-carousel-item>
      </v-carousel>
    </ClientOnly>
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
  </div>
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
    carouselSize() {
      return this.$store.getters.browserWidth < 600 ? "152px" : "765px"
    },
    hideDelimiters() {
      return this.$store.getters.browserWidth < 600 ? true : false
    }
  }
}
</script>
