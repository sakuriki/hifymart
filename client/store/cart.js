import Vue from "vue";
const calculateAmount = obj =>
  Object.values(obj).reduce((acc, { count, price, sale_off_price }) => {
    return sale_off_price ? acc + count * sale_off_price : acc + count * price;
  }, 0);

export const state = () => ({
  total: 0,
  amount: 0,
  coupon: null,
  coupon_value: 0,
  is_percent: false,
  discount: 0,
  total_amount: 0,
  actualStep: 0,
  cart: {},
  success: false,
  shippingInformation: {}
});

export const getters = {
  cart: ({ cart }) => cart,
  total: ({ total }) => total,
  amount: ({ amount }) => amount,
  discount: ({ discount }) => discount,
  total_amount: ({ total_amount }) => total_amount,
  coupon: ({ coupon }) => coupon,
  actualStep: ({ actualStep }) => actualStep,
  success: ({ success }) => success,
  shippingInformation: ({ shippingInformation }) => shippingInformation
};

export const mutations = {
  ADD_ITEM: (state, { product, count }) => {
    if (!product) return;
    state.total += count;
    if (product.name in state.cart) {
      state.cart[product.name].count += count;
    } else {
      let stateItem = { ...product };
      stateItem.count = count;
      // state.cart[item.name] = stateItem;
      Vue.set(state.cart, product.name, stateItem);
    }
    state.amount = calculateAmount(state.cart);
    state.discount = state.is_percent
      ? (state.amount * state.coupon_value) / 100
      : state.coupon_value;
    let total_amount = state.amount - state.discount;
    state.total_amount = total_amount > 0 ? total_amount : 0;
  },
  REMOVE_ITEM: (state, item) => {
    state.total = state.total - item.count;
    delete state.cart[item.name];
    state.amount = calculateAmount(state.cart);
    state.discount = state.is_percent
      ? (state.amount * state.coupon_value) / 100
      : state.coupon_value;
    let total_amount = state.amount - state.discount;
    state.total_amount = total_amount > 0 ? total_amount : 0;
  },
  ADD_COUPON: (state, { coupon, coupon_value, is_percent }) => {
    state.coupon = coupon;
    state.coupon_value = coupon_value;
    state.is_percent = is_percent;
    state.discount = is_percent
      ? (state.amount * coupon_value) / 100
      : coupon_value;
    let total_amount = state.amount - state.discount;
    state.total_amount = total_amount > 0 ? total_amount : 0;
  },
  REMOVE_COUPON: state => {
    state.total_amount = state.total_amount + state.discount;
    state.coupon = null;
    state.coupon_value = 0;
    state.discount = 0;
    state.is_percent = false;
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

  addCoupon: ({ commit }, item) => commit("ADD_COUPON", item),

  removeCoupon: ({ commit }, item) => commit("REMOVE_COUPON", item),

  clearCount: ({ commit }) => commit("CLEAR_COUNT"),

  clearContents: ({ commit }) => commit("CLEAR_CONTENTS"),

  setSuccess: ({ commit }, value) => commit("SET_SUCCESS", value),

  setActualStep: ({ commit }, value) => commit("SET_ACTUAL_STEP", value),

  setTotal: ({ commit }, value) => commit("SET_TOTAL", value),

  setShippingInformation: ({ commit }, payload) =>
    commit("SET_SHIPPING_INFORMATION", payload)
};
