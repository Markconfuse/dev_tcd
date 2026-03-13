
@if(!empty($engineer_per_day[0]))

@php
  $_header = array_keys((array) $engineer_per_day[0]);
  $_headerCnt = count($_header);
@endphp
<div class="row">
  <div class="col-12">
    <div class="card"> 
      <div class="card-header border-transparent">
        <h3 class="card-title">Ticket Count per Engineer & Date</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
        </div>

      </div>
      <!-- /.card-header -->

      <div class="card-body">
        <div class="table-responsive">
          <table id="tblPerCountEngineerDate" class="table table-bordered table-hover m-0" style="font-size:15px;text-align:center;width:100%">
            <thead>
              <tr>
                @foreach($_header as $header)
                  <td class="custom_th_proj">{{ $header }}</td>
                @endforeach
              </tr>
            </thead>
            <tbody>
            @foreach($engineer_per_day as $key => $bpdVal)
              <tr>
                @foreach($_header as $header)
                  <td>{{ is_null($bpdVal->$header)?0:$bpdVal->$header }}</td>
                @endforeach
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <!-- /.card-body -->
    </div>
  </div>
</div>

@endif