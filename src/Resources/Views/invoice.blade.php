@extends("labayar::template")

@section("title") Order @endsection
@section("content")
  <div class="grid grid-cols-3 gap-4 pt-4">
    @foreach($orders as $order)
      @include("labayar::components.invoice-card")
    @endforeach
  </div>
@endsection