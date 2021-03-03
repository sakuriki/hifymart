<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        'is_paid' => 'sometimes|boolean',
        'shipped' => 'sometimes|boolean',
        'status' => 'sometimes|numeric',
      ];
    }

    public function messages()
    {
      return [
        'boolean' => ':attribute phải là dạng boolean',
        'numeric' => ':attribute phải là số',
      ];
    }

    public function attributes()
    {
      return [
        'is_paid' => 'Thanh toán',
        'shipped' => 'Vận chuyển',
        'status' => 'Trạng thái',
      ];
    }
}
