<?php
//checking the user is logged in 
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
  	<title>Login</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="hybustyle.css" rel="stylesheet" type="text/css">
  </head>
	<body>
		<div class="container-fluid" style="height:90px;"> <a class = "navbar-brand" href="#"> <img src = "http://hybu.co.uk/newsite/wp-content/uploads/2015/11/hybu_logo.png" alt = "Hybu Logo" height = 60></a> </div>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">

				<h2>Please Enter an Admin account to continue:</h2>

				<a href="RegistersInput.php" class="btn btn-default">Back</a>
				
				<form class="form-horizontal" method="post" action="registerConfirm.php">
					<div class="form-group">
						<label class="control-label col-sm-4">Username:</label>
						<div class="col-sm-8"><input type="text" name="Username" class="form-control"></div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-4">Password:</label>
						<div class="col-sm-8"><input type="password" name="Password" class="form-control"></div>
					</div>

					<div class="form-group">
						<center>
							<input type="submit" name="submit" class="btn btn-default"></input>
						</center>
					</div>
				</form>

<?php

//when the submit button is clicked 
if(isset($_POST['submit'])) {
	//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	//getting the username and password from the form 
	$username = $_POST['Username'];
	$password = $_POST['Password'];

	//in order to get the users details if they exist 
	$sqlGetUser = "SELECT Type, m.MemID, First, Password FROM members as m
					INNER JOIN login as l ON m.MemID = l.MemID
					WHERE l.Username = '$username'";

	if(mysqli_query($con, $sqlGetUser)) {
		//getting result and counting rows to see if they exist 
		$user = mysqli_query($con, $sqlGetUser);
		$isUser = mysqli_num_rows($user);

		//if they do exist
		if($isUser === 1) {
			//get the row
			$thisUser = mysqli_fetch_row($user);

			//if it's not their first time, would've set their own password so it'll be encrypted
			if($thisUser[2] === "N") {

				//confirming the password 
				$isCorrect = password_verify($password, $thisUser[3]);

				if($isCorrect) {
					//if it's right, check if it's type leader and if so let them through but if not give an error 
					if($thisUser[0] === "L") {
						echo '<script>window.location.href="RegisterDescription.php"</script>';
					} else {
						echo "Error: Account doesn't have the correct permissions";
					}
				} else {
					//if it's not correct, show the username or password is incorrect 
					echo "Error: Password or username is incorrect";
				}
		
		
		//if it's their first time, password wouldn't be changed yet 
		} elseif($thisUser[2] === "Y") {

			//if the password equals one in the database, go on to check if leader or not 
			if($password === $thisUser[3]) {
				if($thisUser[0] === "L") {
						echo '<script>window.location.href="RegisterDescription.php"</script>';
					} else {
						echo "Error: Account doesn't have the correct permissions";
					}
			} else {
				//if password incorrect, show error 
				echo "Error: Password or username is incorrect";
			}
		} 

		} else {
			//if no user exists, show error with password or username incorrect 
			echo "Error: Password or username is incorrect";
		}

	} else {
		echo "Error: system could not log in"; //no query in the error or else password will be shown 
	}
}
?>
				
			</div>
		</div>
		<div id="endGradient"></div>
	</body>
</html>