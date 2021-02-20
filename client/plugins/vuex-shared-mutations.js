import shareMutations from "vuex-shared-mutations";

export default ({ store }) => {
  window.onNuxtReady(nuxt => {
    shareMutations({
      predicate: [
        "cart/ADD_ITEM",
        "cart/REMOVE_ITEM",
        "cart/ADD_COUPON",
        "cart/REMOVE_COUPON",
        "cart/CLEAR_CART",
        "cart/SET_CART_ITEM"
      ]
    })(store);
  });
};
