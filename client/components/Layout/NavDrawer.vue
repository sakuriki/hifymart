<template>
  <v-navigation-drawer
    :value="value"
    app
    fixed
    floating
    temporary
    width="250"
    hide-overlay
    @input="$emit('input', $event)"
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
</template>
<script>
import simplebar from "simplebar-vue";
import "simplebar/dist/simplebar.min.css";
import { mapGetters } from "vuex";
export default {
  components: {
    simplebar
  },
  props: {
    value: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    ...mapGetters(['brands', 'categories']),
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
}
</script>
