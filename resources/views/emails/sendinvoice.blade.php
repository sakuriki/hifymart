<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <style>
      body {
        font-family: arial, sans-serif;
        background-color:#f8f8f8;
        padding: 15px
      }

      table {
        border-collapse: collapse;
        width: 100%;
      }

      td, th {
        text-align: left;
        padding: 8px;
      }

      tr {
        border: 1px solid #dddddd;
      }

      tr:nth-child(even) {
        background-color:#f3f3f3;
      }

      .row {
        display: flex;
        flex-direction: row;
        padding: 10px 0;
      }
      .col {
        flex: 50%;
        width: 50%
      }
      .red--text {
        color: red
      }
      .overflow {
        margin: auto 0;
        margin-left:4px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .img--col {
        display: flex;
        align-items: center
      }
    </style>
  </head>
  <body>
    <div style="max-width: 1000px;margin: auto;background-color:#fff">
      <h1 style="padding:35px 35px;margin:0">{{ \Config::get('app.name') }}</h1>
      <div style="border-top:1.5px solid #dddddd"></div>
      <div style="padding:35px 35px">
        <p style="margin:0;font-size:16px"><b>Xin chào {{ $order->billing_name }},</b></p>
        <br>
        <p style="margin:0;font-size:16px">Cảm ơn bạn đã quan tâm sản phẩm của {{ \Config::get('app.name') }}. Đơn hàng của bạn đã được nhận và sẽ được xử lý ngay khi bạn xác nhận thanh toán.</p>
        </p>
      </div>
      <div style="border-top:1.5px solid #dddddd"></div>
      <div style="padding:10px 35px">
        <h4 style="color:#1976d2">THÔNG TIN ĐƠN HÀNG #{{ $order->id }} <span style="color:#989898">(Ngày đặt {{ date("d/m/Y", strtotime($order->created_at)) }})</span></h4>
        <div style="padding:0 10px">
          <p style="margin:0;font-size:16px"><b>Khách hàng: </b>{{ $order->billing_name }}</p>
          <p style="margin:0;font-size:16px"><b>Email: </b>{{ $order->billing_email }}</p>
          <p style="margin:0;font-size:16px"><b>SĐT: </b>{{ $order->billing_phone }}</p>
          <p style="margin:0;font-size:16px"><b>Địa chỉ: </b>{{ $order->billing_address . ', ' . $order->district->name . ', ' . $order->province->name }}</p>
          {{-- <p style="margin:0;font-size:16px"><b>: </b>{{ $order-> }}</p> --}}
        </div>
      </div>
      <div style="border-top:1.5px solid #dddddd"></div>
      <div style="padding:10px 35px">
        <h4 style="color:#1976d2">CHI TIẾT ĐƠN HÀNG</h4>
        <table>
          <thead style="color:#ffffff;background:#1976d2;text-align:left">
            <tr style="padding:8px">
              <th>Tên</th>
              <th style="text-align:right">SL</th>
              <th style="text-align:right">Giá</th>
              <th style="text-align:right">Tổng</th>
            </tr>
          </thead>
          @foreach($order->products as $product)
            <tr>
              <td class="img--col">
                <img src="{{ \Config::get('app.url') . $product->featured_image }}" alt="{{ $product->name }}" width="100" height="100">
                <span class="overflow">{{ $product->name }}</span>
              </td>
              <td style="text-align:right">{{ $product->pivot->quantity }}</td>
              <td style="text-align:right">{{ number_format($product->pivot->price).'₫' }}</td>
              <td style="text-align:right">{{ number_format($product->pivot->price * $product->pivot->quantity).' ₫' }}</td>
            </tr>
          @endforeach
          <tfoot style="text-align:right">
            <tr style="background-color:#f3f3f3">
              <td style="text-align:right" colspan="3"><b>Thành tiền</b></td>
              <td style="text-align:right">{{ number_format($order->billing_subtotal).' ₫' }}</td>
            </tr>
            @if ($order->billing_discount > 0)
            <tr style="background-color:#f3f3f3">
              <td style="text-align:right" colspan="3"><b>Giảm giá</b></td>
              <td style="text-align:right">{{ number_format($order->billing_discount).' ₫' }}</td>
            </tr>
            @endif
            <tr style="background-color:#f3f3f3">
              <td style="text-align:right" colspan="3"><b>Thuế(VAT)</b></td>
              <td style="text-align:right">{{ number_format($order->billing_tax).' ₫' }}</td>
            </tr>
            <tr style="background-color:#f3f3f3">
              <td style="text-align:right" colspan="3"><b>Phí vận chuyển</b></td>
              <td style="text-align:right">{{ number_format($order->billing_shipping_fee).' ₫' }}</td>
            </tr>
            <tr style="background-color:#f3f3f3">
              <td style="text-align:right" colspan="3"><b>Tổng cộng</b></td>
              <td style="text-align:right">{{ number_format($order->billing_total).' ₫' }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div style="padding:40px;margin:auto;text-align:center">
        <a href="{{ \Config::get('app.client_url') }}" style="width:fit-content;background-color:#1976d2;color:#fff;font-weight:bold;text-align:center;padding:10px 12px;border-radius:2px;margin:auto;font-size:large;text-decoration:none" target="_blank">Tiếp tục mua sắm</a>
      </div>
      <div style="border-top:1.5px solid #dddddd"></div>
      <div style="padding:35px 35px">
        <p style="font-size:16px">
          Nếu gặp bất cứ vấn đề gì xin hãy phản hồi lại thư này cho chúng tôi biết.
        </p>
        <br>
        <p style="font-size:16px">
          Trân trọng,
        </p>
        <p style="font-size:16px">
          {{ \Config::get('app.name') }}
        </p>
      </div>
    </div>
  </body>
</html>
