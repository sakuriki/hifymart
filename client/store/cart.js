const calculateAmount = obj =>
  Object.values(obj)
    .reduce((acc, { count, price }) => acc + count * price, 0)
    .toFixed(2);

export const state = () => ({
  total: 0,
  amount: 0,
  actualStep: 0,
  cart: {},
  success: false,
  shippingInformation: {}
});

export const getters = {
  cart: ({ cart }) => cart,
  total: ({ total }) => total,
  amount: ({ amount }) => amount,
  actualStep: ({ actualStep }) => actualStep,
  success: ({ success }) => success,
  shippingInformation: ({ shippingInformation }) => shippingInformation
};

export const mutations = {
  ADD_ITEM: (state, item) => {
    state.total++;
    if (item.name in state.cart) {
      state.cart[item.name].count++;
    } else {
      let stateItem = { ...item };
      stateItem.count = 1;
      state.cart[item.name] = stateItem;
    }
    state.amount = calculateAmount(state.cart);
  },
  REMOVE_ITEM: (state, item) => {
    state.total = state.total - item.count;
    delete state.cart[item.name];
    state.amount = calculateAmount(state.cart);
  },
  CLEAR_CONTENTS: state => {
    state.cart = {};
  },
  CLEAR_COUNT: state => {
    state.total = 0;
    state.amount = 0;
  },
  SET_ACTUAL_STEP: (state, step) => {
    state.actualStep = step;
  },
  SET_SUCCESS: (state, value) => {
    state.success = value;
  },
  SET_TOTAL: (state, value) => {
    state.total = value;
  },
  SET_SHIPPING_INFORMATION: (state, payload) => {
    state.shippingInformation = payload;
  }
};

export const actions = {
  addItem: ({ commit }, item) => commit("ADD_ITEM", item),

  removeItem: ({ commit }, item) => commit("REMOVE_ITEM", item),

  clearCount: ({ commit }) => commit("CLEAR_COUNT"),

  clearContents: ({ commit }) => commit("CLEAR_CONTENTS"),

  setSuccess: ({ commit }, value) => commit("SET_SUCCESS", value),

  setActualStep: ({ commit }, value) => commit("SET_ACTUAL_STEP", value),

  setTotal: ({ commit }, value) => commit("SET_TOTAL", value),

  setShippingInformation: ({ commit }, payload) =>
    commit("SET_SHIPPING_INFORMATION", payload)
};
