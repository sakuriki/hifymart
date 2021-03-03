export const state = () => ({
  search_text: null
});
export const mutations = {
  setSearchText(state, data) {
    state.search_text = data;
  }
};
export const getters = {
  searchText(state) {
    return state.search_text;
  }
};
export default {
  state,
  mutations,
  getters
};
