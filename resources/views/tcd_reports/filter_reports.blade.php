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
    <div class="col-md-5">
        <label>SELECT MONTH</label>
    <form method="GET" action="filtered_tcd_reports">
            <select class="form-control select2 select2-hidden-accessible mSelect" name="fMonth" id="months" style="width: 100%;" multiple>
                <option value="ALL">ALL</option>
            @foreach($months as $key => $value)
                <option value="{{ $value }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-5">
        <label>SELECT YEAR</label>
        <select class="form-control select2 select2-hidden-accessible ySelect" name="fYear" id="years" style="width: 100%;" multiple>
            @foreach($years as $key => $value)
                <option value="{{ $value }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button id="update-btn" type="submit" style="margin-top: 32px;" 
                class="btn btn-primary btn-sm">
            <i class="fa fa-paper-plane"></i> SUBMIT</button>
        </div>    
    </div>  
</form>
<div class="table-responsive">
<br />
    <table id="filtered-reports" class="table-docs table-bordered" cellspacing="0" style="width: 100%">
        <thead>
            <tr class="eN">
                <th class="s-th">Reference No.</th>
                <th class="s-th">Request Type</th>
                <th class="s-th">Project Name</th>
                <th class="s-th">Company Name</th>
                <th class="s-th">AO</th>
                <th class="s-th">BU</th>
                <th class="s-th">ENGR</th>
            </tr>
        </thead>
    </table>
                <!-- /.col -->
                </div>
            <!-- /.row -->
        </div>
    </div>
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
        white-space: nowrap; 
    }
</style>
@stop

@section('js')
<script type="text/javascript">

filteredTcdDataTable();
    function filteredTcdDataTable(){
    $('.spinner').hide();
    $('.docs-body').show();
    $('#filtered-reports').DataTable().destroy();
        table = $('#filtered-reports').DataTable({
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
                'url': '{{ url("/filtered_tcd_reports") }}',
                "data" : {
                    // "_token"	: "{{ csrf_token() }}"
	    		}
            },

            columnDefs: [
                // { className: 'dt-body-center', targets: 2, "className": "text-center" },
                // { className: 'dt-body-center', targets: 3, "className": "text-center" },
                { targets: 0, orderable: false },
                { width: 10, targets: 0 },
                { width: 20, targets: 1 },
                { width: 60, targets: 2 },
                { width: 60, targets: 3 },
                { width: 60, targets: 4 },
                { width: 60, targets: 5 },
                { width: 60, targets: 6 }
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
                    data: 'engr',
                    name: 'engr'
                }
            ]
        
    });

}
    

</script>
<script type="text/javascript">
      
$( document ).ready(function() {
    
    $('.select2').select2({
        theme: 'bootstrap4',
    });
           
    $('#cAll').val(this.checked);

    $('#cAll').change(function() {
        if(this.checked) {
            var returnVal = confirm("Are you sure?");
            $(this).prop("checked", returnVal);
        }
        $('#cAll').val(this.checked);        
    });
    
    $('.mSelect').on('select2:select', function(e) {
        var data = e.params.data;
        console.log(data.id);
        console.log(data.text);
    });

    $('.mSelect').on('select2:unselect', function(e) {
        var data = e.params.data;
        console.log(data.id);
        console.log(data.text);
    });

})

</script>
@stop

