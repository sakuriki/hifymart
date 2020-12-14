<template>
  <v-system-bar
    app
    color="primary"
    dark
    style="z-index:7"
  >
    <span class="hidden-sm-and-down">Mở cửa: 9h đến 20h, chủ nhật 10h đến 19h</span>
    <v-spacer class="hidden-sm-and-down" />
    <v-btn text>
      <v-icon>mdi-account</v-icon>
      Tài khoản
    </v-btn>
    <v-btn
      text
      class="hidden-sm-and-down"
    >
      <v-icon>mdi-star</v-icon>
      Khuyến mãi
    </v-btn>
    <v-btn
      text
      class="hidden-sm-and-down"
    >
      <v-icon>mdi-map-marker</v-icon>
      Địa chỉ
    </v-btn>
  </v-system-bar>
</template>
