<?php
//putting the selected EventID into the session 
session_start();
$_SESSION['EventID'] = $_POST['EventID'];
if($_POST['EventID'] === "") {
	//alerts the user and goes back to editEvent page if no event was selected 
	echo '<script>alert("Please select an event first!");
			window.location.href="editEvent.php"</script>';
}
//takes user to page to edit attending (if not already redirected back to editEvent page)
echo '<script>window.location.href="editAttending.php";</script>';

?>