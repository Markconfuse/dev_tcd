

function renderDtable(url, tbl_name) {
    $('#'+tbl_name).dataTable().fnClearTable();
    $('#'+tbl_name).dataTable().fnDestroy();

    if ($.fn.dataTable.isDataTable('#'+tbl_name)) {
      var table = $('#'+tbl_name).DataTable();
    } else {
      var table = $('#'+tbl_name).DataTable( {
          processing: true,
          serverSide: true,
          ajax: {
              url: url,
              error: function (jqXHR, textStatus, errorThrown) {
                 // renderDtable(url, tbl_name);
              }
          },
          deferRender: true,
          autoWidth: false,
          "columnDefs": [
            {
                "targets": [ 4 ],
                "visible": false,
                "searchable": true
            },
          ],
          responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function ( row ) {
                return 'More details for Ticket#:'+row.data().ticket_id;
              }
            }),
            renderer: function ( api, rowIdx, columns ) {
              var data = $.map( columns, function ( col, i ) {
                  return col.hidden ?
                      '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                          '<td>'+col.title+':'+'</td> '+
                          '<td>'+col.data+'</td>'+
                      '</tr>' :
                      '';
              } ).join('');

              return data ? $('<table class="table">').append( data ) : false;

              },
            },
          },
          columns: [
              { data: 'status_id', render: makeBlank },
              { data: 'ticket_id',  "render": function ( data, type, row ) { return pad(data,4); }},
              { data: 'subject' },
              { data: 'requestor_name', name:'esrid.AccountName', "render": function ( data, type, row ) { return limitReq(row) }},
              { data: 'ticket_content' },
              { data: 'OwnerName', name: 'tmp.OwnerName', render: getImg},
              { data: 'reply_ctr', name: 'tmp.reply_ctr', render: changeFont},
              { data: 'last_updated', render: time},
              { data: 'date_created', render: time},
              { data: 'customer_name'},
              { data: 'request_type', name: 'lrt.request_type'},
              { data: 'project_name'},
              { data: 'status_description', name:'ls.status_description'},
              { data: 'last_transaction', name:'tmp.last_transaction'}
          ],
          rowCallback: function (row, data) {
              if(data.status_id == 1) {
                $('td', row).eq(0).css('background-color', '#ffc107');
              } else if(data.status_id == 2) { 
                $('td', row).eq(0).css('background-color', '#28a745');
              } else if(data.status_id == 3) { 
                $('td', row).eq(0).css('background-color', '#17a2b8');
              } else if(data.status_id == 4) { 
                $('td', row).eq(0).css('background-color', '#001f3f');
              } 

              $('td', row).eq(1).addClass('tdClick');
              $('td', row).eq(2).addClass('tdClick');
              $('td', row).eq(3).addClass('tdClick');
              $('td', row).eq(4).addClass('tdClick');
              $('td', row).eq(5).addClass('tdClick');
              $('td', row).eq(6).addClass('tdClick');


              $(row).addClass('pointer');
              $(row).attr('data-id', 'tr_'+data.ticket_id);
              $(row).attr('title', 'Click to view request.');
          },
          initComplete: function() {
              var api = this.api();
              var searchWait = 0;
              var searchWaitInterval;
              // Grab the datatables input box and alter how it is bound to events
              $(".dataTables_filter input")
              .unbind() // Unbind previous default bindings
              .bind("input", function(e) { // Bind our desired behavior
                  var item = $(this);
                  searchWait = 0;
                  if(!searchWaitInterval) searchWaitInterval = setInterval(function(){
                      searchTerm = $(item).val();
                      // if(searchTerm.length >= 3 || e.keyCode == 13) {
                          clearInterval(searchWaitInterval);
                          searchWaitInterval = '';
                          // Call the API search function
                          api.search(searchTerm).draw();
                          searchWait = 0;
                      // }
                      searchWait++;
                  },1000);                       
                  return;
              });
          },
          language: {
            processing: '<i class="fas fa-circle-notch fa-spin dT-spin"></i>',
            emptyTable: " No data available in the table" 
          },
          lengthMenu: [[10, 20, 100, 500], [10, 20, 100, 500]],
          pageLength: 10,
          order: [[ 0, "desc" ]]
        });

      $.fn.dataTable.ext.errMode = 'throw';

      return table;
  }
}


function changeFont(data, type, full, meta) {
  return "<center><span style='font-size:25px'>"+full.reply_ctr+"</span></center>";
}

function makeBlank(status_id) {
  if(status_id == 1) {
    return "<td style='background-color:#ffc107'></td>";
  } else if(status_id == 2) {
    return "<td style='background-color:#28a745'></td>";
  } else if (status_id == 3) {
    return "<td style='background-color:#17a2b8'></td>";
  } else if (status_id == 4) {
    return "<td style='background-color:#129c7c'></td>";
  }

}

function limitReq(row) {
    if(row.requestor_nickname === null || row.requestor_nickname == '') {
      if(row.requestor_name.length >= 10) {
        return row.requestor_name.substring(0,10)+'...';
      } else {
        return row.requestor_name.substring(0,10);
      }
    } else {
      if(row.requestor_id == row.account_owner_id) {
        return '<center>'+row.AccountGroup+' - '+row.requestor_nickname+'</center>';
      } else {
        return '<center>'+row.AccountGroup+' - '+row.requestor_nickname+' for '+row.ao_nickname+'</center>';
      }
    }
}

function concat_details(row) {
  var concat_details = row.ticket_content+','+row.ticket_reply;
  concat_details += row.ao_name+','+row.requestor_name+','+row.OwnerName;

  return concat_details;
}

function pad (str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : '<center>' + str + '</center>';
}

function time(dateCreated) {
    var checkDay = (new Date().getTime() - Date.parse(dateCreated)) / 86400000;

    if (checkDay < 1) {
      return $.timeago(dateCreated);
    } else {
      var d = new Date(dateCreated);
      return formatDate(d);
    }
}

function getImg(data, type, full, meta) {
  if(data !== null) {
    return $.map(full.OwnerName.split(';'), function( val, i ) {
      temp = val.split(',');
      console.log(temp)

      if(temp[2] == 0 && temp[3] == 0) {
          border = 'img-bordered-unanswered-sm';
      } else if (temp[2] == 0 && temp[3] == 1) {
          border =  'img-bordered-read-sm';
      } else if (temp[2] == 1 && temp[3] == 1) {
          border =  'img-bordered-answered-sm';
      }
      
      return "<li class='list-inline-item' style='padding-top:3px;'><img title='"+temp[0]+"' class='circular-portrait "+border+"' src='"+temp[1]+"'></li>";
    }).join('');
  } else {
    return 'No Assignee yet';
  }
}
