<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeRequest extends FormRequest
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
      'email' => 'required|email:rfc,dns'
    ];
  }

  public function messages()
  {
    return [
      'required' => ':attribute không được bỏ trống',
      'email' => ':attribute chưa hợp lệ hoặc không có thực',
    ];
  }

  public function attributes()
  {
    return [
      'email' => 'Địa chỉ email',
    ];
  }
}
