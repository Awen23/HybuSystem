<?php
//checks they're logged in 
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
<script>

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
                    <li class = "nav-text"><a href="Statistics.php">Statistics</a></li>
                    <li class = "nav-text"><a href="Registers.php" style="color:#E30713">Registers</a></li>
                    <li class = "nav-text"><a href="Volunteers.php">Volunteers</a></li>
                </ul>
            </div>
                

            </div>
        </nav>
        <div class ="container-fluid" id="backred">
            <div class ="container" id="innerwhite"> <br>
                <h3>Please Enter a Location:</h3>

                <form method="post" action="newRegister.php" class="form-inline">
                    <div class="form-group">
                        <label class="control-label">Please enter location of register:</label>
                        <select id="selectPlace" name="place" class="form-control" required>
                            <option style="display:none;" required></option>
                            <option value="C" required>Cardiff</option>
                            <option value="N" required>Newport</option>
                        </select>

                        <!-- Validation:
                        Must be Cardiff or Newport
                        Required field -->

                    </div>

                    <div class="form-group">
                        <input type="submit" id="submit" name="submit" class="btn btn-default">
                    </div>
                    <br><br>
                </form>

<?php

    //connecting to the database
    require 'dbdetails.php';
    $con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
    
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $day = date("l");
    //getting the day of the week

    //setting the place depending on the day of the week, as the meetings are always on the same day of the week, then going to the next page 
    switch($day){ 
        case "Tuesday":
        $place = "N";
        goOn($place, $con);
        break;

        case "Thursday":
        $place = "C";
        goOn($place, $con);
        break;
    }

//if it's neither of them days, won't be redirected until submit button is pressed 
if(isset($_POST['submit'])) {
    $place = $_POST['place'];
    goOn($place, $con);
}

//called upon either an automatic place being selected or one being selected manually
function goOn($place, $con) {
    //puts it in the session for later use 
    $_SESSION["Place"] = $place;

    //getting the current date
    $date = date("Y-m-d");

    //creating the register 
    $sqlCreateReg = "INSERT INTO registers(RegDate, CarNew) VALUES ('$date', '$place')";

    if(mysqli_query($con, $sqlCreateReg)) {
        //getting the RegID then putting it in the session and redirecting to register input page
        $currentRegID = mysqli_insert_id($con);
        $_SESSION["RegID"] = $currentRegID;
        echo '<script>window.location.href = "RegistersInput.php";</script>';
    } else {
        echo "Error: " . $sqlCreateReg . "<br>" . mysqli_error($con);
    }
}

?>
            </div>
        </div>
        <div id="endGradient"></div>
  </body>
</html>