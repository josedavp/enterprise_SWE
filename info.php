<?php
$userid=$_POST['userid']; //change from _GET to _POST its safer
$email=$_POST['email'];
$firstName=$_POST['firstName'];
echo '<h4> Results from previous page: </h4>';
echo '<p> user id: '.$userid.'</p'; //prof prefers this was over line 7
echo "<p> email: $email </p>";
echo "<p> First Name: $firstName </p>";

?>