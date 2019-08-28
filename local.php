<?php


global $connect;
global $token;
global $Url;

$connect=mysqli_connect("localhost","root","","testing");

//Authentication rest API magento2.Please change url accordingly your url

$Url='http://127.0.0.1/magento/index.php/rest/default/V1';
$adminUrl='http://127.0.0.1/magento/index.php/rest/default/V1/integration/admin/token';


$ch = curl_init();
$data = array("username" => "aryan", "password" => "probook450");
$data_string = json_encode($data);
$ch = curl_init($adminUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
);
$token = curl_exec($ch);
$token=  json_decode($token);
