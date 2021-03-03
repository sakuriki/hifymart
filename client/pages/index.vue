<template>
  <v-container fill-height>
    <v-carousel
      height="calc(100vw/2.3)"
      class="main-carousel"
    >
      <v-carousel-item
        v-for="slide in slides"
        :key="slide.title"
        :src="apiUrl+slide.url"
        :alt="slide.title"
      />
    </v-carousel>
    <LazyIndexList
      title="Đang giảm giá"
      :to="{ name: 'browser-type', params: { type: 'sale-off' } }"
      :products="saleOffProducts"
    />
    <LazyIndexList
      title="Sản phẩm hot"
      :to="{ name: 'browser-type', params: { type: 'best-selling' } }"
      :products="hottestProducts"
      :left="true"
    />
    <LazyIndexList
      title="Sản phẩm mới"
      :to="{ name: 'browser-type', params: { type: 'new' } }"
      :products="latestProducts"
    />
    <LazyIndexList
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
  async asyncData({ app, error }) {
    try {
        let { products: latestProducts } = await app.$axios.$get("/products?per_page=8");
        let { products: randomProducts } = await app.$axios.$get("/products?per_page=8&sortBy=random");
        let { products: saleOffProducts } = await app.$axios.$get("/products?per_page=8&onsale=true");
        let { products: hottestProducts } = await app.$axios.$get("/products?per_page=8&sortBy=orders_count");
        return {
          latestProducts: latestProducts,
          randomProducts: randomProducts,
          saleOffProducts: saleOffProducts,
          hottestProducts: hottestProducts,
        }
    } catch (err) {
      return error({ statusCode: err.response.status, message: err.message })
    }
  },
  head() {
    return {
      title: "Trang chủ",
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: 'Trang chủ'
        },
        {
          property: 'og:site_name',
          content: process.env.appName
        },
        {
          property: 'og:title',
          content: 'HifyMart - Nuxt Laravel Ecommerce'
        },
        {
          property: 'og:description',
          content: ''
        },
        {
          property: 'og:image',
          content: ''
        },
        {
          property: 'twitter:site',
          content: process.env.appName
        },
        {
          property: 'twitter:card',
          content: 'summary_large_image'
        },
        {
          property: 'twitter:title',
          content: 'HifyMart - Nuxt Laravel Ecommerce'
        },
        {
          property: 'twitter:description',
          content: ''
        },
        {
          property: 'twitter:image',
          content: ''
        },
      ]
    }
  },
  computed: {
    ...mapGetters(['settings']),
    slides() {
      return (!this.settings.slides || this.settings.slides.length <= 0 )
        ? [{
          title: 'banner',
          url: '/storage/banners/690x300.png',
          order: 1
        }]
        : [...this.settings.slides].sort((a,b) => a.order - b.order)
    },
    apiUrl() {
      return process.env.apiUrl
    }
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
