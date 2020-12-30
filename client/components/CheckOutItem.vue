<template>
  <v-list-item>
    <v-badge
      :content="product.count"
      bordered
      offset-x="28"
      offset-y="28"
      :value="product.count>1"
    >
      <v-list-item-avatar
        rounded
        size="60"
      >
        <v-img :src="apiUrl+product.featured_image" />
      </v-list-item-avatar>
    </v-badge>
    <div
      class="overflow"
    >
      <NuxtLink
        class="text-decoration-none"
        :to="{ name: 'product-slug', params: { slug: product.slug } }"
      >
        {{ product.name }}
      </NuxtLink>
    </div>
    <v-spacer />
    <v-list-item-action-text
      class="d-inline-flex red--text text--darken-4 font-weight-black text-body-2 mb-1"
    >
      {{ product.sale_off_price ? moneyFormat(product.sale_off_price*product.count) : moneyFormat(product.price*product.count) }}
    </v-list-item-action-text>
  </v-list-item>
</template>
<script>
import { mapActions } from "vuex";
export default {
  props: {
    product: {
      type: Object,
      default: null
    }
  },
  computed: {
    apiUrl() {
      return process.env.apiUrl
    }
  },
  methods: {
    ...mapActions('cart', ['addItem', 'removeItem']),
    increment() {
      return this.product.quantity > this.product.count && this.addItem({ product: this.product, add: 1 })
    },
    decrement() {
      return this.product.count > 1 && this.addItem({ product: this.product, add: -1 })
    },
    moneyFormat(number) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number)
    },
    onChange(number, event) {
      // console.log(number, event);
      // let target = event < 1 ? 1 : event;
      let newNumber = Number(event-number);
      // if(newNumber==0) return
      this.addItem({ product: this.product, add: newNumber })
    }
  },
}
</script>
<style scoped>
.overflow {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis
}
</style>
