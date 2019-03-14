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
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">

				<h3>Please Change your Password:</h3>
				
				<form class="form-horizontal" method="post" action="changePassword.php">
					<div class="form-group">
						<label class="control-label col-sm-4">Password:</label>
						<div class="col-sm-8"><input type="password" pattern=".{6,}" oninvalid="this.setCustomValidity('Passwords must be at least 6 characters long')" onchange="this.setCustomValidity('')" name="password1" class="form-control"></div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4">Please Enter Again:</label>
						<div class="col-sm-8"><input type="password" name="password2" class="form-control"></div>
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
	
//checks they are actually in the process of logging in  
if(isset($_SESSION['Type']) AND isset($_SESSION['VMemID'])) {
	
	//upon submitting the form 
	if(isset($_POST['submit'])) {
		//gets the passwords from the form & the MemID from the session 
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		$memID = $_SESSION['VMemID'];

		//if the passwords entered match 
		if($password1 === $password2) {

			//hashes the password for security and inputs into database
			$hashedPass = password_hash($password1, PASSWORD_DEFAULT);
			$sqlChangePassword = "UPDATE login SET Password = '$hashedPass', First = 'N' WHERE MemID = '$memID'";

			if(mysqli_query($con, $sqlChangePassword)) {
				//alerts the user that the password has been changed 
				echo '<script>alert("Password Changed!");</script>';

				//redirects user to appropriate section 
				if($_SESSION['Type'] === "L") {
					echo '<script>window.location.href="Events.php"</script>';
				} else {
					echo '<script>window.location.href="volunteersEvents.php"</script>';
				}

			} else {
				echo "Error: Password couldn't be changed";
			}

		} else {
			echo "Error: Passwords don't match";
		}
	}

} else {
	//sends them to the login page if they're not logged in 
	echo '<script>window.location.href="loginScreen.php"</script>';
}

?>
				
			</div>
		</div>
		<div id="endGradient"></div>
	</body>
</html>