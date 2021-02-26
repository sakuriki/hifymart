<?php

namespace App\Http\Requests;

use Mews\Purifier\Facades\Purifier;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
   * Get data to be validated from the request.
   *
   * @return array
   */
  public function validationData()
  {
    $all = parent::validationData();
    $all['bank-info'] = Purifier::clean($all['bank-info']);
    $all['delivery'] = Purifier::clean($all['delivery']);
    return $all;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'app-name' => 'nullable|string',
      'working-time' => 'nullable|string',
      'contact-mail' => 'nullable|email:rfc,dns',
      'contact-phone' => 'nullable|string',
      'contact-mobile' => 'nullable|string',
      'contact-address' => 'nullable|string',
      'facebook' => 'nullable|string',
      'twitter' => 'nullable|string',
      'instagram' => 'nullable|string',
      'youtube' => 'nullable|string',
      'bank-info' => 'nullable|string',
      'delivery' => 'nullable|string',
    ];
  }

  public function messages()
  {
    return [
      'string' => ':attribute phải là chuỗi',
      'email' => ':attribute chưa hợp lệ hoặc không có thực',
    ];
  }

  public function attributes()
  {
    return [
      'app-name' => 'Tên ứng dụng',
      'working-time' => 'Thời gian làm việc',
      'contact-mail' => 'Email liên hệ',
      'contact-phone' => 'Hotline',
      'contact-mobile' => 'Số di động',
      'contact-address' => 'Địa chỉ',
      'facebook' => 'Facebook',
      'twitter' => 'Twitter',
      'instagram' => 'Instagram',
      'youtube' => 'Youtube',
      'bank-info' => 'Thông tin bank',
      'delivery' => 'Thông tin vận chuyển',
    ];
  }
}
