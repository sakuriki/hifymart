import Vue from "vue";
import VueFbCustomerChat from "vue-fb-customer-chat";
Vue.use(VueFbCustomerChat, {
  page_id: process.env.fbPage, //  change 'null' to your Facebook Page ID,
  theme_color: "#4caf50", // theme color in HEX
  locale: "vi_VN" // default 'en_US'
});
