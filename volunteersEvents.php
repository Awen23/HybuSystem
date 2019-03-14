<?php
//checks user is logged in - can be member or volunteer
session_start();
if(!isset($_SESSION['VMemID'])) {
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
    th:nth-of-type(7),td:nth-of-type(7), th:nth-of-type(1),td:nth-of-type(1) {
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
					<li class = "nav-text"><a href="volunteersEvents.php" style="color:#E30713">Events</a></li>
					<li class = "nav-text"><a href="volunteersStatistics.php">Statistics</a></li>
					<li class = "nav-text"><a href="volunteersDetails.php">Add Details</a></li>
				</ul>
			</div>
				

			</div>
		</nav>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">
				<br>
				<div class="alert alert-info alert-dismissible">
				<a href="#" class="close" data-dismiss="alert">&times;</a>
				Red text signifies the event has less people than the minimum, sign up to them if possible!</div>

<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}
	
//Setting the timezone
date_default_timezone_set('Europe/London');

//Getting previous and next month
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
 
//Today
$today = date('Y-m-d', time());

//for title
$htmlTitle = date('Y / m', $timestamp);

//prev & next month link...mktime(hour,minute,second,month,day,year)
$prev = date('Y-m', mktime(0,0,0, date('m', $timestamp)-1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0,0,0, date('m', $timestamp)+1, 1, date('Y', $timestamp)));

//number of days in the month
$dayCount = date('t', $timestamp);

//0:Sun, 1:Mon, 2:Tue... 
$str = date('w', mktime(0,0,0, date('m', $timestamp), 1, date('Y', $timestamp)));

//Create calendar
$weeks = array();
$week = '';

//add empty cell
$week .= str_repeat('<td></td>', $str);

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
	if($today == $date) {
		$week .= '<td class = "today" id="' . $day . '">' . $day;
	} else {
		$week .= '<td id="' . $day . '">' . $day;
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
				$week .= '<br> <text style="color:red" title="' . $event['Name'] . '" data-toggle="popover" data-html="true" data-trigger="hover" data-content="<b>Location: </b>' . $event['Location'] . '<br> <b>Time:</b> ' . $event['StartTime'] . ' - ' . $event['EndTime'] . '">';
			} else {
				$week .= '<br> <text style="color:black;" title="' . $event['Name'] . '" data-toggle="popover" data-html="true" data-trigger="hover" data-content="<b>Location: </b>' . $event['Location'] . '<br> <b>Time:</b> ' . $event['StartTime'] . ' - ' . $event['EndTime'] . '">';
			}

			//closes adds event name, closes text tag 
			$week .= $event['Name'] . '</text>';
		}
	}

	//ends cell
	$week .= '</td>';

	//end of week or end of the month
	if($str % 7 === 6 || $day == $dayCount) {
		if($day == $dayCount) {
			$week .= str_repeat('<td></td>', 6 - ($str%7));
		}

		$weeks[] = '<tr>' . $week . '</tr>';

		//prepare for new week 
		$week = '';
	}
}

?>
				<div class = "container col-sm-12" id="#calContainer">
					<h3><a href="?ym=<?php echo $prev; ?>"> < </a> <?php echo $htmlTitle; ?> <a href="?ym=<?php echo $next; ?>"> > </a></h3>
					<br>
					<table class="table table-bordered">
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
						foreach ($weeks as $week) {
							echo $week;
						}
					?>
					</table>
				</div>
				<br>
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>