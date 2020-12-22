import VuexPersistence from "vuex-persist";

export default ({ store }) => {
  new VuexPersistence({
    reducer: state => ({
      cart: {
        cart: state.cart.cart
      }
    }),
    modules: ["cart"]
  }).plugin(store);
};
