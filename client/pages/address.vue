<template>
  <v-container fill-height>
    <v-layout
      row
      wrap
    >
      <v-col
        cols="12"
        md="8"
        sm="12"
      >
        <v-card height="100%">
          <div
            id="map"
            ref="map"
            style="height:100%;width:100%"
          />
        </v-card>
      </v-col>
      <v-col
        cols="12"
        md="4"
        sm="12"
      >
        <v-card>
          <v-card-title>Địa chỉ cửa hàng</v-card-title>
          <v-card-text>
            <v-card-subtitle>
              <v-icon>mdi-map-marker</v-icon> {{ settings['app-name'] }}
            </v-card-subtitle>
            <v-list-item three-line>
              <v-list-item-content>
                <v-list-item-title>Địa chỉ: {{ settings['contact-address'] }}</v-list-item-title>
                <v-list-item-subtitle>
                  Điện thoại: {{ settings['contact-phone'] }}
                </v-list-item-subtitle>
                <v-list-item-subtitle>
                  Email: {{ settings['contact-mail'] }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card-text>
        </v-card>
      </v-col>
    </v-layout>
  </v-container>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  head() {
    return {
      title: 'Hệ thống',
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: 'Hệ thống địa chỉ cửa hàng'
        },
        {
          property: 'og:site_name',
          content: process.env.appName
        },
        {
          property: 'og:title',
          content: 'Hệ thống'
        },
        {
          property: 'og:description',
          content: 'Hệ thống địa chỉ cửa hàng'
        },
        {
          property: 'og:image',
          content: ''
        },
        {
          property: 'twitter:site',
          content: process.env.appName
        },
        {
          property: 'twitter:card',
          content: 'summary_large_image'
        },
        {
          property: 'twitter:title',
          content: 'Hệ thống'
        },
        {
          property: 'twitter:description',
          content: 'Hệ thống địa chỉ cửa hàng'
        },
        {
          property: 'twitter:image',
          content: ''
        },
      ]
    }
  },
  computed: {
    ...mapGetters(['settings'])
  },
  mounted() {
    var t = this;
    var el = document.createElement("script");
    el.onload = function () {
      t.mapInit();
    };
    el.src = `https://maps.googleapis.com/maps/api/js?key=${process.env.GOOGLE_MAPS_API_KEY}`;
    document.body.appendChild(el);
  },
  methods: {
    mapInit() {
      const location = { lat: 21.013939, lng: 105.8423706 };
      // eslint-disable-next-line
      const map = new google.maps.Map(this.$refs.map, {
        zoom: 16,
        center: location,
      });
      // eslint-disable-next-line
      new google.maps.Marker({
        position: location,
        map: map,
      });
    }
  },
};
</script>
