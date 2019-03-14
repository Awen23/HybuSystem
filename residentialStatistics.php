<?php
//checks the user is logged in 
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
  	<title>Statistics</title>
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
					<li class = "nav-text"><a href="Tracking.php">Tracking</a></li>
					<li class = "nav-text"><a href="Statistics.php" style="color:#E30713">Statistics</a></li>
					<li class = "nav-text"><a href="Registers.php">Registers</a></li>
					<li class = "nav-text"><a href="Volunteers.php">Volunteers</a></li>
				</ul>
			</div>
				

			</div>
		</nav>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">
				<br>
				<div class="btn-group btn-group-justified">
    				<a href="Statistics.php" class="btn btn-primary mini-nav">Overall Statistics</a>
    				<a href="individualStatistics.php" class="btn btn-primary mini-nav">Personal Statistics</a>
    				<a href="filterStatistics.php" class="btn btn-primary mini-nav">Filters</a>
    				<a href="residentialStatistics.php" class="btn btn-primary mini-nav" style="background-color: #E30713; color:white;">Residentials</a>
  				</div>

  				<h3>Residentials Tracked:</h3>

  				<table class="table table-bordered">
  					<thead>
	  					<tr>
	  						<th>Date</th>
	  						<th>Location</th>
	  						<th>Attended</th>
	  					</tr>
  					</thead>
<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//sql to get all residential events 
$sqlGetEvents = "SELECT EventID, Date, Location FROM events as e
				INNER JOIN eventtype as et ON et.TypeID = e.TypeID
				WHERE et.Name = 'Residential' OR et.Name = 'Residentials'";


if(mysqli_query($con, $sqlGetEvents)) {
	$allEvents = mysqli_query($con, $sqlGetEvents);

	switch(mysqli_num_rows($allEvents)) {
		//if no results
		case 0:
		echo "No Residentials Found";
		break;

		//if one result, get the one row and output details 
		case 1:
		$event = mysqli_fetch_row($allEvents);
		echo "<tr>
				<td>". $event[1] . "</td>
				<td>" . $event[2] . "</td>
				<td>";

		//sql to get people who attended the one residential 
		$sqlGetAttended = "SELECT m.FName, m.LName
                            FROM members as m 
                            INNER JOIN activity as a ON m.MemID = a.MemID 
                            INNER JOIN events as e ON e.EventID = a.EventID
                            INNER JOIN eventtype as et ON et.TypeID = e.TypeID
                            WHERE et.Name = 'Residential' OR et.Name = 'Residentials'";

   		if(mysqli_query($con, $sqlGetAttended)) {	
   			//getting result of attended query and outputting all names 
   			$allAttended = mysqli_query($con, $sqlGetAttended);

   			foreach($allAttended as $list) {
   				echo $list['FName'] . " " . $list['LName'] . "<br>";
   			}

   			echo "</tr>";
   		} else {
   			echo "Error: " . $sqlGetAttended . "<br>" . mysqli_error($con);
   		}

		break;

		default:
		//going through each residential 
		foreach($allEvents as $event) {
			echo "<tr>
				<td>". $event['Date'] . "</td>
				<td>" . $event['Location'] . "</td>
				<td>";
		$eventID = $event['EventID'];

		//getting attended for that specific residential using the EventID
		$sqlGetThisAttended = "SELECT m.FName, m.LName
                            FROM members as m 
                            INNER JOIN activity as a ON m.MemID = a.MemID 
                            INNER JOIN events as e ON e.EventID = a.EventID
                            INNER JOIN eventtype as et ON et.TypeID = e.TypeID
                            WHERE e.EventID = '$eventID' AND (et.Name = 'Residential' OR et.Name = 'Residentials')";

   		if(mysqli_query($con, $sqlGetThisAttended)) {
   			//getting the result and outputting all member names 
   			$allAttended = mysqli_query($con, $sqlGetThisAttended);

   			foreach($allAttended as $list) {
   				echo $list['FName'] . " " . $list['LName'] . "<br>";
   			}

   			echo "</tr>";
   		} else {
   			echo "Error: " . $sqlGetThisAttended . "<br>" . mysqli_error($con);
   		}
		}

	}
}

?>
  				</table>
				
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>