export default function({ store, redirect }) {
  if (store.getters.authenticated) {
    return redirect("/");
  }
}
