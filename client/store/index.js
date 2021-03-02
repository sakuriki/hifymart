export const state = () => ({
  footer: {
    categories: [],
    brands: []
  },
  brands: [],
  categories: [],
  settings: {},
  browserWidth: 0,
  browserHeight: 0,
  errors: {}
});

export const getters = {
  settings: ({ settings }) => settings,
  footer: ({ footer }) => footer,
  brands: ({ brands }) => brands,
  categories: ({ categories }) => categories,
  browserWidth: ({ browserWidth }) => browserWidth,
  browserHeight: ({ browserHeight }) => browserHeight,
  errors: ({ errors }) => errors,
  authenticated: ({ auth }) => auth.loggedIn,
  user: ({ auth }) => auth.user
};

export const mutations = {
  SET_ERRORS(state, errors) {
    state.errors = errors;
  },
  SET_SETTINGS(state, data) {
    state.settings = data;
  },
  SET_FOOTER(state, data) {
    state.footer = data;
  },
  SET_BRANDS(state, data) {
    state.brands = data;
  },
  SET_CATEGORIES(state, data) {
    state.categories = data;
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
      if (vuexContext.state.auth.loggedIn) {
        let { addresses } = await this.$axios.$get("/addressBooks");
        vuexContext.commit("address-book/SET_ADDRESSES", addresses);
      }

      let cart_id = vuexContext.state.cart.cart_id;
      let cartKeys = Object.keys(cart_id);
      if (cartKeys.length > 0) {
        var { product: cartProduct } = await this.$axios.$get("/productlist", {
          params: {
            listId: cartKeys
          }
        });
        cartKeys.map(function(key) {
          if (cartProduct[key]) {
            cartProduct[key].count = cart_id[key];
          }
        });
        vuexContext.commit("cart/SET_CART_ITEM", cartProduct);
      }

      let { data } = await this.$axios.$get("/settings");
      vuexContext.commit("SET_SETTINGS", data);

      let { categories } = await this.$axios.$get("/categories");
      vuexContext.commit("SET_CATEGORIES", categories);

      let { brands } = await this.$axios.$get("/brands");
      vuexContext.commit("SET_BRANDS", brands);

      let footer = await this.$axios.$get("/footer");
      vuexContext.commit("SET_FOOTER", footer);
    } catch (e) {
      console.error("store: ", e);
    }
  },
  setErrors({ commit }, errors) {
    commit("SET_ERRORS", errors);
  },
  clearErrors({ commit }) {
    commit("SET_ERRORS", {});
  }
};
