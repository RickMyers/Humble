{assign var=status value=$system->cacheCheck()}

<div class="w-full h-full border-2 flex align-center bg-gray-300">
    <canvas id="cache_score_results" class="w-full h-full"></canvas>
</div>
<script>
    (function () {

        const ctx = document.getElementById('cache_score_results');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Modules', 'Controllers', 'Keys', 'Columns', 'Metadata'],
            datasets: [{
              label: 'Cache Scores [100% is only passing grade]',
              data: [{$status.modules.grade}, {$status.controllers.grade}, {$status.entities.keys.grade}, {$status.entities.cols.grade}, {$status.metadata.grade}],
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true,
                max: 100
              }
            }
          }
        });            

    })();
</script>

