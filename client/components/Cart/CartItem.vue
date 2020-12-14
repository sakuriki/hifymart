<template>
  <v-list-item>
    <v-badge
      content="1"
      bordered
      offset-x="28"
      offset-y="28"
    >
      <v-list-item-avatar
        rounded
        ma-0
        mr-2
        size="60"
      >
        <v-img src="https://cdn.shopify.com/s/files/1/2695/0984/products/screen1_c85e2d5f-c56e-42a2-8361-47d8cc853fce.png?v=1605990715" />
      </v-list-item-avatar>
    </v-badge>
    <div
      class="flex"
      style="overflow:hidden"
    >
      <v-list-item-title class="mb-1">
        <nuxt-link to="#">
          Titleaaaaaaaaaaaaaaaaaaaaaaaaaâszzzzzzzzzzzzzzzzzzzzzzzzzzzzzz
        </nuxt-link>
      </v-list-item-title>
      <v-list-item-subtitle
        class="red--text text--darken-4 font-weight-black text-body-2 mb-1"
        v-text="'1000000đ'"
      />
      <v-list-item-subtitle class="grey--text text--darken-1 overline">
        <v-text-field
          v-model="quantity"
          type="number"
          solo-inverted
          dense
          append-outer-icon="mdi-plus"
          prepend-icon="mdi-minus"
          hide-details
          @click:append-outer="increment"
          @click:prepend="decrement"
        />
      </v-list-item-subtitle>
    </div>
    <v-spacer />
    <v-list-item-action>
      <v-btn
        icon
        small
        aria-label="Remove Item from Cart"
      >
        <v-icon small>
          mdi-close-circle
        </v-icon>
      </v-btn>
    </v-list-item-action>
  </v-list-item>
</template>
<script>
export default {
  data() {
    return {
      quantity: 1
    }
  },
  methods: {
    increment() {
      this.quantity++
    },
    decrement() {
      this.quantity--
    }
  },
}
</script>
