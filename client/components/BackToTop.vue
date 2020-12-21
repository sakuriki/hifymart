<template>
  <v-btn
    v-show="show"
    v-scroll="onScroll"
    fab
    dark
    fixed
    bottom
    right
    color="primary"
    @click="backToTop"
  >
    <v-icon>mdi-chevron-up</v-icon>
  </v-btn>
</template>
<script>
export default {
  data() {
    return {
      show: false
    }
  },
  methods: {
    onScroll: function(e) {
      if (typeof window === 'undefined') return;
      const top = window.pageYOffset || e.target.scrollTop || window.scrollY || 0;
      this.show = top > 20;
    },
    backToTop: function() {
      this.$vuetify.goTo(0)
    }
  }
};
</script>
