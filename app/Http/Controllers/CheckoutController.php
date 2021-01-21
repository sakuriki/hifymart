<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
  public function store(Request $request)
  {
    try {
      DB::beginTransaction();
      $products = Product::whereIn("id", collect($request->input("product"))->keys())->get();
      $subTotal = 0;
      $orderProduct = [];
      foreach ($products as $product) {
        $quantity = $request->input("product")[$product->id];
        $price = $product->getSalePrice();
        $subTotal += $price * $quantity;
        $orderProduct[] = [
          "product_id" => $product->id,
          "quantity" => $quantity,
          "price" => $price
        ];
        // $newQuantity = $product->quantity - $quantity;
        // if ($newQuantity < 0) {
        //   $text = $product->quantity == 0 ? "đã hết hàng." : 'không đủ số lượng bạn yêu cầu, vui lòng giảm số lượng hoặc mua lại sau.';
        //   return response()->json([
        //     'errors' => [
        //       'quantity' => ['Xin lỗi, "' . $product->name . '" trong giỏ của bạn hiện ' . $text]
        //     ]
        //   ], 422);
        // }
        // $product->quantity = $newQuantity;
        // $product->save();
      }
      $discount = 0;
      $coupon = $request->coupon ? Coupon::where('code', $request->coupon)->first() : null;
      if ($coupon && (!$coupon->min || $subTotal >= $coupon->min) && $coupon->isRedeemable()) {
        $discount = $coupon->value;
        if ($coupon->is_percent) {
          $discount = $subTotal * $coupon->value / 100;
          $discount = $discount < $coupon->max
            ? $discount
            : $coupon->max;
        }
        $coupon->number = --$coupon->number;
        $coupon->save();
      }
      $tax = $subTotal * 0.1;
      $shipping_fee = $subTotal >= 200000 ? 0 : 19000;
      $totalAfterDiscount = $subTotal - $discount;
      $total = $totalAfterDiscount > 0 ? $totalAfterDiscount + $tax + $shipping_fee : $shipping_fee;
      $order = Order::create([
        'user_id' => auth()->user() ? auth()->user()->id : null,
        'district_id' => $request->district_id,
        'province_id' => $request->province_id,
        'billing_email' => $request->email,
        'billing_name' => $request->name,
        'billing_address' => $request->address,
        'billing_phone' => $request->phone,
        'billing_discount' => $discount,
        'billing_discount_code' => $coupon ? $coupon->code : null,
        'billing_subtotal' => $subTotal,
        'billing_tax' => $tax,
        'billing_shipping_fee' => $shipping_fee,
        'billing_total' => $total,
        'payment_type' => $request->input('payment_type', 'cod')
      ]);
      // return $orderProduct;
      $orderProduct = collect($orderProduct)->map(function ($item) use ($order) {
        $item["order_id"] = $order->id;
        return $item;
      });
      // return $orderProduct;
      OrderProduct::insert($orderProduct->toArray());
      DB::commit();
      $response['id'] = $order->id;
      if ($request->input('payment_type') === 'vnpay') {
        $response['payment_url'] = $this->createVnpayUrl($order->id, $total);
      }
      return response()->json($response);
    } catch (\Exception $exception) {
      DB::rollBack();
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function createVnpayUrl($orderId, $amount)
  {
    $vnp_Url = "http://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Amount = $amount * 100;
    $inputData = array(
      "vnp_Version" => "2.0.0",
      "vnp_TmnCode" => env("VNPAY_TMNCODE"),
      "vnp_Amount" => $vnp_Amount,
      "vnp_Command" => "pay",
      "vnp_CreateDate" => date('YmdHis'),
      "vnp_CurrCode" => "VND",
      "vnp_IpAddr" => request()->ip(),
      "vnp_Locale" => 'vn',
      "vnp_OrderInfo" => "Thanh toán hóa đơn phí dich vụ",
      "vnp_OrderType" => "billpayment",
      "vnp_ReturnUrl" => env("CLIENT_URL") . "/order/verify",
      // "vnp_IpnUrl" => route('vnpay.ipn'),
      "vnp_TxnRef" => $orderId,
    );
    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    foreach ($inputData as $key => $value) {
      if ($i == 1) {
        $hashdata .= '&' . $key . "=" . $value;
      } else {
        $hashdata .= $key . "=" . $value;
        $i = 1;
      }
      $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }
    $vnp_Url = $vnp_Url . "?" . $query;
    $vnpSecureHash = hash('sha256', env("VNP_HASHSECRET") . $hashdata);
    $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
    return $vnp_Url;
  }

  public function vnpayHash()
  {
    $inputData = request()->all();
    unset($inputData['vnp_SecureHashType']);
    unset($inputData['vnp_SecureHash']);
    ksort($inputData);
    $i = 0;
    $hashData = "";
    foreach ($inputData as $key => $value) {
      if ($i == 1) {
        $hashData = $hashData . '&' . $key . "=" . $value;
      } else {
        $hashData = $hashData . $key . "=" . $value;
        $i = 1;
      }
    }
    return hash('sha256', env("VNP_HASHSECRET") . $hashData);
  }

  public function ipnVnpay()
  {
    // $vnp_SecureHash = request('vnp_SecureHash');
    // $secureHash = $this->vnpayHash();
    // $vnpTranId = request('vnp_TransactionNo');
    // $vnp_BankCode = request('vnp_BankCode');
    // $Status = 0;
    // try {
    //   if ($secureHash == $vnp_SecureHash) {
    //     $order = Order::findOrFail(request('vnp_TxnRef'));
    //     if ($order != NULL) {
    //       if ($order["Status"] != NULL && $order["Status"] == 0) {
    //         if (request('vnp_ResponseCode') == '00') {
    //           $Status = 1;
    //         } else {
    //           $Status = 2;
    //         }
    //         //Cài đặt Code cập nhật kết quả thanh toán, tình trạng đơn hàng vào DB
    //         //
    //         //
    //         //
    //         //Trả kết quả về cho VNPAY: Website TMĐT ghi nhận yêu cầu thành công
    //         $returnData['RspCode'] = '00';
    //         $returnData['Message'] = 'Confirm Success';
    //       } else {
    //         $returnData['RspCode'] = '02';
    //         $returnData['Message'] = 'Order already confirmed';
    //       }
    //     } else {
    //       $returnData['RspCode'] = '01';
    //       $returnData['Message'] = 'Order not found';
    //     }
    //   } else {
    //     $returnData['RspCode'] = '97';
    //     $returnData['Message'] = 'Chu ky khong hop le';
    //   }
    // } catch (Exception $e) {
    //   $returnData['RspCode'] = '99';
    //   $returnData['Message'] = 'Unknow error';
    // }
    // //Trả lại VNPAY theo định dạng JSON
    // echo json_encode($returnData);
  }

  public function returnVnpay()
  {
    // cancel
    if (request('vnp_ResponseCode') === '24') {
      return response()->noContent();
    }
    // error
    if (request('vnp_ResponseCode') !== '00') {
      return response()->json([
        "success" => false,
        "msg" => "Có lỗi xảy ra, vui lòng thử lại"
      ]);
    }
    // success
    if (request('vnp_ResponseCode') === '00') {
      $vnp_SecureHash = request('vnp_SecureHash');
      $secureHash = $this->vnpayHash();
      if ($vnp_SecureHash !== $secureHash) {
        return response()->json([
          "success" => false,
          "msg" => "Chữ ký không hợp lệ, vui lòng thử lại"
        ]);
      }
      $order = Order::findOrFail(request('vnp_TxnRef'));
      // Dùng tạm, docs vnpay thiếu, chưa dùng được IPN trong dev env, không update hoá đơn ở đây trong product env
      if (!$order->is_paid) {
        $order->is_paid = true;
        $order->save();
      }
      return response()->json([
        "success" => true,
        "order" => $order
      ]);
    }
  }
}
