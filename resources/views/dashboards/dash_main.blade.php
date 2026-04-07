@extends('layouts.page')

@section('title', 'Dashboard')

@section('content_header', 'Dashboard')

@section('css')

@stop

@section('content')

{{-- <h4>Under Maintenance</h4> --}}

@php
    $selectedYear = request('year', date('Y'));
@endphp

<div class="row mb-2">
    <div class="col-12 d-flex justify-content-end align-items-center" style="gap: 8px;">
        <label for="yearFilter" class="mb-0 font-weight-bold text-secondary" style="font-size:0.95rem; white-space:nowrap;">Filter by Year:</label>
        <select id="yearFilter" class="form-control form-control-sm" style="width:110px;">
            @php
                $startYear = 2020;
                $endYear = (int) date('Y');
            @endphp
            <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>All</option>
            @for($y = $endYear; $y >= $startYear; $y--)
                <option value="{{ $y }}" {{ (string)$y === (string)$selectedYear ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
</div>

@include('modals.mdl_dash_ticket')



@include('dashboards.dash_info')


<div class="row">
	@if(Session('userData')->role_name !== 'requestor')
		@include('dashboards.dash_per_bu')
	@endif

	@include('dashboards.dash_per_request_type')
</div>

@if(Session('userData')->role_name !== 'requestor')
	@include('dashboards.dash_engineer')
@endif


@stop

@section('js')
{{--
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script> --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/Chart.min.js') }}"></script> -->

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/chartjs-plugin-datalabels@0.7.0.js') }}"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/raphael-min.js') }}"></script> -->

<script src="https://cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/dataTables.buttons.min.js') }}"></script> -->

<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.flash.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/buttons.flash.min.js') }}"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/jszip.min.js') }}"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/pdfmake.min.js') }}"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/vfs_fonts.js') }}"></script> -->

<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.html5.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/buttons.html5.min.js') }}"></script> -->

<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.print.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dashboard/buttons.print.min.js') }}"></script> -->

<script src="{{ asset('js/render_ticket_table.js') }}"></script>

<script type="text/javascript">

	$('#tblPerCountAO').DataTable({
		lengthMenu: [[5, 10, -1], [5, 10, "All"]],
		order: [[1, "asc"]]
	});

	$(function () {


		if ('{{ Session('userData')->role_name != 'requestor' }}') {
			$('#tblPerCountEngr').DataTable({
				lengthMenu: [[5, 10, -1], [5, 10, "All"]],
				pageLength: 10,
			});

			$('#tblPerCountEngineerDate').DataTable({
				order: [[0, "desc"]],
				aLengthMenu: [
					[7, 10, 50, 100, -1],
					[7, 10, 50, 100, "All"]
				],
				dom: 'lBfrtip',
				buttons: [
					{ extend: 'excel', text: 'Export to Excel', "className": 'btn btn-info btn-l btn-exportexcel', }
				]
			});

			getChartPerBU('{{ url('getChartPerBU') }}');
		}
		chartRequestType('{{ url('getChartRequestType') }}', 'horizontalBar');

		getChartPerAO('{{ url('getChartPerAO') }}');
	})

	var selectedYear = '{{ request('year', date('Y')) }}';

	$('#yearFilter').on('change', function() {
		var year = $(this).val();
		var url = new URL(window.location.href);
		url.searchParams.set('year', year);
		window.location.href = url.toString();
	});

	function getChartPerAO(url) {
		$.get(url + '?year=' + selectedYear).done(function (data) {
			var dataCount = [];
			var dataLabel = [];
			$.each(data.data, function (key, value) {
				objcount = {};
				objlabel = {};
				dataCount.push(value.cnt);
				dataLabel.push(value.AccountName);

			});

			var chart = new Chart($('#chart-per-ao'), {
				type: 'horizontalBar',
				data: {
					labels: dataLabel,
					datasets: [{
						label: "Total Count",
						backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850", "#6B5B95", "#FF6F61", "#9B1B30", "#F5D6C6", "#3B3A3A"],
						data: dataCount
					}]
				},
				options: {
					plugins: {
						datalabels: {
							backgroundColor: 'gray',
							color: '#ffffff',
							font: {
								weight: 'bold',
								size: 10,
							}
						},
					},
					legend: {
						fontColor: "white",
						display: true,
					},
				},
				legend: {
					display: true,
					position: 'left',
				},
			});

		}).fail(function (data) {
			chartRequestType('{{ url('getChartPerAO') }}');
		});
	}


	function chartRequestType(url, chartType) {
		$.get(url + '?year=' + selectedYear).done(function (data) {
			var dataCount = [];
			var dataLabel = [];
			$.each(data.data, function (key, value) {
				objcount = {};
				objlabel = {};
				dataCount.push(value.count_per_rtype);
				dataLabel.push(value.request_type);
			});

			var chart = new Chart($('#chart-request-type'), {
				type: chartType,
				data: {
					labels: dataLabel,
					datasets: [{
						label: "Total Count",
						backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850", "#6B5B95", "#FF6F61", "#9B1B30", "#F5D6C6", "#3B3A3A"],
						data: dataCount
					}]
				},

				options: {
					maintainAspectRatio: false,
					plugins: {
						datalabels: {
							backgroundColor: 'gray',
							color: '#ffffff',
							font: {
								weight: 'bold',
								size: 10,
							}
						}
					},
					legend: {
						display: true,
					},
				},
				legend: {
					display: true,
					position: 'left',
				}
			});

		}).fail(function (data) {
			chartRequestType('{{ url('getChartRequestType') }}', chartType);
		});
	}


	function getChartPerBU(url) {
		$.get(url + '?year=' + selectedYear).done(function (data) {

			var dataCount = [];
			var dataLabel = [];
			$.each(data.data, function (key, value) {
				objcount = {};
				objlabel = {};
				dataCount.push(value.cnt);
				dataLabel.push(value.AccountGroup);
			});

			var chart = new Chart($('#chart-per-bu'), {
				type: 'horizontalBar',
				data: {
					labels: dataLabel,
					datasets: [{
						label: "Total Count",
						backgroundColor: ["#3e95cd", "#3cba9f", "#e8c3b9", "#c45850", "#6B5B95", "#FF6F61", "#9B1B30", "#F5D6C6", "#3B3A3A"],
						data: dataCount
					}]
				},
				options: {
					maintainAspectRatio: false,
					plugins: {
						datalabels: {
							backgroundColor: 'gray',
							color: '#ffffff',
							font: {
								weight: 'bold',
								size: 10,
							}
						},
					},
					legend: {
						display: true,
					},
					onClick: function (e) {
						var activePoints = chart.getElementsAtEvent(e);
						if (activePoints[0]) {
							var selectedIndex = activePoints[0]._index;
							table = renderDtable('{{ url('getTicketPerBu') }}?bu=' + this.data.labels[selectedIndex], 'tbl_mdl_tickets');
							$('#spnTitle').text('Tickets of ' + this.data.labels[selectedIndex]);
							$('#mdl_dash_ticket').modal('show');
						}
					}
				}
			});

		}).fail(function (data) {
			getChartPerBU('{{ url('getChartPerBU') }}');
		});
	}

	$('#tbl_mdl_tickets').on('click', 'tbody tr', function () {
		if (table.row(this).data() !== undefined) {
			$('.preloader-round').removeAttr('hidden', 'hidden');;
			var url = '{{ route("view-request", ":slug") }}';

			url = url.replace(':slug', btoa(table.row(this).data()['ticket_id']));
			window.location.href = url;
		}

	})

	$('#tblPerCountEngr').on('click', '.spnPerEngineer', function () {
		data = $(this).attr('data-id');

		table = renderDtable('{{ url('getTicketPerEngineer') }}?data=' + data, 'tbl_mdl_tickets');

		engineer = $(this).closest("tr").find('td:eq(0)').text();
		type = $(this).closest('table').find('th').eq($(this).parent().index()).text();

		$('#spnTitle').text(type + ' tickets of ' + engineer);
		$('#mdl_dash_ticket').modal('show');
	})

</script>


@stop