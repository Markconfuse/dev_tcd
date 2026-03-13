$('#btnAssign').on('click', function() {
  summernote('#assignmentRemarks');
  $('#mdl_assignment').modal('show');
})

$('#tblAssignee').on('change', '.chkboxAssignee', function(event) {
  if($(this).is(':checked')) {
    $(this).closest('tr').css('background-color','#eeeeee');
    $(this).addClass('chkAssignee');
  } else {
    $(this).closest('tr').css('background-color','#ffffff');
    $(this).removeClass('chkAssignee');
  }
})

$('#tblAddAssignee').on('change', '.chkboxAddAssignee', function(event) {
  if($(this).is(':checked')) {
    $(this).closest('tr').css('background-color','#d9f5d0');
    $(this).addClass('chkAddAssignee');
  } else {
    $(this).closest('tr').css('background-color','#ffffff');
    $(this).removeClass('chkAddAssignee');
  }
})

$('#btnUpdateAssignment').on('click', function() {
    i = 0;
    
    $('.chkAssignee').map(function() {
        i += 1;
    }).get();

    $('.chkAddAssignee').map(function() {
        i += 1;
    }).get();

    if(i == 0) {
      Swal.fire({
        type: 'error',
        title: 'Please make at least one transaction!',
        showConfirmButton: false,
        width: '370px',
        timer: 1700
      })
    } else {    
      $('.preloader-round').removeAttr('hidden', '');
      $('#frmUpdateAssignment').submit();
    }
})

//Reset
$('#tblResetEngr').on('change', '.checkboxReset', function(event) {
  if($(this).is(':checked')) {
    $(this).closest('tr').css('background-color','#d6d6d6');
    $(this).addClass('chkReset');
  } else {
    $(this).closest('tr').css('background-color','#ffffff');
    $(this).removeClass('chkReset');
  }
})

$('#btnFinalSendReset').on('click', function() {

  var i = 0;
  var engrID = []; 

  $('.chkReset').map(function() {
      i += 1;
      engrID.push($(this).val());
  }).get();

  if (i <= 0) {
    Swal.fire({
      type: 'error',
      title: 'Please choose at least one!',
      showConfirmButton: false,
      width: '333px',
      timer: 1500
    })
  } else {
    $('#engrResetList').val(engrID);
    $('#replyType').val('3');
    $('#mdl_reset_engr').modal('hide');
    $('#preloader-cat').removeAttr('hidden', '');
    $('#formReply').submit();
  }

})