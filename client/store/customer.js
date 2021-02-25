export const state = () => ({
  name: "",
  email: "",
  phone: ""
});

export const getters = {
  name: ({ name }) => name,
  email: ({ email }) => email,
  phone: ({ phone }) => phone
};

export const mutations = {
  SET_NAME: (state, payload) => {
    state.name = payload;
  },
  SET_EMAIL: (state, payload) => {
    state.email = payload;
  },
  SET_PHONE: (state, payload) => {
    state.phone = payload;
  },
  SET_INFO: (state, { name, email, phone }) => {
    state.name = name;
    state.email = email;
    state.phone = phone;
  },
  CLEAR_INFO: state => {
    state.name = "";
    state.email = "";
    state.phone = "";
  }
};

export const actions = {
  setName: ({ commit }, payload) => commit("SET_NAME", payload),

  setEmail: ({ commit }, payload) => commit("SET_EMAIL", payload),

  setPhone: ({ commit }, payload) => commit("SET_PHONE", payload),

  setInfo: ({ commit }, payload) => commit("SET_INFO", payload),

  clearInfo: ({ commit }) => commit("CLEAR_INFO")
};
