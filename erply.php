<?php

global $connect;
//database connect
$connect=mysqli_connect("localhost","root","","testing");

// Check connection with database
if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
session_start();

//Initialize class
global $api;
$api= new EAPI();


$api->clientCode=457184;
$api->username='support@retailcare.com.au';
$api->password='Welcome123$$';
$api->url="https://".$api->clientCode.".erply.com/api/";

 ?>
