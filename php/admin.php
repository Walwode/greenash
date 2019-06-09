<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<!-- bootstrap -->
	<link href="./css/bootstrap.min.css" rel="stylesheet">
	<script src="./js/jquery-3.4.0.min.js"></script> 
	<script src="./js/bootstrap.min.js"></script>

	<!-- x-editable (bootstrap version) -->
	<link href="./css/bootstrap-editable.css" rel="stylesheet">
	<script src="./js/bootstrap-editable.min.js"></script>

	<!-- admin -->
	<link rel="shortcut icon" type="image/x-icon" href="./img/favicon.ico">
	<link rel="stylesheet" href="./css/admin.css">
	<script src="./js/date.js"></script>
	<script src="./js/editable.js"></script>
	<script src="./js/admin.js"></script>
</head>

<body>

<div id="container">

<h4>Teams</h4>
<table id="table-teams" class="table">
	<thead>
		<tr>
			<th>Chip Id</th>
			<th title="Name des Teams">Name</th>
			<th title="Soll Team in Diagrammen angezeigt werden?">Aktiv</th>
			<th title="Farbcode für Diagramm, z.B. #DDE54E oder rgb(125, 255, 000, 1)">Farbcode</th>
			<th title="Letzter Log-Eintrag">Aktivit&auml;t</th>
			<th title="Gesamtdistanz seit Event Startzeit">[km]</th>
			<th title="Atuelle Geschwindigkeit">[km/h]</th>
			<th title="Durchmesser der Rolle/Rad in Millimeter">DM<br/>[mm]</th>
			<th title="Abtast-Timeout-Intervall - Timeout zwischen zwei Interrupt-Signalen in Millisekunden (20ms scheint zu wenig, 50ms ist zu viel)">A-Int.<br/>[ms]</th>
			<th title="Push-Intervall - Pause zwischen dem Senden der Informationen vom Chip an die Datenbank">P-Int.<br/>[ms]</th>
			<th title="Aktionen f&uuml;r das Team: Reset = Log l&ouml;schen">Aktionen</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<h4>Einstellungen</h4>
<div id="setup-container"></div>
<button type="button" onclick="if (confirm('Wirklich alle Log-Daten l&ouml;schen?')) { $.getJSON('ajax.php', { action: 'deleteLog' }, function(data) { console.log(data); }); }">Alle Log-Daten l&ouml;schen</button>
</div> <!-- container -->

<script>
$(document).ready(function () {
	getTeams();
	getSetup();
	
	setInterval(function() {
		getTeams();
	}, 1000);
});
</script>

</body>
</html>