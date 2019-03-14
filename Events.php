<?php
//checks they're logged in as a leader and should have access to this page
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="hybustyle.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet">

    <script>
    	//to enable popups on the calendar
	$(document).ready(function(){
	    $('[data-toggle="popover"]').popover();  
	});
	</script>

    <style>

    th {
    	height:30px;
    	text-align:center;
    	font-weight:700;
    }

    td {
    	height:100px;
    	width:13%;
    }

    .today {
    	background:orange;
    }
/* Making Saturday and Sunday have a different text colour */
    th:nth-of-type(7),td:nth-of-type(7),th:nth-of-type(1),td:nth-of-type(1) {
    	color:blue;
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

<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}
	
//Setting the timezone - always going to be London timezone
date_default_timezone_set('Europe/London');

//Getting the previous and next month
if(isset($_GET['ym'])) {
	$ym = $_GET['ym'];
} else {
	//This month 
	$ym = date('Y-m');
}

//Check format
$timestamp = strtotime($ym, "-01");
if($timestamp === false) {
	$timestamp = time();
}

//setting today and tomorrow 
$today = date('Y-m-d', time());
$tomorrow = date('Y-m-d', strtotime($today .' +1 day'));

//for title
$htmlTitle = date('Y / m', $timestamp);

//creates the previous and next month links 
$prev = date('Y-m', mktime(0,0,0, date('m', $timestamp)-1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0,0,0, date('m', $timestamp)+1, 1, date('Y', $timestamp)));

//number of days in the month
$dayCount = date('t', $timestamp);

//for setting out the days of the week, 0:Sun, 1:Mon, 2:Tue... 
$str = date('w', mktime(0,0,0, date('m', $timestamp), 1, date('Y', $timestamp)));

//to create the calendar for current month
$weeks = array();
$week = '';

//add empty cells for however many days are before the first date determined earlier in str (e.g. if first existing day Tuesday, str will be 2, and there will be two empty cells before it which cover Sunday and Monday)
$week .= str_repeat('<td></td>', $str);

//goes through all the days in the month 
for($day = 1; $day <= $dayCount; $day++, $str++) {

	//0 was ommitted when date less than 0 with just using day, affecting the comparison with today, so adds that 0 when date under 10
	if($day < 10) {
		$date = $ym . '-0' . $day;
	} else {
		$date = $ym . '-' . $day;
	}

	//get events to show on the calendar 
	$sqlGetAnyEvents = "SELECT e.EventID, et.Name, e.StartTime, e.EndTime, e.Date, e.Location, et.MinPeo FROM events as e 
						INNER JOIN eventtype as et ON et.TypeID = e.TypeID
						WHERE Date = '$date'";

	//run the query - not in an if else statement with an error because it would generate an error for each day - overwhelming the user 
	$anyEvents = mysqli_query($con, $sqlGetAnyEvents);

	//colours in today's date
	if($today === $date) {
		$week .= '<td class = "today" id="' . $day . '" onClick="addDate(' . $day . ", '" . $date . "'" . ')">' . $day;
	} else {
		$week .= '<td id="' . $day . '" onClick="addDate(' . $day . ", '" . $date . "'" . ')">' . $day;
	}

	//makes sure it only runs if there is an event on that day, i.e. a row was returned in the query on this date
	if(mysqli_num_rows($anyEvents) !== 0) {
		//goes through all events so more than one can be displayed on the same day 
		foreach($anyEvents as $event) {
			//getting the number who are attending by getting results for all attending and counting the rows 
			$eventID = $event['EventID'];
			$sqlGetNoAttending = "SELECT ActID FROM activity WHERE EventID = '$eventID'";
			$noAttending = mysqli_num_rows(mysqli_query($con, $sqlGetNoAttending));
			
			//checks if number attending is less than the minimum people for this event type, turns text red if so 
			if ($noAttending < $event['MinPeo']) {
				//adds in the hover over popup and outputs red text for the event as less people going to event than minimum people
				$week .= '<br> <text style="color:red" title="' . $event['Name'] . '" data-toggle="popover" data-html="true" data-trigger="hover" data-content="<b>Location: </b>' . $event['Location'] . '<br> <b>Time:</b> ' . $event['StartTime'] . ' - ' . $event['EndTime'] . '">';
			} else {
				//adds in the hover over popup
				$week .= '<br> <text style="color:black;" title="' . $event['Name'] . '" data-toggle="popover" data-html="true" data-trigger="hover" data-content="<b>Location: </b>' . $event['Location'] . '<br> <b>Time:</b> ' . $event['StartTime'] . ' - ' . $event['EndTime'] . '">';
			}

			//closes adds event name, closes text tag 
			$week .= $event['Name'] . '</text>';
		}
	}

	//ends cell
	$week .= '</td>';

	//checks if end of week or end of the month
	if($str % 7 === 6 || $day == $dayCount) {
		if($day == $dayCount) {
			//adds empty cells at the end if it's the end of the month, as it did for start of month 
			$week .= str_repeat('<td></td>', 6 - ($str%7));
		}

		//creates the row for this week 
		$weeks[] = '<tr>' . $week . '</tr>';

		//prepare for new week 
		$week = '';
	}
}

?>
				<div class = "container col-sm-12" id="#calContainer">
					<br>
					<div class="alert alert-info alert-dismissible">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
						Select a date to add a new event, or select multiple dates through holding ctrl<br>
						Red text signifies an event has less than the minimum amount of people</div>
<?php

//to check if there's any events tomorrow
$sqlEventTomorrow = "SELECT et.Name FROM events as e
						INNER JOIN eventtype as et ON e.TypeID = et.TypeID
						WHERE Date = '$tomorrow'";

if(mysqli_query($con, $sqlEventTomorrow)) {
	//getting the result from the query & number of events tomorrow 
	$eventTomorrowResult = mysqli_query($con, $sqlEventTomorrow);
	$noEvents = mysqli_num_rows($eventTomorrowResult);

	//if there's one, get the event and show an information popup saying it's happening tomorrow 
	if($noEvents === 1) {
		$eventRow = mysqli_fetch_row($eventTomorrowResult);

		echo '<div class="alert alert-warning alert-dismissible">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
						Reminder: <b>' . $eventRow[0] . '</b> is happening tomorrow</div>';

	//if there's more than one event, do the same but with multiple events listed in the popup 
	} elseif($noEvents >= 2) {
		echo '<div class="alert alert-warning alert-dismissible">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
						Reminder: <br>';
		foreach($eventTomorrowResult as $event) {
			echo '<b>' . $event['Name'] . "</b><br> ";
		}

		echo  ' are happening tomorrow</div>';
	} 
} else {
	echo "Error: " . $sqlEventTomorrow . "<br>" . mysqli_error($con);
}

?>
					<!-- Outputting the links for previous and next month along with the title for this month, all defined earlier -->
					<h3><a href="?ym=<?php echo $prev; ?>"> < </a> <?php echo $htmlTitle; ?> <a href="?ym=<?php echo $next; ?>"> > </a></h3>
					<br>
					<table class="table table-bordered">
						<!-- Top row with days of the week -->
						<tr>
							<th>S</th>						
							<th>M</th>						
							<th>T</th>				
							<th>W</th>						
							<th>T</th>						
							<th>F</th>						
							<th>S</th>
						</tr>
					<?php
						//goes through all of the weeks in the weeks array created earlier 
						foreach ($weeks as $week) {
							echo $week;
						}
					?>
					</table>
<?php

?>
				</div>
				<br>
				<script>
					var date = "";

					function clearColours() {
						//goes through all cells, <= 31 as max number possible is 31 in one month 
						for (i=1; i<=31; i++) {
								var ele = document.getElementById(i);
								//checks if null, so it doesn't throw an error when there's less than 31 days and it tries to find the 31st day, then turns white
								if(ele!=null) {
									ele.style.backgroundColor = "white";
								}
							}
					}

					function addDate(ID, selectedDate) {

						//ctrl key should enable multiple days to be selected, so checking if it's been pressed
						if (event.ctrlKey) {
							if (date === "") {
								//adding only this one date if no other dates selected yet
								date = selectedDate
							} else {
							//checking if selected date is already in the dates textbox, so it doesn't put in same date twice
							if(date.indexOf(selectedDate) === -1) {
							//adds selected date to list of dates
							var soFar = date;
							date = soFar.concat(", ", selectedDate);
							}
							}
						//turns cell to light grey 
						document.getElementById(ID).style.backgroundColor = "#dddddd";
						} else {
							//puts just this date into dates, as ctrl key is not pressed so just wants to refer to this one
							date = selectedDate
							//turns all other cells white 
							clearColours();
							//turns selected cell a light grey 
							document.getElementById(ID).style.backgroundColor = "#dddddd";
						}

						//puts the date value into the textbox 
						document.getElementById("datesText").value = date;
					}

				</script>
				<form method="post">
					<!-- Point of this section is to be able to send the selected values onto the next page -->
					<label class="col-sm-4">Date(s) Selected:</label> <div class="col-sm-4"> <input class="form-control" name="dates" id="datesText" readonly></input></div>
					<!-- formaction so they all submit to different pages, most submit to a page which then puts variable in session then redirects to proper page -->
					<div class="col-sm-4"><button class="btn btn-default" formaction="newEventDate.php">Add Event</button></div>
					<br><center><h3>Or:</h3>
						<div class="col-sm-4"> <button class="btn btn-default" formaction="editEvent.php">Edit an Event</button> </div>
						<div class="col-sm-4"> <button class="btn btn-default" formaction="editType.php">Edit Event Types</button> </div>
						<div class="col-sm-4"> <button class="btn btn-default" formaction="newReport.php">View Report</button> </div>
					</center>
				</form>
				<br><br><br>
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>