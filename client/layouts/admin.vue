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
              v-if="item.children && authorized(item.permission)"
              :prepend-icon="item.icon"
              no-action
              :group="item.group"
            >
              <template #activator>
                <v-list-item-content>
                  <v-list-item-title v-text="item.title" />
                </v-list-item-content>
              </template>
              <template v-for="(subItem, i) in item.children">
                <v-list-item
                  v-if="authorized(subItem.permission)"
                  :key="'item-'+i"
                  :to="subItem.to"
                  nuxt
                  exact
                >
                  <v-list-item-content>
                    <v-list-item-title v-text="subItem.title" />
                  </v-list-item-content>
                </v-list-item>
              </template>
            </v-list-group>
            <v-list-item
              v-else-if="authorized(item.permission)"
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
    <v-footer
      app
      absolute
      inset
    >
      <span>© {{ new Date().getFullYear() }} — <strong>VietShop</strong></span>
    </v-footer>
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
        permission: "dashboard",
        to: "/admin",
      },
      {
        icon: "mdi-cart-outline",
        title: "Đơn hàng",
        permission: "order.access",
        to: "/admin/orders",
      },
      {
        group: "/admin/products",
        icon: "mdi-alpha-s-box-outline",
        title: "Sản phẩm",
        permission: "product.access",
        children: [
          {
            title: "Danh sách sản phẩm",
            permission: "product.access",
            to: "/admin/products",
          },
          {
            title: "Thêm mới",
            permission: "product.create",
            to: "/admin/products/add",
          },
        ],
      },
      {
        group: "/admin/brands",
        icon: "mdi-alpha-b-box-outline",
        title: "Nhãn hiệu",
        permission: "brand.access",
        children: [
          {
            title: "Danh sách nhãn hiệu",
            permission: "brand.access",
            to: "/admin/brands",
          },
          {
            title: "Thêm mới",
            permission: "brand.create",
            to: "/admin/brands/add",
          },
        ],
      },
      {
        group: "/admin/categories",
        icon: "mdi-archive-outline",
        title: "Danh mục",
        permission: "category.access",
        children: [
          {
            title: "Danh sách danh mục",
            permission: "category.access",
            to: "/admin/categories",
          },
          {
            title: "Thêm mới",
            permission: "category.create",
            to: "/admin/categories/add",
          },
        ],
      },
      {
        group: "/admin/coupons",
        icon: "mdi-ticket-percent-outline",
        title: "Mã giảm giá",
        permission: "coupon.access",
        children: [
          {
            title: "Danh sách mã giảm giá",
            permission: "coupon.access",
            to: "/admin/coupons",
          },
          {
            title: "Thêm mới",
            permission: "coupon.create",
            to: "/admin/coupons/add",
          },
        ],
      },
      {
        group: "/admin/tags",
        icon: "mdi-tag-multiple-outline",
        title: "Tags",
        permission: "tag.access",
        children: [
          {
            title: "Danh sách Tag",
            permission: "tag.access",
            to: "/admin/tags",
          },
          {
            title: "Thêm mới",
            permission: "tag.create",
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
        permission: "user.access",
        children: [
          {
            title: "Danh sách người dùng",
            permission: "user.access",
            to: "/admin/users",
          },
          {
            title: "Thêm mới",
            permission: "user.create",
            to: "/admin/users/add",
          },
        ],
      },
      {
        group: "/admin/roles",
        icon: "mdi-alpha-p-box-outline",
        title: "Phân quyền",
        permission: "role.access",
        children: [
          {
            title: "Danh sách vai trò",
            permission: "role.access",
            to: "/admin/roles",
          },
          {
            title: "Thêm mới",
            permission: "role.create",
            to: "/admin/roles/add",
          },
        ],
      },
      {
        icon: "mdi-cog-outline",
        title: "Cài đặt",
        permission: "dashboard",
        to: "/settings",
      },
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
    },
    authorized(permission) {
      return permission ? this.$auth.user.permissions.includes(permission) : true
    }
  },
};
</script>
<style scoped>
.v-list-group >>> .v-list-item--active {
  color: #ffeb3b;
}
</style>
