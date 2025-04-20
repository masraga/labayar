@extends("labayar::template")

<div class="h-screen flex justify-center items-center">
  <div class="w-[400px]">
    <div class="bg-white rounded shadow px-3 py-6 mt-3">
      <div class="flex justify-center items-center">
        <div>
          @if($payment["payment_status"] == \Koderpedia\Labayar\Utils\Constants::$paymentUnpaid)
          <img src="/labayar-assets/images/pending.png" class="justify-self-center" width="64px" height="64px" alt="pending-payment">
          <div class="text-3xl text-center font-medium text-gray-700 my-2">Payment in progress</div>
          <div class="text-center text-gray-400 text-sm" id="expired-time">Expired in 169:00:00</div>
          @elseif($payment["payment_status"] == \Koderpedia\Labayar\Utils\Constants::$paymentPaid)
          <img src="/labayar-assets/images/checklist.png" class="justify-self-center" width="64px" height="64px" alt="success-payment">
          <div class="text-3xl text-center font-medium text-gray-700 my-2">Payment successful</div>
          <div class="text-center text-gray-400 text-sm">Successfully paid {{\Koderpedia\Labayar\Utils\Str::toCurrency($payment["invoice"]["order_amount"])}}</div>
          @elseif($payment["payment_status"] == \Koderpedia\Labayar\Utils\Constants::$paymentExpired)
          <img src="/labayar-assets/images/warning.png" class="justify-self-center" width="64px" height="64px" alt="expired-payment">
          <div class="text-3xl text-center font-medium text-gray-700 my-2">Payment is expired</div>
          <div class="text-center text-gray-400 text-sm">Your order id {{$payment["order_id"]}} is expired </div>
          @endif
        </div>
      </div>
      <div class="mt-7 p-3 border border-gray-200 rounded w-[90%] m-auto">
        <div class="flex justify-between mb-3">
          <div class="text-gray-500 text-sm mt-[3px]">Order ID</div>
          <div class="font-medium text-gray-700">{{$payment["order_id"]}}</div>
        </div>
        @if($payment['paid_date'])
          <div class="flex justify-between mb-3">
            <div class="text-gray-500 text-sm mt-[3px]">Paid date</div>
            <div class="font-medium text-gray-700">{{date("d-m-Y H:i:s", strtotime($payment["paid_date"]))}}</div>
          </div>
        @endif
        <div class="flex justify-between mb-3">
          <div class="text-gray-500 text-sm mt-[3px]">Amount</div>
          <div class="font-medium text-gray-700">{{\Koderpedia\Labayar\Utils\Str::toCurrency($payment["amount"])}}</div>
        </div>
        <div class="flex justify-between mb-3">
          <div class="text-gray-500 text-sm mt-[3px]">Changes</div>
          <div class="font-medium text-gray-700">{{\Koderpedia\Labayar\Utils\Str::toCurrency($payment["change"])}}</div>
        </div>
        <div class="flex justify-between mb-3">
          <div class="text-gray-500 text-sm mt-[3px]">Nett</div>
          <div class="font-medium text-gray-700">{{\Koderpedia\Labayar\Utils\Str::toCurrency($payment["invoice"]["order_amount"])}}</div>
        </div>
        <div class="flex justify-between mb-3">
          <div class="text-gray-500 text-sm mt-[3px]">Method</div>
          <div class="font-medium text-gray-700">{{ucfirst($payment["payment_method"])}} - {{ucfirst($payment["payment_type"])}}</div>
        </div>
        <div class="flex justify-between">
          <div class="text-gray-500 text-sm mt-[3px]">Gateway</div>
          <div class="font-medium text-gray-700">{{ucfirst($payment["gateway"])}}</div>
        </div>
      </div>
    </div>
  </div>
</div>
@include("labayar::components.expired-countdown")