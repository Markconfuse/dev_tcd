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
        TCD DATA
    </div>
<div class="card-body docs-body">
<div class="row">
    <div class="col-md-5 sel">
        <label>Select Month</label>
            <form method="GET" action="filter_reports">
            <select class="form-control select2 select2-hidden-accessible mSelect" name="fMonth" id="months" style="width: 100%;" multiple>
                <option value="ALL">ALL</option>
            @foreach($months as $key => $value)
                <option value="{{ $value }}">{{ $value }}</option>
            @endforeach
        </select>
		<span class="month-text"></span>
    </div>
    <div class="col-md-5">
        <label>Select Year</label>
        <select class="form-control select2 select2-hidden-accessible ySelect" name="fYear" id="years" style="width: 100%;" multiple>
            @foreach($years as $key => $value)
                <option value="{{ $value }}">{{ $value }}</option>
            @endforeach
        </select>
		<span class="year-text"></span>
    </div>
    <div class="col-md-2">
        <button id="update-btn" type="button" style="margin-top: 32px;" 
                class="btn btn-primary btn-sm" onclick="submitMonthYear(event)">
            <i class="fa fa-filter"></i> Filter</button>
        </div>    
    </div>  
</form>

<br />
    <table id="tcd-reports" class="table-docs table-bordered" cellspacing="0" style="width: 100%">
        <thead>
            <tr class="eN">
                <th class="s-th">Reference No.</th>
                <th class="s-th">Request Type</th>
                <th class="s-th">Project Name</th>
                <th class="s-th">Company Name</th>
                <th class="s-th">AO</th>
                <th class="s-th">BU</th>
                <th class="s-th">ENGR</th>
                <th class="s-th">STATUS</th>
                <th class="s-th">DATE REQUESTED</th>
                <th class="s-th">DATE ASSIGNED</th>
                <th class="s-th">ENGR. 1ST REPLY DATE</th>
                <th class="s-th">REQSTR. LAST REPLY DATE</th>
                <th class="s-th">ENGR. LAST REPLY DATE</th>
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
function submitMonthYear(){
    dM = $('#months').val();
    dY = $('#years').val();
	
    console.log('dm: ', dM);
	
		if (dM.length == 0){
            $('.month-text').text('Please fill months').css('color', '#CF000F');
        }

        if (dY.length == 0){
            $('.year-text').text('Please fill years').css('color', '#CF000F');
        }
		
	$('#tcd-reports').show();

    if(dM.length > 0 && dY.length > 0){
		
        loadTcdReportsDataTable(dM, dY)
        // $('.xP').html('<a href="/export_tcd/'+ dM +'/'+ dY +'"><button id="update-btn" type="button" style="margin-top: 32px;" class="btn btn-success btn-sm" ><i class="fa fa-paper-plane"></i> Export</button>');
    }
}
    function loadTcdReportsDataTable(dM, dY){
	$('.month-text').hide();
	$('.year-text').hide();
    $('.spinner').hide();
    $('.docs-body').show();
    $('#tcd-reports').DataTable().destroy();
        table = $('#tcd-reports').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<button type="button" class="btn btn-success btn-sm"> <i class="fa fa-table"></i> XLS EXPORT </button>',
					title: 'TCD EXPORTS FROM: ' + dM +' '+ dY,
                    titleAttr: 'Export'
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
                    // "_token"	: "{{ csrf_token() }}"
                    "dMonths": dM,
                    "dYears": dY
	    		}
            },

        columnDefs: [
            { width: 3, targets: 0 },
            { width: 3, targets: 1 },
            { width: 280, targets: 2 },
            { width: 200, targets: 3 },
            { width: 180, targets: 4 },
            { width: 100, targets: 5 },
            { width: 100, targets: 6 },
            { width: 120, targets: 7 },
			{ width: 100, targets: 8 },
        ],
        columns:[
            {
                data: 'reference_number',
                name: 'reference_number'
            },
            {
                data: 'request_type',
                name: 'request_type'
            },
            {
                data: 'project_name',
                name: 'project_name'
            },
            {
                data: 'company_name',
                name: 'company_name'
            },
            {
                data: 'ao',
                name: 'ao'
            },
            {
                data: 'bu',
                name: 'bu'
            },
            {
                data: 'engineers',
                name: 'engineers'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'date_requested',
                name: 'date_requested'
            },
            {
                data: 'date_assigned',
                name: 'date_assigned'
            },
            {
                data: 'first_reply_engr',
                name: 'first_reply_engr'
            },
            {
                data: 'last_reply_req',
                name: 'last_reply_req'
            },
            {
                data: 'last_reply_engr',
                name: 'last_reply_engr'
            },
            {
                data: 'last_updated',
                name: 'last_updated'
            }

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

