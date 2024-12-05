function showTime() {
		var timeText = document.getElementById("time");
		var today = new Date();

		var year = today.getFullYear();
		var month = today.toLocaleString('default', {month: 'short' });
		var day = today.getDate();
		var weekday = today.toLocaleString('default', { weekday: 'short' }); ;
    
		var minute = today.getMinutes().toString().padStart(2, '0')
		var hour = today.getHours();
		var period = "AM";
		if (hour == 0) {
			hour = 12;
		} else if (hour >= 12) {
			period = "PM";
			if (hour > 12){
				hour = hour - 12;
			}
		}
    
		var date = weekday + ' ' + month + ' ' + day + ' ' + year;
		var time = hour + ":" + minute + ' ' + period;
		var dateTime = date + ' ' + time;
		timeText.innerHTML = dateTime;
	}

function showUser() {
	if (typeof(Storage) !== "undefined") {
		if (sessionStorage.user) {
			let user = JSON.parse(sessionStorage.user);
			let name = user["first-name"] + " " + user["last-name"]
			document.getElementById("logedUser").innerHTML = "<a href='my-account.html'>" + name + "</a>";
		} else {
			document.getElementById("logedUser").innerHTML = "<a href='login.html'>Log in Here</a>";
		}
	} else {
		document.getElementById("logedUser").innerHTML = "<a href='login.html'>Log in Here</a>";
	}
}
	window.onload = function() {showTime(); showUser(); setInterval(showTime, 1000);}

function isAdmin() {
	if (typeof(Storage) !== "undefined") {
		if (sessionStorage.user) {
			let user = JSON.parse(sessionStorage.user);
			if (user["phone-number"] === "222-222-2222") {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function changeWeb() {
	var font = document.forms["changeForm"]["font"].value.toString() + "px";
	document.getElementsByClassName("main")[0].style.fontSize = font;

	var color = document.forms["changeForm"]["color"].value;
	document.getElementsByClassName("main")[0].style.backgroundColor = color;

	return false;
}

function resetWeb() {
	document.getElementsByClassName("main")[0].style.fontSize ="16px";
	document.getElementsByClassName("main")[0].style.backgroundColor ="#ffffff";
}
