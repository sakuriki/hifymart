<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
      'name' => 'required|string',
      'slug' => 'required|string',
      'description' => 'nullable|string'
    ];
  }

  public function messages()
  {
    return [
      'required' => ':attribute không được bỏ trống',
      'string' => ':attribute phải là chuỗi',
    ];
  }

  public function attributes()
  {
    return [
      'name' => 'Tên role',
      'slug' => 'Slug',
      'description' => 'Giới thiệu',
    ];
  }
}
