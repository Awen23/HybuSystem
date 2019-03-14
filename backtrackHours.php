<?php
//checks the user is logged in first 
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
					<li class = "nav-text"><a href="Events.php">Events</a></li>
					<li class = "nav-text"><a href="Tracking.php" style="color:#E30713">Tracking</a></li>
					<li class = "nav-text"><a href="Statistics.php">Statistics</a></li>
					<li class = "nav-text"><a href="Registers.php">Registers</a></li>
					<li class = "nav-text"><a href="Volunteers.php">Volunteers</a></li>
				</ul>
			</div>

			</div>
		</nav>

		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">

				<h3>Backtrack Hours:</h3>
				
				<form action="backtrackHours.php" method="post" class="form-horizontal">

					<div class="form-group">
						<label class="col-sm-4 control-label">First Name:</label>
						<div class="col-sm-8"><input type="text" class="form-control" name="FName"></input></div>
					</div>

					<div class="form-group">
						<label class="col-sm-4 control-label">Last Name:</label>
						<div class="col-sm-8"><input type="text" class="form-control" name="LName"></input></div>
					</div>

					<!-- Validation:
					Checks first and last name correlate to a member in PHP 
					-->

					<div class="form-group">
						<label class="col-sm-4 control-label">Hours:</label>
						<div class="col-sm-8"><input type="number" class="form-control" name="Hours" min="0" max="999" step="0.25"></input></div>
						<!-- Validation:
						Must be a number 
						Minimum of 0 
						Maximum of 999
						Goes up in increments of 0.25 (i.e. "1.3" is invalid)
						Required field -->
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4">Type:</label>
						<div class="col-sm-8"><label class="radio-inline"><input type="radio" name="Type" value="P" required>Practical</label>
						<label class="radio-inline"><input type="radio" name="Type" value="T" required>Training</input></div></label>
						<!-- Validation:
						Must be either practical or training
						Required field -->
					</div>

					<div class="form-group">
						<center>
							<input type="submit" class="btn btn-default" name="submit">
						</center>
					</div>

				</form>
<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//upon submitting the form 
if(isset($_POST['submit'])) {
	//getting the values from the form 
	$FName = $_POST['FName'];
	$LName = $_POST['LName'];
	$hours = $_POST['Hours'];
	$type = $_POST['Type'];

	//sql to get the MemID of a member with the first and last name from the form 
	$sqlGetMemId = "SELECT MemID FROM members WHERE FName = '$FName' AND LName = '$LName'";

	if(mysqli_query($con, $sqlGetMemId)) {
		//result of the query 
		$memResult = mysqli_query($con, $sqlGetMemId);

		//checking how many results the query came up with 
		switch(mysqli_num_rows($memResult)) {
			//if no member 
            case 0:
       		
            echo "Member doesn't exist";
            break;

            //if only one member 
            case 1:
            //getting the MemID
            $memRow = mysqli_fetch_row($memResult);
            $memID = $memRow[0];

            //to check if the backtracking type already exists 
            $sqlCheckType = "SELECT TypeID FROM eventtype WHERE Name='Backtrack Hours' AND MinPeo='0' AND Reac='N'";

            if(mysqli_query($con, $sqlCheckType)) {
            	$type = mysqli_query($con, $sqlCheckType);

            	//if type doesn't already exist, make a new one 
            	if(mysqli_num_rows($type) === 0) {
            		$sqlCreateEventType = "INSERT INTO eventtype(Name, MinPeo, Reac) VALUES ('Backtrack Hours', '0', 'N')";

            		if(mysqli_query($con, $sqlCreateEventType)) {
            			//typeID is the one of the type just created
            			$typeID = mysqli_insert_id($con);
            		} else {
            			echo "Error: " . $sqlCreateEventType . "<br>" . mysqli_error($con);
            		}
            	} else {
            		//if the type already exists, use the TypeID from that one 
            		$typeRow = mysqli_fetch_row($type);
            		$typeID = $typeRow[0];
            	}

            //sql to create the event for their backtracked hours 
            $sqlCreateEvent = "INSERT INTO events(TypeID, PracTrain, StartTime, EndTime, Date, Location, Finished, ActualHours) VALUES ('$typeID', '$typeID', '00:00:00', '00:00:00', '0000-00-00', 'Backtracking', 'Y', '$hours')";

            if(mysqli_query($con, $sqlCreateEvent)) {
            	//getting the EventID of the event just created
            	$eventID = mysqli_insert_id($con);
            	//sql to add the member to the activity database for this activity 
            	$sqlCreateActivity = "INSERT INTO activity(EventID, MemID, Confirmed, Proficiency) VALUES ('$eventID', '$memID', 'Y', 'G')";

            	if(mysqli_query($con, $sqlCreateActivity)) {
            		//alerting the user that the hours have been added 
            		echo '<script>alert("Hours Added");
            			window.location.href="backtrackHours.php";</script>';
            	} else {
            		"Error: " . $sqlCreateActivity . "<br>" . mysqli_error($con);
            	}

            } else {
            	echo "Error: " . $sqlCreateEvent . "<br>" . mysqli_error($con);
            }

            } else {
            	echo "Error: " . $sqlCheckType . "<br>" . mysqli_error($con);
            }

            break; 

            //if multiple members 
            default:
            echo "Error: Multiple members with same name, please make all first & last names unique";
        }
    }

}

?>
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>