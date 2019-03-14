<?php
//checks if the user has correct permissions to access the page 
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
			<div class = "callapse navbar-collapse" id="myNavbar">
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
				
				<form class="form-horizontal" method="post" action="newEvent.php">

					<div class="col-sm-6">
						<h3> New Event: </h3>
<?php

//getting the dates from the session - the dates which were in the textbox on the events page
$dates = $_SESSION['dates'];

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//if there's dates, then output a read only textbox which has them dates in 
If($dates != null) {
	echo '<div class = "form-group">
				<label class = "control-label col-sm-4">Date:</label>
				<div class = "col-sm-8"><input type="text" class="form-control" value="' . $dates . '" name="dates" readonly required></div>
			</div>';

	//to make them into an array (seperated by , in case of more than one date picked)
	$dates = explode(", ", $dates);
} else {
	//if there's no dates picked, show a date picker 
	echo '<div class = "form-group">
				<label class = "control-label col-sm-4">Date:</label>
				<div class = "col-sm-8"><input type="date" min="2018-01-01" class="form-control" name="dates" required></div>
			</div>';
}

//creating the dropdown list for types 
echo '<div class="form-group">
		<label class="control-label col-sm-4">Type:</label>
		<div class="col-sm-8"> <select class="form-control" id="type" name="typeDropdown"></div>
		<option></option>';

//get all reaccuring types for the dropdown list 
$sqlTypes = "SELECT TypeID, Name FROM eventtype WHERE Reac = 'Y'";

if (mysqli_query($con, $sqlTypes)) {
	//result of query
	$types = mysqli_query($con, $sqlTypes);
	//going through each type and adding it to the dropdown 
	foreach ($types as $type) {
		echo '<option value="' . $type["TypeID"] . '">' . $type["Name"] . '</option>';
	}

} else {
	echo "Error: " . $sqlTypes . "<br>" . mysqli_error($con);
}

//ends the dropdown menu 
echo "</select></div>";

?>
						
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4">Start Time:</label>
						<div class="col-sm-8"><input type="time" class="form-control" name="startTime" min="06:00" required></input></div>
						<!-- Validation:
						Must be in the format of a time
						Minimum time is 6am
						Required field -->
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4">End Time:</label>
						<div class="col-sm-8"><input type="time" class="form-control" name="endTime" min="07:00" required></input></div>
						<!-- Validation:
						Must be in the format of a time
						Minimum time is 7am
						Required field -->
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4">Location:</label>
						<div class="col-sm-8"><input type="text" class="form-control" name="location" required></input></div>
						<!-- Validation:
						Required field -->
					</div>

					<div class="form-group">
						<div class="col-sm-4"></div>
						<div class="col-sm-4">
							<label class="radio-inline"><input type="radio" name="PracTrain" value="P" required>Practical</label>
						</div>
						<div class="col-sm-4">
							<label class="radio-inline"><input type="radio" name="PracTrain" value="T" required>Training</label>
						</div>
						<!-- Validation:
						Must be either practical or training
						Required field -->
					</div>

					</div>

					<div class="col-sm-6">
						<br><br><br>
						<h5>New Event Type:</h5>

						<div class="form-group">
							<label class="control-label col-sm-4">Type:</label>
							<div class="col-sm-8"><input type="text" class="form-control" name="typeText"> </input> </div>
							<!-- Validation:
							New event type OR type dropdown must be filled, required if there's nothing in the type dropdown -->
						</div>

						<div class="form-group">
							<label class="control-label col-sm-4">Minimum People:</label>
							<div class="col-sm-8"><input type="number" class="form-control" name="minPeople" min="1"></input> </div>
							<!-- Validation:
							Must be a number
							Minimum number of 1 
							-->
						</div>

						<div class="form-group">
								<label class ="control-label col-sm-4">Reaccuring?</label>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="Reacc" value="Y">Yes</label>
								</div>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="Reacc" value="N">No</label>
								</div>
								<!-- Validation:
								Must be either Yes or No
								-->
						</div>


					</div>

					<div class="col-sm-12">
						<center>
						<div class="form-group">
							<input type="submit" name="submit" class="btn btn-default"></input>
						</div>
						</center>
					</div>

				</div>

				</form>
<?php

//upon submitting the form 
if(isset($_POST['submit'])) {
	//getting the values from the type dropdown and type textbox
	$typeDrop = $_POST['typeDropdown'];
	$typeText = $_POST['typeText'];
	//array for later to add all EventIDs currently being created 
	$EventIDs = [];

	//checks if either the dropdown or the type text is filled 
	if($typeDrop !== "" XOR $typeText !== "") {
		
		//checks if the type drop is filled, in which case the type is the one in the dropdown (dropdown posting contains the ID of the type)
		if($typeDrop !== "") {
			$type = $typeDrop;
		} else {
			//creates a new type if the type text is the one that's filled 
			$minPeo = $_POST['minPeople'];
			$reaccuring = $_POST['Reacc'];

			//creates the new type 
			$sqlNewType = "INSERT INTO eventtype(Name, MinPeo, Reac) VALUES ('$typeText', '$minPeo', '$reaccuring')";

			if(mysqli_query($con, $sqlNewType)) {
				//makes type the ID of the type just created
				$type = mysqli_insert_id($con);
			} else {
				echo "Error: " . $sqlNewType . "<br>" . mysqli_error($con);
			}
		}

		//gets the rest of the values from the form 
		$startTime = $_POST['startTime'];
		$endTime = $_POST['endTime'];
		$location = $_POST['location'];
		$PracTrain = $_POST['PracTrain'];
		
		//if the dates is an array aka there's multiple dates 
		if(gettype($dates) === "array") {
			//go through all the events and add them 
			foreach($dates as $date) {
				$sqlNewEvent = "INSERT INTO events(TypeID, PracTrain,StartTime, EndTime, Date, Location) VALUES ('$type', '$PracTrain', '$startTime', '$endTime', '$date', '$location')";
				if(mysqli_query($con, $sqlNewEvent)) {
					//put the EventID onto the array of EventIDs that have just been created 
					array_push($EventIDs, mysqli_insert_id($con));
				} else {
					echo "Error: " . $sqlNewEvent . "<br>" . mysqli_error($con);
				}
			}

			//puts the EventIDs into the session 
			$_SESSION['EventID'] = $EventIDs;

		} else {
			//gets the date from the form 
			$date = $_POST['dates'];
			//sql to add the new event
			$sqlNewEvent = "INSERT INTO events(TypeID, PracTrain,StartTime, EndTime, Date, Location) VALUES ('$type', '$PracTrain', '$startTime', '$endTime', '$date', '$location')";

			if(mysqli_query($con, $sqlNewEvent)) {
				//puts the eventID in the array then into the session 
				array_push($EventIDs, mysqli_insert_id($con));
				$_SESSION['EventID'] = $EventIDs;
			} else {
				echo "Error: " . $sqlNewEvent . "<br>" . mysqli_error($con);
			}
		}

		//redirects you to the page to add members 
		echo '<script>window.location.href="addMembers.php"</script>';
	} else {
		//if the XOR came out as false and either neither or both the dropdown type and type text field were filled 
		echo '<script>window.alert("Please fill in either the dropdown field or the text field")</script>';
	}

}

?>
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>