import Vue from "vue";

export const state = () => ({
  comments: [],
  info: {
    name: null,
    phone: null,
    email: null
  },
  total: 0
});

export const getters = {
  comments: ({ comments }) => comments,
  info: ({ info }) => info,
  total: ({ total }) => total
};

export const mutations = {
  SET_ITEM: (state, payload) => {
    state.comments = payload;
  },
  SET_INFO: (state, payload) => {
    state.info = payload;
  },
  SET_TOTAL: (state, total) => {
    state.total = total;
  },
  ADD_ONE: (state, comment) => {
    if (comment.parent_id) {
      let index = state.comments.findIndex(p => p.id == comment.parent_id);
      if (index >= 0) {
        Vue.set(state.comments[index], "replies", [
          ...state.comments[index].replies,
          comment
        ]);
      }
    } else {
      Vue.set(state, "comments", [comment, ...state.comments]);
    }
  },
  ADD_MULTIPLE: (state, comments) => {
    Vue.set(state, "comments", [...state.comments, ...comments]);
  },
  DELETE: (state, comment) => {
    let index = state.comments.findIndex(p => p.id == comment.id);
    if (index >= 0) {
      Vue.delete(state.comments, index);
    }
  },
  CLEAR: state => {
    Vue.set(state, "comment", []);
    Vue.set(state, "total", 0);
  }
};

export const actions = {
  setItem: ({ commit }, comments) => commit("SET_ITEM", comments),
  setInfo: ({ commit }, name) => commit("SET_INFO", name),
  setTotal: ({ commit }, total) => commit("SET_TOTAL", total),
  addOne: ({ commit }, comment) => commit("ADD_ONE", comment),
  addMultiple: ({ commit }, comment) => commit("ADD_MULTIPLE", comment),
  delete: ({ commit }, comment) => commit("DELETE", comment),
  clear: ({ commit }) => commit("CLEAR")
};
