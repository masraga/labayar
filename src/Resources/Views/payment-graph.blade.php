@extends("labayar::template")

@section("title", "Sales")
@section("content")
<div class="bg-white p-4 mt-4 rounded shadow">
  <canvas id="myLineChart" width="400" height="200"></canvas>
</div>
@endsection
@section('scripts')
<script>
  const graph = JSON.parse('{!! $graph !!}');
  const ctx = document.getElementById('myLineChart').getContext('2d');

  const myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: graph.labels,
      datasets: [{
        label: 'Paid order',
        data: graph["paid"],
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        tension: 0.3, // membuat garis agak melengkung
        fill: false
      }, {
        label: 'Unpaid order',
        data: graph["unpaid"],
        borderColor: 'rgb(197, 34, 56)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        tension: 0.3, // membuat garis agak melengkung
        fill: false
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: false
        }
      }
    }
  });
</script>
@endsection