<template>
  <v-container
    fill-height
    class="justify-center"
  >
    <v-card>
      <v-card-title>Thanh toán thành công</v-card-title>
      <v-card-text>
        <p>Cảm ơn bạn đã mua hàng tại VietShop</p>
        <p>Mã đơn hàng: #{{ order.id }}</p>
      </v-card-text>
      <v-card-actions>
        <v-btn
          color="primary"
          :to="{ name: 'order-id', params: { id: order.id } }"
        >
          Xem đơn hàng
        </v-btn>
        <v-btn
          color="primary"
          to="/"
        >
          Quay lại shop
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
</template>
<script>
export default {
  async asyncData({ app, query }) {
    let { success, order } = await app.$axios.$get('/payment/vnpay/process', { params: query });
    return {
      success: success,
      order: order
    }
  },
  mounted() {
    console.log(this.$route)
  },
}
</script>
