
$(function() {

  $('#btnSend').on('click', function() {
alert('hey');
      // intelliSub();
      a = validateAOID();
      b = validateCustomerName();
      // c = validateProjectName();
      d = validateRequestType();
      e = validateBrand();
      f = validateSubject();

      if(role == 'requestor') {
        if(account_group == 'CE01') {
          g = validateEngineer();
          validation = a & b & d & e & f & g;
        } else {
          validation = a & b & d & e & f;
        }
      } else {
        g = validateEngineer();
        validation = a & b & d & e & f & g;
      }
      // if(account_group == 'IT') {
      //     validation = true;
      // }
      console.log(validation);

      if(validation) {
        alert('dont submit')
        $('#preloader-cat').removeAttr('hidden', '');
        $('#btnSend').hide();
        $('#formRequest').submit();
      } else {
        Swal.fire({
          type: 'error',
          title: 'Please fill in all required fields!',
          showConfirmButton: false,
          width: '333px',
          timer: 1300
        })
      }
  })
})

$('#projectName').on('focusout', function() {
  validateProjectName();
  intelliSub();
})

$('#subject').on('focusout', function() {
  validateSubject();
})

$('#aoID').on('select2:close', function() {
  validateAOID();
  intelliSub();
})

$('#requestTypeID').on('select2:close', function() {
  validateRequestType();
  intelliSub();
})

$('#brandID').on('select2:close', function() {
  validateBrand();
  intelliSub();
})

$('#engrID').on('select2:close', function() {
  validateEngineer();
})


function validateAOID() {

  if($('#aoID option[value]:selected').text()=='') { 
    validity = false;
    $('#aoID').addClass('is-invalid');
  } else {
    validity = true;
    $('#aoID').removeClass('is-invalid');
  }

  return validity;
}

function validateCustomerName() {

  if(!$('#customerName').val().trim()) { 
    validity = false;
    $('#customerName').addClass('is-invalid');
  } else {
    validity = true;
    $('#customerName').removeClass('is-invalid');
  }

  return validity;
}

function validateProjectName() {

  if(!$('#projectName').val().trim()) { 
    validity = false;
    $('#projectName').addClass('is-invalid');
  } else {
    validity = true;
    $('#projectName').removeClass('is-invalid');
  }

  return validity;
}

function validateRequestType() {

  if($('#requestTypeID option[value]:selected').text()=='') { 
    validity = false;
    $('#requestTypeID').addClass('is-invalid');
  } else {
    validity = true;
    $('#requestTypeID').removeClass('is-invalid');
  }

  return validity;
}

function validateBrand() {

  if($('#brandID option[value]:selected').text()=='') { 
    validity = false;
    $('#brandID').addClass('is-invalid');
  } else {
    validity = true;
    $('#brandID').removeClass('is-invalid');
  }

  return validity;
}

function validateEngineer() {

  if($('#engrID option[value]:selected').text()=='') { 
    validity = false;
    $('#engrID').addClass('is-invalid');
  } else {
    validity = true;
    $('#engrID').removeClass('is-invalid');
  }

  return validity;
}

function validateSubject() {

  if(!$('#subject').val().trim()) { 
    validity = false;
    $('#subject').addClass('is-invalid');
  } else {
    validity = true;
    $('#subject').removeClass('is-invalid');
  }

  return validity;
}

function intelliSub()
{
  var temp = '';

  var tType = $('#tTypeID').val();

  var brandName = $('#brandID').select2('data');

  brandName = jQuery.map(brandName, function(n, i){
    return ' '+n.text;
  }) + "";

  var customerName = $('#customerName').val();
  var reqType = $('#requestTypeID :selected').text();
  var projName = $('#projectName').val();

  if(!$('#customerName').val().trim()) { 
     customerName = 'Customer Name';
  }

  if($('#requestTypeID option[value]:selected').text()=='') { 
     reqType = 'Request Type';
  }
  if($('#brandID option[value]:selected').text()=='') { 
      brandName = ' Brand';
  }

  temp = customerName+' - '+reqType+':'+projName+' ('+brandName.substring(1)+')';
  if(tType == 2) {
    temp = 'NF: '+customerName+' - '+reqType+':'+projName+' ('+brandName.substring(1)+')';
  } 


  $('#subject').val(temp);
}
