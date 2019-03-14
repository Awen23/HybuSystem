<?php
//checks if the user is logged in as a leader
session_start();
if(isset($_SESSION['Type'])) {
	if($_SESSION['Type'] === "V") {
		echo '<script>window.location.href="volunteersEvents.php" </script>';
	} 
} else {
	echo '<script>window.location.href="loginScreen.php"</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  	<title>Volunteer</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="hybustyle.css" rel="stylesheet" type="text/css">
  </head>
  <body> 
  		<script>
  			
  		</script>
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
    				<a href="Volunteers.php" class="btn btn-primary mini-nav" style="background-color: #E30713; color:white;">Add/Edit Volunteers</a>
    				<a href="editAccounts.php" class="btn btn-primary mini-nav">Edit Volunteer Accounts</a>
  				</div>

				<div class = "row">
					<div class = "container col-sm-6">
						<h5>Add a new volunteer:</h5>
						
						<form class = "form-horizontal" action="Volunteers.php" method="post">


							<div class="form-group">
								<label class = "control-label col-sm-4">First Name:</label>
								<div class = "col-sm-8"><input type="text" pattern="[A-Za-z ]{1,30}" class="form-control" name="VolFName" oninvalid="this.setCustomValidity('Please enter a name less than 30 characters with only letters')" onchange="this.setCustomValidity('')" maxlength="30" required></div>
								<!-- Validation:
								Only letters and spaces
								No longer than 30 characters
								Required field -->
							</div>

							<div class="form-group">
								<label class = "control-label col-sm-4">Last Name:</label>
								<div class="col-sm-8"><input type="text" pattern="[A-Za-z ]{1,30}" oninvalid="this.setCustomValidity('Please enter a name less than 30 characters with only letters')" onchange="this.setCustomValidity('')" class="form-control" name="VolLName" maxlength="30" required></div>
								<!-- Validation:
								Only letters and spaces
								No longer than 30 characters
								Required field -->
							</div>

							<div class="form-group">
								<div class ="col-sm-4">
								</div>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="CarNew" value="C" required>Cardiff</label>
								</div>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="CarNew" value="N" required>Newport</label>
								</div>
								<!-- Validation:
								Can only be Cardiff or Newport
								Required field -->
							</div>

							<div class="form-group">
								<div class ="col-sm-4">
								</div>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="Type" value="V" required>Volunteer</label>
								</div>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="Type" value="L" required>Leader</label>
								</div>
								<!-- Validation:
								Can only be Volunteer or Leader
								Required field -->

							</div>


						<div class="col-sm-4"></div>
						<div class="col-sm-8">
							<input type="submit" name="initialSubmit" class="btn btn-default"></input>
						</div>
						</form>

<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//upon submitting the smaller form 
if (isset($_POST['initialSubmit'])) {
	//getting the values from the form 
    $fname = $_POST['VolFName'];
	$lname = $_POST['VolLName'];
	$carnew = $_POST['CarNew'];
	$type = $_POST['Type'];

	//sql to check if the member exists already or not 
	$sqlCheckMem = "SELECT MemID FROM members WHERE FName = '$fname' AND LName = '$lname'";

	if(mysqli_query($con, $sqlCheckMem)) {
		//fetching the result in order to count how many rows there are
		$memCheckResult = mysqli_query($con, $sqlCheckMem);
		$noResults = mysqli_num_rows($memCheckResult);

		//if there's no match, create the member
		if($noResults === 0) {

			//creating the member
			$sqlInsert = "INSERT INTO members(FName, LName, CarNew, Type) VALUES ('$fname', '$lname', '$carnew', '$type')";

			//throw an error if error can't be made
			if(!mysqli_query($con, $sqlInsert)) {
				echo "Error: " . $sqlInsert . "<br>" . mysqli_error($con);
			}

			//AUTOMATIC ACCOUNT CREATION

			//gets MemID
			$memID = mysqli_insert_id($con);
			//makes username - simply first and last name with no space between them
			$username = $fname . $lname;
			//makes password - first name with 3 random digits after it 
			$password = $fname . mt_rand(100, 999);

			//creates the user
			$sqlCreateUser = "INSERT INTO login(Username, Password, MemID) VALUES ('$username', '$password', '$memID')";
			if(mysqli_query($con, $sqlCreateUser)) {
				//shows username and password under form 
				echo "New user created - <br> Username: " . $username . "<br> Password: " . $password;
			} else {
				//throws error if user can't be created 
				echo "Error: " . $sqlCreateUser . "<br>" . mysqli_error($con);
			}
		} else {
			//throws an error if the number of rows corresponding to the first & last name isn't 0
			echo '<script>alert("User already exists!");</script>';
		}
	} else {
		//throws error if sql to check if the member exists doesn't work
		echo "Error: " . $sqlCheckMem . "<br>" . mysqli_error($con);
	}

}

?>

						<h5>Add more details:</h5>
						<form class = "form-horizontal" id="moreDetailsFrm" action="Volunteers.php" method="post">

							<div class="form-group">
								<label id="try" class = "control-label col-sm-4">MemID:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" name="MemID" id="MemID" readonly></div>
								<!-- Validation:
								Can't be edited, inputted by selecting a member only 
								Required field - validation done in php once submitting -->
							</div>

							<div class="form-group">
								<label class = "control-label col-sm-4">First Name:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[A-Za-z ]{1,30}" oninvalid="this.setCustomValidity('Please enter a name less than 30 characters with only letters')" name="VolFName" id = "FName" required></div>
								<!-- Validation:
								Only letters and spaces
								No longer than 30 characters
								Required field -->
							</div>

							<div class="form-group">
								<label class = "control-label col-sm-4">Last Name:</label>
								<div class="col-sm-8"><input type="text" class="form-control" pattern="[A-Za-z ]{1,30}" oninvalid="this.setCustomValidity('Please enter a name less than 30 characters with only letters')" name="VolLName" id = "LName" required></div>
								<!-- Validation:
								Only letters and spaces
								No longer than 30 characters
								Required field -->
							</div>

							<div class="form-group">
								<div class ="col-sm-4">
								</div>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="CarNew" value="C" id = "C" required>Cardiff</label>
								</div>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="CarNew" value="N" id = "N" required>Newport</label>
								</div>
								<!-- Validation:
								Can only be Cardiff or Newport
								Required field -->

							</div>

							<div class="form-group">
								<div class ="col-sm-4">
								</div>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="Type" value="V" id = "V" required>Volunteer</label>
								</div>
								<div class="col-sm-4">
									<label class="radio-inline"><input type="radio" name="Type" value="L" id = "L" required>Leader</label>
								</div>
								<!-- Validation:
								Can only be Volunteer or Leader
								Required field -->

							</div>

							<div class="form-group">
								<label for = "gender" class = "control-label col-sm-4">Select Gender:</label>
									<div class = "col-sm-8"><select class = "form-control" id="gender" name = "gender" required>
										<option value="">Please select one from list</option>
										<option value="F">Female</option>
										<option value="M">Male</option>
										<option value="O">Other</option>
									</select></div>
								<!-- Validation:
								Can be only Female, Male, or Other
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">DOB:</label>
								<div class = "col-sm-8"><input type="date" min="1900-01-01" class="form-control" name="DOB" id = "DOB" required></div>
								<!-- Validation:
								Must be in date format
								Must be above 1900-01-01
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Address:</label>
								<div class = "col-sm-8">
									<textarea rows="2" class="form-control" pattern="[A-Za-z0-9,- ]{0,40}" oninvalid="this.setCustomValidity('Character not allowed. Allowed characters: letters, numbers, , and -')" onchange="this.setCustomValidity('')" name="Address" id = "Address" maxlength="40" required></textarea>
								</div>
								<!-- Validation:
								Can only be letters, numbers, ",", -, and spaces
								Maximum length of 40 
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Postcode:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[A-Z0-9 ]{6,8}" oninvalid="this.setCustomValidity('Invalid Postcode: Please only use capital letters, numbers and spaces & ensure postcode is 6-8 characters')" onchange="this.setCustomValidity('')" name="Postcode" id = "Postcode" required></div>
								<!-- Validation:
								Can only be uppercase letters, numbers and spaces
								Must be 6-8 characters long
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Tel No:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[0-9 ]{8,15}" oninvalid="this.setCustomValidity('Invalid number: Please enter only numbers and spaces with between 8 and 15 characters')" onchange="this.setCustomValidity('')" name="TelNo" maxlength = "15" id = "TelNo" required></div>
								<!-- Validation:
								Can only be numbers and spaces
								Must be between 8 to 15 characters long
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Mob:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[0-9 ]{8,15}" oninvalid="this.setCustomValidity('Invalid number: Please enter only numbers and spaces with between 8 and 15 characters')" onchange="this.setCustomValidity('')" name="MobNo" maxlength = "15" id = "MobNo" required></div>
								<!-- Validation:
								Can only be numbers and spaces
								Must be between 8 to 15 characters long
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Email:</label>
								<div class = "col-sm-8"><input type="email" class="form-control" name="email" maxlength = "40" id = "email" required></div>
								<!-- Validation:
								Default email type html validation
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">School:</label>
								<div class = "col-sm-8"><input type="text" pattern="[A-Za-z ]{0,25}" oninvalid="this.setCustomValidity('Please enter only letters and spaces, with maximum length of 25')" onchange="this.setCustomValidity('')" class="form-control" name="school" maxlength = "25" id = "school" required></div>
								<!-- Validation:
								Only letters and spaces
								Must be less than 25 characters 
								Required field -->
							</div>

							<h5 style="padding-left:30px;">GP:</h5>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Name:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" name="GPName" pattern="[A-Za-z0-9 ]{0,30}" oninvalid="this.setCustomValidity('Please enter a name less than 30 characters with only letters')" onchange="this.setCustomValidity('')" maxlength = "30" id = "GPName" required></div>
								<!-- Validation:
								Only letters, numbers and spaces
								Must be less than 30 characters
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">TelNo:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[0-9 ]{8,15}" oninvalid="this.setCustomValidity('Invalid number: Please enter only numbers and spaces with between 8 and 15 characters')" onchange="this.setCustomValidity('')" name="GPTelNo" maxlength = "15" id = "GPTelNo" required></div>
								<!-- Validation:
								Only numbers and spaces
								Must be between 8 and 15 characters
								Required field -->
							</div>

							<h5 style="padding-left:30px;">Emergency Contact:</h5>

							<div class = "form-group">
								<label class = "control-label col-sm-4">First Name:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[A-Za-z ]{1-30}" oninvalid="this.setCustomValidity('Please enter a name less than 30 characters with only letters')" onchange="this.setCustomValidity('')" name="EmeFName" maxlength = "30" id = "EmeFName" required></div>
								<!-- Validation:
								Only letters and spaces
								Must be under 30 characters
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Last Name:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[A-Za-z ]{1-30}" oninvalid="this.setCustomValidity('Please enter a name less than 30 characters with only letters')" onchange="this.setCustomValidity('')" name="EmeLName" maxlength = "30" id = "EmeLName" required></div>
								<!-- Validation:
								Only letters and spaces
								Must be under 30 characters
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Relationship To Member:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[A-Za-z ]{1-20}" oninvalid="this.setCustomValidity('Please enter a name less than 20 characters with only letters')" onchange="this.setCustomValidity('')" name="EmeRel" maxlength = "20" id = "EmeRel" required></div>
								<!-- Validation:
								Only letters and spaces
								Must be under 20 characters
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Home Telephone:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[0-9 ]{8,15}" oninvalid="this.setCustomValidity('Invalid number: Please enter only numbers and spaces with between 8 and 15 characters')" onchange="this.setCustomValidity('')" name="EmeHome" maxlength = "15" id = "EmeHome" required></div>
								<!-- Validation:
								Only numbers and spaces 
								Must be between 8 and 15 characters 
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Mobile:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[0-9 ]{8,15}" oninvalid="this.setCustomValidity('Invalid number: Please enter only numbers and spaces with between 8 and 15 characters')" onchange="this.setCustomValidity('')" name="EmeMob" maxlength = "15" id = "EmeMob" required></div>
								<!-- Validation:
								Only numbers and spaces 
								Must be between 8 and 15 characters 
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Work No:</label>
								<div class = "col-sm-8"><input type="text" class="form-control" pattern="[0-9 ]{8,15}" oninvalid="this.setCustomValidity('Invalid number: Please enter only numbers and spaces with between 8 and 15 characters')" onchange="this.setCustomValidity('')" name="EmeWorkNo" maxlength = "15" id = "EmeWorkNo" required></div>
								<!-- Validation:
								Only numbers and spaces 
								Must be between 8 and 15 characters 
								Required field -->
							</div>

							<div class = "form-group">
								<label class = "control-label col-sm-4">Special Requirements:</label>
								<div class = "col-sm-8">
									<textarea rows="2" class="form-control" name="SpecReq" id = "SpecReq" maxlength="255"></textarea>
								</div>
								<!-- Validation:
								Must be under 255 characters 
								-->
							</div>


						<div class="col-sm-4"></div>
						<div class="col-sm-8">
							<input type="submit" name="finalSubmit" class="btn btn-default"></input>
							<button type="button" onClick="confirmDelete()" class="btn btn-default">Delete Member</button>
						</form>
						<form action="Volunteers.php" method="post">
							<input type="text" id="invisID" name="invisID" style="display:none;"><input type="submit" name="Delete" id="invisDelete" style="display:none;">
						</div>
						</form>
<script>

	function confirmDelete() {
		//so that it can ask for a confirmation before deleting the member
		if(confirm("Are you sure you want to delete the selected member? This will also delete all their training and practical hours and their login")) {
			document.getElementById("invisDelete").click();
		}
	}

</script>
<?php 

//upon submitting the full details form 
if (isset($_POST['finalSubmit'])) {
	//checks if the MemID box has been filled in (aka a member has been clicked)
	if($_POST['MemID'] !== "") {
		//Getting all the values from the form 
		$memId = $_POST['MemID'];
	    $fname = $_POST['VolFName'];
		$lname = $_POST['VolLName'];
		$carnew = $_POST['CarNew'];
		$type = $_POST['Type'];
		$gender = $_POST['gender'];
		$DOB = $_POST['DOB'];
		$address = $_POST['Address'];
		$postcode = $_POST['Postcode'];
		$telNo = $_POST['TelNo'];
		$mob = $_POST['MobNo'];
		$email = $_POST['email'];
		$school = $_POST['school'];
		$gpName = $_POST['GPName'];
		$gpTelNo = $_POST['GPTelNo'];
		$emeFName = $_POST['EmeFName'];
		$emeLName = $_POST['EmeLName'];
		$emeRel = $_POST['EmeRel'];
		$emeHome = $_POST['EmeHome'];
		$emeMob = $_POST['EmeMob'];
		$emeWorkNo = $_POST['EmeWorkNo'];
		$specReq = $_POST['SpecReq'];

		//checks if the GP already exists, as if already exists another one doesn't need to be created
		$sqlCheckGP = "SELECT gpID FROM gp WHERE Name = '$gpName' AND TelNo = '$gpTelNo'";

		if(mysqli_query($con, $sqlCheckGP)) {
			//getting the result from the query 
			$gpCheckResult = mysqli_query($con, $sqlCheckGP);
			$noGP = mysqli_num_rows($gpCheckResult);

			if($noGP === 0) {
				//if GP doesn't already exist 
				$sqlGp = "INSERT INTO gp(Name, TelNo) VALUES ('$gpName', '$gpTelNo')";

				if (mysqli_query($con, $sqlGp)) {
					$gpID = mysqli_insert_id($con);
				} else {
					echo "Error: " . $sqlGp . "<br>" . mysqli_error($con);
				}
			} else {
				//if it does already exist, make gp id the one that already exists 
				$gpIDRow = mysqli_fetch_row($gpCheckResult);
				$gpID = $gpIDRow[0];
			}

		}

		//create emergency contact row - not checking for already existing as is much more likely that each will be unique
		$sqlEmeId = "INSERT INTO emergencycont(FName, LName, relToMem, HomeTel, Mob, WorkNo) VALUES ('$emeFName', '$emeLName', '$emeRel', '$emeHome', '$emeMob', '$emeWorkNo')";

		if (mysqli_query($con, $sqlEmeId)) {
			//getting id of the eme id for next time 
			$emeId = mysqli_insert_id($con);
		} else {
			echo "Error: " . $sqlEmeId . "<br>" . mysqli_error($con);
		}

		//full member update sql 
		$sql = "UPDATE members SET FName = '$fname', LName = '$lname', CarNew = '$carnew', Type = '$type', Gender = '$gender', DOB = '$DOB', Address = '$address', Postcode = '$postcode', TelNo = '$telNo', Mob = '$mob', Email = '$email', School = '$school', EmeID = '$emeId', gpID = '$gpID', SpecReq = '$specReq'
			WHERE MemID = $memId";


		if(mysqli_query($con, $sql)) {
			//if successful, inform user that the full details have been added 
			echo "Record has been updated";
		} else {
			echo "Error: " . $sql . "<br>" . mysqli_error($con);
		}
	} else {
		//if no member has been selected (i.e. nothing in the MemID textbox)
		echo '<script>alert("Error: Please pick a member from the lists on the right")</script>';
	}
}

//upon confirming deletion 
if(isset($_POST['Delete'])) {
	//gets the MemID which was inserted through JS
	$memID = $_POST['invisID'];

	//checks if there's a member selected
	if($memID !== "") {

		//all the sql to delete everything associated with this member
		$sqlDeleteAttendance = "DELETE FROM attendance WHERE MemID = '$memID'";
		$sqlDeleteActivity = "DELETE FROM activity WHERE MemID = '$memID'";
		$sqlDeleteLogin = "DELETE FROM login WHERE MemID = '$memID'";
		$sqlDeleteMem = "DELETE FROM members WHERE MemID = '$memID'";

		//doing all the queries at once 
		if(mysqli_query($con, $sqlDeleteAttendance) AND mysqli_query($con, $sqlDeleteLogin) AND mysqli_query($con, $sqlDeleteLogin) AND mysqli_query($con, $sqlDeleteMem)) {
			//informs leader that the member has been deleted, reloads the page so that they disappear from the list on the side
			echo '<script>alert("Member deleted!");
					window.location.href("Volunteers.php")</script>';
		} else {
			echo "Error: " . mysqli_error($con);
		}

	} else {
		//throws an error if the MemID textbox was empty 
		echo '<script>alert("No member selected")</script>';
	}
}


 ?>
					</div>

					<div class = "col-sm-6">
						<h5>Select a name to confirm more details:</h5>
	<script>
			//to change the value of a certain element
			function valEdit(change, value) {
  				document.getElementById(change).value = value;
  			}

  			//called upon clicking one of the names in the first section 
  			function selectName(ID, first, last, CarNew, Type) {
  				//resetting the whole form 
  				document.getElementById("moreDetailsFrm").reset();

  				//adds in the details for the selected user - inc the ID in the invisible textbox (ready in case of chosing to delete) 
  				valEdit("invisID", ID);
  				valEdit("MemID", ID);
  				valEdit("FName", first);
  				valEdit("LName", last);
  				document.getElementById(CarNew).checked = true;
 				document.getElementById(Type).checked = true;

 				//makes their name bold on the side 
  				boldSingle(ID);
  			}

  			//sets a single one of the text elements to be bold 
  			function boldSingle(ID) {
  				//gets all the text elemenets (i.e. all the names)
  				allText = document.getElementsByTagName("Text");

  				//goes through each name and makes it normal text
				for(var i=0; i < allText.length; i++) {
					document.getElementsByTagName("Text")[i].style.fontWeight = "normal";
				}

				//changed the one selected one to bold 
				document.getElementById(ID).style.fontWeight = "bold";
  			}

  			//puts all of the details on a selected user in the long form 
  			function showAllDetails(ID, first, last, carNew, type, gender, DOB, address, postcode, telNo, mob, email, school, gpName, gpTelNo, emeFName, emeLName, emeRel, emeHomeTel, emeMob, emeWorkNo, specReq) {
  				//resets any information that was in the form 
  				document.getElementById("moreDetailsFrm").reset();

  				//puts all the details in the textboxes 
  				valEdit("invisID", ID);
  				valEdit("MemID", ID);
  				valEdit("FName", first);
  				valEdit("LName", last);
  				document.getElementById(carNew).checked = true;
 				document.getElementById(type).checked = true;
 				valEdit("gender", gender)
 				valEdit("DOB", DOB);
 				valEdit("Address", address);
 				valEdit("Postcode", postcode);
 				valEdit("TelNo", telNo);
 				valEdit("MobNo", mob);
 				valEdit("email", email);
 				valEdit("school", school);
 				valEdit("GPName", gpName);
 				valEdit("GPTelNo", gpTelNo);
 				valEdit("EmeFName", emeFName);
 				valEdit("EmeLName", emeLName);
 				valEdit("EmeRel", emeRel);
 				valEdit("EmeHome", emeHomeTel);
 				valEdit("EmeMob", emeMob);
 				valEdit("EmeWorkNo", emeWorkNo);
 				valEdit("SpecReq", specReq);

 				//makes the selected text bold 
 				boldSingle(ID);
  			}
	</script>

	<?php

	//gets the user where full details haven't been filled out (as all but special requirements are necessary fields on form above, just checking one detail is filled is sufficient)
    $sqlToComplete = "SELECT MemID, FName, LName, CarNew, Type FROM members
    					WHERE gender IS NULL";

    //to move to the next member later on when putting all the values in an onClick to be able to put the values in the textbox
    $n = "','";

    if (mysqli_query($con, $sqlToComplete)) {
    	$memToComp = mysqli_query($con, $sqlToComplete);
    	
    	//going through each member
    	foreach($memToComp as $mem) {
    		//outputting their first and last name in a name tag with their MemID as its ID and all their filled details in the onClick referencing the selectName function above 
    		echo '<text id = "' . $mem["MemID"] . '"onClick="selectName(' . "'" . $mem["MemID"] . $n . $mem["FName"] . $n . $mem["LName"] . $n . $mem["CarNew"] . $n . $mem["Type"] . "'" . ')">' .  $mem["FName"] . " " . $mem["LName"] . "</text> 
    		<br>";
    		}			
    } else {
    	echo "Error: " . $sqlToComplete . "<br>" . mysqli_error($con);
    }

    //the next title for completed members 
    echo 	"<br>
			<h5>Select to view and edit details</h5>";

	//getting all members with completed details - no need for a where as only completed members will have a gpID and EmeID
	$sqlToEdit = "SELECT m.MemID, m.FName, m.LName, m.CarNew, m.Type, m.Gender, m.DOB, m.Address, m.Postcode, m.TelNo, m.Mob, m.Email, m.School, m.gpID, m.EmeId, m.SpecReq, gp.Name as gpName, gp.TelNo as gpTelNo, e.FName as emeFName, e.LName as emeLName, e.relToMem as emeRel, e.HomeTel as emeHomeTel, e.Mob as emeMob, e.WorkNo as emeWorkNo
					FROM members as m
					INNER JOIN gp on gp.gpID = m.gpID
					INNER JOIN emergencycont as e on e.EmeID = m.EmeID";

	if (mysqli_query($con, $sqlToEdit)) {
		$memToEdit = mysqli_query($con, $sqlToEdit);

		//outputting their first and last name in a name tag with their MemID as its ID and all their details in the onClick referencing the showAllDetails function above 
		foreach($memToEdit as $mem){
			echo '<text id = "' . $mem["MemID"] . '"onClick="showAllDetails(' . "'" . $mem["MemID"] . $n . $mem["FName"] . $n . $mem["LName"] . $n . $mem["CarNew"] . $n . $mem["Type"] . $n . $mem["Gender"] . $n . $mem["DOB"] . $n . $mem["Address"] . $n . $mem["Postcode"] . $n . $mem["TelNo"] . $n . $mem["Mob"] . $n . $mem["Email"] . $n . $mem["School"] . $n . $mem["gpName"] . $n . $mem["gpTelNo"] . $n . $mem["emeFName"] . $n . $mem["emeLName"] . $n . $mem["emeRel"] . $n . $mem["emeHomeTel"] . $n . $mem["emeMob"] . $n . $mem["emeWorkNo"] . $n . $mem["SpecReq"] . "'" . ')">' . $mem["FName"] . " " . $mem["LName"] . "</text>
			<br>";
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
