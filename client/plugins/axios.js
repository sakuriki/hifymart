export default function({ $axios, store }) {
  $axios.onError(error => {
    store.dispatch("setErrors", error.response.data.errors);
    return Promise.reject(error);
  });

  $axios.onRequest(() => {
    store.dispatch("clearErrors");
  });
}
