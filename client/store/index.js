export const state = () => ({
  footer: {
    categories: [],
    brands: []
  },
  brands: [],
  categories: [],
  settings: {},
  errors: {}
});

export const getters = {
  settings: ({ settings }) => settings,
  footer: ({ footer }) => footer,
  brands: ({ brands }) => brands,
  categories: ({ categories }) => categories,
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
  }
};

export const actions = {
  async nuxtServerInit(vuexContext) {
    try {
      if (vuexContext.state.auth.loggedIn) {
        this.$axios.$get("/addressBooks").then(res => {
          vuexContext.commit("address-book/SET_ADDRESSES", res.addresses);
        });
      }

      let cart_id = vuexContext.state.cart.cart_id;
      let cartKeys = Object.keys(cart_id);
      if (cartKeys.length > 0) {
        this.$axios
          .$get("/productlist", {
            params: {
              listId: cartKeys
            }
          })
          .then(res => {
            let cartProduct = res.product;
            cartKeys.map(function(key) {
              if (cartProduct[key]) {
                cartProduct[key].count = cart_id[key];
              }
            });
            vuexContext.commit("cart/SET_CART_ITEM", cartProduct);
          });
      }
      const [settings, categories, brands, footer] = await Promise.all([
        this.$axios.$get("/settings"),
        this.$axios.$get("/categories"),
        this.$axios.$get("/brands"),
        this.$axios.$get("/footer")
      ]);
      vuexContext.commit("SET_SETTINGS", settings.data);
      vuexContext.commit("SET_CATEGORIES", categories.categories);
      vuexContext.commit("SET_BRANDS", brands.brands);
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
