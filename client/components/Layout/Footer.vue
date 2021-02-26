<template>
  <v-footer
    dark
    padless
  >
    <v-card
      flat
      tile
      width="100%"
      class="primary lighten-1 py-6 white--text text-center"
    >
      <v-card-text>
        <div class="d-flex flex-wrap justify-space-between">
          <div class="pa-1 text-left">
            <p class="text-h6 white--text">
              Đăng ký nhận tin mới
            </p>
            <v-text-field
              label="Địa chỉ email"
              solo
              light
            >
              <template #append>
                <v-btn
                  depressed
                  color="primary"
                  class="mr-n2"
                >
                  Theo dõi
                </v-btn>
              </template>
            </v-text-field>
            <p>Đăng ký theo giõi để nhận tin về sản phẩm mới và giảm giá</p>
          </div>
          <div class="pa-1 text-left">
            <p class="text-h6 white--text">
              Danh mục
            </p>
            <NuxtLink
              v-for="category in footer.categories"
              :key="category.slug"
              :to="category.slug"
            >
              <p>{{ category.name }}</p>
            </NuxtLink>
          </div>
          <div class="pa-1 text-left">
            <p class="text-h6 white--text">
              Nhãn hiệu
            </p>
            <NuxtLink
              v-for="brand in footer.brands"
              :key="brand.slug"
              :to="brand.slug"
            >
              <p>{{ brand.name }}</p>
            </NuxtLink>
          </div>
          <div class="pa-1 text-left">
            <p class="text-h6 white--text">
              Liên hệ
            </p>
            <p>Địa chỉ: {{ settings['contact-address'] }}</p>
            <p>Số điện thoại: {{ settings['contact-phone'] }}</p>
            <p>Số di động: {{ settings['contact-mobile'] }}</p>
          </div>
        </div>
      </v-card-text>

      <v-card-text class="white--text pt-0">
        <v-btn
          v-if="settings.facebook"
          class="mx-4 white--text"
          icon
          :href="settings.facebook"
        >
          <v-icon size="24px">
            mdi-facebook
          </v-icon>
        </v-btn>
        <v-btn
          v-if="settings.twitter"
          class="mx-4 white--text"
          icon
          :href="settings.twitter"
        >
          <v-icon size="24px">
            mdi-twitter
          </v-icon>
        </v-btn>
        <v-btn
          v-if="settings.instagram"
          class="mx-4 white--text"
          icon
          :href="settings.instagram"
        >
          <v-icon size="24px">
            mdi-instagram
          </v-icon>
        </v-btn>
        <v-btn
          v-if="settings.youtube"
          class="mx-4 white--text"
          icon
          :href="settings.youtube"
        >
          <v-icon size="24px">
            mdi-youtube
          </v-icon>
        </v-btn>
      </v-card-text>

      <v-divider />

      <v-card-text class="white--text">
        © {{ new Date().getFullYear() }} — <strong>{{ settings['app-name'] }}</strong>
      </v-card-text>
    </v-card>
  </v-footer>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  data: () => ({
    icons: [
      'mdi-facebook',
      'mdi-twitter',
      'mdi-linkedin',
      'mdi-instagram',
    ],
  }),
  computed: {
    ...mapGetters(['footer', 'settings']),
  },
}
</script>
<style scoped>
a {
  color: rgba(255, 255, 255, 0.7)
}
a:hover {
  color: #ffffff
}
</style>
