module.exports = {
  srcDir: __dirname,
  buildDir: ".nuxt",
  devtools: true,
  server: {
    port: process.env.CLIENT_PORT || 3000, // default: 3000
  },
  components: true,
  /*
   ** Headers of the page
   */
  head: {
    title: "Shoppo",
    meta: [
      { charset: "utf-8" },
      {
        name: "viewport",
        content: "width=device-width, initial-scale=1, shrink-to-fit=no",
      },
      {
        hid: "description",
        name: "description",
        content:
          "Mua điện thoại thông minh mới nhất 2020. Giảm giá hấp dẫn. Trả góp 0%. Bảo hành chính hãng.",
      },
    ],
    link: [{ rel: "icon", type: "image/x-icon", href: "/192.png" }],
  },
  /*
   ** Customize the progress-bar color
   */
  loading: { color: "#fff" },

  /*
   ** Global CSS
   */
  css: ["~assets/css/main.css", "normalize.css/normalize.css"],
  /*
   ** Plugins to load before mounting the App
   */
  plugins: [],

  env: {
    baseUrl: process.env.CLIENT_BASE_URL || "http://localhost:3000",
    apiUrl: process.env.APP_URL || "http://localhost:8000",
  },
  vue: {
    config: {
      productionTip: false,
      devtools: process.env.APP_DEBUG || true,
    },
  },
  auth: {
    strategies: {
      local: {
        endpoints: {
          login: {
            url: "/auth/login",
            method: "post",
            propertyName: "token",
          },
          user: {
            url: "/auth/user",
            method: "get",
            propertyName: "data",
          },
          logout: { url: "/auth/logout", method: "post" },
        },
      },
    },
    redirect: {
      login: "/auth/login",
      home: "/",
    },
    debug: process.env.APP_DEBUG || true,
  },

  /*
   ** Nuxt.js modules
   */
  modules: [
    // Doc: https://axios.nuxtjs.org/usage
    "@nuxtjs/axios",
    // Doc: https://auth.nuxtjs.org/
    "@nuxtjs/auth",
    "cookie-universal-nuxt",
  ],
  buildModules: ["@nuxtjs/vuetify"],
  vuetify: {
    optionsPath: "./vuetify.options.js",
  },
  /*
   ** Axios module configuration
   ** See https://axios.nuxtjs.org/options
   */
  axios: {
    baseURL: process.env.CLIENT_API_URL || "http://localhost:8000/api/",
    https: process.env.CLIENT_HTTPS || true,
  },

  render: {
    bundleRenderer: {
      shouldPrefetch: (file, type) => {
        if (type === "script") {
          const ignoredRoutes = ["admin"];
          if (ignoredRoutes.some((r) => file.includes(r))) {
            return false;
          }
        }
        return ["script", "style", "font"].includes(type);
      },
    },
  },

  /*
   ** Build configuration
   */
  build: {
    /*
     ** You can extend webpack config here
     */
    publicPath: "/dist/",
    extractCSS: true,
    optimization: {
      splitChunks: {
        name: true,
      },
    },
  },
};
