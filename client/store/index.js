export const state = () => ({
  footer: {
    categories: [],
    brands: []
  },
  brands: [],
  categories: [],
  settings: {},
  latestProducts: [],
  onsaleProducts: [],
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
  settings: ({ settings }) => settings,
  latestProducts: ({ latestProducts }) => latestProducts,
  footer: ({ footer }) => footer,
  brands: ({ brands }) => brands,
  categories: ({ categories }) => categories,
  onsaleProducts: ({ onsaleProducts }) => onsaleProducts,
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
  SET_LASTEST_PRODUCTS(state, data) {
    state.latestProducts = data;
  },
  SET_ON_SALE_PRODUCTS(state, data) {
    state.onsaleProducts = data;
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
      if (vuexContext.state.auth.loggedIn) {
        let { addresses } = await this.$axios.$get("/addressBooks");
        // addresses = addresses.reduce((obj, item) => {
        //   return {
        //     ...obj,
        //     [item.id]: item
        //   };
        // }, {});
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

      var latestProducts = await this.$axios.$get("/products?per_page=8");
      vuexContext.commit("SET_LASTEST_PRODUCTS", latestProducts.products);

      var onsaleProducts = await this.$axios.$get(
        "/products?per_page=8&onsale=true"
      );
      vuexContext.commit("SET_ON_SALE_PRODUCTS", onsaleProducts.products);

      var hottestProducts = await this.$axios.$get(
        "/products?per_page=8&sortBy=orders_count"
      );
      vuexContext.commit("SET_HOTTEST_PRODUCTS", hottestProducts.products);

      var randomProducts = await this.$axios.$get(
        "/products?per_page=8&sortBy=random"
      );
      vuexContext.commit("SET_RANDOM_PRODUCTS", randomProducts.products);
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
