<div class="row">
  <div class="modal fade" id="mdl_addBrand" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New Brand</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">

          <button title="Add Row" type="button" id="btnAddRow" class="btn btn-olive btn-sm mb-3">
            <i class="fas fa-plus"></i>  Add Row
          </button>

          <button title="Remove Row" type="button" id="btnRemRow" class="btn btn-danger btn-sm mb-3">
            <i class="fas fa-ban"></i>  Remove Row
          </button>

          <form id="frmAddBrand" method="post" action="{{ url('add-brand') }}">
          <table id="tblAddBrand" class="table table-bordered">
            <thead>
              <tr>
                <th></th>
                <th>Brand Name</th>
                <th>Brand Type</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><center><input class="chkBrand" type="checkbox"></center></td>
                <td><input type="text" name="brandName[]" class="form-control bName" autocomplete="off"></td>
                <td>
                  <select class="form-control" name="brandTypeID[]" id="brandTypeID" style="width: 100%;">
                    @foreach($_brandType as $_type)
                      <option value="{{ $_type->transaction_type_id }}">{{ $_type->transaction_description }}</option>
                    @endforeach
                  </select>
                </td>
              </tr>

            </tbody>
          </table>
          <small>Remarks: Brand Name will be automatically <b>capitalized</b>.</small><br>
          <small>E.g: <i>sap bussiness one = SAB BUSINESS ONE </i></small>
          @csrf
          </form>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" id="btnSaveBrand" class="btn btn-primary align-right"><i class="fas fa-save"></i> Save</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
  </div>
</div>
