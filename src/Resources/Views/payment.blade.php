@extends("labayar::template")
@section("title","Payments")
@section("content")
<div class="flex gap-2 mt-3">
  <div>@include("labayar::components.icons.document")</div>
  <div class="text-gray-500 text-sm hover:text-black hover:font-medium"><a href="/api/labayar/orders">order</a></div>
  <div class="text-gray-500 text-sm">/</div>
  <div class="text-sm font-medium">{{$order["invoice_id"]}}</div>
</div>
<div class="block lg:flex justify-between pt-4 gap-4">
  <div class="w-full overflow-auto lg:w-[65%]">
    <div class="bg-white rounded p-4">
      <table class="w-full bg-white">
        <colgroup>
          <col class="w-[50px]">
        </colgroup>
        <thead>
          <th class="text-left text-sm text-slate-500 bg-slate-200 border border-slate-200 font-normal p-2">No</th>
          <th class="text-left text-sm text-slate-500 bg-slate-200 border border-slate-200 font-normal p-2">Payment</th>
          <th class="text-left text-sm text-slate-500 bg-slate-200 border border-slate-200 font-normal p-2">Created At</th>
          <th class="text-left text-sm text-slate-500 bg-slate-200 border border-slate-200 font-normal p-2">Amount</th>
          <th class="text-left text-sm text-slate-500 bg-slate-200 border border-slate-200 font-normal p-2">Changes</th>
          <th class="text-left text-sm text-slate-500 bg-slate-200 border border-slate-200 font-normal p-2">Nett</th>
          <th class="text-center text-sm text-slate-500 bg-slate-200 border border-slate-200 font-normal p-2">Status</th>
          <th class="text-center text-sm text-slate-500 bg-slate-200 border border-slate-200 font-normal p-2">
            @include("labayar::components.icons.menu")
          </th>
        </thead>
        <tbody>
          @php $i=0 @endphp
          @foreach($payments as $payment)
          <tr>
            <td class="px-2 py-3 border-b border-slate-200 text-gray-600 text-sm">{{$i+1}}</td>
            <td class="px-2 py-3 border-b border-slate-200 text-gray-600 text-sm">{{ucfirst($payment["gateway"])}} - {{$payment["payment_type"]}}</td>
            <td class="px-2 py-3 border-b border-slate-200 text-gray-600 text-sm">{{date("d-m-Y H:i:s", strtotime($payment["created_at"]))}}</td>
            <td class="px-2 py-3 border-b border-slate-200 text-gray-600 text-sm">{{\Koderpedia\Labayar\Utils\Str::toCurrency($payment["amount"])}}</td>
            <td class="px-2 py-3 border-b border-slate-200 text-gray-600 text-sm">{{\Koderpedia\Labayar\Utils\Str::toCurrency($payment["change"])}}</td>
            <td class="px-2 py-3 border-b border-slate-200 text-gray-600 text-sm">{{\Koderpedia\Labayar\Utils\Str::toCurrency($payment["nett_amount"])}}</td>
            <td class="px-2 py-3 border-b border-slate-200 text-gray-600 text-sm text-center">
              @if($payment["payment_status"] == \Koderpedia\Labayar\Utils\Constants::$paymentUnpaid)
              <span class="text-gray-700 text-center bg-yellow-400 font-medium px-2 py-[2px] rounded">Unpaid</span>
              @elseif($payment["payment_status"] == \Koderpedia\Labayar\Utils\Constants::$paymentPaid)
              <span class="text-center bg-green-600 font-medium text-white px-2 py-[2px] rounded">Paid</span>
              @elseif($payment["payment_status"] == \Koderpedia\Labayar\Utils\Constants::$paymentExpired)
              <span class="text-white text-center bg-red-600 font-medium px-2 py-[2px] rounded">Expired</span>
              @endif
            </td>
            <td class="px-2 py-3 border-b border-slate-200 text-gray-600 text-sm text-center">
              <label class="cursor-pointer" for="dropdown-{{$i}}">
                @include("labayar::components.icons.menu")
              </label>
              <input type="checkbox" name="dropdown" id="dropdown-{{$i}}" class="peer hidden">
              <div class="p-2 text-left peer-checked:block hidden rounded bg-white shadow absolute w-[100px]">
                @if($payment["payment_status"] == \Koderpedia\Labayar\Utils\Constants::$paymentUnpaid)
                <div class="mb-2 hover:font-medium"><a href="/api/labayar/pay/{{$payment['order_id']}}" target="_blank">Pay</a></div>
                @endif
                <div class="mb-2 hover:font-medium"><a href=" /api/labayar/payment-status/{{$payment['order_id']}}" target="_blank">Detail</a></div>
                <div class="mb-2 hover:font-medium"><a href="/api/labayar/payment-status/{{$payment['order_id']}}" target="_blank">Download Invoice</a></div>
              </div>
            </td>
          </tr>
          @php $i++ @endphp
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="w-full mt-4 lg:mt-0 lg:w-[35%]">
    @include("labayar::components.invoice-card")
  </div>
</div>
@endsection