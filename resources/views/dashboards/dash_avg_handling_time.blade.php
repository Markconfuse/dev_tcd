<div class="col-md-6 col-sm-12 col-12">
  <div class="card h-95">
    <div class="card-header border-0">
      <h3 class="card-title">Avg. Handling Time per Engineer{{ (isset($selectedYear) && $selectedYear !== 'All') ? ' (' . $selectedYear . ')' : '' }} <small class="text-muted ml-1">(in hours)</small></h3>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
      </div>
    </div>
    <div class="card-body" style="height:350px; position:relative;">
      <div id="chart-avg-handling-time-wrapper" style="height:100%; overflow-y:auto; overflow-x:hidden;">
      <div id="chart-avg-handling-time-container" style="position:relative; height:350px; min-height:350px;">
        <canvas id="chart-avg-handling-time"></canvas>
      </div>
      </div>
    </div>
  </div>
</div>
