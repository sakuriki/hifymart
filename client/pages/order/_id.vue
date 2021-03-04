<template>
  <div>{{ $route.params.id }}</div>
</template>
<script>
export default {
  async asyncData({ app, params, error }) {
    try {
      let { order } = await app.$axios.$get('/orders/'+params.id);
      return {
        order: order
      }
    } catch (err) {
      return error({ statusCode: err.response ? err.response.status : 422, message: err.message || 'Có lỗi xảy ra' })
    }
  },
}
</script>
