<?php
//checks user is logged in before continuing 
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
            <div class="container-fluid">
                <!-- No navigation bar to make it suitable for being left out as a register -->
                <div style = "padding:10px 40px 5px 10px;"><img src = "http://hybu.co.uk/newsite/wp-content/uploads/2015/11/hybu_logo.png" alt = "Hybu Logo" height = 60>
                <text class="pull-right" style=" font-size: 40px;">Register</text> </div>
                <br>
            </div>

        <div class ="container-fluid" id="backred">
            <div class ="container" id="innerwhite"> <br>
                <form method="post" action="RegistersInput.php" class="form-inline">
                    <label>First Name:</label>
                    <input type="text" class="form-control" name="fname"></input>
                    <label>Last Name:</label>
                    <input type="text" class="form-control" name="lname"></input>
                    <input type="submit" name="submit" value="Add" class="btn btn-default"></input>
                    <button type="button" onClick="deleteConfirm()" class="btn btn-default">Remove</button>
                    <input type="submit" style="display:none;" id="delete" name="Delete">
                </form>
                    <br>
<script>

    //to confirm they want to delete a member from the page
    function deleteConfirm() {
        if(confirm("Are you sure you want to delete this person from the register? Their name will be displayed under deleted at the bottom of the page")) {
            document.getElementById("delete").click();
        }
    }

</script>

<?php
    //connecting to the database
require 'dbdetails.php';
$con = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
    
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    //getting the values from the session 
    $regID = $_SESSION["RegID"];

    //when a new member is added
    if(isset($_POST['submit'])){
        //get the first and last name from the form 
        $FName = $_POST['fname'];
        $LName = $_POST['lname'];

        //get the MemID that corresponds to this 
        $sqlMemID = "SELECT MemID FROM members WHERE FName = '$FName' AND LName = '$LName'";
        $MemID = mysqli_query($con, $sqlMemID);

        switch(mysqli_num_rows($MemID)) {
            //if no member, output error 
            case 0:

            echo "<b>Error: Member doesn't exist<br></b>";
            break;

            //if one error, fetch the row
            case 1:
            $memToInsert = mysqli_fetch_row($MemID);

            //sql to check if they've already been added to the register 
            $sqlCheck = "SELECT MemID FROM attendance WHERE RegID = '$regID' AND MemID = '$memToInsert[0]'";

                if(mysqli_query($con, $sqlCheck)) {
                    $check = mysqli_query($con, $sqlCheck);

                    //if 0 rows in the check (aka they haven't been added), continue with inserting them 
                    if(mysqli_num_rows($check) === 0) {

                        $sqlInsert = "INSERT INTO attendance(RegID, MemID) VALUES ('$regID', $memToInsert[0])";

                        if(mysqli_query($con, $sqlInsert)) {
                            //Welcome the user to show they've been added to the list, will also appear in the automatically generated list 
                            echo "<h2> Welcome " . $FName . " " . $LName . "</h2>";
                        } else {
                            echo "Error: " . $sqlInsert . "<br>" . mysqli_error($con);
                        }
                    } else {
                        //if more than 0 rows in the check, aka they've already been added 
                        echo "<b>Error: Member already added </b><br>";
                    }
            } else {
                echo "Error: " . $sqlCheck . "<br>" . mysqli_error($con);
            }

            break; 

            //if not 0 or 1 results for their first and last name 
            default:
            echo "Error: Multiple members with same name, please make all first & last names unique for each group";
        }

}

//upon pressing delete for a member and confirming
if(isset($_POST['Delete'])) {
    $FName = $_POST['fname'];
    $LName = $_POST['lname'];

    //sql to get their MemID
    $sqlMemID = "SELECT MemID FROM members WHERE FName = '$FName' AND LName = '$LName'";
    $MemID = mysqli_query($con, $sqlMemID);

    switch(mysqli_num_rows($MemID)) {
            //if no member, output error 
            case 0:
            echo "<b>Error: Member doesn't exist<br></b>";
            break;

            //if one member, get their MemID and check they're on the register 
            case 1:
            $memToInsert = mysqli_fetch_row($MemID);

            $sqlCheck = "SELECT MemID FROM attendance WHERE RegID = '$regID' AND MemID = '$memToInsert[0]'";

                if(mysqli_query($con, $sqlCheck)) {
                    $check = mysqli_query($con, $sqlCheck);

                    //if they're on the register aka one listing for them 
                    if(mysqli_num_rows($check) === 1) {

                        //sql to delete them from the register 
                        $sqlDelete = "DELETE FROM attendance WHERE RegID = '$regID' AND MemID = '$memToInsert[0]'";

                        if(mysqli_query($con, $sqlDelete)) {
                            //says they're deleted 
                            echo "<h2> Member " . $FName . " " . $LName . " Deleted</h2>";
                        } else {
                            echo "Error: " . $sqlDelete . "<br>" . mysqli_error($con);
                        }

                        //puts them in the session variable to output everyone who's been deleted, if already set add it on but if it isn't already set initiate it 
                        if(isset($_SESSION['Deleted'])) {
                            $_SESSION['Deleted'] .= $FName . " " . $LName . "<br>";
                        } else {
                            $_SESSION['Deleted'] = $FName . " " . $LName . "<br>";
                        }

                    } else {
                        //if no results from the check query 
                        echo "<b>Error: Member not added </b><br>";
                    }
            } else {
                echo "Error: " . $sqlCheck . "<br>" . mysqli_error($con);
            }

            break; 

            //if multiple members in the MemID query 
            default:
            echo "Error: Multiple members with same name, please make all first & last names unique for each group";
        }

}

?>

<text> Full Register: </text> <br>

<?php

//sql to get all members on this register
$sqlList = "SELECT members.FName, members.LName, attendance.RegID 
    FROM members 
    INNER JOIN attendance ON members.MemID = attendance.MemID 
    WHERE attendance.RegID = '$regID'";

    $List = mysqli_query($con, $sqlList);

    //looping through list 
    while($row = mysqli_fetch_assoc($List)) {
        echo $row["FName"]. " " . $row["LName"]. "<br>";
    }

//shows all deleted members when at least one has been deleted 
if(isset($_SESSION['Deleted'])) {
    echo "<h3>Deleted members:</h3>" . $_SESSION['Deleted'];
}

?>
                <br><br>
                <script>
                    //takes them onto the next page 
                function descRedirect() {
                    window.location.href = "registerConfirm.php";
                }
                </script>
                    <button onClick="descRedirect()" class="btn btn-default">Submit Register</button>
                <br><br>

                </form>
            </div>
        </div>
        <div id="endGradient"></div>
  </body>
</html>