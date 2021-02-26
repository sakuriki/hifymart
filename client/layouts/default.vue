<template>
  <v-app
    id="top"
    v-resize="updateBrowserDimensions"
  >
    <v-navigation-drawer
      v-model="drawer"
      app
      fixed
      floating
      temporary
      width="250"
      hide-overlay
    >
      <simplebar
        style="height: 100%"
      >
        <v-divider :style="`margin-top: ${$vuetify.application.bar+$vuetify.application.top}px`" />
        <v-list
          nav
          dense
          shaped
        >
          <div
            v-for="item in items"
            :key="item.group"
          >
            <v-divider
              v-if="item.divider"
              class="my-1"
            />
            <v-subheader
              v-if="item.subheader"
              inset
            >
              {{ item.subheader }}
            </v-subheader>
            <v-list-group
              v-if="item.children"
              :prepend-icon="item.icon"
              no-action
              :group="item.group"
            >
              <template #activator>
                <v-list-item-content>
                  <v-list-item-title v-text="item.title" />
                </v-list-item-content>
              </template>
              <v-list-item
                v-for="(subItem, i) in item.children"
                :key="'item-'+i"
                :to="subItem.to"
                nuxt
                exact
              >
                <v-list-item-content>
                  <v-list-item-title v-text="subItem.title" />
                </v-list-item-content>
              </v-list-item>
            </v-list-group>
            <v-list-item
              v-else
              :to="item.to"
              nuxt
              exact
            >
              <v-list-item-icon>
                <v-icon v-text="item.icon" />
              </v-list-item-icon>

              <v-list-item-content>
                <v-list-item-title v-text="item.title" />
              </v-list-item-content>
            </v-list-item>
          </div>
        </v-list>
        <div :style="`margin-bottom: ${$vuetify.application.bar+$vuetify.application.top}px`" />
      </simplebar>
    </v-navigation-drawer>
    <SystemBar />
    <v-app-bar
      id="main-app-bar"
      v-scroll="onScroll"
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
    <SideCart />
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
      <GlobalSnackBar />
    </v-main>
    <Footer />
  </v-app>
</template>
<script>
import simplebar from "simplebar-vue";
import "simplebar/dist/simplebar.min.css";
import { mapGetters } from "vuex";
export default {
  components: {
    simplebar
  },
  data() {
    return {
      phone: false,
      searchText: "",
      drawer: null,
    }
  },
  computed: {
    ...mapGetters(['brands', 'categories','settings']),
    ...mapGetters('cart', ['total']),
    items() {
      return [
        {
          icon: "mdi-home",
          title: "Trang chủ",
          to: "/",
        },
        {
          icon: "mdi-sale",
          title: "Giảm giá",
          to: "/browser/sale-off",
          divider: true,
          subheader: 'Duyệt'
        },
        {
          icon: "mdi-basket-fill",
          title: "Mua nhiều",
          to: "/browser/best-selling",
        },
        {
          icon: "mdi-alert-decagram",
          title: "Sản phẩm mới",
          to: "/browser/new",
        },
        {
          icon: "mdi-shuffle-variant",
          title: "Khám phá",
          to: "/browser/explore",
        },
        {
          group: "/brand",
          icon: "mdi-alpha-n-box-outline",
          title: "Nhãn hiệu",
          children: this.brands.map(i => ({ title: i.name, to: '/brand/' + i.slug})),
          divider: true,
          subheader: 'Tài nguyên'
        },
        {
          group: "/category",
          icon: "mdi-alpha-d-box-outline",
          title: "Danh mục",
          children: this.categories.map(i => ({ title: i.name, to: '/category/' + i.slug})),
        },
      ];
    }
  },
  beforeMount() {
    this.updateBrowserDimensions()
  },
  methods: {
    onSearchFocus() {

    },
    onScroll() {

    },
    updateBrowserDimensions: function () {
      this.$store.commit('SET_BROWSER_WIDTH', Math.max(document.body.scrollWidth, document.documentElement.scrollWidth, document.body.offsetWidth, document.documentElement.offsetWidth, document.documentElement.clientWidth, 320));
      this.$store.commit('SET_BROWSER_HEIGHT', window.innerHeight);
    },
    openSideCart() {
      this.$nuxt.$emit("open-side-cart", "haha")
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
