<?php
//checks user is logged in as a leader 
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
    				<a href="PastRegCardiff.php" class="btn btn-primary mini-nav" style="background-color:lightgrey; color:black;">Cardiff</a>
    				<a href="PastRegNewport.php" class="btn btn-primary mini-nav" style="background-color: #E30713;">Newport</a>
  				</div> <br><br>			

  				<table class = "table-striped" width=100%>

<?php

//connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
    
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    //gets list of all Newport registers
    $sqlRegList = "SELECT * FROM registers
                    WHERE CarNew = 'N'
                    ORDER BY RegDate DESC;";

    if(mysqli_query($con, $sqlRegList)) {
        //gets result of all registers
        $RegList = mysqli_query($con, $sqlRegList);

        //going through each register
        foreach ($RegList as $register) {
            //getting the RegID
            $RegID = $register['RegID'];
            
                //outputting the date and a delete button in a row 
                echo '<tr> <td style="padding-left:15px;"> <text style = "font-size:20px;">' . $register['RegDate'] . '</text> </td><td> <button style="float:right" class="btn btn-default" onClick = "confirmDelete(' . $RegID . ')"> Delete </button> </td>
                  </tr>';

                //list of everyone who attended current register being gone through 
                $sqlList = "SELECT members.FName, members.LName
                            FROM members 
                            INNER JOIN attendance ON members.MemID = attendance.MemID 
                            WHERE attendance.RegID = '$RegID'";

                $List = mysqli_query($con, $sqlList);

                //starting new row and column, then going through list and outputting all first and last names and closing the column 
                echo "<tr> <td>";
                while($row = mysqli_fetch_assoc($List)) {
                    echo $row["FName"]. " " . $row["LName"]. "<br>";
                }

                echo "</td>";

                //putting the register description in another column, then closing the row 
                echo "<td>" . $register['Description'] . "</td> </tr>";
                
        }

    } else {
        echo "Error: " . $sqlRegList . "<br>" . mysqli_error($con);
    }


//upon confirming deletion of a register 
if(isset($_POST['delete'])) {
    $regID = $_POST['regID'];

    //sql to delete all attendance associated with the register and the register itself 
    $sqlDeleteAttendance = "DELETE FROM attendance WHERE RegID = '$regID'";
    $sqlDeleteReg = "DELETE FROM registers WHERE RegID = '$regID'";

    if(mysqli_query($con, $sqlDeleteAttendance) AND mysqli_query($con, $sqlDeleteReg)) {
        //alerting user the register has been deleted
        echo '<script>alert("Register Deleted!")
                    window.location.href="PastRegNewport.php"</script>';
    } else {
        echo "Error: " . $sqlDeleteAttendance . "<br>" . $sqlDeleteReg . "<br>" . mysqli_error($con);
    }
}
 ?>

 <form action="PastRegNewport.php" method="post">
    <input type="text" id="invisText" name="regID" style="display:none">
    <input type="submit" id="invisButton" name="delete" style="display:none">
 </form>

 <script>

    //called upon clicking delete on any register 
    function confirmDelete(RegID) {
        //if user confirms they want to delete it, send request to PHP, if not then redirect them to same page
        if(confirm("Are you sure you want to delete this register?")) {
            document.getElementById("invisText").value = RegID;
            document.getElementById("invisButton").click();
        } else {
            window.location.href = "PastRegNewport.php";
        }
    }
 </script>

</table>

			</div>
		</div>
        <div id="endGradient"></div>
  </body>
</html>