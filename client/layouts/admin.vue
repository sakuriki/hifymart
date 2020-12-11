<template>
  <v-app v-resize="handleResize">
    <v-app-bar
      app
      dense
      clipped-left
    >
      <v-app-bar-nav-icon
        class="hidden-md-and-down"
        @click.stop="mini = !mini"
      />
      <v-app-bar-nav-icon
        class="hidden-lg-and-up"
        @click.stop="drawer = !drawer"
      />
      <v-toolbar-title class="text-uppercase">
        <span>Dashboard</span>
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
      <v-container
        fluid
        style="min-height:100vh"
      >
        <nuxt />
        <!-- <BackToTop />
        <GeneralSnackBar />
        <GeneralConfirmationDialog /> -->
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
a {
  text-decoration: none;
}
</style>
