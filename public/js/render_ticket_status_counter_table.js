

function renderDtable(url, tbl_name) {
    $('#' + tbl_name).dataTable().fnClearTable();
    $('#' + tbl_name).dataTable().fnDestroy();

    if ($.fn.dataTable.isDataTable('#' + tbl_name)) {
        return $('#' + tbl_name).DataTable();
    } else {
        return $('#' + tbl_name).DataTable({
            processing: true,
            serverSide: true,
            ajax: url,
            deferRender: true,
            autoWidth: false,
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            return 'More details for Ticket#:' + row.data().last_ticket_id;
                        }
                    }),
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.hidden ?
                                '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                '<td>' + col.title + ':' + '</td> ' +
                                '<td>' + col.data + '</td>' +
                                '</tr>' :
                                '';
                        }).join('');

                        return data ? $('<table class="table">').append(data) : false;

                    },
                },
            },
            columns: [
                {
                    data: 'AccountName',
                    title: 'Engineer',
                    render: function (data, type, row) {
                        const avatar = row.GAvatar || '/default-avatar.png';
                        return `
                            <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
                                <img src="${avatar}" width="64" height="64" style="border-radius:50%; object-fit:cover;">
                                <span style="text-align:center; font-weight:600;">${data}</span>
                            </div>
                        `;
                    },
                    width: '120px', // optional, adjust as needed
                    className: 'text-center'
                },
                {
                    data: 'ticket_count',
                    title: 'Total Tickets',
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `<span class="badge badge-primary tRBtn" style="font-size:16px; padding:6px 12px;" data-type="Tickets" data-owner="${row.account_id}">${data}</span>`;
                    }
                },
                {
                    data: 'not_yet_read',
                    title: 'Not Yet Read',
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `<span type="button" class="badge badge-warning tRBtn" style="font-size:16px; padding:6px 12px;" data-type="Not Yet Read" data-owner="${row.account_id}">${data}</span>`;
                    }
                },
                {
                    data: 'not_yet_answered',
                    title: 'Not Yet Answered',
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `<span class="badge badge-info tRBtn" style="font-size:16px; padding:6px 12px;" data-type="Not Yet Answered" data-owner="${row.account_id}">${data}</span>`;
                    }
                },
                { data: 'last_ticket_date_assigned', title: 'Last Ticket Date Assigned', className: 'text-center', render: time },

            ],
            
            rowCallback: function (row, data) {

    // Assign tdClick to specific TD columns (same as your original)
    $('td', row).eq(1).addClass('tdClick');
    $('td', row).eq(2).addClass('tdClick');
    $('td', row).eq(3).addClass('tdClick');
    $('td', row).eq(4).addClass('tdClick');
    $('td', row).eq(5).addClass('tdClick');
    $('td', row).eq(6).addClass('tdClick');
    $('td', row).eq(7).addClass('tdClick');
    $('td', row).eq(8).addClass('tdClick');

    // 🔥 NEW — turn every tdClick into a modal trigger
    $(row).find('td.tdClick').each(function () {
        $(this).addClass("open-modal");
        $(this).attr("data-id", data.last_ticket_id); // ticket ID for modal
        $(this).css("cursor", "pointer");
    });

    // Keep your existing row attributes
    $(row).addClass('pointer');
    $(row).attr('data-id', 'tr_' + data.last_ticket_id);
    $(row).attr('title', 'Click to view list of tickets');
},

            initComplete: function (settings, json) {
                console.log('Full AJAX response:', json); // <-- logs all server data
                if (json.data && json.data.length > 0) {
                    // console.log('First row last_ticket_date_assigned:', json.data[0].last_ticket_date_assigned);
                    // console.log('First row last_ticket_date_assigned:', json.data[0].last_ticket_last_updated);
                }
                var api = this.api();
                var searchWait = 0;
                var searchWaitInterval;
                // Grab the datatables input box and alter how it is bound to events
                $(".dataTables_filter input")
                    .unbind() // Unbind previous default bindings
                    .bind("input", function (e) { // Bind our desired behavior
                        var item = $(this);
                        searchWait = 0;
                        if (!searchWaitInterval) searchWaitInterval = setInterval(function () {
                            searchTerm = $(item).val();
                            // if(searchTerm.length >= 3 || e.keyCode == 13) {
                            clearInterval(searchWaitInterval);
                            searchWaitInterval = '';
                            // Call the API search function
                            api.search(searchTerm).draw();
                            searchWait = 0;
                            // }
                            searchWait++;
                        }, 1000);
                        return;
                    });
            },
            language: {
                processing: '<i class="fas fa-circle-notch fa-spin dT-spin"></i>',
                emptyTable: " No data available in the table"
            },
            lengthMenu: [[10, 20, 100, 500], [10, 20, 100, 500]],
            pageLength: 10,
            order: [[1, "desc"]]
        });
    }
}

$(document).on("click", ".tRBtn", function(e) {
    e.stopPropagation();

    let ownerId = $(this).data("owner");
    let tType = $(this).data("type")

    // Show modal
    $('#globalModal').modal('show');

    $('.mText').text(tType);

    // Show bubble loader
    $("#modalContent").html(`
        <div class="modal-bubbles">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <p class="text-center mt-3" style="font-size:1.2rem;">Loading tickets… please wait</p>
    `);

    // AJAX request
    $.ajax({
        url: `/tickets/${tType}/${ownerId}`,
        type: "GET",
        success: function(res) {
            $("#modalContent").html(res.html);

            // Initialize DataTables
            $('#modalTicketsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 10,
                lengthMenu: [10, 20, 50, 100],
                autoWidth: false,
                responsive: true,
                order: [[0, 'desc']]
            });
        },
        error: function() {
            $("#modalContent").html('<p class="text-danger">Failed to load tickets.</p>');
        }
    });
});





function time(dateCreated) {
    var checkDay = (new Date().getTime() - Date.parse(dateCreated)) / 86400000;

    if (checkDay < 1) {
        return $.timeago(dateCreated);
    } else {
        var d = new Date(dateCreated);
        return formatDate(d);
    }
}


