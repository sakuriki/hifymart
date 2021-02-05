import Vue from "vue";

export const state = () => ({
  coupon: {},
  cart: {},
  cart_id: {}
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
  tax: (state, getters) => {
    return getters.total_after_discount * 0.1;
  },
  shipping_fee: (state, getters) => {
    return getters.amount >= 200000 ? 0 : 19000;
  },
  discount: (state, getters) => {
    if (
      !state.coupon.value ||
      (state.coupon.min && getters.amount < state.coupon.min)
    )
      return 0;
    if (!state.coupon.is_percent) return state.coupon.value;
    let discount = (getters.amount * state.coupon.value) / 100;
    let max = state.coupon.max;
    return !max || discount < max ? discount : max;
  },
  total_after_discount: (state, getters) => {
    return getters.amount - getters.discount;
  },
  total_amount: (state, getters) => {
    let total_amount = getters.total_after_discount;
    return total_amount > 0
      ? total_amount + getters.tax + getters.shipping_fee
      : getters.shipping_fee;
  },
  coupon: ({ coupon }) => coupon
};

export const mutations = {
  ADD_ITEM: (state, { product, add }) => {
    if (!product) return;
    if (product.count + add <= 0) {
      Vue.delete(state.cart, product.id);
    } else {
      if (product.id in state.cart) {
        let addValue = state.cart[product.id].count + add;
        Vue.set(state.cart_id, product.id, addValue);
        Vue.set(state.cart[product.id], "count", addValue);
      } else {
        let stateItem = { ...product };
        stateItem.count = add;
        Vue.set(state.cart_id, product.id, add);
        Vue.set(state.cart, product.id, stateItem);
      }
    }
  },
  REMOVE_ITEM: (state, item) => {
    if (state.cart[item.id]) {
      Vue.delete(state.cart, item.id);
    }
  },
  ADD_COUPON: (state, coupon) => {
    Vue.set(state, "coupon", coupon);
  },
  REMOVE_COUPON: state => {
    Vue.set(state, "coupon", {});
  },
  CLEAR_CART: state => {
    Vue.set(state, "cart", {});
    Vue.set(state, "cart_id", {});
  },
  SET_CART_ITEM: (state, cart) => {
    Vue.set(state, "cart", cart);
  }
};

export const actions = {
  addItem: ({ commit }, item) => commit("ADD_ITEM", item),

  removeItem: ({ commit }, item) => commit("REMOVE_ITEM", item),

  addCoupon: ({ commit }, item) => commit("ADD_COUPON", item),

  removeCoupon: ({ commit }, item) => commit("REMOVE_COUPON", item),

  clearCart: ({ commit }) => commit("CLEAR_CART")
};
