<?php
function Cambiargrafica($fechas, $costos, $horas, $bool) {
  if ($bool) {
    Barras($fechas, $costos, $horas);
  } else {
    Pastel($fechas, $costos, $horas);
  }
}

function Barras($fechas, $costos, $horas) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
  <div class="container text-center">
    <h1>Gráfico de Consumo - Barras</h1>
    <canvas id="graficoBarras" width="250" height="150"></canvas>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    var fechas = <?php echo $fechas; ?>;
    var costos = <?php echo $costos; ?>;
    var horas = <?php echo $horas; ?>;

    var ctx = document.getElementById('graficoBarras').getContext('2d');
    var graficoBarras = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: fechas,
        datasets: [{
          label: 'Horas',
          data: horas,
          backgroundColor: 'rgba(255, 99, 132, 0.5)',
          borderColor: 'rgba(255, 99, 132, 1)',
          borderWidth: 1,
          yAxisID: 'y1',
          type: 'line',
          fill: false
        }, {
          label: 'Costo Total ($)',
          data: costos,
          backgroundColor: 'rgba(54, 162, 235, 0.5)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1,
          yAxisID: 'y2'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y1: { beginAtZero: true, position: 'left' },
          y2: { beginAtZero: true, position: 'right', ticks: { callback: function(value) { return "$" + value.toFixed(2); }} }
        },
        plugins: { legend: { position: 'top' } }
      }
    });
  </script>

</body>
</html>
<?php
}

function Pastel($fechas, $costos, $horas) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .grafico-container {
      width: 500px;
      height: 300px;
    }
  </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
  <div class="container text-center grafico-container">
    <h1>Gráfico de Consumo - Pastel</h1>
    <canvas id="graficoPastel"></canvas>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    var fechas = <?php echo $fechas; ?>;
    var costos = <?php echo $costos; ?>;

    var ctx = document.getElementById('graficoPastel').getContext('2d');
    var graficoPastel = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: fechas,
        datasets: [{
          label: 'Consumo',
          data: costos,
          backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)'],
          borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          tooltip: { callbacks: { label: function(tooltipItem) { return "$" + tooltipItem.raw.toFixed(2); }} }
        }
      }
    });
  </script>
</body>
</html>
<?php
}
?>
