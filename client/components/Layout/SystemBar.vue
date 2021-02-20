<template>
  <v-system-bar
    app
    color="primary"
    dark
    style="z-index:7"
  >
    <span class="hidden-sm-and-down">Mở cửa: {{ settings['working-time'] }}</span>
    <v-spacer class="hidden-sm-and-down" />
    <v-menu
      v-if="!$auth.loggedIn"
      top
      offset-y
      open-on-hover
    >
      <template #activator="{ on, attrs }">
        <v-btn
          text
          v-bind="attrs"
          v-on="on"
        >
          <v-icon>mdi-account</v-icon>
          <span>Tài khoản</span>
        </v-btn>
      </template>
      <v-list>
        <v-list-item
          nuxt
          exact
          :to="{ name: 'auth-login' }"
        >
          <v-list-item-title>Đăng nhập</v-list-item-title>
        </v-list-item>
        <v-list-item
          nuxt
          exact
          :to="{ name: 'auth-register' }"
        >
          <v-list-item-title>Đăng ký</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
    <v-menu
      v-else
      top
      offset-y
      open-on-hover
    >
      <template #activator="{ on, attrs }">
        <v-btn
          text
          v-bind="attrs"
          v-on="on"
        >
          <v-icon>mdi-account</v-icon>
          <span>Hi! {{ $auth.user.name }}</span>
        </v-btn>
      </template>
      <v-list>
        <v-list-item
          nuxt
          exact
          :to="{ name: 'account' }"
        >
          <v-list-item-title>Tài khoản</v-list-item-title>
        </v-list-item>
        <v-list-item
          nuxt
          exact
          :to="{ name: 'account-wishlist' }"
        >
          <v-list-item-title>Danh sách muốn mua</v-list-item-title>
        </v-list-item>
        <v-list-item
          @click="logout"
        >
          <v-list-item-title>Đăng xuất</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
    <v-btn
      text
      class="hidden-sm-and-down"
      nuxt
      :to="{ name: 'compare' }"
    >
      <v-icon>mdi-compare</v-icon>
      So sánh ({{ compareCount }})
    </v-btn>
    <v-btn
      text
      class="hidden-sm-and-down"
      nuxt
      :to="{ name: 'browser-type', params: { type: 'sale-off' } }"
    >
      <v-icon>mdi-star</v-icon>
      Khuyến mãi
    </v-btn>
    <v-btn
      text
      class="hidden-sm-and-down"
      :to="{ name: 'address' }"
    >
      <v-icon>mdi-map-marker</v-icon>
      Địa chỉ
    </v-btn>
  </v-system-bar>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
  computed: {
    ...mapGetters(['settings']),
    compareCount() {
      return this.$store.getters['compare/count']
    }
  },
  methods: {
    async logout() {
      try {
        await this.$auth.logout();
      } catch (e) {
        this.$notifier.showMessage({
          content: 'Có lỗi, vui lòng thử lại',
          color: 'error',
          right: false
        })
      }
    },
  },
}
</script>
