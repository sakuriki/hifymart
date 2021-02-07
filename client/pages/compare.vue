<template>
  <v-container fill-height>
    <div v-for="product in products" :key="product.id">
      {{ product }}
    </div>
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
  head() {
    return {
      title: "So sánh sản phẩm",
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: 'So sánh thông tin sản phẩm'
        },
        {
          property: 'og:site_name',
          content: process.env.appName
        },
        {
          property: 'og:title',
          content: 'So sánh sản phẩm'
        },
        {
          property: 'og:description',
          content: 'So sánh sản phẩm'
        },
        // {
        //   property: 'og:image',
        //   content: ''
        // },
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
          content: 'So sánh sản phẩm'
        },
        {
          property: 'twitter:description',
          content: 'So sánh sản phẩm'
        },
        // {
        //   property: 'twitter:image',
        //   content: ''
        // },
      ]
    }
  },
  computed: {
    ...mapGetters('compare', ['products'])
  }
}
</script>
