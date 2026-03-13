<div class="row">
  <div class="col-12">
    <div class="card"> 
      <div class="card-header border-transparent">
        <h3 class="card-title">Ticket Count per AO & Category</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
        </div>

      </div>
      <!-- /.card-header -->

      <div class="card-body" style="overflow-x:scroll">
        <div class="table-responsive">
          <table id="tblPerCountAO" class="table table-bordered table-hover m-0" style="font-size:15px;text-align:center">
            <thead>
              <tr>
                <th>AO</th>
                <th>BU</th>
                <th>Unassigned</th>
                <th>Assigned</th>
                <th>Answered</th>
                <th>Closed</th>
              </tr>
            </thead>
            <tbody>
            @foreach($ticket_count_ao as $tca)
              <tr>
                <td style="text-align:left">{{ $tca->AccountName }}</td>
                <td style="text-align:center;">{{ $tca->AccountGroup }}</td>
                <td><span class="badge badge-count badge-danger"><a>{{ \Common::instance()->nullRetZero($tca->unassigned_count) }}</a></span></td>
                <td><span class="badge badge-count badge-info">{{ \Common::instance()->nullRetZero($tca->assigned_count) }}</span></td>
                <td><span class="badge badge-count badge-closed">{{ \Common::instance()->nullRetZero($tca->answered_count) }}</span></td>
                <td><span class="badge badge-count badge-danger">{{ \Common::instance()->nullRetZero($tca->closed_count) }}</span></td>
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
