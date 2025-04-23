@extends("labayar::template")
@php
$paymentMethod="";
$paymentType="Cash";
$paymentImage="/labayar-assets/images/cash-payment.png";
@endphp
@foreach($payment["metadata"] as $metadata)
@if($metadata["key"] == \Koderpedia\Labayar\Utils\Constants::$paymentMethod)
@php
$paymentMethod = $metadata['value'];
@endphp
@endif
@if($metadata["key"] == \Koderpedia\Labayar\Utils\Constants::$paymentTypeName)
@php
$paymentType = $metadata['value'];
@endphp
@endif
@if($metadata["key"] == \Koderpedia\Labayar\Utils\Constants::$paymentGatewayImage)
@php
$paymentImage = $metadata['value'];
@endphp
@endif
@endforeach
<div class="h-screen flex justify-center items-center">
  <div class="w-[400px]">
    <div class="text-center text-lg text-gray-600">Total amount</div>
    <div class="text-center text-4xl my-2 font-bold">{{\Koderpedia\Labayar\Utils\Str::toCurrency($payment['invoice']['order_amount'])}}</div>
    <div class="text-gray-500 text-center" id="expired-time">Expired in 169:00:00</div>
    <form action="/api/labayar/pay" method="post">
      @csrf
      <div class="bg-white rounded shadow px-3 py-6 mt-3">
        <div class="mb-2">
          <div class="text-gray-800 font-medium mb-2">Payment method</div>
          <div>
            <div class="flex">
              <img src="{{$paymentImage}}" class="w-[60px] h-auto" alt="">
              <div class="mt-[-2px] ml-2">{{$paymentMethod}} - {{$paymentType}}</div>
            </div>
          </div>
          <div class="mt-4">
            <label for="" class="block font-medium text-gray-700">Amount</label>
            <input type="hidden" name="useBuiltIn" value="true">
            <input type="hidden" name="paymentId" value="{{$payment['order_id']}}">
            <input type="text" value="{{\Koderpedia\Labayar\Utils\Str::toCurrency($payment['amount'])}}" readonly class="border text-gray-700 border-gray-300 w-full rounded text-base/9 px-3" name="amount" required>
          </div>
          <div class="mt-4">
            <label for="" class="block font-medium text-gray-700">Changes</label>
            <input type="text" value="{{\Koderpedia\Labayar\Utils\Str::toCurrency($payment['change'])}}" class="border text-gray-700 border-gray-300 w-full rounded text-base/9 px-3" name="changes" readonly required>
          </div>
        </div>
      </div>
      <button id="button-submit" class="mt-2 block bg-gray-800 hover:bg-gray-900 text-white w-full p-2 rounded font-medium cursor-pointer">Pay</button>
    </form>
  </div>
</div>
@include("labayar::components.expired-countdown")