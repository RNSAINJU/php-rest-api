<?php

//fetchs orders from magento to database

include ("local.php");
  //request url for order page in magento
  $requestUrl=$Url.'/orders?searchCriteria';

    $ch = curl_init();
    $ch = curl_init($requestUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result1 = curl_exec($ch);
    $result = json_decode($result1, true);
print "<pre>";
print_r($result);
print "</pre>";


    $orders=$result['total_count'];
    echo '<h2>Order Log</h2>';
    for($i=0;$i<$orders;$i++) {

        $order_id = $result['items'][$i]['items'][0]['order_id'];
        $customer_firstname=$result["items"][$i]['billing_address']['firstname'];
        $customer_lastname=$result["items"][$i]['billing_address']['lastname'];
        $customer_name=$customer_firstname.''.$customer_lastname;
        $product_id=$result['items'][$i]['items'][0]['product_id'];
        $total=$result['items'][$i]['total_due'];
        $quantity=$result['items'][$i]['items'][0]['qty_invoiced'];
        $status=$result['items'][$i]['status'];
        $sql="SELECT * from orders where order_id='$order_id'";
        $check = mysqli_query($connect, $sql);
        $checkrows = mysqli_num_rows($check);

        //checks if orders exiss else adds order into database
        if ($checkrows > 0) {
            echo "<br>Order no: $order_id=>Data exists<br>";
        } else {
            $sql = "INSERT INTO orders(order_id,date,customer_name,email, location,total,quantity,status) VALUES ('$order_id','$customer_name','$product_id','$total','$quantity','$status')";
            mysqli_query($connect, $sql);
            echo "<br>Order no: $order_id=>Data inserted successfully<br>";
        }
    }


?>
