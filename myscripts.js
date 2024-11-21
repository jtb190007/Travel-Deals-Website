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
	window.onload = function() {showTime(); setInterval(showTime, 1000);}

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

function addHotelToCart(hotelId) {
	const hotel = availableHotels.hotels.find(hotel => hotel['hotel-id'] === hotelId);
	if (!hotel) {
		alert('Hotel not found');
		return;
	}
	let cart = JSON.parse(localStorage.getItem('cart')) || [];
	cart.push(hotel);
	localStorage.setItem('cart', JSON.stringify(cart));
	alert('Hotel added to cart!');
}

function handleBooking(hotelId) {
    let selectedHotel = availableHotels.hotels.find(hotel => hotel['hotel-id'] === parseInt(hotelId));

    if (!selectedHotel) {
        alert('No hotel selected for booking!');
        return;
    }

    if (selectedHotel['available-rooms'] > 0) {
        fetch("http://localhost/test/availablehotels", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                hotelId: selectedHotel['hotel-id'],
                decrementBy: 1
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                selectedHotel['available-rooms'] -= 1;
                localStorage.setItem('cart', JSON.stringify(cart));
                alert('Booking confirmed!');
            } else {
                alert('Failed to book the hotel.');
            }
        })
        .catch(error => {
            console.error('Error updating available rooms:', error);
            alert('Failed to book the hotel. Please try again.');
        });
    } else {
        alert('No rooms available!');
    }
}
