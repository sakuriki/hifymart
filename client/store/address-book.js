import Vue from "vue";

export const state = () => ({
  addresses: []
});

export const getters = {
  addresses: ({ addresses }) => addresses
};

export const mutations = {
  ADD_ADDRESS: (state, address) => {
    let index = state.addresses.findIndex(p => p.id == address.id);
    Vue.set(state.addresses, index, address);
  },
  REMOVE_ADDRESS: (state, address) => {
    if (state.addresses[address.id]) {
      Vue.delete(state.addresses, address.id);
    }
  },
  CLEAR_ADDRESSES: state => {
    Vue.set(state, "addresses", {});
  },
  SET_ADDRESSES: (state, addresses) => {
    Vue.set(state, "addresses", addresses);
  }
};

export const actions = {
  addAddress: ({ commit }, item) => commit("ADD_ADDRESS", item),

  removeAddress: ({ commit }, item) => commit("REMOVE_ADDRESS", item),

  clearAddresses: ({ commit }) => commit("CLEAR_ADDRESSES"),

  setAddresses: ({ commit }) => commit("SET_ADDRESSES")
};
