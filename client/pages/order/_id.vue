<template>
  <div>{{ $route.params.id }}</div>
</template>
<script>
export default {
  async asyncData({ app, params }) {
    let { order } = await app.$axios.$get('/orders/'+params.id);
    return {
      order: order
    }
  },
}
</script>
