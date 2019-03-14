<?php
//checks the user is logged on before letting them stay on the page 
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
				<br>

				<script> function goBack() { window.location.href = "editEvent.php"; } </script>
				<button type="button" class="btn btn-default" onClick="goBack();">Back</button>

			<h5>Edit Event:

<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	//getting the EventID from the session
	$eventID = $_SESSION['EventID'];

	//sql to get the details on the selected event 
	$sqlGetDetails = "SELECT et.Name, e.Date FROM events as e
					INNER JOIN eventtype as et ON e.TypeID = et.TypeID
					WHERE e.EventID = '$eventID'";

	if(mysqli_query($con, $sqlGetDetails)) {
		//getting the result from the query 
		$detailsResult = mysqli_query($con, $sqlGetDetails);
		$details = mysqli_fetch_row($detailsResult);

		//showing the name and date of the selected event 
		echo " " . $details[0] . " on " . $details[1] . "</h5>";
	} else {
		echo "Error: " . $sqlGetDetails . "<br>" . mysqli_error($con);
	}
?>



			<div class="col-sm-6">
				<form class="form-horizontal" method="post" action="editAttending.php">

					<div class="form-group">
						<label class="control-label">First Name:</label>
						<input type="text" name="FName" class="form-control">
					</div>

					<div class="form-group">
						<label class="control-label">Last Name:</label>
						<input type="text" name="LName" class="form-control">
					</div>

					<!-- Validation:
					Checks a member with the first and last name exists in PHP 
					-->

					<div class="form-group">
						<input type="submit" name="add" class="btn btn-default" value="Add">
						<input type="submit" name="remove" class="btn btn-default" value="Remove">
					</div>
				</form>
			</div>
				
			<div class="col-sm-6">

				<h3>Members Added:</h3>
<?php

//if they try to add a member 
if(isset($_POST['add'])) {
	//getting the first and last name from the form 
	$fName = $_POST['FName'];
	$lName = $_POST['LName'];
	
	//sql to get the MemID of any members with that first and last name 
	$sqlGetMemId = "SELECT MemID FROM members WHERE FName = '$fName' AND LName='$lName'";

	if(mysqli_query($con, $sqlGetMemId)) {
		//getting the result from the query 
		$MemID = mysqli_query($con, $sqlGetMemId);

		switch(mysqli_num_rows($MemID)) {
			//if there's no members with that first and last name 
            case 0:
       		
            echo "<b>Error: Member doesn't exist</b><br>";
            break;

            //if there's only one member with that first and last name
            case 1:
            //get their MemID
            $memToInsert = mysqli_fetch_row($MemID);

            //check if they're already added to the event 
            $sqlCheckIfAlready = "SELECT ActID FROM activity WHERE EventID='$eventID' AND MemID = '$memToInsert[0]'";

            if(mysqli_query($con, $sqlCheckIfAlready)) {
            	//getting the result from the query 
            	$ifAlready = mysqli_query($con, $sqlCheckIfAlready);

            	//if they're not already added for that event 
            	if(mysqli_num_rows($ifAlready) === 0) {

            		//SQL to add their activity to the database 
		            $sqlAddActivity = "INSERT INTO activity(EventID, MemID) VALUES ('$eventID', '$memToInsert[0]')";

					if(mysqli_query($con, $sqlAddActivity)) {
						
					} else {
						echo "Error: " . $sqlAddActivity . "<br>" . mysqli_error($con);
					}

				} else {
					//if there's not 0 results for the query, aka more than one aka member is already added 
					echo "<b>Error: Member already added</b><br>";
				}
			} else {
				echo "Error: " . $sqlCheckIfAlready . "<br>" . mysqli_error($con);
			}

            break; 

            //if there's more than one result from the MemID query 
            default:
            echo "Error: Multiple members with same name, please make all first & last names unique";
        }


	} else {
		echo "Error: " . $sqlGetMemId . "<br>" . mysqli_error($con);
	}

}

//upon clicking to remove a member 
if(isset($_POST['remove'])) {
	//getting the first and last name from the form 
	$fName = $_POST['FName'];
	$lName = $_POST['LName'];
	
	//sql to get the MemID for that first and last name 
	$sqlGetMemId = "SELECT MemID FROM members WHERE FName = '$fName' AND LName='$lName'";

	if(mysqli_query($con, $sqlGetMemId)) {
		//getting the result from the query 
		$MemID = mysqli_query($con, $sqlGetMemId);

		switch(mysqli_num_rows($MemID)) {
			//if no rows aka no members exist 
            case 0:
       		
            echo "<b>Error: Member doesn't exist</b><br>";
            break;

            //if one member exists with that first and last name 
            case 1:
            $memToDelete = mysqli_fetch_row($MemID);

            //sql to delete their activity from the database 
            $sqlDeleteActivity = "DELETE FROM activity WHERE MemID = '$memToDelete[0]' AND EventID = '$eventID'";

			if(mysqli_query($con, $sqlDeleteActivity)) {
				
			} else {
				echo "Error: " . $sqlAddActivity . "<br>" . mysqli_error($con);
			}

            break; 

            //if there's multiple members with the same name 
            default:
            echo "Error: Multiple members with same name, please make all first & last names unique";
        }


	} else {
		echo "Error: " . $sqlGetMemId . "<br>" . mysqli_error($con);
	}
}

//to get the list of members attending 
$sqlList = "SELECT members.FName, members.LName, activity.MemID, activity.EventID 
    FROM members 
    INNER JOIN activity ON members.MemID = activity.MemID 
    WHERE activity.EventID='$eventID'";

    $List = mysqli_query($con, $sqlList);

    //outputting all their first and last names 
    while($row = mysqli_fetch_assoc($List)) {
        echo $row["FName"]. " " . $row["LName"]. "<br>";
    }

?>
 
			</div>
		</div></h5></div>
		<div id="endGradient"></div>
  </body>
</html>