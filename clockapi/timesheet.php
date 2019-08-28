
<?php


//database connect
include 'db.php';

// Just run via command line php -q sample_timesheet_fetch.php
date_default_timezone_set("Australia/Sydney");
$actual_db = array();
$total_count = 0;

$sql="SELECT e.deputy_employeeid, t.erplyemployee_id,t.in_unixtime, t.out_unixtime FROM rodeo_employee e Inner join rodeo_timesheet t ON e.employee_id=t.erplyemployee_id  WHERE e.status='1' AND t.status='0'";
$query=mysqli_query($connect,$sql);
$Employee=mysqli_fetch_assoc($query);
$rows=mysqli_num_rows($query);

if($rows){
$erplyempid=$Employee['erplyemployee_id'];
$employeeid=$Employee['deputy_employeeid'];
$starttime=$Employee['in_unixtime'];
$endtime=$Employee['out_unixtime'];

$data= array(
  'Employee'=>$employeeid,
  'StartTime'=>$starttime,
  'EndTime'=>$endtime,
  'Mealbreak'=>'2018-12-03T00:30:00+05:45',
  'TotalTime'=>8,
  'TotalTimeInv'=>8.5,
  'Disputed'=>0,
  'TimeApproved'=>1,
  'ValidationFlag'=>0,
  'Invoiced'=>0,
  'PayRuleApproved'=>1,
  'PayStaged'=>0,
  'PaycycleId'=>5337,
  );


while(true){
    $fetched = dp_api("24ce0207080849.as.deputy.com", "resource/Timesheet" , "5f1dacc602c2aec7e5abc6feac134ae6", $data);
    $last_count = count($fetched);
    $total_count += count($fetched);
    $actual_db = array_merge($actual_db , $fetched);
    if($last_count < 500)
        break;
}
// timesheets here
print "<pre>";
        print_r($actual_db);
        print "</pre>";

if(isset($actual_db['Id'])){
$timesheetid=$actual_db['Id'];
$sql="UPDATE rodeo_timesheet SET timesheet_id='$timesheetid', status=1 Where erplyemployee_id='$erplyempid'";
mysqli_query($connect,$sql);
}
}

else{
echo "no any timesheets";
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
