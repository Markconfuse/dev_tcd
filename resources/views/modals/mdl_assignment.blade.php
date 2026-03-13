<div class="row">
  <div class="modal fade" id="mdl_assignment" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        @include('layouts.components.preloader-round')
        <div class="modal-header">
          <h4 class="modal-title">Assignment Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="frmUpdateAssignment" method="post" action="{{ url('update-assignment') }}">
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Choose an engineer to be removed</h3>
                </div>
                <div class="card-body">
                  <table id="tblAssignee" class="table tblAssignment">
                    <thead hidden>
                      <th></th>
                      <th></th>
                    </thead>
                    <tbody>
                      @forelse($_ticketAssignment as $engr)
                        <tr>
                          <td><center><input type="checkbox" name="assignmentID[]" class="chkboxAssignee" value="{{ $engr->assignment_id }}"></center></td>
                          <td>
                            <img style="cursor:pointer;" class="circular-portrait" alt="{{ $engr->AccountName }}" title="{{ $engr->AccountName }}" 
                                 src="{{ $engr->GAvatar }}"/>
                            {{ $engr->AccountName }}
                          </td>
                        </tr>
                      @empty
                      <tr>
                        <td hidden></td>
                        <td colspan="2"><center>No Assignee Yet</center></td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Choose an engineer to be assigned</h3>
                </div>
                <div class="card-body">
                  <table id="tblAddAssignee" class="table tblAssignment">
                    <thead hidden>
                      <th></th>
                      <th></th>
                    </thead>
                    <tbody>
                      @foreach($_engr as $engr)
                        <tr>
                          <td><center><input type="checkbox" name="engrID[]" class="chkboxAddAssignee" value="{{ $engr->account_id }}"></center></td>
                          <td>
                            <img style="cursor:pointer;" class="circular-portrait" alt="img-{{ $engr->AccountName }}" title="img-{{ $engr->AccountName }}" 
                                 src="{{ $engr->GAvatar }}"/>
                            {{ $engr->AccountName }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Carbon Copy:</label>
                <select class="form-control select2 select2-hidden-accessible" name="ccID[]" id="ccIDAssign" style="width: 100%;" multiple>
                  @foreach($_cc as $carbonCopy)
                    <option value="{{ $carbonCopy->account_id }}">{{ $carbonCopy->AccountName }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Remarks:</label>
                <textarea class="form-control" id="assignmentRemarks" name="assignmentRemarks" rows="3" autocomplete="off"></textarea>
              </div>
            </div>
          </div>
        </div>
        @csrf
        </form>
        <div class="modal-footer justify-content-right">
          <button type="button" id="btnUpdateAssignment" class="btn btn-primary align-right">Update Assignment</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
  </div>
</div>
