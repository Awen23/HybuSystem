<?php
//checks user is logged in 
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

<style>
.table-bordered {
	border: 2px solid black;
	width:100%;
}

th {
	text-align:center;
	width:33%;
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

				<div class="col-sm-4">
<div class="hidden-print">
					<h3>Select an Event:</h3>
<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//gets today's date 
$today = date('Y-m-d', time());
//get all events in the future 
$sqlGetAllEvents = "SELECT e.EventID, et.Name, e.Date, e.StartTime, e.EndTime, e.Location, e.PracTrain FROM events as e 
					INNER JOIN eventtype as et ON et.TypeID = e.TypeID
					WHERE e.Date >= '$today'
					ORDER BY Date";

if(mysqli_query($con, $sqlGetAllEvents)) {
	$allEvents = mysqli_query($con, $sqlGetAllEvents);

	//if there were no results 
	if(mysqli_affected_rows($con) === 0) {
		echo "No future events found";

		//if there was only one result 
	} elseif(mysqli_affected_rows($con) === 1) {
		$oneEvent = mysqli_fetch_row($allEvents);

		//setting up the tag - invisInsert puts eventID in an invisible textbox, then submits it to PHP 
		echo '<text onClick="invisInsert(' . "'" . $oneEvent[0] . "')" . '">';
		//actual output
		echo 	'<b>Type: </b>' . $oneEvent[1] . 
				'<br><b>Date: </b>' . $oneEvent[2] . 
				'<br><b>Start Time: </b>' . $oneEvent[3] . 
				'<br><b>End Time: </b> ' . $oneEvent[4] . 
				'<br><b>Location: </b> ' . $oneEvent[5] . 
				'<br><b>Practical/Training: </b> ' . $oneEvent[6] . '<br><br><br>';
		//closing the tag 
		echo '</text>';

		//if multiple events were found 
		} else {
		foreach($allEvents as $event) {
			//setting up the tag - invisInsert puts eventID in an invisible textbox, then submits it to PHP 
			echo '<text onClick="invisInsert(' . "'" . $event['EventID'] . "')" . '">';
			//actual output
			echo 	'<b>Type: </b>' . $event['Name'] . 
					'<br><b>Date: </b>' . $event['Date'] . 
					'<br><b>Start Time: </b>' . $event['StartTime'] . 
					'<br><b>End Time: </b> ' . $event['EndTime'] . 
					'<br><b>Location: </b> ' . $event['Location'] . 
					'<br><b>Practical/Training: </b> ' . $event['PracTrain'] . '<br><br><br>';
			//closing the tag 
			echo '</text>';
			}
		}
	} else {
	echo "Error: " . $sqlGetAllEvents . "<br>" . mysqli_error($con);
	}

?>
			<form action="newReport.php" method="post">
				<input type="text" id="invisText" name="invisText" style="display:none">
				<input type="submit" id="invisSub" name="invisSub" style="display:none">
			</form>

<script>
	//puts values in an invisible textbox then submits it using an invisible button 
	function invisInsert(EventID) {
		document.getElementById("invisText").value = EventID;
		document.getElementById("invisSub").click();
	}
</script>	
			</div>
			</div>

			<div class="col-sm-8">
<?php

//upon them clicking on an event 
if(isset($_POST['invisSub'])) {
	//gets the EventID from the invisible textbox 
	$eventID = $_POST['invisText'];

	//gets all the details of the event 
	$sqlGetDetails = "SELECT et.Name, e.Date, e.Location FROM events as e
						INNER JOIN eventtype as et ON et.TypeID = e.TypeID 
						WHERE EventID = '$eventID'";

	if(mysqli_query($con, $sqlGetDetails)) {
		//getting the result from the query 
		$detailsResult = mysqli_query($con, $sqlGetDetails);
		$details = mysqli_fetch_row($detailsResult);

		//explains what the report actually shows
		echo '<br> <div class="hidden-print"> <div class="alert alert-info alert-dismissible">
			<a href="#" class="close" data-dismiss="alert">&times;</a>
			Names are ordered by proficiency in this event type, with most proficient being on top. First will appear by a name if they' . "'ve not done this event type before</div>";

		//title of the report and explains how to print 
		echo '<h3>Report for ' . $details[0] . " on " . $details[1] . "</h3>
										<h5>Press Ctrl + P to Print</h5></div>";

		//logo which is only visible when printing 
		echo '<div class="visible-print"><center><img src = "http://hybu.co.uk/newsite/wp-content/uploads/2015/11/hybu_logo.png" alt = "Hybu Logo" height=70></center><br></div>';
	} else {
		echo "Error: " . $sqlGetDetails . "<br>" . mysqli_error($con);
	}

	//table showing the event details 
	echo '<table class="table-bordered col-sm-12">
			<tr>
			<th>Event: ' . $details[0] . '</th>
			<th>On: ' . $details[1] . '</th>
			<th>At: ' . $details[2] . '</th>
			</tr>
			</table>
			<br>';

	//SQL to get all the leaders attending 
	$sqlGetLeaders = "SELECT m.FName, m.LName FROM members as m 
						INNER JOIN activity as a ON m.MemID = a.MemID
						WHERE m.Type = 'L' AND a.EventID = '$eventID'";

	if(mysqli_query($con, $sqlGetLeaders)) {
		//getting all the leaders 
		$allLeaders = mysqli_query($con, $sqlGetLeaders);
		$numLeaders = mysqli_num_rows($allLeaders);

		switch($numLeaders) {
			//if no leaders, show nothing 
			case 0:
			break;

			//if one leader, show leader with the one leader's name after it 
			case 1:
			$leader = mysqli_fetch_row($allLeaders);
			echo "<br><h5>Leader:</h5>" . $leader[0] . " " . $leader[1] . "<br>";

			break;

			//if multiple leaders, output a list of them 
			default:
			echo "<br><h5>Leaders:</h5>";
			foreach($allLeaders as $leader) {
				echo $leader['FName'] . " " . $leader['LName'] . "<br>";
			}
		}
	} else {
		echo "Error: " . $sqlGetLeaders . "<br>" . mysqli_error($con);
	}

	//sql to get all volunteers attending 
	$sqlGetVolunteers = "SELECT m.FName, m.LName, m.MemID FROM members as m
						INNER JOIN activity as a ON a.MemID = m.MemID
						WHERE m.Type = 'V' AND a.EventID = '$eventID'";

	if(mysqli_query($con, $sqlGetVolunteers)) {
		//getting result from the query 
		$allVolunteers = mysqli_query($con, $sqlGetVolunteers);
		$numVolunteers = mysqli_num_rows($allVolunteers);

		switch($numVolunteers) {
			//if no volunteers added, show an error 
			case 0:
			echo "Error: No volunteers added";
			break;

			//if only one volunteer added, just output their name 
			case 1:
			$oneVolunteer = mysqli_fetch_row($allVolunteers);
			echo "<h5>Volunteers:</h5>" . $oneVolunteer[0] . " " . $oneVolunteer[1];
			break;

			//when multiple volunteers added 
			default:
			echo "<h5>Volunteers:</h5>";
			//get the typeID for this event 
			$sqlGetTypeID = "SELECT TypeID FROM events WHERE EventID = '$eventID'";

			if(mysqli_query($con, $sqlGetTypeID)) {
				//getting the typeID
				$typeIDResult = mysqli_query($con, $sqlGetTypeID);
				$typeIDRow = mysqli_fetch_row($typeIDResult);
				$typeID = $typeIDRow[0];
				//setting up an Array to deal with the different proficiencies 
				$profArray = array();

				foreach($allVolunteers as $volunteer) {
					//get the memID for this volunteer
					$memID = $volunteer['MemID'];

					//SQL to get all their proficiency listings
					$sqlGetProficiency = "SELECT Proficiency FROM activity as a
											INNER JOIN members as m ON a.MemID = m.MemID
											INNER JOIN events as e ON e.EventID = a.EventID 
											WHERE e.TypeID = '$typeID' AND a.MemID = '$memID' AND a.Confirmed = 'Y'";

					if(mysqli_query($con, $sqlGetProficiency)) {
						//getting the results from the query 
						$profResult = mysqli_query($con, $sqlGetProficiency);
						$noOfProf = mysqli_num_rows($profResult);

						switch($noOfProf) {
							//if there's no Proficiency readings aka they haven't done this type before 
							case 0:
							$prof = -1;
							break;

							//if they only have one proficincy result, their proficiency is just based on that
							case 1:
							$profRow = mysqli_fetch_row($profResult);

							switch($profRow[0]) {
								case "G":
								$prof = 100;
								break;

								case "N":
								$prof = 50;
								break;

								case "B":
								$prof = 0;
								break;
							}
							break;

							//if multiple proficiency results 
							default:
							//set total to 0 
							$profTotal = 0;

							foreach($profResult as $profRow) {
								//add all proficiencies 
								switch($profRow['Proficiency']) {
									case "G":
									$profTotal += 100;
									break;

									case "N":
									$profTotal += 50;
									break;

									case "B":
									$profTotal += 0;
									break;
								}
							}
							//divive by number of proficiencies for an average 
							$prof = $profTotal / $noOfProf;
						}

						//put it into the associative array and link its MemID to the proficiency just calculated 
						$profArray[$memID] = $prof;

					} else {
						echo "Error: " . $sqlGetProficiency . "<br>" . mysqli_error($con);
					}

				}

				//sort the proficiency array in reverse order and maintains index associations 
				arsort($profArray);

				//go through each item of array - for each, let $memID by the index and $prof be the value from the array 
				foreach($profArray as $memID => $prof) {
					//get the name for this volunteer 
					$sqlGetName = "SELECT FName, LName FROM members WHERE MemID = '$memID'";
					
					if(mysqli_query($con, $sqlGetName)) {
						//get result from query 
						$nameResult = mysqli_query($con, $sqlGetName);
						$nameRow = mysqli_fetch_row($nameResult);
						
						//if the proficiency is higher than -1, output their name
						if($prof > -1) {
							echo $nameRow[0] . " " . $nameRow[1] . "</br>";

						//if not - aka it is -1 and they haven't done this event before
						} else {
							echo $nameRow[0] . " " . $nameRow[1] . " - First <br>";
						}

					} else {
						echo "Error: " . $sqlGetName . "<br>" . mysqli_error($con);
						break;
					}
				}

			} else {
				echo "Error: " . $sqlGetTypeID . "<br>" . mysqli_error($con);
			}

		}

	} else {
		echo "Error: " . $sqlGetVolunteers . "<br>" . mysqli_error($con);
	}
}

?>
			</div>

			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>