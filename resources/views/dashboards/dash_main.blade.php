@extends('layouts.page')

@section('title', 'Dashboard')

@section('content_header', 'Dashboard')

@section('css')

@stop

@section('content')

{{-- <h4>Under Maintenance</h4> --}}



@include('modals.mdl_dash_ticket')



@include('dashboards.dash_info')


<div class="row">
	@if(Session('userData')->role_name !== 'requestor')
		@include('dashboards.dash_per_bu')
	@endif

	@include('dashboards.dash_per_request_type')
</div>

@if(Session('userData')->role_name === 'admin' || Session('userData')->role_name === 'super_user')
	<div class="row">
		@include('dashboards.dash_monthly_trend')
		@include('dashboards.dash_avg_handling_time')
	</div>

@endif

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

		if ($('#chart-per-ao').length) {
			getChartPerAO('{{ url('getChartPerAO') }}');
		}
	})

	var selectedYear = '{{ $selectedYear }}';

	function getChartPerAO(url) {
		var aoCanvas = document.getElementById('chart-per-ao');
		if (!aoCanvas) {
			return;
		}

		$.get(url + '?year=' + selectedYear).done(function (data) {
			var dataCount = [];
			var dataLabel = [];
			$.each(data.data, function (key, value) {
				objcount = {};
				objlabel = {};
				dataCount.push(value.cnt);
				dataLabel.push(value.AccountName);

			});

			var chart = new Chart(aoCanvas, {
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
		var requestTypeCanvas = document.getElementById('chart-request-type');
		if (!requestTypeCanvas) {
			return;
		}

		$.get(url + '?year=' + selectedYear).done(function (data) {
			var dataCount = [];
			var dataLabel = [];
			$.each(data.data, function (key, value) {
				objcount = {};
				objlabel = {};
				dataCount.push(value.count_per_rtype);
				dataLabel.push(value.request_type);
			});

			var chart = new Chart(requestTypeCanvas, {
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
		var buCanvas = document.getElementById('chart-per-bu');
		if (!buCanvas) {
			return;
		}

		$.get(url + '?year=' + selectedYear).done(function (data) {

			var dataCount = [];
			var dataLabel = [];
			$.each(data.data, function (key, value) {
				objcount = {};
				objlabel = {};
				dataCount.push(value.cnt);
				dataLabel.push(value.AccountGroup);
			});

			var chart = new Chart(buCanvas, {
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

	});

	$('#tblPerCountEngr').on('click', '.spnPerEngineer', function () {
		data = $(this).attr('data-id');

		table = renderDtable('{{ url('getTicketPerEngineer') }}?data=' + data, 'tbl_mdl_tickets');

		engineer = $(this).closest("tr").find('td:eq(0)').text();
		type = $(this).closest('table').find('th').eq($(this).parent().index()).text();

		$('#spnTitle').text(type + ' tickets of ' + engineer);
		$('#mdl_dash_ticket').modal('show');
	});

	// ─── Monthly Ticket Trend (Line Chart - Live Data) ───
	@if(Session('userData')->role_name !== 'requestor')
			(function () {
				var monthlyTrendCanvas = document.getElementById('chart-monthly-trend');
				if (!monthlyTrendCanvas) {
					return;
				}

				$.get('{{ url('getMonthlyTicketTrend') }}').done(function (res) {
					var months = (res && res.labels) ? res.labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
					var ticketsOpened = (res && res.opened) ? res.opened : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
					var ticketsClosed = (res && res.closed) ? res.closed : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

					new Chart(monthlyTrendCanvas, {
						type: 'line',
						data: {
							labels: months,
							datasets: [{
								label: 'Tickets Opened',
								borderColor: '#e74c3c',
								backgroundColor: 'rgba(231, 76, 60, 0.1)',
								data: ticketsOpened,
								fill: true,
								tension: 0.3,
								pointRadius: 4,
								pointBackgroundColor: '#e74c3c'
							}, {
								label: 'Tickets Closed',
								borderColor: '#2ecc71',
								backgroundColor: 'rgba(46, 204, 113, 0.1)',
								data: ticketsClosed,
								fill: true,
								tension: 0.3,
								pointRadius: 4,
								pointBackgroundColor: '#2ecc71'
							}]
						},
						options: {
							maintainAspectRatio: false,
							plugins: {
								datalabels: { display: false }
							},
							legend: { display: true, position: 'top' },
							scales: {
								yAxes: [{ ticks: { beginAtZero: true } }]
							}
						}
					});
				}).fail(function () {
					new Chart(monthlyTrendCanvas, {
						type: 'line',
						data: {
							labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
							datasets: [{
								label: 'Tickets Opened',
								borderColor: '#e74c3c',
								backgroundColor: 'rgba(231, 76, 60, 0.1)',
								data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
								fill: true
							}, {
								label: 'Tickets Closed',
								borderColor: '#2ecc71',
								backgroundColor: 'rgba(46, 204, 113, 0.1)',
								data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
								fill: true
							}]
						},
						options: {
							maintainAspectRatio: false,
							plugins: {
								datalabels: { display: false }
							},
							legend: { display: true, position: 'top' },
							scales: {
								yAxes: [{ ticks: { beginAtZero: true } }]
							}
						}
					});
				});
			})();

		// ─── Avg Handling Time per Engineer (Bar Chart - Live Data) ───
		(function () {
			var avgHandlingCanvas = document.getElementById('chart-avg-handling-time');
			var avgHandlingContainer = document.getElementById('chart-avg-handling-time-container');
			if (!avgHandlingCanvas || !avgHandlingContainer) {
				return;
			}

			function resizeAvgHandlingChart(rows) {
				var minHeight = 350;
				var rowHeight = 34;
				var computedHeight = Math.max(minHeight, rows * rowHeight);

				avgHandlingCanvas.height = computedHeight;
				avgHandlingContainer.style.height = computedHeight + 'px';
			}

			$.get('{{ url('getAvgHandlingTimePerEngineer') }}').done(function (res) {
				var engineers = (res && res.labels && res.labels.length) ? res.labels : ['No data'];
				var avgHours = (res && res.values && res.values.length) ? res.values : [0];

				resizeAvgHandlingChart(engineers.length);

				var barColors = avgHours.map(function (h) {
					if (h <= 4) return '#2ecc71';
					if (h <= 6) return '#f39c12';
					return '#e74c3c';
				});

				new Chart(avgHandlingCanvas, {
					type: 'horizontalBar',
					data: {
						labels: engineers,
						datasets: [{
							label: 'Avg. Hours',
							backgroundColor: barColors,
							data: avgHours,
							barPercentage: 0.6
						}]
					},
					options: {
						maintainAspectRatio: false,
						plugins: {
							datalabels: {
								anchor: 'end',
								align: 'right',
								color: '#333',
								font: { weight: 'bold', size: 11 },
								formatter: function (value) { return value + 'h'; }
							}
						},
						legend: { display: false },
						scales: {
							yAxes: [{ ticks: { autoSkip: false } }],
							xAxes: [{
								ticks: { beginAtZero: true },
								scaleLabel: { display: true, labelString: 'Hours' }
							}]
						}
					}
				});
			}).fail(function () {
				resizeAvgHandlingChart(1);

				new Chart(avgHandlingCanvas, {
					type: 'horizontalBar',
					data: {
						labels: ['No data'],
						datasets: [{
							label: 'Avg. Hours',
							backgroundColor: ['#95a5a6'],
							data: [0],
							barPercentage: 0.6
						}]
					},
					options: {
						maintainAspectRatio: false,
						plugins: {
							datalabels: {
								anchor: 'end',
								align: 'right',
								color: '#333',
								font: { weight: 'bold', size: 11 },
								formatter: function (value) { return value + 'h'; }
							}
						},
						legend: { display: false },
						scales: {
							yAxes: [{ ticks: { autoSkip: false } }],
							xAxes: [{
								ticks: { beginAtZero: true },
								scaleLabel: { display: true, labelString: 'Hours' }
							}]
						}
					}
				});
			});
		})();
	@endif

</script>


@stop