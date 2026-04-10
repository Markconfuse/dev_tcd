<div class="col-md-6 col-sm-12 col-12">
  <div class="card h-95">
    <div class="card-header border-0">
      <h3 class="card-title">Monthly Ticket Trend{{ (isset($selectedYear) && $selectedYear !== 'All') ? ' (' . $selectedYear . ')' : '' }}</h3>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
      </div>
    </div>
    <div class="card-body" style="height:350px; position:relative;">
      <div style="position:relative; height:100%;">
        <canvas id="chart-monthly-trend"></canvas>
      </div>
    </div>
  </div>
</div>
