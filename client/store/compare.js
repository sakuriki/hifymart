import Vue from "vue";

export const state = () => ({
  product: {}
});

export const getters = {
  product: ({ product }) => product
};

export const mutations = {
  SET_PRODUCT: (state, payload) => {
    state.product = payload;
  },
  ADD_PRODUCT: (state, product) => {
    if (!(product.id in state.product)) {
      Vue.set(state.product, product.id, product);
    }
  },
  REMOVE_PRODUCT: (state, product) => {
    if (state.product[product.id]) {
      Vue.delete(state.product, product.id);
    }
  },
  CLEAR_PRODUCT: state => {
    state.product = {};
  }
};

export const actions = {
  setProduct: ({ commit }) => commit("SET_PRODUCT"),

  addProduct: ({ commit }) => commit("ADD_PRODUCT"),

  removeProduct: ({ commit }) => commit("REMOVE_PRODUCT"),

  clearProduct: ({ commit }) => commit("CLEAR_PRODUCT")
};
