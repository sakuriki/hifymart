export const state = () => ({
  latestProducts: [],
  saleProducts: [],
  hottestProducts: [],
  randomProducts: [],
  browserWidth: 0,
  browserHeight: 0,
  errors: {}
});

export const getters = {
  // latestProducts(state) {
  //   return state.latestProducts;
  // },
  // saleProducts(state) {
  //   return state.saleProducts;
  // },
  // hottestProducts(state) {
  //   return state.hottestProducts;
  // },
  // randomProducts(state) {
  //   return state.randomProducts;
  // },
  latestProducts: ({ latestProducts }) => latestProducts,
  saleProducts: ({ saleProducts }) => saleProducts,
  hottestProducts: ({ hottestProducts }) => hottestProducts,
  randomProducts: ({ randomProducts }) => randomProducts,
  browserWidth: ({ browserWidth }) => browserWidth,
  browserHeight: ({ browserHeight }) => browserHeight,
  errors: ({ errors }) => errors,
  authenticated: ({ auth }) => auth.loggedIn,
  user: ({ auth }) => auth.user
  // authenticated(state) {
  //   return state.auth.loggedIn;
  // },
  // user(state) {
  //   return state.auth.user;
  // }
};

export const mutations = {
  SET_ERRORS(state, errors) {
    state.errors = errors;
  },
  SET_LASTEST_PRODUCTS(state, data) {
    state.latestProducts = data;
  },
  SET_SALE_PRODUCTS(state, data) {
    state.saleProducts = data;
  },
  SET_HOTTEST_PRODUCTS(state, data) {
    state.hottestProducts = data;
  },
  SET_RANDOM_PRODUCTS(state, data) {
    state.randomProducts = data;
  },
  SET_BROWSER_WIDTH(state, data) {
    state.browserWidth = data;
  },
  SET_BROWSER_HEIGHT(state, data) {
    state.browserHeight = data;
  }
};

export const actions = {
  async nuxtServerInit(vuexContext) {
    try {
      var latestProducts = await this.$axios.$get("/products?per_page=8");
      vuexContext.commit("SET_LASTEST_PRODUCTS", latestProducts.products);

      var saleProducts = await this.$axios.$get("/products?per_page=8");
      vuexContext.commit("SET_SALE_PRODUCTS", saleProducts.products);

      var hottestProducts = await this.$axios.$get(
        "/products?per_page=8&sortBy=orders_count&sortDesc=true"
      );
      vuexContext.commit("SET_HOTTEST_PRODUCTS", hottestProducts.products);

      var randomProducts = await this.$axios.$get("/products?per_page=8");
      vuexContext.commit("SET_RANDOM_PRODUCTS", randomProducts.products);
    } catch (e) {
      console.log("store: ", e);
    }
  },
  setErrors({ commit }, errors) {
    commit("SET_ERRORS", errors);
  },
  clearErrors({ commit }) {
    commit("SET_ERRORS", {});
  }
};
