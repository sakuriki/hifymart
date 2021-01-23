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
  const checker = (arr, target) => target.every(v => arr.includes(v));
  if (permission && !checker(store.getters.user.permissions, permission)) {
    return redirect("/");
  }
}
