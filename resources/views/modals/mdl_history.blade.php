<div class="row">
  <div class="modal fade" id="mdl_history" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        @include('layouts.components.preloader-round')
        <div class="modal-header">
          <h4 class="modal-title">Transaction History: Ticket#{{ sprintf('%04d', $_ticketDetail[0]->ticket_id) }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body" style="overflow-y:scroll;font-size: 15px;">
          <table id="tblHistory" class="table">
            <thead>
              <tr>
                <th>Activity</th>
                <th>Date Created</th>
              </tr>
            </thead>
            <tbody id="tbodyHistory">
              
            </tbody>
          </table>
          @csrf
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
  </div>
</div>
