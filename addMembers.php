<?php
//checks that the person attempting to access the page is a leader 
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
				
				<h5>Add Members to Event on

<?php

//getting the eventIDs and dates from the session 
$eventID = $_SESSION['EventID'];
$dates = $_SESSION['dates'];

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//turns it into an array so it can loop through adding people to all the events
$datesArray = explode(", ", $dates);
//number of times the page needs to display 
$indexesToDo = count($eventID) - 1;

//puts it in the session if it's not already set (won't be set if it's the first time displaying the page - don't want it to go to 0 the second viewing of the page etc.)
if(isset($_SESSION['doneTimes']) === FALSE) {
	$_SESSION['doneTimes'] = 0;
}

//outputs the date date being done now - doneTimes will be the index of the EventID currently being done and hence the index of the date being done
echo " " . $datesArray[$_SESSION['doneTimes']] . "</h5>";

?>

			<div class="col-sm-6">
				<form class="form-horizontal" method="post" action="addMembers.php">
					<div class="form-group">
						<label class="control-label">First Name:</label>
						<input type="text" name="FName" class="form-control">
					</div>

					<div class="form-group">
						<label class="control-label">Last Name:</label>
						<input type="text" name="LName" class="form-control">
					</div>

					<!-- Validation:
					Will be checked if a member with this first and last name exists in PHP
					-->
					<div class="form-group">
						<input type="submit" name="submit" class="btn btn-default">
					</div>
				</form>
			</div>
				
			<div class="col-sm-6">
				<h3>Members Added:</h3>
<?php
//getting eventID currently being done in this page
$thisEventID = $eventID[$_SESSION['doneTimes']];

//upon submitting a first and last name 
if(isset($_POST['submit'])) {
	//getting the values from the form 
	$fName = $_POST['FName'];
	$lName = $_POST['LName'];
	
	//sql to get any MemId for the member 
	$sqlGetMemId = "SELECT MemID FROM members WHERE FName = '$fName' AND LName='$lName'";

	if(mysqli_query($con, $sqlGetMemId)) {
		//fetches the result from the query
		$MemID = mysqli_query($con, $sqlGetMemId);

		//switching based on number of members 
		switch(mysqli_num_rows($MemID)) {
			//if no member exists 
            case 0:
       		
            echo "<b>Error: Member doesn't exist</b><br>";
            break;

            //if a member exists 
            case 1:
            //getting the MemID
            $MemToInsert = mysqli_fetch_row($MemID);

            //check if they're already added to the event 
            $sqlCheckIfAlready = "SELECT ActID FROM activity WHERE EventID='$thisEventID' AND MemID = '$MemToInsert[0]'";

            if(mysqli_query($con, $sqlCheckIfAlready)) {
            	//gets the result from the query 
            	$ifAlready = mysqli_query($con, $sqlCheckIfAlready);

            	//if they're not already added 
            	if(mysqli_num_rows($ifAlready) === 0) {

			            //sql for adding them to the activity database
			            $sqlAddActivity = "INSERT INTO activity(EventID, MemID) VALUES ('$thisEventID', '$MemToInsert[0]')";

			            //executing sql 
						if(mysqli_query($con, $sqlAddActivity)) {
							
						} else {
							echo "Error: " . $sqlAddActivity . "<br>" . mysqli_error($con);
						}

			} else {
				//if they are already added 
					echo "<b>Error: Member already added</b><br>";
				}
			} else {
				echo "Error: " . $sqlCheckIfAlready . "<br>" . mysqli_error($con);
			}

            break; 

            //if multiple members exist 
            default:
            echo "Error: Multiple members with same name, please make all first & last names unique";
        }


	} else {
		echo "Error: " . $sqlGetMemId . "<br>" . mysqli_error($con);
	}

}

//sql to get the list of members attending 
$sqlList = "SELECT members.FName, members.LName, activity.MemID, activity.EventID 
    FROM members 
    INNER JOIN activity ON members.MemID = activity.MemID 
    WHERE activity.EventID='$thisEventID'";

    //gets all members 
    $List = mysqli_query($con, $sqlList);

    //outputs all member names 
    while($row = mysqli_fetch_assoc($List)) {
        echo $row["FName"]. " " . $row["LName"]. "<br>";
    }

//called upon clicking final submit button at bottom 
if(isset($_POST['finalSubmit'])) {

	//if all indexes have been done, redirect back to events page 
	if($_SESSION['doneTimes'] === $indexesToDo) {
		unset($_SESSION['doneTimes']);
		echo '<script>window.location.href="Events.php"</script>';

		//if still more indexes to do, add one to amount of times done then redirect to same page 
	} else {
		$_SESSION['doneTimes'] += 1;
		echo '<script>window.location.href="addMembers.php"</script>';
	}
}

?>

			</div> 

			<div class="col-sm-12">
				<form action="addMembers.php" method="post">
				<center>
					<input type="submit" name="finalSubmit" class="btn btn-default" value="Final Submit"></input>
				</center>
				</form>
				<br>
			</div>		
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>