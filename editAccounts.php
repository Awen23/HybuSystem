<?php
//checks is logged in as a leader
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
					<li class = "nav-text"><a href="Statistics.php">Statistics</a></li>
					<li class = "nav-text"><a href="Registers.php">Registers</a></li>
					<li class = "nav-text"><a href="Volunteers.php" style="color:#E30713">Volunteers</a></li>
				</ul>
			</div>
				

			</div>
		</nav>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">
				<br>
				<div class="btn-group btn-group-justified">
    				<a href="Volunteers.php" class="btn btn-primary mini-nav">Add/Edit Volunteers</a>
    				<a href="individualStatistics.php" class="btn btn-primary mini-nav" style="background-color: #E30713; color:white;">Edit Volunteer Accounts</a>
  				</div>

  				<div class="col-sm-6">
  					<h5>Edit an account:</h5>

  					<form action="editAccounts.php" method="post" class="form-horizontal">

  						<div class="form-group">
  							<label class="col-sm-4 control-label">Username:</label>
  							<div class="col-sm-8"><input type="text" class="form-control" name="Username" id="Username" readonly></div>
  						</div>

  						<div class="form-group">
  							<label class="col-sm-4 control-label">Password:</label>
  							<div class="col-sm-8"><input type="text" class="form-control" name="Password" required></div>
  						</div>

  						<div class="form-group">
  							<center>
  								<input type="submit" name="submit" class="btn btn-default">
  							</center>
  						</div>
  					</form>
  				</div>

  				<div class="col-sm-6">
  					<h5>Accounts:</h5>
<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//sql to get all of the accounts
$sqlGetAccounts = "SELECT Username, Password FROM login";

if(mysqli_query($con, $sqlGetAccounts)) {
	$allAccounts = mysqli_query($con, $sqlGetAccounts);

	//sets i as 0, for use as the ID of the text 
	$i = 0;

	foreach($allAccounts as $account) {
		//outputs username with putin(username for user, id of element) onClick
		echo '<text onClick="' . "putIn('" . $account['Username'] . "', '" . $i . "'" . ')"' . ' id="'. $i . '">' . $account['Username'] . "</text><br>";
		$i++;
	}

} else {
	echo "Error: " . $sqlGetAccounts . "<br>" . mysqli_error($con);
}


if(isset($_POST['submit'])) {
	//gets username and password from form
	$username = $_POST['Username'];
	$password = $_POST['Password'];

	//sql to get the relevant record
	$sqlGetRecord = "SELECT loginID FROM login WHERE Username = '$username'";

	if(mysqli_query($con, $sqlGetRecord)) {
		//getting the loginID from the result
		$loginIDResult = mysqli_query($con, $sqlGetRecord);
		$loginIDRow = mysqli_fetch_row($loginIDResult);
		$loginID = $loginIDRow[0];

		//sql to update the password, setting first as Y so user is prompted to enter a new password next time around 
		$sqlUpdatePassword = "UPDATE login SET Password = '$password', First = 'Y' WHERE loginID = '$loginID'";

		if(mysqli_query($con, $sqlUpdatePassword)) {
			//alerts user password has been changed
			echo '<script>alert("Password Changed!")</script>';
		} else {
			echo "Error: " . $sqlUpdatePassword . "<br>" . mysqli_error($con);
		}

	} else {
		echo "Error: " . $sqlGetRecord . "<br>" . mysqli_error($con);
	}
}

?>

<script>

	//puts username into the textbox and makes selected text bold
	function putIn(Username, ID) {
		document.getElementById("Username").value = Username;
		allText = document.getElementsByTagName("Text");
		for(var i=0; i < allText.length; i++) {
			document.getElementsByTagName("Text")[i].style.fontWeight = "normal";
		}
		document.getElementById(ID).style.fontWeight = "900";
	}

	//called upon clicking to view all accounts, switches to button which says hide all accounts 
	function switchHide() {
		document.getElementById("allAccounts").innerHTML = '<input type="submit" name="hide" value="Hide all generated accounts" class="btn">'
	}
</script>
  				</div>
				
				<div class="col-sm-12">
					
					<form class="form-horizontal" method="post" action="editAccounts.php">
						<center>
							<div id="allAccounts"><input type="submit" name="view" value="View all generated accounts" class="btn"></div>
						</center>
					</form>

					<div id="phpOutput">
<?php

//if they click to view all generated accounts 
if(isset($_POST['view'])) {
	//sql to get all the accounts where first is true (aka they haven't changed their passwords themselves)
	$sqlGetAccounts = "SELECT Username, Password FROM login WHERE First = 'Y'";

	if(mysqli_query($con, $sqlGetAccounts)) {
		$allAccounts = mysqli_query($con, $sqlGetAccounts);

		//outputs all usernames and passwords
		foreach($allAccounts as $account) {
			echo "Username: " . $account['Username'] . "<br>Password: " . $account['Password'] . "<br><br><br>";
		}

		//changes the button to one which says hide 
		echo "<script>switchHide();</script>";
	} else {
		echo "Error: " . $sqlGetAccounts . "<br>" . mysqli_error($con);
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