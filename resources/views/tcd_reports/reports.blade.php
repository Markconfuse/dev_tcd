@extends('layouts.page')

@section('title', 'TCD Reports')

@section('content_header', 'TCD Reports')

@section('css')
@stop

@section('content')

<div class="container-fluid">
    <div class="row charts-docs">
        <div class="col-xl-12 docs-table">
<div class="card mb-4">         
<div class="card-header subscription-h">
    <i class="fa fa-database"></i>
        TCD
    </div>
<div class="card-body docs-body">
    <!-- <div class="row">
        <label>SELECT MONTH</label>
        <div class="col-md-3">
            <input id="cAll" type="checkbox" name="all" value="All" style="width: 2em;" />
        </div>
    </div> -->

<div class="row">
    <div class="col-md-4 sel">
        <label>Select Month</label>
            <form method="GET" action="filter_reports">
            <select class="form-control select2 select2-hidden-accessible mSelect" name="fMonth" id="months" style="width: 100%;" multiple required>
            <option value="ALL">ALL</option>
            @foreach($months as $key => $value)
                <option value="{{ $value }}">{{ $value }}</option>
            @endforeach
        </select>
        <span id="month-text"></span>
    </div>
    <div class="col-md-4">
        <label>Select Year</label>
        <select class="form-control select2 select2-hidden-accessible ySelect" name="fYear" id="years" style="width: 100%;" multiple>
            @foreach($years as $key => $value)
                <option value="{{ $value }}">{{ $value }}</option>
            @endforeach
        </select>
        <span id="year-text"></span>
    </div>
    <div class="col-md-4">
        <label>Status</label>
            <select class="form-control statusSelect" name="ticket_status" id="t_status" style="width: 100%;">
                <option value="20">ALL</option>
                <option value="1">UNASSIGNED</option>
                <option value="2">ASSIGNED</option>
                <option value="3">ANSWERED</option>
                <option value="7">ESCALATED</option>
				{{-- <option value="9">DECLINED</option> --}}
                <option value="4">CLOSED</option>
            </select>
        <span id="status-text"></span>
    </div>
    <div class="col-md-2">
        <button id="update-btn" type="button" style="margin-top: 32px;" 
                class="btn btn-primary btn-sm filterBtn" onclick="submitMonthYear(event)">
            <i class="fa fa-search"></i> Search</button>
        </div>    
    </div>  
</form>
<br />
    <table id="tcd-reports" class="table-docs table-bordered" cellspacing="0" style="width: 100%">
        <thead>
            <tr class="eN">
                <th class="s-th">REF. No.</th>
                <th class="s-th">REQ. TYPE</th>
                <th class="s-th">PROJECT NAME</th>
                <th class="s-th">COMPANY</th>
                <th class="s-th">AO</th>
                <th class="s-th">BU</th>
                <th class="s-th">ENGR.</th>
                <th class="s-th">ESCALATION STATUS</th>
				<th class="s-th">DESCRIPTION</th>
				<th class="s-th">DATE ESCALATED</th>
                <th class="s-th">TICKET STATUS</th>
                <th class="s-th">DATE REQUESTED</th>
                <th class="s-th">DATE ASSIGNED</th>
                <th class="s-th">ACKNOWLEDGED BY</th>
                <th class="s-th">1ST ENGR. REPLY D</th>
                <th class="s-th">REQ. LAST REPLY D</th>
                <th class="s-th">ENGR. LAST REPLY D</th>
                <th class="s-th">DATE CLOSED</th>
            </tr>
        </thead>
    </table>
                        <!-- /.col -->
                    </div>
                <!-- /.row -->
            </div>
        </div>
    <div class="col-md-5 xP">
</div>

<style>
    
    .btn-group-xs > .btn, .btn-xs {
        padding: .25rem .4rem;
        font-size: .875rem;
        line-height: .5;
        border-radius: .2rem;
    }
    .s-th {
        font-size: 14px;
        font-family: "Montserrat", sans-serif;
        text-transform: uppercase;
        background-color: #D2D7D3;
        height: 2.3em;
        white-space: nowrap; 
        /* background-color: #E83E8C; */
    }
    .center {
        display: block;
        margin-left: auto;
        margin: auto;
        width: 100%;
    }  
    .tcd-h {
        background-color: #D2D7D3;
    }
    td { 
        /* white-space: nowrap;  */
        word-break:break-all;
    }
</style>

@stop

@section('js')

<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
<script type="text/javascript">

var dM = '';
var dY = '';
var tStatus = '';
function submitMonthYear(){
    dM = $('#months').val();
    dY = $('#years').val();
    tStatus = $('#t_status').val();

    $('#tcd-reports').show();

        if (!dM){
            $('#month-text').text('Please fill months').css('color', '#CF000F');
            $('#tcd-reports').hide();
        }

        if (!dY){
            $('#year-text').text('Please fill years').css('color', '#CF000F');
            $('#tcd-reports').hide();

        }

        $('.mSelect').bind('click', function(){
            $('#month-text').text('');
          
        });

    if(dM && dY){
        loadTcdReportsDataTable(dM, dY, tStatus)
        // $('.xP').html('<a href="/export_tcd/'+ dM +'/'+ dY +'"><button id="update-btn" type="button" style="margin-top: 32px;" class="btn btn-success btn-sm" ><i class="fa fa-paper-plane"></i> Export</button>');
    }
}
 
    function loadTcdReportsDataTable(dM, dY, tStatus){
    $('#month-text').hide();
    $('#year-text').hide();
    $('#status-text').hide();

    $('.spinner').hide();
    $('.docs-body').show();
    $('#tcd-reports').DataTable().destroy();

    table = $('#tcd-reports').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<button type="button" class="btn btn-success btn-sm"> <i class="fa fa-table"></i> XLS EXPORT </button>',
                titleAttr: 'Export',
                title: function() {
                    // Create a dynamic filename
                    let months = dM.join('-');
                    let years = dY.join('-');
                    return `TCD_Report_${months}_${years}`;
                }
            }
        ],
        "word-break": 'break-all',
        "searching": true,
        "processing": false,
        "serverSide": false,
        "paginate": true,
        "responsive": true,
        "paging": true,
        "scrollX": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "lengthChange": true,
        "pageLength": 10,
        "ajax": {
            'url': '{{ url("/tcd_reports_datatable") }}',
            "data" : {
                "dMonths": dM,
                "dYears": dY,
                "tStatus": tStatus
            }
        },
        columnDefs: [
            { width: 3, targets: 0 },
            { width: 3, targets: 1 },
            { width: 180, targets: 2 },
            { width: 180, targets: 3 },
            { width: 180, targets: 4 },
            { width: 180, targets: 5 },
            { width: 210, targets: 6 },
            { width: 210, targets: 7 },
            { width: 180, targets: 8 },
            { width: 180, targets: 9 }
        ],
        columns:[
            { data: 'reference_number', name: 'reference_number' },
            { data: 'request_type', name: 'request_type' },
            { data: 'project_name', name: 'project_name' },
            { data: 'company_name', name: 'company_name' },
            { data: 'ao', name: 'ao' },
            { data: 'bu', name: 'bu' },
            { data: 'engineers', name: 'engineers' },
            { data: 'is_approved', name: 'is_approved' },
            { data: 'escalated_reply', name: 'escalated_reply' },
            { data: 'escalation_date', name: 'escalation_date' },
            { data: 'status', name: 'status' },
            { data: 'date_requested', name: 'date_requested' },
            { data: 'date_assigned', name: 'date_assigned' },
            { data: 'approved_by', name: 'approved_by' },
            { data: 'first_reply_engr', name: 'first_reply_engr' },
            { data: 'last_reply_req', name: 'last_reply_req' },
            { data: 'last_reply_engr', name: 'last_reply_engr' },
            { data: 'last_updated', name: 'last_updated' }
        ]
    });
}

</script>

<script type="text/javascript">
     
$( document ).ready(function() {
    // $('.eM').hide();
    $('.select2').select2({
        theme: 'bootstrap4',
    });

    $('.sAll').val(this.checked);

    $('.sAll').change(function() {
    if(this.checked) {
        var returnVal = confirm("Are you sure?");
        $(this).prop("checked", returnVal);
    }
    $('.sAll').val(this.checked); 

});

    $('.mSelect').on('select2:select', function(e) {
        
    var data = e.params.data;
    var dAll = data.text;
    if(dAll == 'ALL'){
        var val = $('.mSelect').select2('val');
        $('#mSelect').empty();
            $.each($('.mSelect').select2('data'), function(i, item) {
        });
        
        $('.sel').html('<label>Select Month</label><div class="row"><div class="col-md-6" style="margin-top: 1px; margin-left: -2px;"><select onchange="getData()" class="form-control select3 select2 name="fMonth" id="months" style="width: 23em;" sAll multiple> @foreach($months as $key => $value)<option value="{{ $value }}">{{ $value }}</option>  @endforeach <option value="ALL" selected>ALL</option></select></div></div>');

        $('.select3').select2({
            theme: 'bootstrap4',
        });
    }

    });

})

function getData(){
    $('.sel').html('<label>Select Month</label><div class="row"><div class="col-md-6" style="margin-top: 1px; margin-left: -2px;"><select class="form-control select3 select2 name="fMonth" id="months" style="width: 23em;" mSelect select2-hidden-accessible multiple> @foreach($months as $key => $value)<option value="{{ $value }}">{{ $value }}</option>  @endforeach </select></div></div>');
    $('.select2').select2({
    theme: 'bootstrap4',
    });
}

hideTable()
function hideTable(){
    $('#tcd-reports').hide();
}

</script>
@stop

