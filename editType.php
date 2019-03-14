<?php
//checks the member is logged in 
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

			<h5>Select Type:</h5>

<script>
	//puts the values for that type into the form 
	function insertIntoText(TypeID, name, minPeo, reac) {
		document.getElementById("TypeID").value = TypeID;
		document.getElementById("Name").value = name;
		document.getElementById("minPeo").value = minPeo;
		document.getElementById(reac).checked = true;
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

//to get all of the existing types 
$sqlGetAllTypes = "SELECT TypeID, Name, MinPeo, Reac FROM eventtype";

if(mysqli_query($con, $sqlGetAllTypes)) {
	$allTypes = mysqli_query($con, $sqlGetAllTypes);

	if(mysqli_affected_rows($con) === 0) {
		echo "No types found";
	} elseif(mysqli_affected_rows($con) === 1) {
		$type = mysqli_fetch_row($allTypes);

		//setting up the tag - insertIntoText references function above with all the details to be put in the textboxes 
		echo '<text onClick="insertIntoText(' . "'" . $type[0] . "', '" . $type[1] . "', '" . $type[2] . "', '" . $type[3] . "')" . '">';
		//actual output
		echo 	'<b>Name: </b>' . $type[1] . 
				'<br><b>Minimum People: </b>' . $type[2] . 
				'<br><b>Reaccuring: </b> ' . $type[3] . '<br><br><br>';
		//closing the tag 
		echo '</text>';
		} else {
		foreach($allTypes as $type) {
			//setting up the tag - insertIntoText references function above with all the details to be put in the textboxes
			echo '<text onClick="insertIntoText(' . "'" . $type['TypeID'] . "', '" . $type['Name'] . "', '" . $type['MinPeo'] . "', '" . $type['Reac'] . "')" . '">';
			//actual output
			echo 	'<b>Type: </b>' . $type['Name'] . 
					'<br><b>Minimum People: </b>' . $type['MinPeo'] . 
					'<br><b>Reaccuring: </b>' . $type['Reac'] . '<br><br><br>';
			//closing the tag 
			echo '</text>';
			}
		}
	} else {
	echo "Error: " . $sqlGetAllTypes . "<br>" . mysqli_error($con);
	}




?>

</div>

<div class="col-sm-8">
<h5>Edit Type Details:</h5>

				<form class="form-horizontal" method="post" action="editType.php">

				<div class="form-group">
					<label class="control-label col-sm-4">TypeID:</label>
					<div class="col-sm-8"><input type="text" class="form-control" name="TypeID" id="TypeID" readonly></div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-4">Name:</label>
					<div class="col-sm-8"><input type="text" class="form-control" name="Name" id="Name" required></div>
					<!-- Validation: 
					Required field
					-->
				</div>

				<div class="form-group">
					<label class="control-label col-sm-4">Minimum People:</label>
					<div class="col-sm-8"><input type="number" class="form-control" min="1" name="minPeo" id="minPeo" required></div>
					<!-- Validation:
					Must be a number
					Minimum number of 1 
					Required field -->
				</div>

				<div class="form-group">
					<label class ="control-label col-sm-4">Reaccuring?</label>
						<div class="col-sm-4">
							<label class="radio-inline"><input type="radio" id="Y" name="Reac" value="Y">Yes</label>
						</div>
						<div class="col-sm-4">
							<label class="radio-inline"><input type="radio" id="N" name="Reac" value="N">No</label>
						</div>
						<!-- Validation:
						Must be either Yes or No
						Required field -->
				</div>

				<div class="form-group">
					<center>
						<input type="submit" name="submit" class="btn btn-default"></input>
					</center>
				</div>
				
				</form>

<?php

//executed upon clicking submit on the form 
if(isset($_POST['submit'])) {

	//getting the values from the form 
	$typeID = $_POST['TypeID'];
	$name = $_POST['Name'];
	$minPeo = $_POST['minPeo'];
	$reac = $_POST['Reac'];

	//to put the new values in 
	$sqlEditRow = "UPDATE eventtype SET Name = '$name', MinPeo = '$minPeo', Reac = '$reac' WHERE TypeID = '$typeID'";

	if(mysqli_query($con, $sqlEditRow)) {
		//alerts user that the changes were made and reloads page so changes can be seen on the left hand side 
		echo '<script>alert("Changes made!");
				window.location.href="editType.php";</script>';
	} else {
		echo "Error: " . $sqlEditRow . "<br>" . mysqli_error($con);
	}
}

?>
</div>
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>