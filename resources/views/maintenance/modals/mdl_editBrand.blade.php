<div class="row">
  <div class="modal fade" id="mdl_editBrand" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Brand: <span id="spnBrand"></span></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="frmEditBrand" method="post" action="{{ url('edit-brand') }}">

            <div class="col-md-12">
              <div class="form-group">
                <label>Brand Name:</label>
                <input type="text" name="editBrandName" id="editBrandName" class="form-control" autocomplete="off">
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Brand Type:</label>
                <select class="form-control" name="editBrandTypeID" id="editBrandTypeID" style="width: 100%;">
                  @foreach($_brandType as $_type)
                    <option value="{{ $_type->transaction_type_id }}">{{ $_type->transaction_description }}</option>
                  @endforeach
                </select>
              </div>
              <small>Remarks: Brand Name will be automatically <b>capitalized</b>.</small><br>
              <small>E.g: <i>sap bussiness one = SAB BUSINESS ONE </i></small>
            </div>
          @csrf
          <input type="hidden" name="editBrandID" id="editBrandID">
          </form>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" id="btnUpdateBrand" class="btn btn-primary align-right"><i class="fas fa-save"></i> Update</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
  </div>
</div>
