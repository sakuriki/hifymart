<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
      'name' => 'required_without:user_id|string|max:255',
      'email' => 'required_without:user_id|email:rfc,dns',
      'phone' => 'nullable|numeric',
      'content' => 'required|string|max:2000',
      'parent_id' => 'nullable|numeric|exists:comments,id',
      'user_id' => 'required_without_all:name,email|numeric|exists:users,id',
      'product_id' => 'required|numeric|exists:products,id'
    ];
  }

  public function messages()
  {
    return [
      'required' => ':attribute không được bỏ trống',
      'string' => ':attribute phải là chuỗi',
      'name.max' => 'Tên tối đa 255 ký tự',
      'content.max' => 'Bình luận tối đa 2000 ký tự',
      'email' => ':attribute chưa hợp lệ hoặc không có thực',
      'numeric' => ':attribute phải là số',
      'exists' => ':attribute không tồn tại',
    ];
  }

  public function attributes()
  {
    return [
      'name' => 'Tên',
      'email' => 'Địa chỉ email',
      'phone' => 'Số điện thoại',
      'content' => 'Nội dung bình luận',
      'user_id' => 'Người dùng',
      'parent_id' => 'Bình luận cha',
      'product_id' => 'Sản phẩm',
    ];
  }
}
