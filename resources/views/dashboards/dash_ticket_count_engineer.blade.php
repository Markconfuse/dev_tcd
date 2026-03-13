<div class="row">
  <div class="col-12">
    <div class="card"> 
      <div class="card-header border-transparent">
        <h3 class="card-title">Ticket Count per Engineer & Category</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
        </div>

      </div>
      <!-- /.card-header -->

      <div class="card-body" style="overflow-x:scroll">
        <div class="table-responsive">
          <table id="tblPerCountEngr" class="table table-bordered table-hover m-0" style="font-size:15px;text-align:center">
            <thead>
              <tr>
                <th>AO</th>
                <th>Pending</th>
                <th>Answered</th>
                <th>Closed</th>
              </tr>
            </thead>
            <tbody>
            @foreach($ticket_count_engineer as $tce)
              <tr>
                <td style="text-align:left">{{ $tce->AccountName }}</td>
                <td><span class="badge badge-count badge-danger spnPerEngineer" data-id="{{ $tce->account_id.'|1' }}" style="cursor:pointer;"><a>{{ \Common::instance()->nullRetZero($tce->pending_count) }}</a></span></td>
                <td><span class="badge badge-count badge-closed spnPerEngineer" data-id="{{ $tce->account_id.'|3' }}" style="cursor:pointer;">{{ \Common::instance()->nullRetZero($tce->answered_count) }}</span></td>
                <td><span class="badge badge-count badge-danger spnPerEngineer" data-id="{{ $tce->account_id.'|4' }}" style="cursor:pointer;">{{ \Common::instance()->nullRetZero($tce->closed_count) }}</span></td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <span><center><b><i>Click the data of the table to view more details.</i></b></center></span>
            </tfoot>
          </table>
        </div>
      </div>
      <!-- /.card-body -->
    </div>
  </div>
</div>
