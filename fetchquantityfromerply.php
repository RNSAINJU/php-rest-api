<?php


include ("EAPI.class.php");
include ("erply.php");

global $api;

    $result=$api->sendRequest("getProductStock");

    $outputs=json_decode($result,true);

        print "<pre>";
        print_r($outputs);
        print "</pre>";

    $products=$outputs['records'];

    //Update stocks of existing products
    foreach ($products as $product) {
        $productID = $product['productID'];
        $amountinstock = (int)$product['amountInStock'];

        $sql="UPDATE products SET quantity='$amountinstock' WHERE product_id='$productID'";
        mysqli_query($connect,$sql);
    }
