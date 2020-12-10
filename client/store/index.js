export const state = () => ({
  errors: {}
});

export const getters = {
  errors(state) {
    return state.errors;
  }
};

export const mutations = {
  SET_ERRORS(state, errors) {
    state.errors = errors;
  }
};

export const actions = {
  setErrors({ commit }, errors) {
    commit("SET_ERRORS", errors);
  },
  clearErrors({ commit }) {
    commit("SET_ERRORS", {});
  }
};
