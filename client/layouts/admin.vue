<template>
  <v-app v-resize="handleResize">
    <v-app-bar
      app
      dense
      clipped-left
      color="primary"
      dark
    >
      <v-app-bar-nav-icon
        class="hidden-md-and-down"
        @click.stop="mini = !mini"
      />
      <v-app-bar-nav-icon
        class="hidden-lg-and-up"
        @click.stop="drawer = !drawer"
      />
      <v-toolbar-title class="headline text-uppercase">
        <NuxtLink
          class="toolbar-title white--text text-decoration-none"
          to="/"
        >
          shop.re
        </NuxtLink>
      </v-toolbar-title>
      <v-spacer />
      <v-btn
        icon
        @click.stop="darkMode"
      >
        <v-icon>{{ dark ? 'mdi-brightness-5' : 'mdi-brightness-4' }}</v-icon>
      </v-btn>
    </v-app-bar>
    <v-navigation-drawer
      v-model="drawer"
      :temporary="temporary"
      :expand-on-hover.sync="mini"
      app
      clipped
      width="250"
      color="primary"
      dark
    >
      <simplebar
        style="height: 100%"
      >
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
      </simplebar>
    </v-navigation-drawer>
    <v-main>
      <nuxt />
      <GlobalSnackBar />
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
  data: () => ({
    dark: false,
    mini: false,
    temporary: false,
    drawer: false,
    current_width: 0,
    items: [
      {
        icon: "mdi-view-dashboard",
        title: "Dashboard",
        to: "/admin",
      },
      {
        icon: "mdi-cart-outline",
        title: "Đơn hàng",
        to: "/admin/orders",
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
        title: "Nhãn hiệu",
        children: [
          {
            title: "Danh sách nhãn hiệu",
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
        icon: "mdi-alpha-d-box-outline",
        title: "Danh mục",
        children: [
          {
            title: "Danh sách danh mục",
            to: "/admin/categories",
          },
          {
            title: "Thêm mới",
            to: "/admin/categories/add",
          },
        ],
      },
      {
        group: "/admin/coupons",
        icon: "mdi-ticket-percent-outline",
        title: "Mã giảm giá",
        children: [
          {
            title: "Danh sách mã giảm giá",
            to: "/admin/coupons",
          },
          {
            title: "Thêm mới",
            to: "/admin/coupons/add",
          },
        ],
      },
      {
        group: "/admin/tags",
        icon: "mdi-tag-multiple-outline",
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
      // {
      //   group: "/admin/medias",
      //   icon: "mdi-image-outline",
      //   title: "Bộ sưu tập",
      //   children: [
      //     {
      //       title: "Thư viện",
      //       to: "/admin/medias",
      //     },
      //     {
      //       title: "Thêm mới",
      //       to: "/admin/medias/add",
      //     },
      //   ],
      // },
      {
        group: "/admin/users",
        icon: "mdi-account-multiple-outline",
        title: "Người dùng",
        children: [
          {
            title: "Danh sách người dùng",
            to: "/admin/users",
          },
          {
            title: "Thêm mới",
            to: "/admin/users/add",
          },
        ],
      },
      {
        group: "/admin/roles",
        icon: "mdi-alpha-p-box-outline",
        title: "Phân quyền",
        children: [
          {
            title: "Danh sách vai trò",
            to: "/admin/roles",
          },
          {
            title: "Thêm mới",
            to: "/admin/roles/add",
          },
        ],
      }
    ],
  }),
  mounted() {
    this.handleResize();
  },
  methods: {
    handleResize() {
      let browserWidth = Math.max(document.body.scrollWidth, document.documentElement.scrollWidth, document.body.offsetWidth, document.documentElement.offsetWidth, document.documentElement.clientWidth, 320);
      // this.$store.commit('setBrowserWidth', browserWidth);
      // this.$store.commit('setBrowserHeight', window.innerHeight);
      if (browserWidth < 1264) {
        this.temporary = true;
        this.mini = false;
        if (this.current_width != browserWidth) this.drawer = false;
      } else {
        this.temporary = false;
        this.drawer = true;
      }
      this.current_width = browserWidth;
    },
    darkMode() {
      this.dark = !this.dark;
      this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
    }
  },
};
</script>
<style scoped>
.v-list-group >>> .v-list-item--active {
  color: #ffeb3b;
}
</style>
