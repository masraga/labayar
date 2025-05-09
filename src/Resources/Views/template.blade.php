<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="/labayar-assets/css/labayar.css" rel="stylesheet">
  <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>Labayar | Seamless pay</title>
</head>

<body class="bg-slate-100">
  <div class="w-[90%] m-auto py-4">
    <div class="text-black text-3xl">@yield('title')</div>
    @yield('content')
  </div>
</body>
@yield('scripts')

</html>