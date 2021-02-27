<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{

  use AuthenticatesUsers;

  public function register(UserRequest $request)
  {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'phone' => $request->phone,
      'password' => bcrypt($request->password)
    ]);
    $role = Role::where('slug', 'customer')->firstOrFail();
    $user->roles()->sync([$role->id]);
    // sau khi lưu dữ liệu user mới vào CSDL, tiến hành đăng nhập luôn rồi trả về token đăng nhập
    if (!$token = JWTAuth::attempt($request->only(['email', 'password']))) {
      return abort(422);
    }
    return response()->json([
      'token' => $token
    ]);
  }
  public function login(Request $request)
  {

    // attempt login with token

    if ($request->input('token')) {
      JWTAuth::setToken($request->input('token'));
      $user = JWTAuth::authenticate();
      if ($user) {
        return response()->json([
          'success' => true,
          'token' => $request->input('token')
        ]);
      }
    }
    $messages = [
      'required' => ':attribute không được để trống',
      'string' => ':attribute phải là chuỗi',
      'email' => ':attribute chưa hợp lệ hoặc không có thực'
    ];
    $nicenames = [
      'email' => 'Địa chỉ email',
      'password' => 'Mật khẩu',
    ];
    // validate data
    $this->validate($request, [
      'email' => 'required|email:rfc,dns',
      'password' => 'required|string',
    ], $messages, $nicenames);

    if (!$token = JWTAuth::attempt($request->only(['email', 'password']))) {
      return response()->json([
        'errors' => [
          'msg' => ['Thông tin đăng nhập sai']
        ]
      ], 422);
    }

    return response()->json([
      'token' => $token
    ]);
  }
  public function logout(Request $request)
  {
    try {
      // vô hiệu hoá token
      JWTAuth::parseToken()->invalidate();
      return response()->json([
        'success' => true,
        'msg' => "Đăng xuất thành công"
      ]);
    } catch (JWTException $e) {
      // có lỗi khi giải mã token
      return response()->json([
        'success' => false,
        'msg' => 'Có lỗi khi đăng xuất, vui lòng thử lại'
      ], 500);
    }
  }
  public function user()
  {
    $user = auth()->user();
    $permissions = [];
    $user->rolesWithPer->each(function ($item) use (&$permissions) {
      $item->permissions->each(function ($item) use (&$permissions) {
        $permissions[] = $item->name;
      });
    });
    return [
      'data' => collect($user)->only(['id', 'name', 'email'])->merge(["permissions" => array_values(array_unique($permissions))])
    ];
  }
}
