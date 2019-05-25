function getSetup() {
	$.getJSON('ajax.php', { action: 'getSetup' }, function(data) {
		var setupContainer = $(`#title`).text(data.data.title);
	});
}

function getTeams() {
	$.getJSON('ajax.php', { action: 'getTeams' }, function(data) {
		var teams = data.data;
		$.each(teams, function(index, team) {

			var teamContainer = $(`#team-container-${team.chipId}`);
			if (!teamContainer.length) { createTeamContainer(team); }
		});
	});
}

function getLog() {
	$.getJSON('ajax.php', { action: 'getLog' }, function(response) {
		var data = response.data;

		setHistoryChartLabels(data.labels);

		var activeTeams = 0;
		for (let i = 0; i < data.teams.length; i++) { (data.teams[i].active == "1") ? activeTeams++ : ""; }

		for (let i = 0; i < data.teams.length; i++) {
			var team = data.teams[i];

			updateTeamContainer(team, activeTeams);
			updateTeamHistoryChart(team, i);
			updateTotalDistanceChart(team, i);
		}
		
		historyChart.update();
		totalDistanceChart.update();
	});
}

function updateTotalDistanceChart(team, i) {
	var dataset = configTotalDistanceChart.data.datasets[0];
	if (!dataset) {
		dataset = { data: [0,0,0,0,0,0,0,0], backgroundColor: [0,0,0,0,0,0,0,0], labels: ["","","","","","","",""] };
		configTotalDistanceChart.data.datasets.push(dataset);
	}

	if (team.active == "1") {
		dataset.data[i] = (parseFloat(team.totalDistance || "0") / 1000).toFixed(2);
		dataset.backgroundColor[i] = team.colorCode;
		dataset.labels[i] = team.name;
	} else {
		dataset.data[i] = NaN;
	}
}

function updateTeamHistoryChart(team, i) {
	var dataset = configHistoryChart.data.datasets[i];

	if (!dataset) {
		dataset = { data: [0,0,0,0,0,0,0,0,0,0,0,0] };
		configHistoryChart.data.datasets.push(dataset);
	}
	
	if (team.active == "1") {

		dataset.label = team.name;
		dataset.borderColor = team.colorCode;
		dataset.fill = false;
		dataset.data[0] = { x: 0, y: getY((team.distanceInterval0 / 1000).toFixed(2)) };
		dataset.data[1] = { x: 1, y: getY((team.distanceInterval1 / 1000).toFixed(2)) };
		dataset.data[2] = { x: 2, y: getY((team.distanceInterval2 / 1000).toFixed(2)) };
		dataset.data[3] = { x: 3, y: getY((team.distanceInterval3 / 1000).toFixed(2)) };
		dataset.data[4] = { x: 4, y: getY((team.distanceInterval4 / 1000).toFixed(2)) };
		dataset.data[5] = { x: 5, y: getY((team.distanceInterval5 / 1000).toFixed(2)) };
		dataset.data[6] = { x: 6, y: getY((team.distanceInterval6 / 1000).toFixed(2)) };
		dataset.data[7] = { x: 7, y: getY((team.distanceInterval7 / 1000).toFixed(2)) };
		dataset.data[8] = { x: 8, y: getY((team.distanceInterval8 / 1000).toFixed(2)) };
		dataset.data[9] = { x: 9, y: getY((team.distanceInterval9 / 1000).toFixed(2)) };
		dataset.data[10] = { x: 10, y: getY((team.distanceInterval10 / 1000).toFixed(2)) };
		dataset.data[11] = { x: 11, y: getY((team.distanceInterval11 / 1000).toFixed(2)) };
	} else {
		for (let i = 0; i < 12; i++) {
			dataset.data[i] = { x: i, y: NaN };
		}
	}
}

function createTeamContainer(team) {
	var teamContainer = $(`<div id="team-container-${team.chipId}" class="team-container-left team-container-3"></div>`);
	var containerInlay = $('<div class="team-container-inlay"></div>');
	containerInlay.append($(`<div class="team-color" style="background-color: ${team.colorCode};"></div>`));
	containerInlay.append($(`<div class="team-name">${team.name}</div>`));
	containerInlay.append($('<div class="team-distance team-text-small">0 km</div>'));
	containerInlay.append($('<div class="team-pace team-text-small">0 km/h</div>'));

	teamContainer.append(containerInlay);
	$('#team-container').append(teamContainer);
}

function updateTeamContainer(team, activeTeams) {
	var container = $(`#team-container-${team.chipId}`);

	console.log(activeTeams);
	container.removeClass("team-container-3 team-container-2").addClass("team-container-4");
	(activeTeams < 7) ? container.removeClass("team-container-4 team-container-2").addClass("team-container-3") : "";
	(activeTeams < 5) ? container.removeClass("team-container-4 team-container-3").addClass("team-container-2") : "";

	if (team.active == '0') container.hide("slow");
	else container.show("slow");
	
	container.find(".team-color").css("background-color", team.colorCode);
	container.find(".team-name").text(team.name);
	container.find(".team-distance").text((parseFloat(team.totalDistance || "0") / 1000).toFixed(2) + " km");
	container.find(".team-pace").text(parseFloat(team.currentSpeed || "0").toFixed(1) + " km/h");

	container.find(".team-distance").removeClass("team-text-big").addClass("team-text-small");
	(activeTeams < 7) ? container.find(".team-distance").removeClass("team-text-small").addClass("team-text-big") : "";

	container.find(".team-pace").removeClass("team-text-big").addClass("team-text-small");
	(activeTeams < 7) ? container.find(".team-pace").removeClass("team-text-small").addClass("team-text-big") : "";
}

function getY(value) {
	if (!value || (value == 0)) return NaN;
	return value;
}

function setHistoryChartLabels(labels) {
	configHistoryChart.data.labels = labels;
}

function createHistoryChartConfig() {
	var config = {
		type: 'line',
		options: {
			scales: {
				yAxes: [{
					ticks: {
						// beginAtZero: true
					}
				}]
			},
			maintainAspectRatio: true,
			legend: {
				display: false,
				position: 'right',
				labels: {
					fontColor: 'black',
					fontFamily: 'Verdana',
					// fontSize: '20',
				},
			},
			tooltips: {
				enabled: false,
			},
		}
	};
	
	return config;
}

function createTotalDistanceChartConfig() {
	var config = {
		type: 'polarArea',
		data: {
			labels: [],
			datasets: [],
			options: {
				responsive: true,
				title: { display: false },
				tooltips: { display: false },
			}
		}
	};
	
	return config;
}
