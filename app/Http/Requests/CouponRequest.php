<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
      'code' => 'nullable|string|max:255',
      'value' => 'required|numeric',
      'number' => 'nullable|numeric',
      'min' => 'nullable|numeric',
      'max' => 'nullable|numeric',
      'is_percent' => 'nullable|boolean',
      'starts_at' => 'nullable|date_format:Y-m-d\TH:i:s.v\Z',
      'expires_at' => 'nullable|date_format:Y-m-d\TH:i:s.v\Z',
    ];
  }

  public function messages()
  {
    return [
      'required' => ':attribute không được bỏ trống',
      'string' => ':attribute phải là chuỗi',
      'max' => ':attribute tối đa 255 ký tự',
      'numeric' => ':attribute phải là số',
      'boolean' => ':attribute phải là kiểu boolean',
      'date_format' => ':attribute chưa đúng format',
    ];
  }

  public function attributes()
  {
    return [
      'code' => 'Mã',
      'value' => 'Giá trị',
      'number' => 'Số lượng',
      'min' => 'Tối thiểu',
      'max' => 'Tối đa',
      'starts_at' => 'Ngày bắt đầu',
      'expires_at' => 'Ngày hết hạn',
    ];
  }
}
