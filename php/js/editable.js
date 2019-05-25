function teamEditableText(control, chipId, field, width = '100%') {
	control.editable({
		type: 'text',
		send: 'always',
		mode: 'inline',
		url: 'ajax.php?action=updateTeam',
		showbuttons: false,
		tpl: `<input type='text' style='width: ${width}'>`,
		// ajaxOptions: { contentType: 'application/json; charset=utf-8' },
		params: function(params) {
			delete params.pk;
			delete params.name;
			params.chipId = chipId;
			params.field = field;
			return params;
		},
		success: function(response, newValue) { console.log(response); },
		error: function(response, newValue) {},
	});
}

function teamEditableBool(control, chipId, field) {
	control.editable({
		type: 'select',
		send: 'always',
		mode: 'inline',
		url: 'ajax.php?action=updateTeam',
		source: [{value: 0, text: "Aus"}, {value: 1, text: "Ein"}],
		showbuttons: false,
		params: function(params) {
			delete params.pk;
			delete params.name;
			params.chipId = chipId;
			params.field = field;
			return params;
		},
		success: function(response, newValue) { console.log(response); },
		error: function(response, newValue) {},
	});
}

function setupEditableText(control, field) {
	control.editable({
		type: 'text',
		send: 'always',
		mode: 'inline',
		url: 'ajax.php?action=updateSetup',
		showbuttons: false,
		params: function(params) {
			delete params.pk;
			delete params.name;
			params.field = field;
			return params;
		},
		success: function(response, newValue) { console.log(response); },
		error: function(response, newValue) {},
	});
}

function setupEditableDateTime(control, field) {
	control.editable({
		type: 'text',
		send: 'always',
		mode: 'inline',
		url: 'ajax.php?action=updateSetup',
		showbuttons: false,
		params: function(params) {
			delete params.pk;
			delete params.name;
			params.field = field;
			params.value = textToTimestamp(params.value);
			return params;
		},
		success: function(response, newValue) { console.log(response); },
		error: function(response, newValue) {},
	});
}
