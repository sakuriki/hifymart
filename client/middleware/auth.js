export default function({ store, redirect, route }) {
  // console.log(route);
  // {
  //   name: 'admin',
  //   meta: [
  //     {
  //       permission: 'dashboard'
  //     }
  //   ],
  //   path: '/admin',
  //   hash: '',
  //   query: {},
  //   params: {},
  //   fullPath: '/admin',
  // }

  if (!store.getters.authenticated) {
    return redirect({
      name: "auth-login",
      query: {
        redirect: route.fullPath
      }
    });
  }
  const permission = route.meta.map(meta => {
    if (meta.auth && typeof meta.auth.permission !== "undefined")
      return meta.auth.permission;
    return null;
  });
  if (
    permission != null &&
    store.getters.user.permissions.includes(permission)
  ) {
    return redirect("/");
  }
}
