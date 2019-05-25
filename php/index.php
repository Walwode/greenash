<html>

<head>
	<!-- bootstrap -->
	<script src="./js/jquery-3.4.0.min.js"></script> 
	<script src="./js/bootstrap.min.js"></script>
	<script src="./js/Chart.min.js"></script>

	<!-- dashboard -->
	<link rel="shortcut icon" type="image/x-icon" href="./img/favicon.ico">
	<link rel="stylesheet" href="./css/dashboard.css">
	<script src="./js/dashboard.js"></script>
	<meta name="viewport" content="width=device-width" />
</head>

<body>

<div id="cloud-container">

	<div id="title-container"><div id="title"></div></div>

	<table height="100%" width="100%">
		<tr height="*"><td>
			<table height="100%" width="100%">
				<tr>
					<td width="50%">
						<div class="chart-name gold">Distanzverlauf</div>
						<canvas id="historyChart"></canvas>
					</td>
					<td width="50%">
						<div class="chart-name gold">Gesamtdistanz</div>
						<canvas id="totalDistanceChart"></canvas>
					</td>
				</tr>
			</table>
		</td></tr>
		<tr height="280px"><td>
			<div id="team-container"></div> <!-- team-container -->
		</td></tr>
	</table>

</div> <!-- cloud-container -->

<script>
var configHistoryChart = createHistoryChartConfig();
var configTotalDistanceChart = createTotalDistanceChartConfig();
var historyChart = new Chart($('#historyChart'), configHistoryChart);
var totalDistanceChart = new Chart($('#totalDistanceChart'), configTotalDistanceChart);

$(document).ready(function () {
	setInterval(function() {
		getSetup();
		getTeams();
		getLog();
	}, 1000);
});
</script>

</body>
</html>