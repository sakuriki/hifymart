<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
      'title' => 'required|string|max:255',
      'brand_id' => 'required|numeric',
      'category_id' => 'required|numeric',
      'description' => 'nullable|string',
      'price' => 'required|numeric',
      'featured_image' => 'mimes:jpeg,jpg,png,gif|sometimes|max:10000'
    ];
  }
}
