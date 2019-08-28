<?php

///V1/attributeMetadata/customer

include ("EAPI.class.php");
include ("erply.php");

global $api;
global $connect;



//Adds magento customers to erply
function addCustomerstoErply(){
global $api;
global $connect;

$sql = "SELECT * from customers WHERE status ='0'";
$customer = mysqli_query($connect, $sql);


foreach($customer as $customers){
$id=$customers['magento_id'];
$firstname=$customers['firstname'];
// $middlename=$customers['middlename'];
$lastname=$customers['lastname'];
$fullname=$customers['lastname'];
$companyname=$customers['companyname'];
$email=$customers['email'];
$fax=$customers['fax'];
$gender=$customers['gender'];
$dateofbirth=$customers['birthday'];
$mobile=$customers['mobile'];
$address=$customers['address'];
$address2=$customers['address2'];
$city=$customers['city'];
$postal_code=$customers['postal_code'];
$country_id=$customers['country_id'];
$company=$customers['companyname'];


$inputParameters = array(

  "companyName"=>$companyname,
  "firstName"=>$firstname,
  "lastName"=>$lastname,
  "fullName"=>$fullname,
  "gender"=>$gender,
  // "groupID"=>"",
  // "code"=>"",
  // "vatNumber"=>"",
  "email"=>$email,
  // "phone"=>"",
  "mobile "=>$mobile,
  "fax"=>$fax,
  // "imageContent"=>"",
  "birthday"=>$dateofbirth,
  "countryID"=>$country_id,
);

$result = $api->sendRequest("saveCustomer", $inputParameters);

$output = json_decode($result, true);

$erply_id=$output['records'][0]['customerID'];
$sql="UPDATE customers SET erply_id='$erply_id' WHERE magento_id='$id'";
mysqli_query($connect,$sql);
print "<pre>";
print_r($output);
print "</pre>";
}
address();

}


//updates customer address in erply
function address(){
global $api;
global $connect;

$sql = "SELECT * from customers WHERE status ='0'";
$customer = mysqli_query($connect, $sql);


foreach($customer as $customers){
$erply_id=$customers['erply_id'];
$address=$customers['address'];
$address2=$customers['address2'];
$city=$customers['city'];
$postal_code=$customers['postal_code'];
$country_id=$customers['country_id'];
$state=$customers['state'];


$inputParameters = array(
  // "addressID"=>
  "ownerID"=>$erply_id,
  "typeID"=>1,
  "street"=>$address,
  "address2"=>$address2,
  "city"=>$city,
  "postalCode"=>$postal_code,
  "state"=>$state,
  // "country"=>$country_id,
);

$result = $api->sendRequest("saveAddress", $inputParameters);

$output = json_decode($result, true);

print "<pre>";
print_r($output);
print "</pre>";

$sql="UPDATE customers SET status='1' WHERE erply_id='$erply_id'";
mysqli_query($connect,$sql);
}
}
addCustomerstoErply();

?>
