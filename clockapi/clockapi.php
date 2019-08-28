<?php

// include ERPLY API class
include ("EAPI.class.php");

//configuration settings
include ("erply.php");

set_time_limit(0);
ini_set('memory_limit', '512M');

//Request clock ins from erply
$result1=$api->sendRequest("getClockIns");

$output=json_decode($result1,true);

$records=$output['records'];

foreach($records as $output){

  print "<pre>";
  print_r($output);
  print "</pre>";

$employeeid=$output['employeeID'];
$timeclockid=$output['timeclockRecordID'];
$inunixtime=$output['InUnixTime'];
$outunixtime=$output['OutUnixTime'];


$result2=$api->sendRequest("getEmployees",array('id'=>$employeeid));

$output2=json_decode($result2,true);


$employeedetails=$output2['records'][0];

print "<pre>";
print_r($employeedetails);
print "</pre>";

$firstname=$employeedetails['firstName'];
$lastname=$employeedetails['lastName'];
$phone=$employeedetails['phone'];
$mobile=$employeedetails['mobile'];
$email=$employeedetails['email'];
$username=$employeedetails['username'];


$sql="SELECT * FROM rodeo_timesheet WHERE timeclockrecord_id='$timeclockid'";
$check=mysqli_query($connect,$sql);
$checkrows=mysqli_num_rows($check);

if($checkrows){
$sql="UPDATE rodeo_timesheet SET in_unixtime='$inunixtime', out_unixtime='$outunixtime' where erplyemployee_id='$employeeid'";
$query=mysqli_query($connect,$sql);
}

else{
echo $sql="INSERT INTO rodeo_timesheet (timeclockrecord_id,erplyemployee_id, in_unixtime,out_unixtime) VALUES ('$timeclockid','$employeeid','$inunixtime','$outunixtime')";
mysqli_query($connect,$sql);
echo '<br>'.'Records inserted successfully'.'<br>';
}

$no=checkemployexist($employeeid);
if($no){
  // $sql="UPDATE rodeo_employess SET firstname='$firstname', lastname='$lastname', phone='$phone', mobile='$mobile', email='$email', username='$username', status='2' where erplyemployee_id='$employeeid'";
  // $query=mysqli_query($connect,$sql);
}

else{
echo $sql="INSERT INTO rodeo_employee (employee_id, firstname, lastName,phone,mobile,email,username) VALUES ('$employeeid','$firstname','$lastname','$phone','$mobile','$email','$username')";
mysqli_query($connect,$sql);
}


}


function checkemployexist($id){
  global $connect;
  $sql1="SELECT * FROM rodeo_employee WHERE employee_id='$id'";
  $check1=mysqli_query($connect,$sql1);
  $employeerows=mysqli_num_rows($check1);

  return $employeerows;
}


?>
