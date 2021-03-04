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
  async asyncData ({ app, error }) {
    try {
      const [latestProducts, randomProducts, saleOffProducts, hottestProducts] = await Promise.all([
        app.$axios.$get("/products?per_page=8"),
        app.$axios.$get("/products?per_page=8&sortBy=random"),
        app.$axios.$get("/products?per_page=8&onsale=true"),
        app.$axios.$get("/products?per_page=8&sortBy=orders_count"),
      ])
      return {
        latestProducts: latestProducts.products,
        randomProducts: randomProducts.products,
        saleOffProducts: saleOffProducts.products,
        hottestProducts: hottestProducts.products,
      }
    } catch (err) {
      return error({ statusCode: err.response ? err.response.status : 422, message: err.message || 'Có lỗi xảy ra' })
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
