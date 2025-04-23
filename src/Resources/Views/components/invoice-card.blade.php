@php
$adminFee=0;
$subTotalFee=0;
@endphp
@foreach($order["metadata"] as $metadata)
@if($metadata["key"] == \Koderpedia\Labayar\Utils\Constants::$subTotal)
@php
$subTotalFee = \Koderpedia\Labayar\Utils\Str::toCurrency($metadata['value'])
@endphp
@elseif($metadata["key"] == \Koderpedia\Labayar\Utils\Constants::$adminFee)
@php
$adminFee = \Koderpedia\Labayar\Utils\Str::toCurrency($metadata['value'])
@endphp
@endif
@endforeach
<a href="/api/labayar/payments/{{$order['invoice_id']}}">
  <div class="card bg-white rounded py-2 shadow">
    <div class="px-4 pt-2">
      <div class="text-lg text-gray-700 font-bold">{{$order["invoice_id"]}}</div>
      <div class="flex justify-between pt-4">
        <div>
          <div class="text-gray-400 font-medium mb-[3px] text-sm">Order Date</div>
          <div class="text-gray-700 text-sm font-medium">{{date("d-m-Y", strtotime($order["created_at"]))}}</div>
        </div>
        <div>
          <div class="text-gray-400 font-medium mb-[3px] text-sm">Customer</div>
          <div class="text-gray-700 text-sm font-medium">{{$order["customer"]["name"]}}</div>
        </div>
        <div>
          <div class="text-gray-400 font-medium mb-[3px] text-sm">Status</div>
          @if($order["payment_status"] == \Koderpedia\Labayar\Utils\Constants::$paymentUnpaid)
          <div class="text-gray-700 text-sm font-bold bg-yellow-400 px-2 pt-[2px] pb-[3px] rounded">Unpaid</div>
          @elseif($order["payment_status"] == \Koderpedia\Labayar\Utils\Constants::$paymentPaid)
          <div class="text-white text-sm font-bold bg-green-600 px-2 pt-[2px] pb-[3px] rounded">Paid</div>
          @endif
        </div>
      </div>
      <div class="flex pt-4">
        <div>
          <div class="text-gray-400 font-medium mb-[3px] text-sm">Customer address</div>
          <div class="text-gray-700 text-sm font-medium w-[60%]">{{$order["customer"]["email"]}}</div>
          <div class="text-gray-700 text-sm font-medium">{{$order["customer"]["phone"]}}</div>
          <!-- <div class="text-gray-600 font-medium">Jl utama gg cipta no 18, pekanbaru, tenayan raya, riau, indonesia</div> -->
        </div>
      </div>
    </div>
    <div class="border-b border-gray-200 my-4"></div>
    <div class="item max-h-[150px] min-h-[150px] overflow-y-auto">
      @foreach($order["item"] as $item)
      @if($item["type"] == \Koderpedia\Labayar\Utils\Constants::$sellItem)
      <div class="flex px-4 mb-3">
        <div class="w-[50%]">
          <div class="text-gray-500">{{$item["name"]}}</div>
        </div>
        <div class="w-[20%] text-right text-gray-500">qty {{$item["quantity"]}}</div>
        <div class="w-[30%] text-right font-bold">{{\Koderpedia\Labayar\Utils\Str::toCurrency($item["price"])}}</div>
      </div>
      @endif
      @endforeach
    </div>
    <div class="border-b border-gray-200 my-4"></div>
    <div class="px-4">
      <div class="flex justify-between mb-2">
        <div class="text-base text-gray-400">Subtotal</div>
        <div class="font-bold">{{$subTotalFee}}</div>
      </div>
      <div class="flex justify-between mb-2">
        <div class="text-base text-gray-400">Admin Fee</div>
        <div class="font-bold">{{$adminFee}}</div>
      </div>
      <div class="flex justify-between mb-2">
        <div class="text-base text-gray-400">Discount</div>
        <div class="font-bold">Rp0</div>
      </div>
    </div>
    <div class="border-b border-gray-200 my-4"></div>
    <div class="flex justify-between mb-2 px-4">
      <div class="text-base text-gray-400">Total</div>
      <div class="font-bold text-green-600">{{\Koderpedia\Labayar\Utils\Str::toCurrency($order["order_amount"])}}</div>
    </div>
  </div>
</a>