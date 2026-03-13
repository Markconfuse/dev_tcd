<div class="row">
  <div class="modal fade" id="mdl_dash_ticket" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        @include('layouts.components.preloader-round')
        <div class="modal-header">
          <h4 class="modal-title"><span id="spnTitle"></span></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body" style="overflow-y:scroll;font-size: 15px;">
          <table id="tbl_mdl_tickets" class="table table-bordered table-hover">
            <thead>
              <tr style="font-size:14px !important">
              <th style="min-width:1px"></th>
              <th style="width:80px;">Reference No</th>
              <th style="width:410px">Subject</th>
              <th>Requestor</th>
              <th hidden>content,reply,ao_name,requestor,OwnerName</th>
              <th style="width:98px;">Assignee</th>
              <th>Replies</th>
              <th>Last Updated</th>
              <th class="none">Date Created</th>
              <th class="none">Customer Name</th>
              <th class="none">Request Type</th>
              <th class="none">Project Name</th>
              <th class="none">Status</th>
              <th class="none">Last Transaction</th>
            </tr>
            </thead>
            <tbody style="font-size:15px">

            </tbody>
          </table>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
  </div>
</div>
