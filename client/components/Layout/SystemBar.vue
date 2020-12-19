<template>
  <v-system-bar
    app
    color="primary"
    dark
    style="z-index:7"
  >
    <span class="hidden-sm-and-down">Mở cửa: 9h đến 20h, chủ nhật 10h đến 19h</span>
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
          :to="{ name: 'tai-khoan' }"
        >
          <v-list-item-title>Tài khoản</v-list-item-title>
        </v-list-item>
        <v-list-item
          nuxt
          exact
          :to="{ name: 'tai-khoan-wishlist' }"
        >
          <v-list-item-title>Wishlist</v-list-item-title>
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
      :to="{ name: 'danh-sach-type', params: { type: 'dang-giam-gia' } }"
    >
      <v-icon>mdi-star</v-icon>
      Khuyến mãi
    </v-btn>
    <v-btn
      text
      class="hidden-sm-and-down"
      :to="{ name: 'dia-chi' }"
    >
      <v-icon>mdi-map-marker</v-icon>
      Địa chỉ
    </v-btn>
  </v-system-bar>
</template>
<script>
export default {
  methods: {
    async logout() {
      try {
        await this.$auth.logout();
      } catch (e) {
        console.error('lỗi logout: ', e);
        return;
      }
    },
  },
}
</script>
