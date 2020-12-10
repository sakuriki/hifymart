<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{

  use AuthenticatesUsers;

  public function register(Request $request)
  {
    $messages = [
      'required' => ':attribute không được để trống',
      'exists' => ':attribute đã tồn tại',
      'password.min' => 'Mật khẩu dài tối thiếu 8 ký tự',
      'password.confirmed' => 'Mật khẩu nhập lại không khớp',
      'email' => ':attribute phải là địa chỉ email hợp lệ',
      'max' => ':attribute tối đa 255 ký tự',
      'string' => ':attribute phải là chuỗi'
    ];
    $nicenames = [
      'email' => 'Địa chỉ email',
      'password' => 'Mật khẩu',
      'name' => 'Tên hiển thị'
    ];
    // validate data
    $this->validate($request, [
      'name' => 'required|string|max:255',
      'email' => 'required|email:rfc,dns|unique:users,email',
      'password' => 'required|string|min:8|confirmed'
    ], $messages, $nicenames);
    // create new user
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password)
    ]);
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
          'data' => $request->user(),
          'token' => $request->input('token')
        ], 200);
      }
    }
    $messages = [
      'required' => ':attribute không được để trống',
      'string' => ':attribute phải là chuỗi',
      'email' => ':attribute phải là địa chỉ email hợp lệ'
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
        'status' => 'success',
        'message' => "User successfully logged out."
      ]);
    } catch (JWTException $e) {
      // có lỗi khi giải mã token
      return response()->json([
        'status' => 'error',
        'message' => 'Failed to logout, please try again.'
      ], 500);
    }
  }
  public function user()
  {
    return [
      'data' => auth()->user()
    ];
  }
}
