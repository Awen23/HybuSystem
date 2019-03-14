<?php
//checks if they're logged in as a leader 
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
  	<title>Register</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="http://localhost/hybu/hybustyle.css" rel="stylesheet" type="text/css">
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
					<li class = "nav-text"><a href="Statistics.php">Statistics</a></li>
					<li class = "nav-text"><a href="Registers.php" style="color:#E30713">Registers</a></li>
					<li class = "nav-text"><a href="Volunteers.php">Volunteers</a></li>
				</ul>
			</div>
				

			</div>
		</nav>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite"> <br>
				<text>Please enter the contents of the meeting: </text>
				<form method="post" action="RegisterDescription.php">
					<textarea maxlength="255" rows="4" name="regDesc" class="form-control" style="width:100%;"></textarea> 
					<br><br>
					<input type="submit" name="submit" class="btn btn-default" style="width:100%;"></input>
					<br><br>
				</form>

<?php

unset($_SESSION['Deleted']);
    //connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	if(isset($_POST['submit'])) {
		//posting values from the page
		$desc = $_POST['regDesc'];
		$regID = $_SESSION["RegID"];

		//to make sure ' signs and stuff don't mess with adding it to the database later on 
		$descForSql = mysqli_escape_string($con, $desc);

		//sql to update the register in the database to include the description 
		$sqlDesc = "UPDATE registers 
					SET Description = '$descForSql'
					WHERE RegID = $regID;";

		if(mysqli_query($con, $sqlDesc)) {
				//popup if query goes through, then goes back to start of registers
                echo '<script>alert("Register submitted!");</script>';
                echo '<script>window.location.href = "Registers.php";</script>';
            } else {
                echo "Error: " . $sqlDesc . "<br>" . mysqli_error($con);
            }
            
	}

?>
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>