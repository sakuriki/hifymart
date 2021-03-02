<template>
  <v-app id="top">
    <LazyNavDrawer v-model="drawer" />
    <LazySystemBar />
    <v-app-bar
      id="main-app-bar"
      dark
      app
      fixed
      clipped-left
      color="primary"
      style="z-index: 7"
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title class="headline">
        <NuxtLink
          class="toolbar-title white--text text-decoration-none"
          to="/"
        >
          {{ settings['app-name'] }}
        </NuxtLink>
      </v-toolbar-title>
      <v-spacer class="hidden-sm-and-down" />
      <v-text-field
        ref="searchInput"
        v-model="searchText"
        color="#fff"
        label="Tìm kiếm"
        single-line
        hide-details
        class="default-appbar-search hidden-sm-and-down"
        height="40"
        @focus="onSearchFocus"
      />
      <v-spacer class="hidden-sm-and-down" />
      <div class="d-flex flex-row align-center justify-center">
        <ToolbarIcon
          icon="mdi-phone"
        >
          <a :href="'tel:'+settings['contact-phone']">{{ settings['contact-phone'] }}</a>
          <span style="font-size:12px;color: #ffba00">DĐ: {{ settings['contact-mobile'] }}</span>
        </ToolbarIcon>
        <ToolbarIcon
          class="pl-4 pointer"
          icon="mdi-cart"
          @click.native="openSideCart"
        >
          <span>({{ total }}) Sản phẩm</span>
          <span style="font-size:12px">Giỏ hàng</span>
        </ToolbarIcon>
      </div>
    </v-app-bar>
    <LazySideCart />
    <v-main>
      <nuxt />
      <v-btn
        fab
        dark
        fixed
        bottom
        left
        color="success"
        :href="'tel:'+settings['contact-phone']"
        @mouseover="phone = true"
        @mouseleave="phone = false"
      >
        <v-icon>mdi-phone</v-icon>
      </v-btn>
      <v-slide-x-transition>
        <v-btn
          v-show="phone"
          dark
          fixed
          bottom
          left
          color="success"
          style="left:60px;bottom:26px;z-index:3"
        >
          {{ settings['contact-phone'] }}
        </v-btn>
      </v-slide-x-transition>
      <LazyGlobalSnackBar />
    </v-main>
    <LazyFooter />
  </v-app>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      phone: false,
      searchText: "",
      drawer: null,
    }
  },
  computed: {
    ...mapGetters(['settings']),
    ...mapGetters('cart', ['total'])
  },
  methods: {
    onSearchFocus() {

    },
    openSideCart() {
      this.$nuxt.$emit("open-side-cart")
    },
  },
}
</script>
<style scoped>
a {
  color: #fff;
}
a:hover {
  color: inherit;
}
</style>
