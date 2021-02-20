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
      class="flex"
      style="overflow:hidden"
    >
      <v-list-item-title class="mb-1">
        <NuxtLink
          class="text-decoration-none"
          :to="{ name: 'product-slug', params: { slug: product.slug } }"
        >
          {{ product.name }}
        </NuxtLink>
      </v-list-item-title>
      <v-list-item-subtitle
        class="red--text text--darken-4 font-weight-black text-body-2 mb-1"
      >
        {{ product.sale_off_price ? $moneyFormat(product.sale_off_price) : $moneyFormat(product.price) }}
        <span
          v-if="product.sale_off_price"
          class="pl-1 grey--text text-decoration-line-through"
        >{{ $moneyFormat(product.price) }}</span>
      </v-list-item-subtitle>
      <v-list-item-subtitle class="grey--text text--darken-1 overline">
        <v-text-field
          :value="product.count"
          type="number"
          solo-inverted
          dense
          append-outer-icon="mdi-plus"
          prepend-icon="mdi-minus"
          hide-details
          style="max-width:200px"
          @click:append-outer="increment"
          @click:prepend="decrement"
          @change="onChange(product.count,$event)"
        />
      </v-list-item-subtitle>
    </div>
    <v-spacer />
    <v-list-item-action>
      <v-btn
        icon
        small
        aria-label="Bỏ sản phẩm khỏi giỏ hàng"
        @click="removeItem(product)"
      >
        <v-icon small>
          mdi-close-circle
        </v-icon>
      </v-btn>
    </v-list-item-action>
  </v-list-item>
</template>
<script>
import { mapActions } from "vuex";
export default {
  name: "CartItem",
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
    onChange(number, event) {
      let newNumber = Number(event-number);
      this.addItem({ product: this.product, add: newNumber })
    }
  },
}
</script>
