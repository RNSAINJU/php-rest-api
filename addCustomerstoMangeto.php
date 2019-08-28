<?php

include 'local.php';

    $requestUrl=$Url.'/customers';

    $sql = "SELECT * from customers WHERE status ='0'";
    $customer = mysqli_query($connect, $sql);


foreach($customer as $customers){
  $id=$customers['erply_id'];
    $email=$customers['email'];
    $firstname=$customers['firstname'];
    // $middlename=$customers['middlename'];
    $lastname=$customers['lastname'];
    $gender=$customers['gender'];
    echo $gender;
    if($gender=='male'){
      $gen=1;
    }
    else if($gender=='female'){
      $gen=2;
    }
    else{
      $gen=0;
    }
    $dateofbirth=$customers['birthday'];
    $mobile=$customers['mobile'];
    $address=$customers['address'];
    $address2=$customers['address2'];
    $city=$customers['city'];
    $postal_code=$customers['postal_code'];
    $country_id=$customers['country_id'];
    $company=$customers['companyname'];



    $customerData = [
        'firstname'=>$firstname,
        "lastname"=>$lastname,
          "email"=>$email,
          "dob"=>$dateofbirth,
          "gender"=>$gen
    ];

    // "addresses"=>[
    // [
    //   "firstname"=>$firstname,
    //   "lastname"=>$lastname,
    //   "countryId"=>$country_id,
    //   "street"=>["$address","$address2"],
    //   "company"=>$company,
    //   "telephone"=>$mobile,
    //   "postcode"=>$postal_code,
    //   "city"=>$city,
    //   "defaultBilling"=>true,
    // ],
    // [
    //   "firstname"=>$firstname,
    //   "lastname"=>$lastname,
    //   "countryId"=>$country_id,
    //   "street"=>["$address","$address2"],
    //   "company"=>$company,
    //   "telephone"=>$mobile,
    //   "postcode"=>$postal_code,
    //   "city"=>$city,
    //   "defaultShipping"=>true,
    // ]
    // ]

    $customerData = json_encode(array('customer' => $customerData));


    $setHaders = array('Content-Type:application/json', 'Authorization:Bearer ' . $token);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $requestUrl);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $customerData);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $setHaders);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if (curl_exec($ch) === false) {
        echo "Curl error: " . curl_error($ch) . "\n";
    } else {
        $response = curl_exec($ch) ?: "";
    }

    curl_close($ch);

    $output = json_decode($response);
    print "<pre>";
    print_r($output);
    print "</pre>";

    // $magento_id = $output->id;
    // $magento_type=$output->type_id;
    // $sql = "UPDATE customers SET magento_id='$magento_id'WHERE erply_id='$id'";
    // $query = mysqli_query($connect, $sql);
    $sql = "UPDATE customers SET status='1' WHERE erply_id='$id'";
    $query = mysqli_query($connect, $sql);
}


    ?>
