
<?php

//database connect
include 'db.php';

// Just run via command line php -q sample_timesheet_fetch.php
date_default_timezone_set("Australia/Sydney");
$actual_db = array();
$total_count = 0;

$sql="SELECT * FROM rodeo_employee WHERE status='0' Limit 1";
$query=mysqli_query($connect,$sql);
$rows=mysqli_num_rows($query);
$Employee=mysqli_fetch_assoc($query);

if($rows){
$employeeid=$Employee['employee_id'];
$firstname=$Employee['firstname'];
$lastname=$Employee['lastname'];
if($Employee['mobile']!=0){
  $mobilno=$Employee['mobile'];
}
else{
  $mobilno=$Employee['phone'];
}
$email=$Employee['email'];

//Employee details
$employeedetails= array(
  'intCompanyId'=>3,
  'strFirstName'=>$firstname,
  'strLastName'=>$lastname,
  // 'intGender'=>0,
  // 'strStartDate'=>'2018-12-03T00:30:00+05:45',
  // 'strMobilePhone'=>$mobilno,
  // 'strEmail'=>$email,
  );

while(true){
  //24ce0207080849.as.deputy.com = Domain, supervise/employee -Request sqlite_fetch_column_types ,5f1dacc602c2aec7e5abc6feac134ae6- token
    $fetched = dp_api("24ce0207080849.as.deputy.com", "supervise/employee" , "5f1dacc602c2aec7e5abc6feac134ae6", $employeedetails);
    $last_count = count($fetched);
    $total_count += count($fetched);
    $actual_db = array_merge($actual_db , $fetched);
    if($last_count < 500)
        break;
}

print "<pre>";
        print_r($actual_db);
        print "</pre>";


if(isset($actual_db['Id'])){
  $deputyemployeeid=$actual_db['Id'];
$sql="UPDATE rodeo_employee SET deputy_employeeid='$deputyemployeeid' ,status=1 WHERE employee_id='$employeeid'";
mysqli_query($connect,$sql);
}
}
/**
 * Get Resource from an url
 *
 * @return string
 */
function dp_wget($url , $postvars = null , $curlOpts = array()){
        // Purpose : refresh session token every hour
        $piTrCurlHandle = curl_init();
        curl_setopt($piTrCurlHandle, CURLOPT_HTTPGET, 1);
        curl_setopt($piTrCurlHandle, CURLOPT_RESUME_FROM, 0);
        curl_setopt($piTrCurlHandle, CURLOPT_URL, $url);
        curl_setopt($piTrCurlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($piTrCurlHandle, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($piTrCurlHandle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($piTrCurlHandle, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($piTrCurlHandle, CURLOPT_TIMEOUT, 500); // 500 secs

        if($postvars){
            curl_setopt($piTrCurlHandle, CURLOPT_POST, 1);
            curl_setopt($piTrCurlHandle, CURLOPT_POSTFIELDS, $postvars);
            curl_setopt($piTrCurlHandle, CURLOPT_CUSTOMREQUEST, "POST");
        }

        if($curlOpts)
        foreach($curlOpts as $opt=>$value)
            curl_setopt($piTrCurlHandle, $opt, $value);


        $data = curl_exec($piTrCurlHandle);

        return $data;
}
/**
 * Call Deputy API
 */
function dp_api($endpoint  , $url , $token , $postvars = null){
  var_dump($postvars);
    $get = dp_wget("https://" . $endpoint . "/api/v1/" . $url
    , $postvars?json_encode($postvars):null
    , array(
        CURLOPT_HTTPHEADER => array(
              'Content-type: application/json'
            , 'Accept: application/json'
            , 'Authorization : OAuth ' . $token
            , 'dp-meta-option : none'
            )
        )
    );

    return json_decode($get , true);
}
