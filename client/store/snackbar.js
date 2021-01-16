export const state = () => ({
  content: "",
  color: "",
  right: false
});

export const mutations = {
  showMessage(state, { content, color, right }) {
    state.content = content;
    state.color = color;
    state.right = right;
  }
};
