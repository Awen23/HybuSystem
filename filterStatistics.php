<?php
//checking user is logged in 
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
    				<a href="individualStatistics.php" class="btn btn-primary mini-nav">Personal Statistics</a>
    				<a href="filterStatistics.php" class="btn btn-primary mini-nav" style="background-color: #E30713; color:white;">Filters</a>
    				<a href="residentialStatistics.php" class="btn btn-primary mini-nav">Residentials</a>
  				</div>
				
				<div class="col-sm-5">
  					<h3>Filter By:</h3>

  					<form class="form-inline" method="post" action="filterStatistics.php">

  						<div class="form-group col-sm-12">
  							Completed <b>over</b> 
  							<input type="number" step="0.25" class="form-control" name="overHours" min="0">
  							practical hours 
  						</div>
  							<br><br><br>
  						<div class="form-group col-sm-12">
  							Completed <b>under</b>
  							<input type="number" step="0.25" class="form-control" name="underHours" min="0">
  							practical hours
  						</div>
  							<br><br><br>
  						<div class="form-group col-sm-12">
  							Meeting attendance <b>over</b>
  							<input type="number" step="1" class="form-control" name="attendanceOver" min="0" max="100">
  							%
  						</div>
  							<br><br><br>
  						<div class="form-group col-sm-12">
  							Meeting attendance <b>under</b>
  							<input type="number" step="1" class="form-control" name="attendanceUnder" min="0" max="100">
  							%
  						</div>
  							<br><br><br>
  						<div class="form-group col-sm-12">
  							<center>
  								<input type="submit" name="submit" class="btn btn-default">
  							</center>
  						</div>
  							<br><br><br>

  					</form>
  				</div>

  				<div class="col-sm-7">

            <h3>Results:</h3>
            <div class="statistics">
              <h4>

 <?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
  
  if (!$con) {
      die("Connection failed: " . mysqli_connect_error());
  }

//upon submitting the form 
if(isset($_POST['submit'])) {

//getting any values from the form 
$overHours = $_POST['overHours'];
$underHours = $_POST['underHours'];
$attendanceOver = $_POST['attendanceOver'];
$attendanceUnder = $_POST['attendanceUnder'];

  //checking only one of the textboxes are filled 
  if($overHours !== "" XOR $underHours !== "" XOR $attendanceOver !== "" XOR $attendanceUnder !== "") {

    //PRACTICAL HOURS SECTION 

    if($overHours !== "" OR $underHours) {

      //getting all members that have at least one activity listing 
      $sqlGetMembers= "SELECT DISTINCT a.MemID, m.FName, m.LName FROM activity as a
                        INNER JOIN members as m on m.MemID = a.MemID";

      if(mysqli_query($con, $sqlGetMembers)) {
        $allMembers = mysqli_query($con, $sqlGetMembers);
        //tracking if there's any members at all in the results 
        $anyMember = "F";

        //looping through all members
        foreach($allMembers as $mem) {
          //getting the MemID for the user
          $memID = $mem['MemID'];

          //sql to get all their practical hours 
          $sqlGetPractical = "SELECT a.EventID, e.ActualHours
          FROM events as e
          INNER JOIN activity as a ON e.EventID = a.EventID
          WHERE a.confirmed = 'Y' AND e.PracTrain = 'P' AND a.MemID = '$memID'";

          if(mysqli_query($con, $sqlGetPractical)) {
            $confirmedPractical = mysqli_query($con, $sqlGetPractical);
            //setting total practical hours to nothing 
            $practicalHours = 0;

          switch(mysqli_num_rows($confirmedPractical)) {
            case 0:
            break;

            //if one event, total hours will be just the hours for that event 
            case 1:
            $act = mysqli_fetch_row($confirmedPractical);
            $practicalHours += $act[1];
            break;

            //if multiple events, add together all the hours 
            default:

            foreach($confirmedPractical as $act) {
            $practicalHours += $act['ActualHours'];
              }
            }

          //if it was the over hours box filled, output their name if they're over the amount of hours 
          if($overHours !== "") {
            if($practicalHours >= $overHours) {
              echo "<b>" . $mem['FName'] . " " . $mem['LName'] . "</b> " . $practicalHours . " hours <br>";
              $anyMember = "T";
            }
          }

          //if it was the under hours box filled, output their name if they're under the amount of hours 
          if($underHours !== "") {
            if($practicalHours <= $underHours) {
              echo "<b>" . $mem['FName'] . " " . $mem['LName'] . "</b> " . $practicalHours . " hours <br>";
              $anyMember = "T";
            }

          }
        } else {
          echo "Error: " . $sqlGetPractical . "<br>" . mysqli_error($con);
      }

    }

    //if no results were found aka $anyMember was never set to T
    if($anyMember === "F") {
      echo "No results found";
    }

  } else {
    echo "Error " . $sqlGetMembers . "<br>" . mysqli_error($con);
  } 

}

//ATTENDANCE SECTION 

if($attendanceOver !== "" OR $attendanceUnder) {

  //get all members in the attendance section at least once 
  $sqlGetMembers= "SELECT DISTINCT a.MemID, m.FName, m.LName, m.CarNew FROM attendance as a
                        INNER JOIN members as m on m.MemID = a.MemID";

  if(mysqli_query($con, $sqlGetMembers)) {
    $allMembers = mysqli_query($con, $sqlGetMembers);
    //set $anyMember to false as no member satisfying criteria has been found yet 
    $anyMember = "F";

    foreach($allMembers as $mem) {
      //get MemID for this member
      $memID = $mem['MemID'];

      //sql to get all attendance listings for that member 
      $sqlAttendance = "SELECT r.RegID FROM attendance as a
            INNER JOIN registers as r ON a.RegID = r.RegID
            WHERE a.MemID = '$memID'";

    if(mysqli_query($con, $sqlAttendance)) {
      //getting result and number of attendances from that 
      $allAttendance = mysqli_query($con, $sqlAttendance);
      $noAttendance = mysqli_num_rows($allAttendance);

      //getting the place of the member so that only the possible attended registers are selected
      $place = $mem['CarNew'];

      //sql to get all registers
      $sqlNoReg = "SELECT RegID FROM registers
            WHERE CarNew = '$place'";

      if(mysqli_query($con, $sqlNoReg)) {
        //get result from query and count number of registers from that 
        $allReg = mysqli_query($con, $sqlNoReg);
        $noReg = mysqli_num_rows($allReg);

        //check if 0 to avoid /0 and then calculate average if not 
        if($noReg !== 0) {
          $avgAttendance = $noAttendance / $noReg;
        } else {
          $avgAttendance = 0;
        }

        //if the attendance over box was filled, check if their attendance is over the threshold and if so output them 
        if($attendanceOver !== "") {        
          if(($avgAttendance * 100) >= $attendanceOver) {
            echo '<b>' . $mem['FName'] . " " . $mem['LName'] . "</b> " . ($avgAttendance * 100) . "% <br>";
            $anyMember = "T";
          }
        }

        //if the attendance under box was filled, check if their attendance is under the threshold and if so output them 
        if($attendanceUnder !== "") {
          if(($avgAttendance * 100) <= $attendanceUnder) {
            echo '<b>' . $mem['FName'] . " " . $mem['LName'] . "</b> " . ($avgAttendance * 100) . "% <br>";
            $anyMember = "T";
          }
        }

      } else {
        echo "Error: " . $sqlNoReg . "<br>" . mysqli_error($con);
      }

    } else {
      echo "Error: " . $sqlAttendance . "<br>" . mysqli_error($con);
    }
  }

  //if no members were found aka $anyMember never got set to T then say no results were found 
  if($anyMember === "F") {
      echo "No results found";
    }

  } else {
    echo "Error " . $sqlGetMembers . "<br>" . mysqli_error($con);
  } 
}

} else {
  //if the xor check failed - aka none or multiple of the fields were filled 
    echo "Please fill in one of the fields";
  }
}




?>
</h4>
  </div>
  					
  				</div>
			</div>
		</div>
    <div id="endGradient"></div>
  </body>
</html>