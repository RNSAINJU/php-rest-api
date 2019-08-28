<?php
// include ERPLY API class
include ("EAPI.class.php");

//configuration settings
include ("erply.php");

//fetch all customers from erply
function syncCustomer(){
    global $api;
    global $connect;
    $result=$api->sendRequest("getCustomers");

    $outputs = json_decode($result, true);

   print "<pre>";
   print_r($outputs);
   print "</pre>";
    echo '<h2>Customers Log</h2>';
    $records=$outputs['records'];
    foreach ($records as $record){
        $id=$record['id'];
        $fullName=$record['fullName'];
        $companyName=$record['companyName'];
        $firstname=$record['firstName'];
        $lastname=$record['lastName'];
        $mobile=$record['mobile'];
        $email=$record['email'];
        $fax=$record['fax'];
        $birthday=$record['birthday'];
        $address=$record['address'];
        $street=$record['street'];
        $address2=$record['address2'];
        $city=$record['city'];
        $postalcode=$record['postalCode'];
        $countryid=$record['countryID'];
        $country=$record['country'];
        $state=$record['state'];
        $gender=$record['gender'];

        $sql = "SELECT * FROM customers WHERE erply_id='$id'";
        $check = mysqli_query($connect, $sql);
        $checkrows = mysqli_num_rows($check);

        //checks if customer exists or not else adds customers into database
        if($checkrows>0){
            echo "<br>Customer no: $id=>Data exists<br>";
        }
        else {
            $sql = "INSERT INTO customers (erply_id,magento_id,fullname,companyname,firstname,lastname,mobile,email,fax,birthday,address,street,address2,city,postal_code,country_id,country,state,gender) VALUES ('$id','','$fullName','$companyName','$firstname','$lastname','$mobile','$email','$fax','$birthday','$address','$street','$address2','$city','$postalcode','$country_id','$country','$state','$gender')";
            mysqli_query($connect, $sql);
            echo "<br>Customer no: $id=>Data inserted successfully<br>";
        }
    }
}
 syncCustomer();
?>
