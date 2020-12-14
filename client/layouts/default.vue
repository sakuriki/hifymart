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
      <v-toolbar-title
        class="headline text-uppercase"
      >
        <nuxt-link
          class="toolbar-title"
          to="/"
        >
          shop.re
        </nuxt-link>
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
          <a href="tel:01212122">01212122</a>
          <span style="font-size:12px;color: #ffba00">DĐ: 01212123</span>
        </ToolbarIcon>
        <ToolbarIcon
          class="pl-4"
          icon="mdi-cart"
          style="cursor: pointer"
          @click.native="cart_drawer = !cart_drawer"
        >
          <span>(6) sản phẩm</span>
          <span style="font-size:12px">Giỏ hàng</span>
        </ToolbarIcon>
      </div>
    </v-app-bar>
    <v-navigation-drawer
      v-model="cart_drawer"
      app
      right
      fixed
      temporary
      hide-overlay
      width="340"
    >
      <div class="d-flex align-center justify-space-between py-4 px-4">
        <span class="text-h6 black--text">
          Giỏ hàng
        </span>
        <v-btn
          fab
          small
          depressed
          @click="cart_drawer = false"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </div>

      <v-divider />

      <v-list two-line>
        <template v-if="items.length>0">
          <CartItem
            v-for="item in items"
            :key="item.title"
            :title="item.title"
          />
        </template>
        <div
          v-else
          class="ma-4"
        >
          <v-list-item class="mb-4">
            <v-list-item-title class="text-center body-2">
              Giỏ hàng trống
            </v-list-item-title>
          </v-list-item>
          <v-btn
            block
            color="accent"
            @click="cart_drawer = false"
          >
            Tiếp tục shopping
          </v-btn>
        </div>
        <v-divider />
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <v-container
        fill-height
      >
        <nuxt />
      </v-container>
    </v-main>
  </v-app>
</template>
<script>
import simplebar from "simplebar-vue";
import "simplebar/dist/simplebar.min.css";
export default {
  components: {
    simplebar,
  },
  data() {
    return {
      searchText: "",
      drawer: null,
      cart_drawer: null,
      items2: [
        { title: 'Home', icon: 'mdi-view-dashboard' },
        { title: 'About', icon: 'mdi-forum' },
      ],
      items: [
      {
        icon: "mdi-home",
        title: "Trang chủ",
        to: "/",
      },
      {
        group: "/admin/products",
        icon: "mdi-alpha-s-box-outline",
        title: "Sản phẩm",
        children: [
          {
            title: "Danh sách sản phẩm",
            to: "/admin/products",
          },
          {
            title: "Thêm mới",
            to: "/admin/products/add",
          },
        ],
      },
      {
        group: "/admin/brands",
        icon: "mdi-alpha-b-box-outline",
        title: "Hãng",
        children: [
          {
            title: "Danh sách hãng",
            to: "/admin/brands",
          },
          {
            title: "Thêm mới",
            to: "/admin/brands/add",
          },
        ],
      },
      {
        group: "/admin/categories",
        icon: "mdi-alpha-t-box-outline",
        title: "Thể loại",
        children: [
          {
            title: "Danh sách Thể loại",
            to: "/admin/categories",
          },
          {
            title: "Thêm mới",
            to: "/admin/categories/add",
          },
        ],
      },
      {
        group: "/admin/tags",
        icon: "mdi-tag-outline",
        title: "Tags",
        children: [
          {
            title: "Danh sách Tag",
            to: "/admin/tags",
          },
          {
            title: "Thêm mới",
            to: "/admin/tags/add",
          },
        ],
      },
      {
        group: "/admin/medias",
        icon: "mdi-image-outline",
        title: "Bộ sưu tập",
        children: [
          {
            title: "Thư viện",
            to: "/admin/medias",
          },
          {
            title: "Thêm mới",
            to: "/admin/medias/add",
          },
        ],
      },
      {
        group: "/admin/users",
        icon: "mdi-account-outline",
        title: "Users",
        children: [
          {
            title: "Danh sách User",
            to: "/admin/users",
          },
          {
            title: "Thêm User",
            to: "/admin/users/add",
          },
        ],
      },
      {
        group: "/admin/roles",
        icon: "mdi-alpha-r-box-outline",
        title: "Roles",
        children: [
          {
            title: "Danh sách Role",
            to: "/admin/roles",
          },
          {
            title: "Thêm Role",
            to: "/admin/roles/add",
          },
        ],
      }
    ],

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
  },
}
</script>
<style scoped>
a {
  color: #fff;
  text-decoration: none;
}
a:hover {
  color: inherit;
  text-decoration: none;
}
</style>
