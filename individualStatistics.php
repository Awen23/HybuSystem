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
					<li class = "nav-text"><a href="Events.php">Events</a></li>
					<li class = "nav-text"><a href="Tracking.php">Tracking</a></li>
					<li class = "nav-text"><a href="Statistics.php" style="color:#E30713">Statistics</a></li>
					<li class = "nav-text"><a href="Registers.php">Registers</a></li>
					<li class = "nav-text"><a href="Volunteers.php">Volunteers</a></li>
				</ul>
			</div>

			</div>
		</nav>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite">
				<br>
				<div class="btn-group btn-group-justified">
    				<a href="Statistics.php" class="btn btn-primary mini-nav">Overall Statistics</a>
    				<a href="individualStatistics.php" class="btn btn-primary mini-nav" style="background-color: #E30713; color:white;">Personal Statistics</a>
    				<a href="filterStatistics.php" class="btn btn-primary mini-nav">Filters</a>
    				<a href="residentialStatistics.php" class="btn btn-primary mini-nav">Residentials</a>
  				</div>
  				<br><br>
  				<form method="post" action="individualStatistics.php" class="form-horizontal">
  					<div class="form-group">
  						<label class="control-label col-sm-4">First Name:</label>
  						<div class="col-sm-8"><input type="text" name="FName" class="form-control"></div>
  					</div>

  					<div class="form-group">
  						<label class="control-label col-sm-4">Last Name:</label>
  						<div class="col-sm-8"><input type="text" name="LName" class="form-control"></div>
  					</div>

  					<div class="form-group">
  						<center>
  							<input type="submit" name="submit" class="btn btn-default">
  						</center>
  					</div>

  				</form>

  				<div class="col-sm-5">	
  					<br><br>
<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

if(isset($_POST['submit'])) {
	//getting the names from the form
	$FName = $_POST['FName'];
	$LName = $_POST['LName'];

	//outputting a title for the user 
	echo "<h2> Statistics for: " . $FName . " " . $LName . '</h2> <br><div class="statistics"> <h4>';

	//sql to get the MemID of any users with the inputted first and last name 
    $sqlMemID = "SELECT MemID, CarNew FROM members WHERE FName = '$FName' AND LName = '$LName'";

    if(mysqli_query($con, $sqlMemID)) {
    	//result from the query 
    	$MemID = mysqli_query($con,$sqlMemID);

    	switch(mysqli_num_rows($MemID)) {
        case 0:
        //if there's no nows - aka no results aka no members with that first and last name 
        echo "Error: no member with the name " . $FName . " " . $LName;
        break;

        case 1:
        //if only one member is found
   		$memToShow = mysqli_fetch_row($MemID);
   		//putting in session for later use
		$_SESSION['MemID'] = $memToShow[0];

   		//FIRST STATISTIC - total no of hours tracked
   		$sqlTheirActivity = "SELECT a.EventID, e.ActualHours
					FROM events as e
					INNER JOIN activity as a ON e.EventID = a.EventID
					WHERE a.confirmed = 'Y' AND a.MemID = '$memToShow[0]'";

	if(mysqli_query($con, $sqlTheirActivity)) {
		//adding all the hours tracked 
		$theirActivity = mysqli_query($con, $sqlTheirActivity);
		$theirHours = 0;
		foreach($theirActivity as $act) {
			$theirHours += $act['ActualHours'];
		}

		//sql to get all the registers they've attended
		$sqlTheirRegisters = "SELECT MemID FROM attendance WHERE MemID = '$memToShow[0]'";

		if(mysqli_query($con, $sqlTheirRegisters)) {
			//adding these register hours to total hours (each register denotes 2 hours of time)
			$allRegisters = mysqli_query($con, $sqlTheirRegisters);
			$noRegisters = mysqli_num_rows($allRegisters);
			$noRegHours = $noRegisters * 2;
			$theirHours += $noRegHours;
		} else {
			echo "Error: " . $sqlTheirRegisters . "<br>" . mysqli_error($con);
		}

		//outputing total number of hours tracked 
		echo "No of Hours Tracked: " . $theirHours;


	
	} else {
		echo "Error: " . $sqlTheirActivity . "<br>" . mysqli_error($con);
	}

        break; 

        default:
        //if neither one nor 0 members are found - aka must be 2 or more 
        echo "Error: Multiple members with same name, please make all first & last names unique for each group";
        }

	} else {
		echo "Error: " . $sqlMemID . "<br>" . mysqli_error($con);
	}

if(isset($memToShow)) {
	//NEXT STATISTIC - training hours 
	$sqlTheirTraining = "SELECT a.EventID, e.ActualHours
					FROM events as e
					INNER JOIN activity as a ON e.EventID = a.EventID
					WHERE a.confirmed = 'Y' AND e.PracTrain = 'T' AND a.MemID = '$memToShow[0]'";

	if(mysqli_query($con, $sqlTheirTraining)) {
	//getting all the confirmed training hours from the result 
	$confirmedTraining = mysqli_query($con, $sqlTheirTraining);
	$trainingHours = 0;

	switch(mysqli_num_rows($confirmedTraining)) {
	//if no events, do nothing 
	case 0:
	break;

	//if one event, set total training hours for hours just for that event 
	case 1:
	$act = mysqli_fetch_row($confirmedTraining);
	$trainingHours += $act[1];
	break;

	//if multiple events, add all hours for them events 
	default:
	foreach($confirmedTraining as $act) {
		$trainingHours += $act['ActualHours'];
	}
}

	//add the number of register hours calculated earlier
	$trainingHours += $noRegHours;

	//outputting the result 
	echo "<br><br>Total Training Hours: " . $trainingHours;
} else {
	echo "Error: " . $sqlTheirTraining . "<br>" . mysqli_error($con);
}

	//NEXT STATISTIC - practical hours

	$sqlTheirPractical = "SELECT a.EventID, e.ActualHours
					FROM events as e
					INNER JOIN activity as a ON e.EventID = a.EventID
					WHERE a.confirmed = 'Y' AND e.PracTrain = 'P' AND a.MemID = '$memToShow[0]'";
if(mysqli_query($con, $sqlTheirPractical)) {
	//getting all the practical hours 
	$confirmedPractical = mysqli_query($con, $sqlTheirPractical);
	//setting total to 0 
	$practicalHours = 0;

	switch(mysqli_num_rows($confirmedPractical)) {
		//if no hours tracked, do nothing 
		case 0:
		break;

		//if one event, set total hours as hours for that event 
		case 1:
		$act = mysqli_fetch_row($confirmedPractical);
		$practicalHours += $act[1];
		break;

		//if multiple events, add all hours up from those events 
		default:

		foreach($confirmedPractical as $act) {
		$practicalHours += $act['ActualHours'];
			}
		}

		//outputting the total practical hours 
		echo "<br><br> Total Practical Hours: " . $practicalHours; 
} else {
	echo "Error: " . $sqlTheirPractical . "<br>" . mysqli_error($con);
}

	//NEXT STATISTIC - MEETING ATTENDANCE

//sql to get all attendance listed for them 
$sqlAttendance = "SELECT r.RegID FROM attendance as a
					INNER JOIN registers as r ON a.RegID = r.RegID
					WHERE a.MemID = '$memToShow[0]'";

if(mysqli_query($con, $sqlAttendance)) {
	//getting all attendance listings and number of attendances from this 
	$allAttendance = mysqli_query($con, $sqlAttendance);
	$noAttendance = mysqli_num_rows($allAttendance);
	
	//sql to get the number of registers for where they're based 
	$sqlNoReg = "SELECT RegID FROM registers
				WHERE CarNew = '$memToShow[1]'";

	if(mysqli_query($con, $sqlNoReg)) {
		//getting the result of all registers and number from this 
		$allReg = mysqli_query($con, $sqlNoReg);
		$noReg = mysqli_num_rows($allReg);

		//checking if it's 0 to avoid /0
		if($noReg !== 0) {
			//calculating average attendance for the user 
			$avgAttendance = $noAttendance / $noReg;
		} else {
			$avgAttendance = 0;
		}

		//outputting percentage meeting attendance 
		echo "<br><br>Average Meeting Attendance: " . round(($avgAttendance * 100), 1) . "%";
	} else {
		echo "Error: " . $sqlNoReg . "<br>" . mysqli_error($con);
	}

} else {
	echo "Error: " . $sqlAttendance . "<br>" . mysqli_error($con);
}

	//NEXT STATISTIC - AVG PROFICIENCY 

	//getting all proficiency listings for the user 
	$sqlGetAllProf = "SELECT Proficiency FROM activity WHERE MemID = '$memToShow[0]' AND Confirmed = 'Y'";

if(mysqli_query($con, $sqlGetAllProf)) {
	//getting result from query and no of proficiencies from this 
	$allProf = mysqli_query($con, $sqlGetAllProf);
	$noProf = mysqli_num_rows($allProf);

	//setting total to 0 
	$totalProf = 0;

	foreach($allProf as $prof) {
		
		switch($prof['Proficiency']) {
			case "B":
			//adding 0 for bad (aka doing nothing)
			break;

			case "N":
			//adding 1 for neutral 
			$totalProf += 1;
			break;

			case "G":
			//adding 2 for good
			$totalProf += 2;
			break;

		}
	}

	//calculating the average
	$avgProfNum = $totalProf / $noProf;

	//calculating which proficiency this is closest to 
	
	switch(round($avgProfNum, 0)) {
		case 0:
		$avgProf = "Bad";
		break;

		case 1:
		$avgProf = "Neutral";
		break;

		case 2:
		$avgProf = "Good";
		break;
	}

	//outputting the average proficiency 
	echo "<br><br> Average Proficiency: " . $avgProf;

} else {
	echo "Error: " . $sqlGetAllProf . "<br>" . mysqli_error($con);
}


}

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
                	//done when the form has been submitted 
                	if(isset($_POST['submit']) AND isset($_SESSION['MemID'])) {
                		$MemID = $_SESSION['MemID'];

                		//getting amount of times each event type has been done for this user 
	                	$sqlPieChart = "SELECT et.Name, COUNT(e.EventID) as Num FROM eventtype as et INNER JOIN events as e ON e.TypeID = et.typeID INNER JOIN activity as a ON a.EventID = e.EventID WHERE Finished = 'Y' AND a.MemID = '$MemID' GROUP BY(e.TypeID)"; // select column
	    				$pieChartResult = mysqli_query($con, $sqlPieChart);

	    				//going through the result and outputting each of them as a row in the data table 
	                    foreach($pieChartResult as $row){
	                        echo "['".$row["Name"]."', ".$row["Num"]."],";
	                    }
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
//only making the chart upon submitting the form 
if(isset($_POST['submit'])) {
    echo "<script>makeChart();</script>";
}
?>
  				</div>

 <?php
 
 if(isset($_POST['submit'])) {

 	//putting into the session for use on the next page - link shows all individual events tracked

 	echo 	'<div class="col-sm-12">
 				<center><h5><a href="allEventsTracked.php">View all hours tracked for ' . $FName . " " . $LName . '</a></h5></center>
 			</div>';
 }

 ?>
				
			</div>
		</div>
		<div id="endGradient"></div>
  </body>
</html>