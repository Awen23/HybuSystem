<!DOCTYPE html>
	<html lang="en">
  <head>
  	<title>Login</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="hybustyle.css" rel="stylesheet" type="text/css">
  </head>
	<body>
		<div class="container-fluid" style="height:90px;"> <a class = "navbar-brand" href="http://hybu.co.uk/"> <img src = "http://hybu.co.uk/newsite/wp-content/uploads/2015/11/hybu_logo.png" alt = "Hybu Logo" height = 60></a> </div>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">
				<br>
				<div class="alert alert-info alert-dismissible">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
						If you have forgotten your password, please ask a leader to change it</div>

				<h2>Please Login:</h2>
				
				<form class="form-horizontal" method="post" action="loginScreen.php">
					<div class="form-group">
						<label class="control-label col-sm-4">Username:</label>
						<div class="col-sm-8"><input type="text" name="Username" class="form-control" required></div>
						<!-- Validation:
						Required field
						-->
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4">Password:</label>
						<div class="col-sm-8"><input type="password" name="Password" class="form-control" required></div>
						<!-- Validation:
						Required field
						-->
					</div>

					<div class="form-group">
						<center>
							<input type="submit" name="submit" class="btn btn-default"></input>
						</center>
					</div>
				</form>

<?php
session_start();

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

if(isset($_POST['submit'])) {

	//getting the username and password 
	$username = $_POST['Username'];
	$password = $_POST['Password'];

	//in order to get the users details if they exist 
	$sqlGetUser = "SELECT Type, m.MemID, First, Password FROM members as m
					INNER JOIN login as l ON m.MemID = l.MemID
					WHERE l.Username = '$username'";

	if(mysqli_query($con, $sqlGetUser)) {
		//gets the result
		$user = mysqli_query($con, $sqlGetUser);
		$isUser = mysqli_num_rows($user);

		//if a user does exist 
		if($isUser === 1) {
			$thisUser = mysqli_fetch_row($user);

			//if it's not their first time i.e. the password is changed and hence encrypted 
			if($thisUser[2] === "N") {

				//verifies if the password matches and stores the result
				$isCorrect = password_verify($password, $thisUser[3]);

				//checks if it's right 
				if($isCorrect) {
					//puts the type and MemID in the session so they can access the right pages 
					$_SESSION['Type'] = $thisUser[0];
					$_SESSION['VMemID'] = $thisUser[1];

					if($thisUser[0] === "L") {
						//redirects leaders to the events page
						echo '<script>window.location.href="Events.php"</script>';
					} else {
						//redirects members to the volunteer's events page
						echo '<script>window.location.href="volunteersEvents.php"</script>';
					}
				} else {
					//produces an error if the verify password came out as false 
					echo "Error: Password or username is incorrect";
				}
		
		//if they are new 
		} elseif($thisUser[2] === "Y") {

			//if the password equals the one in the database
			if($password === $thisUser[3]) {
				//puts the type and MemID in the session so they can access the right pages 
				$_SESSION['Type'] = $thisUser[0];
				$_SESSION['VMemID'] = $thisUser[1];
				//takes them to the change password page as they are new 
				echo '<script>window.location.href="changePassword.php"</script>';
			} else {
				//error if the password doesn't match 
				echo "Error: Password or username is incorrect";
			}
		} 

		} else {
			//if their username had no match 
			echo "Error: Password or username is incorrect";
		}

	} else {
		echo "Error: system could not log in"; //no query in the error or else password will be shown 
	}
}

//if they logout from any page
if(isset($_POST['logout'])) {
	//unsetting the type and MemID so they can't go back without logging in again
	unset($_SESSION['Type']);
	unset($_SESSION['VMemID']);
}

?>
				
			</div>
		</div>
		<div id="endGradient"></div>
	</body>
</html>