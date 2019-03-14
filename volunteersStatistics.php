<?php
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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

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
					<li class = "nav-text"><a href="volunteersEvents.php">Events</a></li>
					<li class = "nav-text"><a href="volunteersStatistics.php" style="color:#E30713">Statistics</a></li>
					<li class = "nav-text"><a href="volunteersDetails.php">Add Details</a></li>
				</ul>
			</div>

			</div>
		</nav>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">
				<br>

  				<div class="col-sm-5">	
  					<br>
<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	$memID = $_SESSION['VMemID'];

	$sqlGetName = "SELECT FName, LName, CarNew FROM members WHERE MemID = '$memID'";

	if(mysqli_query($con, $sqlGetName)) {
		$nameResult = mysqli_query($con, $sqlGetName);
		$nameRow = mysqli_fetch_row($nameResult);
		$FName = $nameRow[0];
		$LName = $nameRow[1];
		$carNew = $nameRow[2];

		echo "<h3>Showing statistics for: " . $FName . " " . $LName . '</h3><div class="statistics"><h4>';
	} else {
		echo "Error: " . $sqlGetName . "<br>" . mysqli_error($con);
	}

	//FIRST STATISTIC - total no of hours tracked

		//sql to get all activity listings 
   		$sqlTheirActivity = "SELECT a.EventID, e.ActualHours
					FROM events as e
					INNER JOIN activity as a ON e.EventID = a.EventID
					WHERE a.confirmed = 'Y' AND a.MemID = '$memID'";

	if(mysqli_query($con, $sqlTheirActivity)) {
		$theirActivity = mysqli_query($con, $sqlTheirActivity);

		//setting hours to 0 to start off with 
		$theirHours = 0;

		//adding all the hours 
		foreach($theirActivity as $act) {
			$theirHours += $act['ActualHours'];
		}

		//sql to get all the registers 
		$sqlTheirRegisters = "SELECT MemID FROM attendance WHERE MemID = '$memID'";

		if(mysqli_query($con, $sqlTheirRegisters)) {
			//getting the result, then getting hours by multiplying number of results by 2 (each register is worth 2 hours)
			$allRegisters = mysqli_query($con, $sqlTheirRegisters);
			$noRegisters = mysqli_num_rows($allRegisters);
			$noRegHours = $noRegisters * 2;

			//adding these hours to total hours 
			$theirHours += $noRegHours;
		} else {
			echo "Error: " . $sqlTheirRegisters . "<br>" . mysqli_error($con);
		}

		//outputting total number of hours 
		echo "No of Hours Tracked: " . $theirHours;


	
	} else {
		echo "Error: " . $sqlTheirActivity . "<br>" . mysqli_error($con);
	}

	//NEXT STATISTIC - training hours 

	//sql to get all training hours 
	$sqlGetTraining = "SELECT a.EventID, e.ActualHours
					FROM events as e
					INNER JOIN activity as a ON e.EventID = a.EventID
					WHERE a.confirmed = 'Y' AND e.PracTrain = 'T' AND a.MemID = '$memID'";

	if(mysqli_query($con, $sqlGetTraining)) {
	$confirmedTraining = mysqli_query($con, $sqlGetTraining);

	//setting training hours to 0 
	$trainingHours = 0;

	switch(mysqli_num_rows($confirmedTraining)) {
	//if no results, do nothing 
	case 0:
	break;

	//if only one result, set result to that 
	case 1:
	$act = mysqli_fetch_row($confirmedTraining);
	$trainingHours += $act[1];
	break;

	//if multiple results, add all hours 
	default:
	foreach($confirmedTraining as $act) {
		$trainingHours += $act['ActualHours'];
	}
}

	//adding the reg hours from earlier 
	$trainingHours += $noRegHours;

	//outputting these training hours 
	echo "<br><br>Total Training Hours: " . $trainingHours;
} else {
	echo "Error: " . $sqlGetTraining . "<br>" . mysqli_error($con);
}

	//NEXT STATISTIC - practical hours

	//sql to get all practical events 
	$sqlGetPractical = "SELECT a.EventID, e.ActualHours
					FROM events as e
					INNER JOIN activity as a ON e.EventID = a.EventID
					WHERE a.confirmed = 'Y' AND e.PracTrain = 'P' AND a.MemID = '$memID'";
	if(mysqli_query($con, $sqlGetPractical)) {
	$confirmedPractical = mysqli_query($con, $sqlGetPractical);

	//setting practical hours to 0
	$practicalHours = 0;

	switch(mysqli_num_rows($confirmedPractical)) {
		//if no hours, do nothing
		case 0:
		break;

		//if there's only one result, practical hours are just hours for that event
		case 1:
		$act = mysqli_fetch_row($confirmedPractical);
		$practicalHours += $act[1];
		break;

		//if there's multiple events, add all event hours to practical hours 
		default:

		foreach($confirmedPractical as $act) {
		$practicalHours += $act['ActualHours'];
			}
		}

		//output total tractical hours 
		echo "<br><br> Total Practical Hours: " . $practicalHours; 
	} else {
		echo "Error: " . $sqlGetTraining . "<br>" . mysqli_error($con);
	}

	//NEXT STATISTIC - MEETING ATTENDANCE

//sql to get all attendance listings for member
$sqlAttendance = "SELECT r.RegID FROM attendance as a
					INNER JOIN registers as r ON a.RegID = r.RegID
					WHERE a.MemID = '$memID'";

if(mysqli_query($con, $sqlAttendance)) {
	//getting all attendance and getting number of results from this 
	$allAttendance = mysqli_query($con, $sqlAttendance);
	$noAttendance = mysqli_num_rows($allAttendance);
	
	//getting all registers which are possible for them to attend (aka ones in their location, Cardiff or Newport)
	$sqlNoReg = "SELECT RegID FROM registers
				WHERE CarNew = '$carNew'";

	if(mysqli_query($con, $sqlNoReg)) {
		//getting the result and number of registers from that result 
		$allReg = mysqli_query($con, $sqlNoReg);
		$noReg = mysqli_num_rows($allReg);

		//checking $noReg isn't 0 to stop /0 errors
		if($noReg !== 0) {
			$avgAttendance = $noAttendance / $noReg;
		} else {
			$avgAttendance = 0;
		}

		//outputting meeting attendance
		echo "<br><br>Meeting Attendance: " . round(($avgAttendance * 100), 1) . "%";
	} else {
		echo "Error: " . $sqlNoReg . "<br>" . mysqli_error($con);
	}

} else {
	echo "Error: " . $sqlAttendance . "<br>" . mysqli_error($con);
}

?>
				</h4>
				</div>
  				</div>

  				<div class="col-sm-7">
  					<div id="inidivualPieChart"></div>

    <script type="text/javascript">

    	function makeChart() {
        google.charts.load('current', {'packages':['corechart']});

        google.charts.setOnLoadCallback(drawChart);
    }

        function drawChart(){
            var data = new google.visualization.DataTable();
            var data = google.visualization.arrayToDataTable([
                ['Name','Num'],

                <?php

                	//sql to get amount of times each event type has been attended by the user 
                	$sqlPieChart = "SELECT et.Name, COUNT(e.EventID) as Num FROM eventtype as et INNER JOIN events as e ON e.TypeID = et.typeID INNER JOIN activity as a ON a.EventID = e.EventID WHERE Finished = 'Y' AND a.MemID = '$memID' GROUP BY(e.TypeID)";
	    			$pieChartResult = mysqli_query($con, $sqlPieChart);

	    			//outputs each event type and number in the row
	               	foreach($pieChartResult as $row){
	                   	echo "['". $row["Name"]."', ". $row["Num"]."],";
	               	}
                ?>

               ]);

            var options = {
                title: 'Events Completed Per Type',
                height: 500,
                legend: { position: 'bottom' }
            };

            var chart = new google.visualization.PieChart(document.getElementById('inidivualPieChart'));
            chart.draw(data, options);
        }

    </script>
<?php

	//making the chart
    echo "<script>makeChart();</script>"

?>
  				</div>

 <?php



 	//putting into the session for use on the next page

 	echo 	'<div class="col-sm-12">
 				<center><h5><a href="volunteersAllEvents.php">View all hours tracked for ' . $FName . " " . $LName . '</a></h5></center>
 			</div>'


 ?>
				
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>