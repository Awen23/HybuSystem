<?php
//checks user is logged in before continuing 
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
<?php
//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}
	
//getting the MemID from the session - put in on individualStatistics.php
$memID = $_SESSION['MemID'];

//getting the name that correlates to this MemID
$sqlGetName = "SELECT FName, LName FROM members WHERE MemID = '$memID'";

if(mysqli_query($con, $sqlGetName)) {
	//getting the result and then outputting the name in a title 
	$nameResult = mysqli_query($con, $sqlGetName);
	$nameRow = mysqli_fetch_row($nameResult);

	echo "<h3>All events tracked for: " . $nameRow[0] . " " . $nameRow[1] . "</h3>";
} else {
	echo "Error: " . $sqlGetName . "<br>" . mysqli_error($con);
}

?>
				<br><br>
				<table class="table table-bordered col-sm-12">
					<thead>
						<tr>
							<th>Name</th>
							<th>Date</th>
							<th>Hours</th>
						</tr>
					</thead>
				
<?php

//getting all of the activities attended by the user 
$sqlGetData = "SELECT m.FName, m.LName, et.Name, e.Date, e.ActualHours
				FROM activity as a
				INNER JOIN members as m ON m.MemID = a.MemID
				INNER JOIN events as e ON e.EventID = a.EventID
				INNER JOIN eventtype as et ON e.TypeID = et.TypeID
				WHERE a.MemID = '$memID' AND a.Confirmed = 'Y'";

if(mysqli_query($con, $sqlGetData)) {
	//getting the result of the query and outputting it all in the table 
	$allData = mysqli_query($con, $sqlGetData);

	foreach($allData as $row) {
		echo 	'<tr> 
					<td>' . $row['Name'] . '</td>
					<td>' . $row['Date'] . '</td>
					<td>' . $row['ActualHours'] . '</td>
				</tr>';
	}

} else {
	echo "Error: " . $sqlGetData . "<br>" . mysqli_error($con);
}

?>
</table>
				
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>