<?php

//to get the dates from the Events page 
session_start();
$_SESSION['dates'] = $_POST['dates'];
//redirects to newEvent page
echo '<script>window.location.href="newEvent.php";</script>';

?>