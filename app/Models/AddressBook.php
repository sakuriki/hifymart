<?php

namespace App\Models;

use App\Models\User;
use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AddressBook
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property int $user_id
 * @property int|null $province_id
 * @property int|null $district_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read District|null $district
 * @property-read Province|null $province
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook query()
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddressBook whereUserId($value)
 * @mixin \Eloquent
 */
class AddressBook extends Model
{
  protected $fillable = [
    'user_id', 'province_id', 'district_id', 'email', 'name', 'address', 'phone',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function province()
  {
    return $this->belongsTo(Province::class);
  }

  public function district()
  {
    return $this->belongsTo(District::class);
  }
}
