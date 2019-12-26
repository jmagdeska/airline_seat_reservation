function ajaxRequest() {
	try { // Non IE Browser? 
    	var request = new XMLHttpRequest()    	
	} catch(e1){ // No
    	try { // IE 6+?
        	request = new ActiveXObject("Msxml2.XMLHTTP")
    	} catch(e2){ // No
	   		try { // IE 5?
	       		request = new ActiveXObject("Microsoft.XMLHTTP")
	   		} catch(e3){ // No AJAX Support
				request = false
	   		}
  		}
	}
	return request;
}

function configMap(rows, seatsRow, loggedIn) {
	var el = document.getElementById("planeMap");
	var body = "<div class=plane><ul>";
	
	for(var i = 1; i <= rows; i++){
		body += "<li><ul class=row>";
		for(var j = 1, l = 'A'; j <= seatsRow; j++){
			var id = l+i;
			
			if(j == seatsRow/2) {
				if(loggedIn == 'y')
					body += "<li id="+id+" class='seatAisle' onmousedown='reserveSeat(this.id)'>"+id+"</li>";
				else body += "<li id="+id+" class='seatAisle'>"+id+"</li>";
			}				
			else {
				if(loggedIn == 'y')
					body += "<li id="+id+" onmousedown='reserveSeat(this.id)'>"+id+"</li>";
				else body += "<li id="+id+">"+id+"</li>";
			}
			l = String.fromCharCode(l.charCodeAt() + 1);
		}
		body += "</ul></li>";
	}
	body += "</ul></div>";
	el.innerHTML = body;
}

function configStyle(id, status, color) {
	var el = document.getElementById(id);
	el.style.backgroundColor = color; 
	if(status == 2) 
		el.className += " reserved";
}

function updateSeatMap() {
	window.location = "./index.php";
}

function updateSeatAvailability() {
	req = ajaxRequest();	
	req.onreadystatechange = function () {
	  if(req.readyState === 4 && req.status === 200) {
		  if(req.responseText != "login") {
			  console.log(req.responseText);
			  var fields = req.responseText.split(",");
			  document.getElementById("seatNumbers").innerHTML = "<b>Purchased: </b> "+fields[0]+"&ensp; <b> Reserved: </b>"+fields[1]+" <b>&ensp; Free: </b>"+fields[2];
		  }
		  else window.location = "./login.php?msg=sessionExp";
	  }
	}
	req.open("GET","seats.php",true);
	req.setRequestHeader("X-Requested-With", "XMLHttpRequest"); 
	req.send();  
}

function buySeats() {
	req = ajaxRequest();
	req.onreadystatechange = function () {
	  if(req.readyState === 4 && req.status === 200) {  
		  if(req.responseText == "fail") 
			  window.location = "./index.php?msg=error";
		  
		  else if(req.responseText == "login") 
			  window.location = "./login.php?msg=sessionExp";
			  
		  else if(req.responseText == "success")
			  window.location = "./index.php?msg=success";			  		  
	  }	  
	};
	req.open("GET","buy.php",true);
	req.setRequestHeader("X-Requested-With", "XMLHttpRequest"); 
	req.send();
}

function reserveSeat(id) {
	req = ajaxRequest();	
	req.onreadystatechange = function () {
	  if(req.readyState === 4 && req.status === 200) {
		  var el = document.getElementById("msgText");
		  if(req.responseText == "unreserved") {
			  el.innerHTML = "Seat unreserved";
			  el.className = "success";
			  document.getElementById(id).style.backgroundColor = 'green'; 
			  updateSeatAvailability();
		  }
		  else if(req.responseText == "bought") {
			  el.innerHTML = "Seat has been already purchased!";
			  el.className = "errorMsg";
			  document.getElementById(id).style.backgroundColor = 'red'; 
		  }
		  else if(req.responseText == "login") {
			  window.location.href = "./login.php?msg=sessionExp";
		  }
		  else {
			  el.innerHTML = "Seat reserved";
			  el.className = "success";
			  document.getElementById(id).style.backgroundColor = 'yellow';
			  updateSeatAvailability();
		  }				  
	  }
	};
	req.open("GET","reservation.php?seatId="+id,true);
	req.setRequestHeader("X-Requested-With", "XMLHttpRequest"); 
	req.send();
}