<div class="modal fade" id="mdl_custval" tabindex="-1" role="dialog"  aria-labelledby="favoritesModalLabel" style="z-index:9999!important">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" {{-- style="margin-left: 15%;" --}}>
      <div class="modal-header">
        <h4 class="modal-title">Customer Validation:</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">
        @include('layouts.components.preloader-round')
        <div class="row">
          <div class="col-md-7 mb-3">
            <div class="input-group mb-3" id="divSimpleSearch">
              <input type="text" class="form-control ui-autocomplete complete" name="cvalCustName" id="cvalCustName" autocomplete="off" placeholder="Example: Integrated Computer Systems Incorporated" spellcheck style="z-index: 0 !important">
              <div class="input-group-append" id="btnCustomerName">
                <span class="input-group-text adon"><i class="fa fa-search"></i></span>
              </div>
            </div>
          </div>

          <div class="col-md-5 mb-3">
            <div class="form-group">
              <a href="https://ice-cream.ics.com.ph/NewProspect" target="_blank" title="Create New Prospect" class="btn btn-sm btn-success pull-right" id="btnCreateProject"> Create New Prospect</a>
            </div>
          </div>

          <div class="col-md-12">
            <div class="box box-solid">
              <!-- /.box-header -->
              <div class="box-body">
                <table class="table table-bordered table-hover dt-responsive" id="tblCustomerList" style="width: 100% !important">
                  <thead>
                    <tr class="success">
                      <th class="custom_th_proj" style="width: 10px!important">#</th>
                      <th class="custom_th_proj">Customer Name</th>
                      <th class="custom_th_proj" style="width:70px !important">Sales Area</th>
                      <th class="custom_th_proj">BU - AO</th>
                      <th class="custom_th_proj" style="font-size:12px;width:90px">Actions</th>
                      <th style="font-size:12px" class="none custom_th_proj">Payment Terms</th>
                      <th style="font-size:12px" class="none custom_th_proj">Status</th>
                      <th style="font-size:12px" class="none custom_th_proj">Last Change Date</th>
                      <th style="font-size:12px" class="none custom_th_proj">Old AO</th>
                      <th style="font-size:12px" class="none custom_th_proj">DateCreated</th>
                      <th style="font-size:12px" class="none custom_th_proj">CreatedBy</th>
                    </tr>
                  </thead>
                  <tbody id="tbodyCustomerList">
                    
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>