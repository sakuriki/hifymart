<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required|string|max:255',
      'email' => 'required|email:rfc,dns',
      'password' => 'sometimes|string|min:8|confirmed',
      'phone' => 'nullable|numeric',
    ];
  }

  public function messages()
  {
    return [
      'required' => ':attribute không được để trống',
      'password.min' => 'Mật khẩu dài tối thiếu 8 ký tự',
      'password.confirmed' => 'Mật khẩu nhập lại không khớp',
      'email' => ':attribute chưa hợp lệ hoặc không có thực',
      'max' => ':attribute tối đa 255 ký tự',
      'string' => ':attribute phải là chuỗi',
      'numeric' => ':attribute phải là số hợp lệ',
    ];
  }

  public function attributes()
  {
    return [
      'email' => 'Địa chỉ email',
      'password' => 'Mật khẩu',
      'name' => 'Tên hiển thị',
      'phone' => 'Số điện thoại'
    ];
  }
}
