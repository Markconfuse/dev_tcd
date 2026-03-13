@extends('layouts.page')

@section('title', $_details['_status'])

@section('content_header', $_details['_status'])

@section('css')

    <link rel="stylesheet" href="{{ asset('/adminlte/plugins/iCheck/all.css') }}">

    <style type="text/css">
        .preloader {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /*z-index: 9999;*/
            background-image: url('https://digitalsynopsis.com/wp-content/uploads/2016/06/loading-animations-preloader-gifs-ui-ux-effects-11.gif');
            background-repeat: no-repeat;
            background-color: #FFF;
            background-position: center;
        }
    </style>
@stop

@section('content')

    <div class="row">
        <div class="col-sm-4">
            {{-- <h5 style="background-color: white;padding: 15px;font-style:italic;">Please (Hold SHIFT + F5) to get the latest version of the Portal</h5> --}}
            {{-- <h5 style="background-color: white;padding: 15px;font-style:italic;">To get the latest version of the Portal (Hold SHIFT + F5)</h5> --}}
            {{-- <h5 style="background-color: white;padding: 15px">The <b>Search</b> feature now includes searching for everything on the request, e.g. replies, request type, requestor, buyers, even combined (Cost Inquiry Cata 42 inch).</h5> --}}

            <div class="form-group clearfix">
                <div class="icheck-primary d-inline">
                    <h5 style="background-color:white;padding:15px;font-size: 17px!important">
                        <input type="checkbox" name="on_new_tab" value="is_checked" checked>
                        <label>Open request in new tab</label>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                @include('layouts.components.preloader-round')
                <div class="card-header">
                    <h3 class="card-title">{{ $_details['_status'] }}</h3>
                    {{-- @if ($_details['_status'] == 'Tickets Counter')
                        @include('requestor.status_request.sr_legend_counter')
                    @else
                        @include('requestor.status_request.sr_legend')
                    @endif --}}
                </div>
                <!-- /.card-header -->

                <div class="card-body" style="overflow-x:scroll">
                    <table id="tickets" class="table table-bordered table-hover">
                        <thead>
                            <tr style="font-size:14px !important">
                                <th>Engineer Name</th>
                                <th>Total Tickets</th>
                                <th>Today's Not Yet Read</th>
                                <th>Today's Not Yet Answered</th>
                                <th style="white-space: nowrap">Last Ticket Date Assigned</th>

                                {{-- <th class="none">is approved</th> --}}
                            </tr>
                        </thead>
                        <tbody style="font-size:15px">
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

@stop

    <div class="modal fade" id="globalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"><!-- extra-wide -->
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title mText"></h5>
                <button type="button" class="btn cBtn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-body" id="modalContent">
                <!-- Optional: placeholder table structure -->
                <table id="modalTicketsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- AJAX content will populate rows here -->
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btnClose" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@section('js')
    <script>
        window._details = {!! json_encode($_details['_status']) !!};
        console.log('Blade data loaded:', window._details);
    </script>
    <script src="{{ asset('/adminlte/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
    <!-- <script src="{{ asset('public/adminlte/cdnjs-local/datetime-moment.js') }}"></script> -->
    <script src="{{ asset('/js/render_ticket_status_counter_table.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $("input[name='on_new_tab']").iCheck({
                checkboxClass: 'icheckbox_flat',
                increaseArea: '20%'
            });

            setTimeout(function() {
                // alert('{{ route('getTicket') }}?sid=' + '{{ $_details['_statusID'] }}');
                table = renderDtable('{{ route('getTicket') }}?sid=' + '{{ $_details['_statusID'] }}',
                    'tickets');
            }, 500)

            $('#tickets').on('click', 'td.tdClick', function() {

            })
        });

        $('.cBtn').on('click', function(){
            $('.modal').modal("hide");
        })

        $('.btnClose').on('click', function(){
            $('.modal').modal("hide");
        })

        function initModalTable() {
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

}

$("#modalContent").html(`
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3" style="font-size: 1.2rem;">Loading tickets… please wait</p>
    </div>
`);



    </script>

@stop

<style>
    /* Bubble loader for modal */
.modal-bubbles {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    height: 200px; /* adjust height */
}

.modal-bubbles div {
    width: 15px;
    height: 15px;
    background-color: #007bff;
    border-radius: 50%;
    animation: bubbleBounce 0.6s infinite alternate;
}

.modal-bubbles div:nth-child(2) {
    animation-delay: 0.2s;
}
.modal-bubbles div:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes bubbleBounce {
    from { transform: translateY(0); }
    to { transform: translateY(-20px); }
}

</style>