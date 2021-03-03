<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
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
      'approved' => 'nullable|boolean',
      'rating' => 'sometimes|numeric|min:0',
      'review' => 'sometimes|string|max:2000',
      'user_id' => 'sometimes|numeric|exists:users,id',
      'product_id' => 'sometimes|numeric|exists:products,id',
    ];
  }

  public function messages()
  {
    return [
      'string' => ':attribute phải là chuỗi',
      'max' => ':attribute tối đa 2000 ký tự',
      'numeric' => ':attribute phải là số',
      'exists' => ':attribute không tồn tại',
      'boolean' => ':attribute phải là dạng boolean',
    ];
  }

  public function attributes()
  {
    return [
      'approved' => 'Duyệt',
      'user_id' => 'Người dùng',
      'product_id' => 'Sản phẩm',
      'rating' => 'Số sao',
      'review' => 'Đánh giá',
    ];
  }
}
