<?php
//checks if the user is a leader before allowing them to access the page 
session_start();
if(isset($_SESSION['Type'])) {
	if($_SESSION['Type'] === "V") {
		echo '<script>window.location.href="volunteersEvents.php"</script>';
	} 
} else {
	echo '<script>window.location.href="loginScreen.php"</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  	<title>Events</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="hybustyle.css" rel="stylesheet" type="text/css">
  </head>
  <body> 
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class = "navbar-header">
					<button type="button" class = "navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
						<span class = "icon-bar"></span>
						<span class = "icon-bar"></span>
						<span class = "icon-bar"></span>
					</button>
					<a class = "navbar-brand" href="http://hybu.co.uk/"><img src = "http://hybu.co.uk/newsite/wp-content/uploads/2015/11/hybu_logo.png" alt = "Hybu Logo" height = 50></a>
					<form class="logout" method="post" action="loginScreen.php">
						<button name="logout" class="btn btn-default btn-sm">
	          				<span class="glyphicon glyphicon-log-out"></span> Log out
	        			</button>
	        		</form>
				</div>
			<div class = "collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav navbar-right">
					<li class = "nav-text"><a href="Events.php" style="color:#E30713">Events</a></li>
					<li class = "nav-text"><a href="Tracking.php">Tracking</a></li>
					<li class = "nav-text"><a href="Statistics.php">Statistics</a></li>
					<li class = "nav-text"><a href="Registers.php">Registers</a></li>
					<li class = "nav-text"><a href="Volunteers.php">Volunteers</a></li>
				</ul>
			</div>
				

			</div>
		</nav>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">

			<h5>Edit Event(s) on:</h5>

<script>
	//puts the values for that event in the textbox - called upon clicking the text 
	function insertIntoText(EventID, date, sTime, eTime, location, pracTrain) {
		document.getElementById("eventID").value = EventID;
		document.getElementById("date").value = date;
		document.getElementById("startTime").value = sTime;
		document.getElementById("endTime").value = eTime;
		document.getElementById("location").value = location;
		document.getElementById(pracTrain).checked = true;
	}
</script>

<div class="col-sm-4">

<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//gets the date for today 
$today = date('Y-m-d', time());
//sql to get all events in the future 
$sqlGetAllEvents = "SELECT e.EventID, et.Name, e.Date, e.StartTime, e.EndTime, e.Location, e.PracTrain FROM events as e 
					INNER JOIN eventtype as et ON et.TypeID = e.TypeID
					WHERE e.Date >= '$today'
					ORDER BY Date";

if(mysqli_query($con, $sqlGetAllEvents)) {
	//gets the result for the query 
	$allEvents = mysqli_query($con, $sqlGetAllEvents);

	//if there's no events 
	if(mysqli_affected_rows($con) === 0) {
		echo "No future events found";

	//if there's only one event 
	} elseif(mysqli_affected_rows($con) === 1) {
		$oneEvent = mysqli_fetch_row($allEvents);

		//setting up the tag 
		echo '<text onClick="insertIntoText(' . "'" . $oneEvent[0] . "', '" . $oneEvent[2] . "', '" . $oneEvent[3] . "', '" . $oneEvent[4] . "', '" . $oneEvent[5] . "', '" . $oneEvent[6] . "')" . '">';
		//actual output
		echo 	'<b>Type: </b>' . $oneEvent[1] . 
				'<br><b>Date: </b>' . $oneEvent[2] . 
				'<br><b>Start Time: </b>' . $oneEvent[3] . 
				'<br><b>End Time: </b> ' . $oneEvent[4] . 
				'<br><b>Location: </b> ' . $oneEvent[5] . 
				'<br><b>Practical/Training: </b> ' . $oneEvent[6] . '<br><br><br>';
		//closing the tag 
		echo '</text>';

		//if there's multiple events 
		} else {
		foreach($allEvents as $event) {
			//setting up the tag 
			echo '<text onClick="insertIntoText(' . "'" . $event['EventID'] . "', '" . $event['Date'] . "', '" . $event['StartTime'] . "', '" . $event['EndTime'] . "', '" . $event['Location'] . "', '" . $event['PracTrain'] . "')" . '">';
			//actual output
			echo 	'<b>Type: </b>' . $event['Name'] . 
					'<br><b>Date: </b>' . $event['Date'] . 
					'<br><b>Start Time: </b>' . $event['StartTime'] . 
					'<br><b>End Time: </b> ' . $event['EndTime'] . 
					'<br><b>Location: </b> ' . $event['Location'] . 
					'<br><b>Practical/Training: </b> ' . $event['PracTrain'] . '<br><br><br>';
			//closing the tag 
			echo '</text>';
			}
		}
	} else {
	echo "Error: " . $sqlGetAllEvents . "<br>" . mysqli_error($con);
	}




?>

</div>

<div class="col-sm-8">
<h5>Edit Event Details:</h5>

				<form class="form-horizontal" method="post" action="editEvent.php">

				<div class="form-group">
					<label class="control-label col-sm-4">EventID:</label>
					<div class="col-sm-8"><input type="text" class="form-control" name="EventID" id="eventID" readonly></div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-sm-4">Date:</label>
					<div class="col-sm-8"><input type="date" min="2018-01-01" class="form-control" id="date" name="date" required></div>
					<!-- Validation:
					Must be in a date format
					Minimum date of 1st Jan 2018 
					Required field -->
				</div>

				<div class="form-group">
					<label class="control-label col-sm-4">Start Time:</label>
					<div class="col-sm-8"><input type="time" class="form-control" id="startTime" name="startTime" min="06:00" required></input></div>
					<!-- Validation:
					Must be in the time format
					Minimum time of 6am 
					Required field -->
				</div>

				<div class="form-group">
					<label class="control-label col-sm-4">End Time:</label>
					<div class="col-sm-8"><input type="time" class="form-control" id="endTime" name="endTime" min="07:00" required></input></div>
					<!-- Validation:
					Must be in the time format
					Minimum time of 7am 
					Required field -->
				</div>

				<div class="form-group">
					<label class="control-label col-sm-4">Location:</label>
					<div class="col-sm-8"><input type="text" class="form-control" id="location" name="location" required></input></div>
					<!-- Validation:
					Required field 
					-->
				</div>

				<div class="form-group">
					<div class="col-sm-4"></div>
					<div class="col-sm-4">
						<label class="radio-inline"><input type="radio" name="PracTrain" id="P" value="P" required>Practical</label>
					</div>
					<div class="col-sm-4">
						<label class="radio-inline"><input type="radio" name="PracTrain" id="T" value="T" required>Training</label>
					</div>
					<!-- Validation:
					Must be either practical or training
					Required field -->
				</div>

				<div class="form-group">
					<center>
						<input type="submit" name="submit" class="btn btn-default"></input>
						<button type="button" onClick="confirmDelete()" class="btn btn-default">Delete Event</button>
						<input type="submit" formaction="editAttendingDate.php" class="btn btn-default" value="Edit Attending">
						<input type="submit" id="DeleteButton" name="delete" class="btn btn-default" style="display:none;">
					</center>
				</div>
				
				</form>

<script>

	function confirmDelete() {
		if(confirm("Are you sure you want to delete this event?")) {
		//clicks an invisible delete button in order to submit the request to php
		document.getElementById("DeleteButton").click();
	}
	}

</script>

<?php

//if you click submit on the edits to an event 
if(isset($_POST['submit'])) {

	//gets all the values from the page 
	$eventID = $_POST['EventID'];
	$date = $_POST['date'];
	$startTime = $_POST['startTime'];
	$endTime = $_POST['endTime'];
	$location = $_POST['location'];
	$pracTrain = $_POST['PracTrain'];

	//sql to add the changes to the database 
	$sqlEditRow = "UPDATE events SET Date = '$date', StartTime = '$startTime', EndTime = '$endTime', Location = '$location', PracTrain = '$pracTrain' WHERE EventID = '$eventID'";

	if(mysqli_query($con, $sqlEditRow)) {
		//alerts the user that the changes have been made and sends them to the same page so that the details on the side of the page display as the new ones 
		echo '<script>alert("Changes made!");
				window.location.href="editEvent.php";</script>';
	} else {
		echo "Error: " . $sqlEditRow . "<br>" . mysqli_error($con);
	}
}

//upon confirming deletion 
if(isset($_POST['delete'])) {
	//getting the eventID of the event selected 
	$eventID = $_POST['EventID'];

	//sql to delete all associated activity and the event itself 
	$sqlDeleteActivity = "DELETE FROM activity WHERE EventID = '$eventID';";
	$sqlDeleteEvent = "DELETE FROM events WHERE EventID = '$eventID'";

	//executing the queries
	if(mysqli_query($con, $sqlDeleteActivity) AND mysqli_query($con, $sqlDeleteEvent)) {
		//alerting the user that the event has been deleted and redirecting to the same page so it disappears from the side of the page 
		echo '<script>alert("Event Deleted!");
						window.location.href="editEvent.php";</script>';
	} else {
		echo "Error: " . $sqlDeleteEvent . "<br>" . mysqli_error($con);
	}
}

?>
</div>
			</div> 
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>