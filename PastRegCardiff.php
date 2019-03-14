<?php
//checks user is logged in 
session_start();
if(isset($_SESSION['Type'])) {
    if($_SESSION['Type'] === "V") {
        echo '<script>window.location.href="volunteersWelcome.php"</script>';
    } 
} else {
    echo '<script>window.location.href="loginScreen.php"</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  	<title>Register</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="http://localhost/hybu/hybustyle.css" rel="stylesheet" type="text/css">
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
					<li class = "nav-text"><a href="Statistics.php">Statistics</a></li>
					<li class = "nav-text"><a href="Registers.php" style="color:#E30713">Registers</a></li>
					<li class = "nav-text"><a href="Volunteers.php">Volunteers</a></li>
				</ul>
			</div>
				

			</div>
		</nav>
		<div class ="container-fluid" id="backred">
			<div class ="container" id="innerwhite"> <br>
				<a href="Registers.php">Back</a>
				<br><br>
				
				<div class="btn-group btn-group-justified">
    				<a href="PastRegCardiff.php" class="btn btn-primary mini-nav" style="background-color: #E30713;">Cardiff</a>
    				<a href="PastRegNewport.php" class="btn btn-primary mini-nav" style="background-color:lightgrey; color:black">Newport</a>
  				</div> <br><br>
  				<table class = "table-striped" width=100%>

 <?php

 	//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
    
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    //sql to get all registers for Cardiff 
    $sqlRegList = "SELECT * FROM registers
                    WHERE CarNew = 'C'
                    ORDER BY RegDate DESC;";

    if(mysqli_query($con, $sqlRegList)) {

        //result from query 
    	$RegList = mysqli_query($con, $sqlRegList);

        //going through each register 
    	foreach ($RegList as $register) {
    		$RegID = $register['RegID'];
    		
            //outputting the date in the table along with a delete button referencing the confirmDelete JavaScript function 
    			echo '<tr> <td style="padding-left:15px;"> <text style = "font-size:20px;">' . $register['RegDate'] . '</text>  </td><td> <button style="float:right" class="btn btn-default" onClick = "confirmDelete(' . $RegID . ')"> Delete </button> </td>
    			  </tr>';

                //sql to get all people that attended that register
    			$sqlList = "SELECT members.FName, members.LName
    						FROM members 
    						INNER JOIN attendance ON members.MemID = attendance.MemID 
    						WHERE attendance.RegID = '$RegID'";

    			$List = mysqli_query($con, $sqlList);

                //starting a new row and column 
    			echo "<tr> <td>";

                //outputting each name 
    			while($row = mysqli_fetch_assoc($List)) {
        			echo $row["FName"]. " " . $row["LName"]. "<br>";
    			}

                //ending column 
    			echo "</td>";

                //outputting description in next column then ending row
    			echo "<td>" . $register['Description'] . "</td> </tr>";
    			
    	}

    } else {
    	echo "Error: " . $sqlRegList . "<br>" . mysqli_error($con);
    }


//if a deletion is confirmed 
if(isset($_POST['delete'])) {

    //getting the RegID from the invisible textbox  
    $regID = $_POST['regID'];

    //sql to delete all attendance for the register and the register 
    $sqlDeleteAttendance = "DELETE FROM attendance WHERE RegID = '$regID'";
    $sqlDeleteReg = "DELETE FROM registers WHERE RegID = '$regID'";

    if(mysqli_query($con, $sqlDeleteAttendance) AND mysqli_query($con, $sqlDeleteReg)) {
        //alerting the user it's been successful 
        echo '<script>alert("Register Deleted!")
                    window.location.href="PastRegCardiff.php"</script>';
    } else {
        echo "Error: " . $sqlDeleteAttendance . "<br>" . $sqlDeleteReg . "<br>" . mysqli_error($con);
    }
}
 ?>

 <form action="PastRegCardiff.php" method="post">
    <input type="text" id="invisText" name="regID" style="display:none">
    <input type="submit" id="invisButton" name="delete" style="display:none">
 </form>

 <script>

    //if they click on a delete button 
    function confirmDelete(RegID) {
        //if they confirm, put RegID in an invisible textbox then click an invisible submit button, if they don't then redirect to same page 
        if(confirm("Are you sure you want to delete this register?")) {
            document.getElementById("invisText").value = RegID;
            document.getElementById("invisButton").click();
        } else {
            window.location.href = "PastRegCardiff.php";
        }
    }
 </script>
				</table>
			</div>
		</div>
        <div id="endGradient"></div>
  </body>
</html>