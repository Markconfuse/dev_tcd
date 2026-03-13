$('#customerName').on('click', function() {

  $('#mdl_custval').modal('show');
  
})

$('#btnSearchCustomer').on('click', function() {

  $('#mdl_custval').modal('show');

})


function createTable(url) {
  $('.preloader-round').removeAttr('hidden', '');
  $('#tblCustomerList').dataTable().fnClearTable();
  $('#tblCustomerList').dataTable().fnDestroy();

  $.get(url, function(data){
    var qryRes = jQuery.parseJSON(JSON.stringify(data.data));
    var listbody = '';
    var listheader = '';
    var i = 0;
    var cType = '';
    var cNumber = '';

    $.each(qryRes, function (key,value) {

        cNumber = value.CustomerNumber;

        if (cNumber == null) { cNumber = '';}


        if (!(value.SalesGroup == null) && !(value.DistributionChannel == null)) {
              var FS = value.SalesGroup + ' ' + value.DistributionChannel;
        } else {
          var FS = 'Prospective Customer';
        }

        if (!(value.BU == null) && !(value.AO == null)) {
              var BUAO = value.BU + ' - ' + value.AO;
        } else {
          var BUAO = '';
        }

        var lastDate = value.trDate;
        if(lastDate != '0.0' && lastDate != null && lastDate) {
            lastDate = moment(value.trDate).format("MM/DD/YYYY");
        } else {
            lastDate = '';
        }

        if(!(value.CustomerType == null || value.CustomerType == '')) {
            // var cType = '';
        // } else { 
            cType = value.CustomerType;
        }

        if (value.CustomerType == "Prospective Customer" && value.SourceDB == "SAP CustomerValidation") {
            cType = "Old Prospective Customer from SAP R/3";
            cNumber = '';
        }

        var oldAO = value.OLDAO;
        if(oldAO == null) {
          oldAO = '';
        }

        var d = new Date(value.DateCreated);
        var formatted = formatDate(d);

        var btn = "<button id='"+btoa(unescape(encodeURIComponent(value.CustomerID+'|'+value.CustomerName)))+"' class='btnSelect btn btn-xs btn-success' style='margin-right:5px'>SELECT</button>";


        var _dateCreated = formatted;
        if (_dateCreated == 'January 1, 1900  12:00 am') {
          _dateCreated = "<span style='color:red;font-style:italic'>N/A</span>";
        }
        if (_dateCreated.includes('1970')) { 
          _dateCreated = "<span style='color:red;font-style:italic'>N/A</span>";
        }

        _createdBy = value.CreatedBy;
        if (_createdBy == null) { _createdBy = "<span style='color:red;font-style:italic'>N/A</span>"; }

        listbody += "<tr>" +
            '<td>'+cNumber+'</td>' + 
            '<td style=width:35%>'+value.CustomerName+"</td>" + 
            '<td>'+ FS + "</td>" + 
            '<td style=font-size:13px;>'+ BUAO + "</td>" + 
            // '<td>'+value.F9+"</td>" + 
            "<td style='text-align:center'>"+ btn +"</td>"+     
            '<td>'+value.Reason+"</td>" + 
            '<td>'+cType+"</td>" + 
            '<td>'+lastDate+"</td>" + 
            '<td>'+oldAO+"</td>" + 
            '<td>'+_dateCreated+"</td>" + 
            '<td>'+_createdBy+"</td>" + 
            "</tr>";

            i +=1;
  });

  $('#tbodyCustomerList').html(listbody);
      if ($.fn.dataTable.isDataTable('#tbodyCustomerList')){
            var table = $('#tblCustomerList').DataTable();
      }
      else {
          var table = $('#tblCustomerList').DataTable({
                "responsive": true,
                "order": [ 1, "asc" ],
            });   
        }

    $('.preloader-round').attr('hidden', 'hidden');
  });

}



