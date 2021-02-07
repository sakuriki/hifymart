import Vue from "vue";

export const state = () => ({
  products: {}
});

export const getters = {
  products: ({ products }) => products,
  count: ({ products }) => Object.keys(products).length
};

export const mutations = {
  SET_PRODUCT: (state, payload) => {
    state.products = payload;
  },
  ADD_PRODUCT: (state, product) => {
    console.log(product);
    if (!(product.id in state.products)) {
      Vue.set(state.products, product.id, product);
    }
  },
  REMOVE_PRODUCT: (state, product) => {
    if (state.products[product.id]) {
      Vue.delete(state.products, product.id);
    }
  },
  CLEAR_PRODUCT: state => {
    state.products = {};
  }
};

export const actions = {
  setProduct: ({ commit }, payload) => commit("SET_PRODUCT", payload),

  addProduct: ({ commit }, item) => commit("ADD_PRODUCT", item),

  removeProduct: ({ commit }, item) => commit("REMOVE_PRODUCT", item),

  clearProduct: ({ commit }) => commit("CLEAR_PRODUCT")
};
