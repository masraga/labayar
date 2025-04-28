@php
$adminFee = 0;
$paymentMethod = "Cash";
$paymentType = "Cash";
$paymentCode = "";
$paymentStatus = "Unpaid";
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
@if($order['payment_status'] == \Koderpedia\Labayar\Utils\Constants::$paymentPaid)
@php $paymentStatus = "Paid"; @endphp
@endif
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{$filename}}</title>
  <style>
    html {
      padding: 0;
      margin: 0;
    }

    body {
      margin: 20px;
    }

    .brand-name {
      font-size: 2rem;
      font-weight: bold;
    }

    .text-lg {
      font-size: 1.2rem;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .mb-3 {
      margin-bottom: .3rem;
    }

    .pb-5 {
      padding-bottom: .5rem;
    }

    .table-item {
      margin-top: 30px;
    }

    .table-item>thead th,
    .table-item>tbody td {
      padding: 5px 0;
      padding-right: 5px;
    }

    .summary-table {
      margin-top: 30px;
    }
  </style>
</head>

<body>
  <table width="100%">
    <tr>
      <td valign="top">
        <div class="brand-name">{{$store['name']}}</div>
      </td>
    </tr>
    <tr>
      <td>
        <div class="text-lg full-address">{{$store['address']}}</div>
      </td>
      <td class="text-lg text-right">
        <table width="100%">
          <tr>
            <td class="text-lg text-right" style="width: 40%;"><b>Order Id</b></td>
            <td class="text-lg text-right" style="width: 50%;">{{$order["invoice_id"]}}</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>
        <div class="text-lg mb-3 phone">{{$store['phone']}}</div>
      </td>
      <td class="text-lg text-right">
        <table width="100%">
          <tr>
            <td class="text-lg text-right" style="width: 40%;"><b>Inv. date</b></td>
            <td class="text-lg text-right" style="width: 50%;">{{date('d-m-Y H:i:s', strtotime($order["created_at"]))}}</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>
        <div class="text-lg mb-3 email">{{$store['email']}}</div>
      </td>
      <td class="text-lg">
        <table width="100%">
          <tr>
            <td class="text-lg text-right" style="width: 40%;"><b>Status</b></td>
            <td class="text-lg text-right" style="width: 50%;">{{$paymentStatus}}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <table style="margin-top: 40px;">
    <tr>
      <td class="text-lg"><b>Customer</b></td>
    </tr>
    <tr>
      <td class="text-lg">{{$order['customer']['name']}}</td>
    </tr>
    <tr>
      <td class="text-lg">{{$order['customer']['email']}}</td>
    </tr>
    <tr>
      <td class="text-lg">{{$order['customer']['phone']}}</td>
    </tr>
  </table>

  <table class="table-item" width="100%" cellpadding="0" cellspacing="0" border="1">
    <thead>
      <th class="text-lg">No</th>
      <th class="text-lg">Items</th>
      <th class="text-lg">Qty</th>
      <th class="text-lg">Price</th>
      <th class="text-lg text-right">Gross Total</th>
    </thead>
    <tbody>
      @php $i=0; $grossTotal = 0; @endphp
      @foreach($order['item'] as $item)
      @if($item["type"] == \Koderpedia\Labayar\Utils\Constants::$sellItem)
      @php $i++; $grossTotal += $item['gross_total'] @endphp
      <tr>
        <td class="text-lg text-center">{{$i}}</td>
        <td class="text-lg text-center">{{$item['name']}}</td>
        <td class="text-lg text-center">{{$item['quantity']}}</td>
        <td class="text-lg text-center">{{\Koderpedia\Labayar\Utils\Str::toCurrency($item['price'])}}</td>
        <td class="text-lg text-right">{{\Koderpedia\Labayar\Utils\Str::toCurrency($item['gross_total'])}}</td>
      </tr>
      @endif
      @endforeach
    </tbody>
  </table>
  <table class="summary-table" width="100%">
    <tr>
      <td><b class="text-lg">Transfer VIA</b></td>
      <td>
        <table width="100%">
          <tr>
            <td class="text-lg text-right" style="width: 60%;"><b>Gross Total</b></td>
            <td class="text-lg text-right">{{\Koderpedia\Labayar\Utils\Str::toCurrency($grossTotal)}}</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td><b class="text-lg">{{$paymentMethod}} - {{$paymentType}}</b></td>
      <td>
        <table width="100%">
          <tr>
            <td class="text-lg text-right" style="width: 60%;"><b>Admin Total</b></td>
            <td class="text-lg text-right">{{\Koderpedia\Labayar\Utils\Str::toCurrency($adminFee)}}</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="text-lg">{{$paymentCode}}</td>
      <td>
        <table width="100%">
          <tr>
            <td class="text-lg text-right" style="width: 60%;"><b>Nett Total</b></td>
            <td class="text-lg text-right">{{\Koderpedia\Labayar\Utils\Str::toCurrency($order["order_amount"])}}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <table width="100%" style="margin-top: 20px;">
    <tr>
      <td class="text-lg text-center" style="padding-bottom: 50px;">Owner</td>
    </tr>
    <tr>
      <td class="text-lg text-center"><b>{{$store['owner_name']}}</b></td>
    </tr>
  </table>
</body>

</html>