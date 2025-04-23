@extends("labayar::template")
@section("content")
<form action="/api/labayar/snap" method="post">
  @csrf
  <div class="lg:flex justify-between gap-3">
    <div class="payment bg-white rounded p-4 w-full lg:w-[65%] mb-6 lg:mb-0">
      <div class="text-xl text-gray-800 font-medium my-4">Customer information</div>
      <div class="lg:flex justify-between gap-3">
        <div class="rounded px-2 py-4 w-full lg:border border-gray-200">
          <div class="flex gap-2">
            <div class="text-gray-700">@include("labayar::components.icons.person")</div>
            <div class="text-gray-700 font-medium">{{$customer['name']}}</div>
          </div>
        </div>
        <div class="rounded px-2 py-4 w-full lg:border border-gray-200">
          <div class="flex gap-2">
            <div class="text-gray-700">@include("labayar::components.icons.envelope")</div>
            <div class="text-gray-700 font-medium">{{$customer['email']}}</div>
          </div>
        </div>
        <div class="rounded px-2 py-4 w-full lg:border border-gray-200">
          <div class="flex gap-2">
            <div class="text-gray-700">@include("labayar::components.icons.phone")</div>
            <div class="text-gray-700 font-medium">{{$customer['phone']}}</div>
          </div>
        </div>
      </div>
      <div class="text-xl text-gray-800 font-medium my-4">Payment</div>
      <div class="bg-gray-50 py-6 rounded">
        <div class="text-gray-500 text-center">Total amount</div>
        <div class="text-gray-700 font-bold my-1 text-3xl text-center" id="totalAmount">{{\Koderpedia\Labayar\Utils\Str::toCurrency($amount)}}</div>
        <div class="text-gray-400 text-sm text-center">Your payment is secure</div>
      </div>
      <div class="text-xl text-gray-800 font-medium my-4">Select method</div>
      <div class="accordion border border-gray-200 border-t-0 rounded">
        @foreach($channels as $channel)
        <div class="item border-t-1 border-gray-200">
          <div class="header p-2 relative">
            <label for="{{$channel['method']}}" class="peer cursor-pointer">
              <input type="radio" id="{{$channel['method']}}" name="paymentMethod" value="{{$channel['method']}}">
              <span class="ml-3">{{$channel["method"]}}</span>
            </label>
            <div class="hidden peer-has-checked:block mt-2">
              <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                @foreach($channel["types"] as $type)
                <div>
                  <input
                    type="radio"
                    name="paymentType"
                    id="{{$type['name']}}"
                    value="{{$type['code']}}"
                    tax-fix="{{$type['taxFix']}}"
                    tax-percent="{{$type['taxPercent']}}"
                    image="{{$type['image']}}"
                    type-name="{{$type['name']}}"
                    class="peer hidden">
                  <label for="{{$type['name']}}" class="cursor-pointer border border-gray-200 peer-checked:border-sky-500 peer-checked:border-2 rounded flex justify-center items-center w-full h-[100px] lg:w-[150px] lg:h-[100px]">
                    <div>
                      <img
                        src="{{$type['image']}}"
                        alt="{{$type['name']}}"
                        height="{{$type['height']}}"
                        width="{{$type['width']}}">
                    </div>
                  </label>
                </div>
                @endforeach
              </div>
            </div>
          </div>
          <div class="content"></div>
        </div>
        @endforeach
      </div>
    </div>
    <div class="invoice rounded w-full lg:w-[35%]">
      <div class="bg-white pb-4 rounded">
        <div class="flex justify-between px-4 pt-4">
          <div class="block">
            <div class="font-medium text-gray-800">Order ID</div>
            <div class="text-gray-500">{{$orderId}}</div>
          </div>
          <div class="block">
            <div class="font-medium text-gray-800">Order Date</div>
            <div class="text-gray-500">{{$createdAt}}</div>
          </div>
        </div>
        <div class="sepatarator h-[1px] w-full bg-gray-200 my-4"></div>
        <div class="items px-4">
          <div class=" text-gray-800 font-medium mb-2">Purchase items</div>
          <div class="max-h-[250px] overflow-y-auto">
            @foreach($items as $item)
            <input type="hidden" name="itemName[]" value="{{$item['name']}}">
            <input type="hidden" name="itemQuantity[]" value="{{$item['quantity']}}">
            <input type="hidden" name="itemPrice[]" value="{{$item['price']}}">
            <input type="hidden" name="itemId[]" value="{{$item['productId']}}">
            <div class="flex justify-between mb-3">
              <div class="text-gray-500 w-[65%] break-all">{{$item['name']}}</div>
              <div class="text-gray-500 w-[10%]">x{{$item['quantity']}}</div>
              <div class="text-gray-800 w-[25%] text-right">{{\Koderpedia\Labayar\Utils\Str::toCurrency($item['price'])}}</div>
            </div>
            @endforeach
          </div>
        </div>
        <div class="sepatarator h-[1px] w-full bg-gray-200 my-4"></div>
        <div class="summary px-4 mb-3 mt-2">
          <div class="flex justify-between mb-2">
            <div class="text-gray-500">Payment method</div>
            <div class="text-gray-800 font-medium" id="selectedPayment">-</div>
          </div>
          <div class="flex justify-between mb-2">
            <div class="text-gray-500">Subtotal</div>
            <div class="text-gray-800 font-medium">{{\Koderpedia\Labayar\Utils\Str::toCurrency($amount)}}</div>
          </div>
          <div class="flex justify-between mb-2">
            <div class="text-gray-500">Admin fee</div>
            <div class="text-gray-800 font-medium" id="adminFee">0</div>
          </div>
          <div class="flex justify-between mb-2">
            <div class="text-gray-500">Nett Fee</div>
            <div class="text-gray-800 font-medium" id="nettFee">{{\Koderpedia\Labayar\Utils\Str::toCurrency($amount)}}</div>
          </div>
          <input type="hidden" name="adminFee" value="0">
          <input type="hidden" name="subTotal" value="{{$amount}}">
          <input type="hidden" name="gateway" value="{{$gateway}}">
          <input type="hidden" name="expiryUnit" value="{{$expiry['unit']}}">
          <input type="hidden" name="expiryDuration" value="{{$expiry['duration']}}">
          <input type="hidden" name="orderId" value="{{$orderId}}">
          <input type="hidden" name="customerName" value="{{$customer['name']}}">
          <input type="hidden" name="customerEmail" value="{{$customer['email']}}">
          <input type="hidden" name="customerPhone" value="{{$customer['phone']}}">
          <input type="hidden" name="paymentImage">
          <input type="hidden" name="paymentName">
          <button type="submit" class="bg-gray-700 hover:bg-gray-800 rounded mt-4 px-4 py-2 text-white cursor-pointer w-full">Pay</button>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
@section('scripts')
<script>
  const methodEl = document.querySelectorAll(`input[name="paymentType"]`);
  const selectedPaymentEl = document.querySelector(`#selectedPayment`);
  const adminFeeEl = document.querySelector(`#adminFee`);
  const nettFeeEl = document.querySelector(`#nettFee`);
  const amountEl = document.querySelector(`#totalAmount`);
  let subTotal = Number(document.querySelector(`[name="subTotal"]`).value)
  methodEl.forEach(radio => {
    radio.addEventListener('change', (e) => {
      const id = e.target.getAttribute('id');
      const taxFix = Number(e.target.getAttribute('tax-fix'));
      const taxPercent = Number(e.target.getAttribute('tax-percent'));
      const image = e.target.getAttribute('image')
      const name = e.target.getAttribute('type-name')

      selectedPaymentEl.innerHTML = id

      let percentFee = 0;
      if (taxPercent > 0) {
        percentFee = subTotal * (taxPercent / 100)
      }
      const adminFee = taxFix + percentFee;
      adminFeeEl.innerHTML = "Rp" + new Intl.NumberFormat("id-ID").format(adminFee)
      nettFeeEl.innerHTML = "Rp" + new Intl.NumberFormat("id-ID").format(subTotal + adminFee)
      amountEl.innerHTML = "Rp" + new Intl.NumberFormat("id-ID").format(subTotal + adminFee)
      document.querySelector(`input[name="adminFee"]`).value = adminFee;
      document.querySelector(`input[name="paymentImage"]`).value = image;
      document.querySelector(`input[name="paymentName"]`).value = name;
    })
  })
</script>
@endsection