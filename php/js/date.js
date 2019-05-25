function timestampToText(UNIX_timestamp){
	var a = new Date(UNIX_timestamp * 1000);
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var year = a.getFullYear();
	var month = ("0" + (a.getMonth() + 1)).slice(-2);
	var date = ("0" + a.getDate()).slice(-2);
	var hour = ("0" + a.getHours()).slice(-2);
	var min = ("0" + a.getMinutes()).slice(-2);
	var sec = ("0" + a.getSeconds()).slice(-2);
	var time = date + '.' + month + '.' + year + ' ' + hour + ':' + min + ':' + sec ;
	return time;
}

function textToTimestamp(dateTimeText) {
	var reggie = /(\d{2}).(\d{2}).(\d{4}) (\d{2}):(\d{2}):(\d{2})/;
	var dateArray = reggie.exec(dateTimeText); 
	var dateObject = new Date(
		(+dateArray[3]),
		(+dateArray[2])-1, // Careful, month starts at 0!
		(+dateArray[1]),
		(+dateArray[4]),
		(+dateArray[5]),
		(+dateArray[6])
	);
	return dateObject.getTime()/1000;
}
