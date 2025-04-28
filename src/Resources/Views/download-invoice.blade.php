@php
$adminFee = 0;
$paymentMethod = "Cash";
$paymentType = "Cash";
$paymentCode = ""
@endphp
@foreach($order["metadata"] as $meta)
@if($meta['key'] == \Koderpedia\Labayar\Utils\Constants::$adminFee)
@php $adminFee = $meta['value']; @endphp
@endif
@if($meta['key'] == \Koderpedia\Labayar\Utils\Constants::$paymentMethod)
@php $paymentMethod = $meta['value']; @endphp
@endif
@if($meta['key'] == \Koderpedia\Labayar\Utils\Constants::$gatewayMerchantName)
@php $paymentType = $meta['value']; @endphp
@endif
@if($meta['key'] == \Koderpedia\Labayar\Utils\Constants::$gatewayMerchantCode)
@php $paymentCode = $meta['value']; @endphp
@endif
@endforeach
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<style>
  html {
    padding: 0;
    margin: 0;
  }

  body {
    padding: 20px;
  }

  .store-info {
    display: flex;
    justify-content: space-between;
  }

  .logo-image {
    width: 70px;
  }

  .brand-name {
    font-size: 2.2rem;
    font-weight: bold;
  }

  .brand {
    display: flex;
  }

  .brand-name {
    margin-top: 10px;
    margin-left: 15px;
  }

  .text-lg {
    font-size: 1.2rem;
  }

  .text-right {
    text-align: right;
  }

  .text-left {
    text-align: left;
  }

  .mb-3 {
    margin-bottom: 5px;
  }

  .pb-5 {
    padding-bottom: 10px;
  }

  .pr-3 {
    padding-right: 10px;
  }

  .text-center {
    text-align: center;
  }

  .invoice {
    margin-top: 50px;
    display: flex;
    justify-content: space-between;
  }

  .invoice-detail>tbody>tr>th {
    text-align: left;
  }

  .table-items {
    margin-top: 20px;
  }

  .invoice-item>thead th,
  .invoice-item>tbody td {
    padding: 5px;
  }

  .invoice-summary {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
  }
</style>

<body>
  <div class="store-info">
    <div class="brand">
      <div><img src="{{$store['logo']}}" alt="logo" class="logo-image"></div>
      <div class="brand-name">{{$store['name']}}</div>
    </div>
    <div class="address">
      <div class="text-lg mb-3 full-address">{{$store['address']}}</div>
      <div class="text-lg mb-3 phone">{{$store['phone']}}</div>
      <div class="text-lg mb-3 email">{{$store['email']}}</div>
    </div>
  </div>
  <hr>
  <div class="invoice">
    <div class="customer">
      <div class="text-lg mb-3"><b>Customer</b></div>
      <div class="text-lg mb-3 full-address">{{$order['customer']['name']}}</div>
      <div class="text-lg mb-3 phone">{{$order['customer']['phone']}}</div>
      <div class="text-lg mb-3 email">{{$order['customer']['email']}}</div>
    </div>
    <div class="invoice-info">
      <table class="invoice-detail">
        <tbody>
          <tr>
            <th class="text-lg">No. invoice</th>
            <td class="text-lg">: {{$order['invoice_id']}}</td>
          </tr>
          <tr>
            <th class="text-lg">Inv. date</th>
            <td class="text-lg">: {{date('d-m-Y H:i:s', strtotime($order['created_at']))}}</td>
          </tr>
          <tr>
            <th class="text-lg">Inv. status</th>
            <td class="text-lg">:
              @if($order['payment_status'] == \Koderpedia\Labayar\Utils\Constants::$paymentUnpaid)
              Unpaid
              @else
              Paid
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="table-items">
    <table cellpadding=="0" cellspacing="0" class="invoice-item" width="100%" border="1">
      <thead>
        <th class="text-lg">No</th>
        <th class="text-lg">Items</th>
        <th class="text-lg">Qty</th>
        <th class="text-lg">Price</th>
        <th class="text-lg text-right">Total</th>
      </thead>
      <tbody>
        @php $i = 0; $grossTotal = 0 @endphp
        @foreach($order["item"] as $item)
        @php $i++ @endphp
        @if($item['type'] == \Koderpedia\Labayar\Utils\Constants::$adminFee)
        @php continue; @endphp
        @endif
        @php $grossTotal+= $item["gross_total"] @endphp
        <tr>
          <td class="text-center text-lg">{{$i}}</td>
          <td class="text-center text-lg">{{$item['name']}}</td>
          <td class="text-center text-lg">{{$item['quantity']}}</td>
          <td class="text-center text-lg">{{\Koderpedia\Labayar\Utils\Str::toCurrency($item['price'])}}</td>
          <td class="text-right text-lg">{{\Koderpedia\Labayar\Utils\Str::toCurrency($item['gross_total'])}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="invoice-summary">
    <div class="transfer">
      <div class="text-lg mb-3"><b>TRANSFER VIA</b></div>
      <div class="text-lg mb-3">{{$paymentMethod}} - {{$paymentType}}</div>
      <div class="text-lg mb-3">{{$paymentCode}}</div>
    </div>
    <div class="owner">
      <div class="text-lg text-center">Owner</div>
      <br>
      <br>
      <br>
      <div class="text-lg text-center"><b>{{$store["owner_name"]}}</b></div>
    </div>
    <div class="fee">
      <table>
        <tr>
          <th class="text-left pb-5 pr-3 text-lg">Gross total</th>
          <td class="text-right text-lg pb-5">{{\Koderpedia\Labayar\Utils\Str::toCurrency($grossTotal)}}</td>
        </tr>
        <tr>
          <th class="text-left pb-5 pr-3 text-lg">Admin Fee</th>
          <td class="text-right text-lg pb-5">{{\Koderpedia\Labayar\Utils\Str::toCurrency($adminFee)}}</td>
        </tr>
        <tr>
          <th class="text-left pb-5 pr-3 text-lg">Nett</th>
          <td class="text-right text-lg pb-5">{{\Koderpedia\Labayar\Utils\Str::toCurrency($order["order_amount"])}}</td>
        </tr>
      </table>
    </div>
  </div>
</body>

</html>