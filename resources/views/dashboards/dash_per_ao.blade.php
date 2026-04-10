<div class="col-md-12 col-sm-6 col-12">
  <div class="card">
    <div class="card-header border-0">
      <h3 class="card-title">Tickets per AO{{ (isset($selectedYear) && $selectedYear !== 'All') ? ' (' . $selectedYear . ')' : '' }}</h3>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
      </div>
    </div>
    <div class="card-body">
      {{-- <span><center><b><i>Click the data inside of the chart to view more details</i></b></center></span><br> --}}
      @if(Session('userData')->role_name == 'requestor')
        <div id="chart-box">
          <canvas id="chart-per-ao" width="600" height="250"></canvas>
        </div>
      @else 
        <div id="chart-box" style="overflow-y: scroll;max-height:500px">
        <canvas id="chart-per-ao" width="600" height="800"></canvas>
      @endif 
    </div>
  </div>
  <!-- /.info-box -->
</div>