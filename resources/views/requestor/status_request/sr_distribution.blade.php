@extends('layouts.page')

@section('title', $_details['_status'] . ' Request')

@section('content_header', $_details['_status'] . ' Request')

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
                    {{-- @include('requestor.status_request.sr_legend') --}}
                </div>
                <!-- /.card-header -->

                <div class="card-body" style="overflow-x:scroll">
                    <table id="tickets" class="table table-bordered table-hover">
                        <thead>
                            <tr style="font-size:14px !important">
                                <th style="white-space: nowrap">Engineer</th>
                                <th style="white-space: nowrap">Tickets</th>
                                <th style="white-space: nowrap; width:410px;text-align:center">Last Ticket Subject</th>
                                <th style="white-space: nowrap">Last Ticket ID</th>
                                <th style="white-space: nowrap">Status</th>
                                <th style="white-space: nowrap">Last Ticket Date Assigned</th>
                                <th style="white-space: nowrap">Last Ticket Update</th>
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

@section('js')

    <script src="{{ asset('/adminlte/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
    <script src="{{ asset('adminlte/cdnjs-local/datetime-moment.js') }}"></script>
    <script src="{{ asset('/js/render_dt_ticket_table.js') }}"></script>
    <script src="{{ asset('/js/data_table_filter.js') }}"></script>
    <script>
        $(function() {

            $("input[name='on_new_tab']").iCheck({
                checkboxClass: 'icheckbox_flat',
                increaseArea: '20%'
            });
            setTimeout(function() {
                const url = '{{ route('getTicket') }}?sid=' + '{{ $_details['_statusID'] }}';
                const table_name = 'tickets'
                
                table = renderDtable(url, table_name, function() {
                    return {
                        status_filter: $('#status_filter').val()
                    };
                });

                new data_table_filter(table, {
                    options: [
                        { value: 'Not yet viewed', label: 'Not yet viewed' },
                        { value: 'Not yet answered', label: 'Not yet answered' },
                        { value: 'Answered', label: 'Answered' },
                        { value: 'Escalated', label: 'Escalated' },
                        { value: 'Escalation Checked', label: 'Escalation Checked' },
                        { value: 'Escalation Approved', label: 'Escalation Approved' }
                    ]
                });
            }, 500);

            $('#tickets').on('click', 'td.tdClick', function() {
                console.log(table.row(this).data());

                if (table.row(this).data() !== undefined) {
                    var url = '{{ route('view-request', ':slug') }}';

                    url = url.replace(':slug', btoa(table.row(this).data()['last_ticket_number']));

                    is_new_tab = $("input[name='on_new_tab']").iCheck('update')[0].checked
                    if (is_new_tab) {
                        window.open(url, '_blank');
                    } else {
                        $('.preloader-round').removeAttr('hidden', 'hidden');
                        window.location.href = url;
                    }
                }

            })
            $('#tickets').on('xhr.dt', function(e, settings, json, xhr) {
                console.log('Total Tickets:', json.total_ticket_count);
            });
        });
    </script>
    <!--  -->
    {{--
    {{  }}<script type="text/javascript">
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
                console.log(table.row(this).data());

                if (table.row(this).data() !== undefined) {
                    var url = '{{ route('view-request', ':slug') }}';

                    url = url.replace(':slug', btoa(table.row(this).data()['last_ticket_id']));

                    is_new_tab = $("input[name='on_new_tab']").iCheck('update')[0].checked
                    if (is_new_tab) {
                        window.open(url, '_blank');
                    } else {
                        $('.preloader-round').removeAttr('hidden', 'hidden');
                        window.location.href = url;
                    }
                }

            })
        });
    </script> --}}

@stop
