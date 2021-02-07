<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\District;
use App\Models\Province;
use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $status
 * @property int|null $user_id
 * @property int|null $province_id
 * @property int|null $district_id
 * @property string|null $billing_email
 * @property string|null $billing_name
 * @property string|null $billing_address
 * @property string|null $billing_note
 * @property string|null $billing_phone
 * @property int $billing_discount
 * @property string|null $billing_discount_code
 * @property int $billing_subtotal
 * @property int $billing_tax
 * @property int $billing_shipping_fee
 * @property int $billing_total
 * @property string $payment_type
 * @property bool $is_paid
 * @property bool $shipped
 * @property string|null $error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read District|null $district
 * @property-read \Illuminate\Database\Eloquent\Collection|OrderProduct[] $order_product
 * @property-read int|null $order_product_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read Province|null $province
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingDiscountCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingShippingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipped($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
  protected $fillable = [
    'status', 'user_id', 'province_id', 'district_id', 'billing_email', 'billing_name', 'billing_address', 'billing_note', 'billing_phone', 'billing_discount', 'billing_discount_code', 'billing_subtotal', 'billing_tax', 'billing_shipping_fee', 'billing_total', 'payment_type', 'is_paid', 'shipped', 'error'
  ];

  protected $casts = [
    "shipped" => "boolean",
    "is_paid" => "boolean",
  ];

  public function user()
  {
    return $this->belongsTo(User::class)->withCount('orders');
  }

  public function products()
  {
    return $this->belongsToMany(Product::class)->withPivot('quantity', 'price');
  }

  public function order_product()
  {
    return $this->hasMany(OrderProduct::class, "order_id");
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
