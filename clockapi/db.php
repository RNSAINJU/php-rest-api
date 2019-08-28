<?php
global $connect;
//database connect
$connect=mysqli_connect("localhost","root","","testing");

// Check connection with database
if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>
