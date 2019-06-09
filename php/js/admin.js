function getTeams() {
	$.getJSON('ajax.php', { action: 'getTeams' }, function(data) {
		var teams = data.data;
		$.each(teams, function(index, team) {
			
			var teamRow = $(`#team-row-${team.chipId}`);
			if (!teamRow.length) { createTeamRow(team); }
			else { updateTeamRow(team, teamRow); }
		});
	});
}

function getSetup() {
	$.getJSON('ajax.php', { action: 'getSetup' }, function(data) {

		var setup = data.data;
		var setupContainer = $(`#setup-container`);
		var setupTitle = $(`<div>Titel: <a>${setup.title}</a></div>`);
		var setupTimeOffset = $(`<div>Event Startzeit: <a>${timestampToText(setup.timeOffset)}</a></div>`);
		
		setupContainer.append(setupTitle);
		setupContainer.append(setupTimeOffset);
		
		setupEditableText(setupTitle.find('a'), 'title');
		setupEditableDateTime(setupTimeOffset.find('a'), 'timeOffset');
	});
}

function deleteLog(chipId) {
	$.getJSON('ajax.php', { action: 'deleteLog', chipId: chipId }, function(data) { console.log(data); });
}

function createTeamRow(team) {
	var teamRow = $(`<tr id="team-row-${team.chipId}"></tr>`);
	var teamChipId = $(`<td>${team.chipId}</td>`);
	var teamName = $(`<td><a>${team.name}</a></td>`);
	var teamActive = $(`<td><a>${team.active}</a></td>`);
	var teamDiameter = $(`<td><a>${team.diameter}</a></td>`);
	var teamFaintInterval = $(`<td><a>${team.faintInterval}</a></td>`);
	var teamPushInterval = $(`<td><a>${team.pushInterval}</a></td>`);
	var teamColorCode = $(`<td><span class="team-color" style="background-color: ${team.colorCode};">&nbsp;</span><a>${team.colorCode}</a></td>`);
	var teamHeartbeat = $(`<td class="team-heartbeat">${timestampToText(team.heartbeat)}</td>`);
	var teamDistance = $(`<td class="team-distance">${team.distance / 1000}</td>`);
	var teamSpeed = $(`<td class="team-speed">${team.speed}</td>`);
	// var teamAction = $(`<td><button type="button" onclick="if (confirm('Wirklich alle Log-Daten f&uuml;r das Team ${addslashes(team.name)} l&ouml;schen?')) { $.getJSON('ajax.php', { action: 'deleteLog', chipId: '${team.chipId}' }, function(data) { console.log(data); }); }">Reset</button></td>`);
	var teamAction = $(`<td><button type="button" onclick="if (confirm('Wirklich alle Log-Daten f&uuml;r das Team ${removeQuotes(team.name)} l&ouml;schen?')) { $.getJSON('ajax.php', { action: 'deleteLog', chipId: '${team.chipId}' }, function(data) { console.log(data); }); }">Reset</button></td>`);
	
	teamRow.append(teamChipId);
	teamRow.append(teamName);
	teamRow.append(teamActive);
	teamRow.append(teamColorCode);
	teamRow.append(teamHeartbeat);
	teamRow.append(teamDistance);
	teamRow.append(teamSpeed);
	teamRow.append(teamDiameter);
	teamRow.append(teamFaintInterval);
	teamRow.append(teamPushInterval);
	teamRow.append(teamAction);
	$('#table-teams tbody').append(teamRow);
	
	teamEditableText(teamName.find('a'), team.chipId, 'name', '350px');
	teamEditableBool(teamActive.find('a'), team.chipId, 'active');
	teamEditableText(teamDiameter.find('a'), team.chipId, 'diameter', '60px');
	teamEditableText(teamFaintInterval.find('a'), team.chipId, 'faintInterval', '60px');
	teamEditableText(teamPushInterval.find('a'), team.chipId, 'pushInterval', '90px');
	teamEditableText(teamColorCode.find('a'), team.chipId, 'colorCode', '120px');
}

function updateTeamRow(team, teamRow) {
	teamRow.find('.team-heartbeat').text(timestampToText(team.heartbeat));
	teamRow.find('.team-color').css('background-color', team.colorCode);
}

function removeQuotes(str) {
	return str.replace(/"/g,"");
}
