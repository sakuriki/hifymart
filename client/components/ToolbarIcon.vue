<template>
  <div class="hidden-sm-and-down df flex-wrap flex-row align-center justify-center">
    <div class="toolbar_icon d-flex align-center justify-center">
      <v-icon>{{ icon }}</v-icon>
    </div>
    <div class="ml-1 d-flex flex-column justify-center">
      <slot />
    </div>
  </div>
</template>
<script>
export default {
  props: {
    icon: {
      type: String,
      default: null
    }
  }
}
</script>
<style scoped>
.toolbar_icon {
  height: 40px!important;
  width: 40px!important;
  border-radius: 50%;
  border: 2px solid #fff;
}
.df {
  display: flex
}
</style>

