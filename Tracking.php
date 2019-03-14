<?php
//checks user is logged in as a leader
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
  	<title>Tracking</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="hybustyle.css" rel="stylesheet" type="text/css">

<script>

//called upon clicking on any event on the list on the left of the page
function putIn(EventID, desc) {
	//putting in the values to the invisible textboxes in order to submit to php
	document.getElementById("IDText").value = EventID;
	document.getElementById("EventDetails").value = desc;
	//clicking the submit button
	document.getElementById("IDSubmit").click();
}

//called upon clicking(either checking or unchecking) any of the checkboxes that confirm the person's attendance 
function addRadio(MemID) {
	ID = "R" + MemID
	var radioSpace = document.getElementById(ID)

	//checks if there's nothing in the row - then adds in the radio buttons if there isn't, takes them away if they're already there 
	if(radioSpace.innerHTML === '') {
	radioSpace.innerHTML = '<td><input type="radio" name="' + MemID + '" value="G" class="col-sm-4" required></input> </td> <td> <input type="radio" name="' + MemID + '" value="N" class="col-sm-4"> </input> </td> <td> <input type="radio" name="' + MemID + '" value="B" class="col-sm-4"> </input></td>';
	} else {
		radioSpace.innerHTML = '';
	}
}

//gets called upon clicking the delete event button 
function confirmDelete() {
	if(confirm("Are you sure you want to delete this event?")) {
		//clicks an invisible delete button in order to submit the request to php
		document.getElementById("DeleteButton").click();
	}
}

function error() {
	alert("Member doesn't exist!");
}

</script>

<style>

.radioButtons {
	height:37px;
}

th {
	height:28px;
}

</style>

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
					<li class = "nav-text"><a href="Tracking.php" style="color:#E30713">Tracking</a></li>
					<li class = "nav-text"><a href="Statistics.php">Statistics</a></li>
					<li class = "nav-text"><a href="Registers.php">Registers</a></li>
					<li class = "nav-text"><a href="Volunteers.php">Volunteers</a></li>
				</ul>
			</div>
				

			</div>
		</nav>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">

				<div class="col-sm-4">
					<h3>Tracking</h3>
					<h5>Please select an event:</h5>
<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//getting today to make sure it can only display events that have been 
$today = date('Y-m-d', time());

//gets all events that have both not been marked as the tracking having been done and are in the past, orders by date so it's outputted in an organised structure
$sqlGetIncomplete = "SELECT e.EventID, e.Date, et.Name 
					FROM Events as e 
					INNER JOIN eventtype as et ON et.TypeID = e.TypeID
					WHERE e.finished = 'N' AND e.Date <= '$today'
					ORDER BY e.Date";

if(mysqli_query($con, $sqlGetIncomplete)) {
	$incompleteEvents = mysqli_query($con, $sqlGetIncomplete);
	//going through each event gotten from the sql query 
	foreach($incompleteEvents as $event) {
		//putting it in the page - onClick calling the function above to bring up the input form, passes the parameters of the event ID so it displays the right event on the form, then submits name and event as the other parameter to display in the title. Text output is the name and the date
		echo '<text onClick="putIn(' . $event['EventID'] . ', ' . "'" . $event['Name'] . ' ' . $event['Date'] . "'" . ')" id="' . $event['EventID'] . '">' . $event['Name'] . " " . $event['Date'] . '</text>
		<br>';
	}
} else {
	echo "Error: " . $sqlGetIncomplete . "<br>" . mysqli_error($con);
}

?>

<form action="Tracking.php" method="post">
	<input type="text" id="IDText" name="IDText" style="display:none;">
	<input type="text" id="EventDetails" name="EventDetails" style="display:none;">
	<input type="submit" id="IDSubmit" name="IDSubmit" style="display:none;">
</form>

<form action="Tracking.php" method="post">
	<input type="submit" id="DeleteButton" name="finalDelete" style="display:none;">
</form>
<div id="errorSpace"></div>

				</div>

				<div class="col-sm-8">
<?php

//called onClick of any of the events on the left of the page
if(isset($_POST['IDSubmit'])) {
	//putting all values into session they need to be able to be called after submitting the add a new name to the register (and hence reloading the page)
	//getting the values from the invisible textboxes
	$eventID = $_POST['IDText'];
	$_SESSION['EventID'] = $eventID;
	$details = $_POST['EventDetails'];
	$_SESSION['details'] = $details;

	//title for page
	echo "<h3>Event: " . $_SESSION['details'] . "</h3>";
	
	//displaying the form for adding a new name to the attendance list 
	echo 	'<div class="col-sm-12">

					<div class="alert alert-info alert-dismissible">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
						Please add all members to the list before confirming attendances</div>

					<h5>Add anybody not on list:</h5>
					<br>

				<form method="post" action="Tracking.php" class="form-horizontal">

					<div class="form-group">
						<label class="col-sm-4 control-label">First Name:</label>
						<div class="col-sm-8"><input type="text" class="form-control" name="FName"></div>
					</div>

					<div class="form-group">
						<label class="col-sm-4 control-label">Last Name:</label>
						<div class="col-sm-8"><input type="text" class="form-control" name="LName"></div>
					</div>
					
					<div class="form-group">
						<center>
								<input type="submit" id="IDSubmit" name="nameSubmit" class="btn btn-default">
						</center>

					</div>
				</form>
			</div>

			<form action="Tracking.php" method="post" id="mainForm">
				<div class="col-sm-6">

			<h5>Please confirm attendance to the event</h5><br><br>';
	
	//new form
	//getting all members signed up for the event 
	$sqlGetMembers = "SELECT m.MemID, m.FName, m.LName
						FROM members as m 
						INNER JOIN activity as a ON a.MemID = m.MemID
						WHERE a.EventID = '$eventID'";

	if(mysqli_query($con, $sqlGetMembers)) {
		$members = mysqli_query($con, $sqlGetMembers);
		foreach($members as $mem) {
			//creating a checkbox for each member - onChange passes the MemID (aka the changeable part of the ID of the row the radio buttons will reside in so it can display or stop displaying them next to it)
			echo 	'<div class="form-group">
						<label class="checkbox-inline col-sm-6">' . $mem['FName'] . " " . $mem['LName'] . '</label>
						<div ="col-sm-6"><input type="checkbox" name="confirmed[]" value="' . $mem['MemID'] . '" onchange="addRadio(' . $mem['MemID'] . ')"></div>
					</div>';
		}

	} else {
		echo "Error: " . $sqlGetMembers . "<br>" . mysqli_error($con);
	}

	//setting up the table for rating their performances
	echo 	'</div> <div class="col-sm-6">
			<h5>Please rate their performances</h5><table>
			<tr><th class="col-sm-4">Good </th> 
			<th class="col-sm-4">Neutral</th> 
			<th class="col-sm-4">Bad </th>
			</tr>';
	
	foreach($members as $mem) {
		//setting up the blank place for the radio buttons to be put in 
	echo	'<tr class="radioButtons" id="R' . $mem['MemID'] . '"></tr>';
	}

	//sets up the part of the form that asks for hours and has the submit button
	echo '</table>
	</div>
				<div class="col-sm-12">

				<div class="form-group">
					<label class="control-label">Please confirm the number of hours:</label>
					<div><input type="number" class="form-control" name="hours" step="0.25" max="24" min="1" required></div>
				</div>

				<div class="form-group">
					<center>
						<input type="submit" name="finalSubmit" class="btn btn-default"></input>
						<button type="button" class="btn btn-default" onClick="confirmDelete();">Delete Event</button>
					</center>
				</div>

				</form>

				</div>';
			}

//called onClick of the first form, containing just first name and last name 
if(isset($_POST['nameSubmit'])) {

	//gets the first name and last name values from the form 
	$FName = $_POST['FName'];
	$LName = $_POST['LName'];

	//gets the ID of the member that's been inputted
	$sqlGetMemId = "SELECT MemID FROM members WHERE FName = '$FName' AND LName='$LName'";

	if(mysqli_query($con, $sqlGetMemId)) {
		$MemID = mysqli_query($con, $sqlGetMemId);

		//checking the member exists
		switch(mysqli_num_rows($MemID)) {
            case 0:

            //throws an error if there's no results 
            echo '<script>error();</script>';
            break;

            case 1:
            //getting the MemID in the case they do exist 
            $memToInsert = mysqli_fetch_row($MemID);

            //getting EventID from the session 
            $eventID = $_SESSION['EventID'];

            $sqlCheck = "SELECT MemID FROM activity WHERE EventID = '$eventID' AND MemID = '$memToInsert[0]'";

            if(mysqli_query($con, $sqlCheck)) {
                    $check = mysqli_query($con, $sqlCheck);

                    if(mysqli_num_rows($check) === 0) {
			            //adds the member to the activity database, hence the code from earlier will include them on the list 
			            $sqlAddActivity = "INSERT INTO activity(EventID, MemID) VALUES ('$eventID', '$memToInsert[0]')";

						if(mysqli_query($con, $sqlAddActivity)) {
				
						} else {
							echo "Error: " . $sqlAddActivity . "<br>" . mysqli_error($con);
						}
					}
			}
            break; 

            default:
            echo "Error: Multiple members with same name, please make all first & last names unique";
        }


	} else {
		echo "Error: " . $sqlGetMemId . "<br>" . mysqli_error($con);
	}

	//makes sure the form is still there 
	echo "<script>putIn(" . $_SESSION['EventID'] . ", '" . $_SESSION['details'] . "');" . ';</script>';

}

//called upon clicking the final submit button
if(isset($_POST['finalSubmit'])) {
	//getting the values from the page - confirmed will be an array of the checked values 
	$hours = $_POST['hours'];
	$confirmed = $_POST['confirmed'];
	//getting the EventID from the session 
	$eventID = $_SESSION['EventID'];

	foreach($confirmed as $mem) {
		//getting the proficiency for the member - name of prof objects is MemID, which are the values of the checkboxes
		$profID = strval($mem);
		$prof = $_POST[$profID];

		//confirms their activity and puts proficiency in database
		$sqlConfirmAttendance = "UPDATE activity SET Confirmed = 'Y', Proficiency = '$prof' WHERE EventID = '$eventID' AND MemID = '$mem'";

		if(mysqli_query($con, $sqlConfirmAttendance)) {

		} else {
			echo "Error: " . $sqlConfirmAttendance . "<br>" . mysqli_error($con);
		}
	}

	//sets the event to confirmed in the database and puts in the hours for the event 
	$sqlFinishEvent = "UPDATE events SET Finished = 'Y', ActualHours = '$hours' WHERE EventID = '$eventID'";

	if(mysqli_query($con, $sqlFinishEvent)) {
		//alerts the user the tracking is done and reloads the page so the event disappears from the list of events 
		echo '<script>alert("Tracking Done");
					window.location.href = "Tracking.php";</script>';
	} else {
		echo "Error: " . $sqlFinishEvent . "<br>" . mysqli_error($con);
	}
}

//called upon click of the invisible delete button, which is clicked after asking for a confirmation message through JS from the form delete button
if(isset($_POST['finalDelete'])) {
	//getting the EventID from the session
	$eventID = $_SESSION['EventID'];

	//sql for deleting both all activity listed for members and the event itself 
	$sqlDeleteActivity = "DELETE FROM activity WHERE EventID = '$eventID';";
	$sqlDeleteEvent = "DELETE FROM events WHERE EventID = '$eventID'";

	if(mysqli_query($con, $sqlDeleteActivity) AND mysqli_query($con, $sqlDeleteEvent)) {
		//alerts the user the event is deleted and reloads the page so that the event disappears from the list of events
		echo '<script>alert("Event Deleted");
				window.location.href = "Tracking.php"; </script>';
	} else {
		echo "Error: " . $sqlDeleteEvent . "<br>" . mysqli_error($con);
	}
}

?>
				</div>

				<div class="col-sm-12"><center><h5><a href="backtrackHours.php">Backtrack hours</a></h5></center></div>


			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>