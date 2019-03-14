<?php
//checks they're logged in as a leader before continuing 
session_start();
if(isset($_SESSION['Type'])) {
	if($_SESSION['Type'] === "V") {
		echo '<script>window.location.href="volunteersEvents.php"</script>';
	} 
} else {
	echo '<script>window.location.href="loginScreen.php"</script>';
}

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	//sql to get the name and number of events with that type for all finished events 
    $sqlPieChart = "SELECT et.Name, COUNT(e.EventID) as Num FROM eventtype as et INNER JOIN events as e ON e.TypeID = et.typeID WHERE Finished = 'Y' GROUP BY(e.TypeID)"; // select column
    $pieChartResult = mysqli_query($con, $sqlPieChart);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
  	<title>Statistics</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="http://localhost/hybu/hybustyle.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    	//loads up google charts
        google.charts.load('current', {'packages':['corechart']});

        //creates the data table
        google.charts.setOnLoadCallback(drawChart);
        function drawChart(){
            var data = new google.visualization.DataTable();
            var data = google.visualization.arrayToDataTable([
            	//column titles
                ['Name','Num'],
                <?php
                	//gets the results from the sql query and puts them in
                    foreach($pieChartResult as $row){
                        echo "['".$row["Name"]."', ".$row["Num"]."],";
                    }
                ?>
               ]);

            var options = {
          		//setting title, height, and putting the legens at the bottom 
                title: 'Events Completed Per Type',
                height: 500,
                legend: { position: 'bottom' }
            };

            //draws the chart in the specified ID
            var chart = new google.visualization.PieChart(document.getElementById('pieChart'));
            chart.draw(data, options);
        }

    </script>
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
    				<a href="Statistics.php" class="btn btn-primary mini-nav" style="background-color: #E30713; color:white;">Overall Statistics</a>
    				<a href="individualStatistics.php" class="btn btn-primary mini-nav">Personal Statistics</a>
    				<a href="filterStatistics.php" class="btn btn-primary mini-nav">Filters</a>
    				<a href="residentialStatistics.php" class="btn btn-primary mini-nav">Residentials</a>
  				</div>

  				<div class="col-sm-6">
  					<br>
  					<h2>Statistics</h2>
  					<br>
  					<div class="statistics">
  					<h4>
  					Hours Logged: 
<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
	
	if (!$con) {
    	die("Connection failed: " . mysqli_connect_error());
	}

//gets all activity that has been submitted as finally tracked 
$sqlGetActivity = "SELECT a.EventID, e.ActualHours
					FROM events as e
					INNER JOIN activity as a ON e.EventID = a.EventID
					WHERE a.confirmed = 'Y'";

if(mysqli_query($con, $sqlGetActivity)) {
	//gets the result of all confirmed activity
	$confirmedActivity = mysqli_query($con, $sqlGetActivity);

	//setting total hours to 0 then adding all hours for each activity 
	$totalHours = 0;
	foreach($confirmedActivity as $act) {
		$totalHours += $act['ActualHours'];
	}

	//sql to get all registers 
	$sqlGetRegisters = "SELECT MemID FROM attendance";

	if(mysqli_query($con, $sqlGetRegisters)) {
		//getting all registers, then getting number of registers from it
		$allRegisters = mysqli_query($con, $sqlGetRegisters);
		$noRegisters = mysqli_num_rows($allRegisters);

		//hours is number of registers times 2 (each meeting is 2 hours long)
		$noRegHours = $noRegisters * 2;

		//adding this to the total number of hours 
		$totalHours += $noRegHours;
	} else {
		echo "Error: " . $sqlGetRegisters . "<br>" . mysqli_error($con);
	}

	//outputting the total amount of hours 
	echo $totalHours;

} else {
	echo "Error: " . $sqlGetActivity . "<br>" . mysqli_error($con);
}

?>
					<br><br>Training hours:
<?php

//sql to get the training events 
$sqlGetTraining = "SELECT a.EventID, e.ActualHours
					FROM events as e
					INNER JOIN activity as a ON e.EventID = a.EventID
					WHERE a.confirmed = 'Y' AND e.PracTrain = 'T'";

if(mysqli_query($con, $sqlGetTraining)) {
$confirmedTraining = mysqli_query($con, $sqlGetTraining);
//setting training hours to 0 
$trainingHours = 0;

switch(mysqli_num_rows($confirmedTraining)) {
	//if no training hours, do nothing 
	case 0:
	break;

	case 1:
	//if one result, make total just that number of hours 
	$train = mysqli_fetch_row($confirmedTraining);
	$trainingHours += $train[1];
	break;

	default:
	//if multiple training hours, go through and add each 
	foreach($confirmedTraining as $train) {
	$trainingHours += $train['ActualHours'];
	}
}

	//add reg hours counted earlier to total training hours
	$trainingHours += $noRegHours;

	//output training hours 
	echo $trainingHours;
} else {
	echo "Error: " . $sqlGetTraining . "<br>" . mysqli_error($con);
}

?>
					<br><br>Practical hours:

<?php

//sql to get all the practical hours 
$sqlGetPractical = "SELECT a.EventID, e.ActualHours
					FROM events as e
					INNER JOIN activity as a ON e.EventID = a.EventID
					WHERE a.confirmed = 'Y' AND e.PracTrain = 'P'";
if(mysqli_query($con, $sqlGetPractical)) {
$confirmedPractical = mysqli_query($con, $sqlGetPractical);
//setting total to 0 
$practicalHours = 0;

switch(mysqli_num_rows($confirmedPractical)) {
	//if no results, do nothing 
	case 0:
	break;

	case 1:
	//if only one result, set total hours to hours for that result 
	$act = mysqli_fetch_row($confirmedPractical);
	$practicalHours += $act['ActualHours'];
	break;

	default:
	//if multiple results, set total hours as them all added up 
	foreach($confirmedPractical as $act) {
	$practicalHours += $act['ActualHours'];
		}
	}

	//output total practical hours 
	echo $practicalHours; 
} else {
	echo "Error: " . $sqlGetPractical . "<br>" . mysqli_error($con);
}



?>
					<br><br> Average Attendance in Cardiff:
<?php

//sql to get all attendance listings for cardiff 
$sqlCardAttendance = "SELECT a.MemID FROM attendance as a
						INNER JOIN registers as r ON a.RegID = r.RegID
						WHERE r.CarNew = 'C'";

if(mysqli_query($con, $sqlCardAttendance)) {
	//getting number of cardiff attendances from the result
	$allCardAttendance = mysqli_query($con, $sqlCardAttendance);
	$noCardAttendance = mysqli_num_rows($allCardAttendance);
	
	//sql to get all created cardiff registers 
	$sqlNoCardReg = "SELECT RegID FROM registers WHERE CarNew = 'C'";

	if(mysqli_query($con, $sqlNoCardReg)) {
		//getting result then number of registers from result 
		$allCardReg = mysqli_query($con, $sqlNoCardReg);
		$noCardReg = mysqli_num_rows($allCardReg);

		//checking number not 0 to avoid /0 errors 
		if($noCardReg !== 0) {
			$avgCardAttendance = $noCardAttendance / $noCardReg;
		} else {
			$avgCardAttendance = 0;
		}

		//outputting average attendance rounded so the number isn't too long 
		echo round($avgCardAttendance, 1) . " people";
	} else {
		echo "Error: " . $sqlNoCardReg . "<br>" . mysqli_error($con);
	}

} else {
	echo "Error: " . $sqlCardAttendance . "<br>" . mysqli_error($con);
}


?>
					<br><br> Average Attendance in Newport:
<?php

//getting all attendance listings for Newport 
$sqlNewpAttendance = "SELECT a.MemID FROM attendance as a
						INNER JOIN registers as r ON a.RegID = r.RegID
						WHERE r.CarNew = 'N'";

if(mysqli_query($con, $sqlNewpAttendance)) {
	//getting result and getting no of attendances from the result 
	$allNewpAttendance = mysqli_query($con, $sqlNewpAttendance);
	$noNewpAttendance = mysqli_num_rows($allNewpAttendance);
	
	//sql to get all newport registers
	$sqlNoNewpReg = "SELECT RegID FROM registers WHERE CarNew = 'N'";

	if(mysqli_query($con, $sqlNoNewpReg)) {
		//getting result and number of newport registers from the result 
		$allNewpReg = mysqli_query($con, $sqlNoNewpReg);
		$noNewpReg = mysqli_num_rows($allNewpReg);

		//checking isn't 0 to avoid /0 errors 
		if($noNewpReg !== 0) {
			$avgNewpAttendance = $noNewpAttendance / $noNewpReg;
		} else {
			$avgNewpAttendance = 0;
		}

		//outputting rounded to make sure it isn't too long 
		echo round($avgNewpAttendance, 1) . " people";
	} else {
		echo "Error: " . $sqlNoNewpReg . "<br>" . mysqli_error($con);
	}

} else {
	echo "Error: " . $sqlNewpAttendance . "<br>" . mysqli_error($con);
}


?>
					<br><br> Number of Registered Volunteers:
<?php

//getting all registered members
$sqlGetAllMem = "SELECT MemID FROM members";

if(mysqli_query($con, $sqlGetAllMem)) {
	//getting result and counting number of members from that 
	$memResult = mysqli_query($con, $sqlGetAllMem);
	$numMembers = mysqli_num_rows($memResult);

	//outputting number of members 
	echo " " . $numMembers; 
} else {
	echo "Error: " . $sqlGetAllMem . "<br>" . mysqli_error($con);
}

?>
					<br><br> Average proficiency:

<?php

//sql to get all proficiency listings 
$sqlGetAllProf = "SELECT Proficiency FROM activity";

if(mysqli_query($con, $sqlGetAllProf)) {
	//getting result and getting no of proficiencies from that 
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

	//outputting average proficiency 
	echo $avgProf;

} else {
	echo "Error: " . $sqlGetAllProf . "<br>" . mysqli_error($con);
}

 ?>
 				</h4>
 			</div>
  				</div>

  				<div class="col-sm-6">
  					<div id="pieChart"> </div>
  				</div>

			</div>
		</div>
		<div id="endGradient"> </div>
  </body>
</html>