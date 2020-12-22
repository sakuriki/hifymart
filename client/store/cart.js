import Vue from "vue";

export const state = () => ({
  coupon: null,
  coupon_value: 0,
  is_percent: false,
  actualStep: 0,
  cart: {},
  success: false,
  shippingInformation: {}
});

export const getters = {
  cart: ({ cart }) => cart,
  total: ({ cart }) => {
    return Object.values(cart).reduce((total, { count }) => count + total, 0);
  },
  amount: ({ cart }) => {
    return Object.values(cart).reduce(
      (total, { count, price, sale_off_price }) =>
        sale_off_price ? total + count * sale_off_price : total + count * price,
      0
    );
  },
  discount: (state, getters) => {
    return state.is_percent
      ? (getters.amount * state.coupon_value) / 100
      : state.coupon_value;
  },
  total_amount: (state, getters) => {
    let total_amount = getters.amount - getters.discount;
    return total_amount > 0 ? total_amount : 0;
  },
  coupon: ({ coupon }) => coupon,
  coupon_value: ({ coupon_value }) => coupon_value,
  is_percent: ({ is_percent }) => is_percent,
  actualStep: ({ actualStep }) => actualStep,
  success: ({ success }) => success,
  shippingInformation: ({ shippingInformation }) => shippingInformation
};

export const mutations = {
  ADD_ITEM: (state, { product, add }) => {
    if (!product) return;
    if (product.count + add < 0) {
      Vue.delete(state.cart, product.id);
    } else {
      if (product.id in state.cart) {
        Vue.set(
          state.cart[product.id],
          "count",
          (state.cart[product.id].count += add)
        );
      } else {
        let stateItem = { ...product };
        stateItem.count = add;
        Vue.set(state.cart, product.id, stateItem);
      }
    }
  },
  REMOVE_ITEM: (state, item) => {
    if (state.cart[item.id]) {
      Vue.delete(state.cart, item.id);
    }
  },
  ADD_COUPON: (state, { coupon, coupon_value, is_percent }) => {
    state.coupon = coupon;
    state.coupon_value = coupon_value;
    state.is_percent = is_percent;
  },
  REMOVE_COUPON: state => {
    state.coupon = null;
    state.coupon_value = 0;
    state.is_percent = false;
  },
  CLEAR_CART: state => {
    Vue.set(state, "cart", {});
    state.cart = {};
  },
  SET_ACTUAL_STEP: (state, step) => {
    state.actualStep = step;
  },
  SET_SUCCESS: (state, value) => {
    state.success = value;
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

  clearCart: ({ commit }) => commit("CLEAR_CART"),

  setSuccess: ({ commit }, value) => commit("SET_SUCCESS", value),

  setActualStep: ({ commit }, value) => commit("SET_ACTUAL_STEP", value),

  setShippingInformation: ({ commit }, payload) =>
    commit("SET_SHIPPING_INFORMATION", payload)
};
