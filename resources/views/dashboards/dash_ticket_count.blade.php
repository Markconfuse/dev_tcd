<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Total Ticket Count per Engineer & Category</h3>
      </div>
      <!-- /.card-header -->

      <div class="card-body" style="overflow-x:scroll">
        <div class="table-responsive">
          <table id="tblPerCount" class="table table-bordered table-hover m-0" style="font-size:15px;text-align:center">
            <thead>
            <tr>
              <th>Engineer</th>
              <th>Pending</th>
              <th>Answered</th>
              <th>Closed</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ticket_count as $tc)
              <tr>
                <td style="text-align:left">{{ $tc->AccountName }}</td>
                <td><span class="badge badge-count badge-danger"><a>{{ \Common::instance()->nullRetZero($tc->pending_count) }}</a></span></td>
                <td><span class="badge badge-count badge-info">{{ \Common::instance()->nullRetZero($tc->answered_count) }}</span></td>
                <td><span class="badge badge-count badge-closed">{{ \Common::instance()->nullRetZero($tc->closed_count) }}</span></td>
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
