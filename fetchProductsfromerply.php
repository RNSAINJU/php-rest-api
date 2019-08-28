<?php

include ("EAPI.class.php");
include ("erply.php");

global $api;

set_time_limit(0);
ini_set('memory_limit', '512M');

//sending request
$results = $api->sendRequest("getProducts");

//Default output format is JSON, so we'll decode it inti PHP array

$output = json_decode($results, true);
$totalproducts = $output['status']['recordsTotal'];

//pages to iterrate throgh
$count = ($totalproducts/100)+1;

$count=(int)$count;
echo '<br>'.'Total page:'.$count.'</br>';

for ($j = 1; $j <= $count; $j++) {
    echo '<br>'.'Page No:'.$j.'</br>';
$result = $api->sendRequest("getProducts", array("type"=>'PRODUCT,MATRIX',"pageNo" => $j, "recordsOnPage" => '100','changedSince'=>'0'));

//Default output format is JSON, so we'll decode it inti PHP array
global $output;
$output = json_decode($result, true);

print "<pre>";
        print_r($output);
        print "</pre>";

$alloutput = $output['records'];
echo '<h2>Products Log</h2>';
foreach ($alloutput as $outputs) {
    $productid = $outputs['productID'];
    $name = $outputs['name'];
    $code = $outputs['code'];
    $code2=$outputs['code2'];
    $price = $outputs['price'];
    $active = $outputs['active'];
    $wholesale_price=$outputs['cost'];
    $price_withvat=$outputs['priceWithVat'];
    $description = $outputs['description'];
    $longdescription=$outputs['longdesc'];
    $brand_id = $outputs['brandID'];
    $group_id = $outputs['groupID'];
    $category_id = $outputs['categoryID'];
    $type=$outputs['type'];
    $added=$outputs['added'];
    $modified=$outputs['lastModified'];
    $manufacturer=$outputs['manufacturerName'];

    //checks for images,if found downloads and adds url to database
    if(isset($outputs['images'])){
        $thumburl=$outputs['images'][0]['thumbURL'];
        $smallurl=$outputs['images'][0]['smallURL'];
        $largeurl=$outputs['images'][0]['largeURL'];
        $my_save_dir='image/';

        $complete_save_loc=$my_save_dir.$name.'thumb.jpg';

        file_put_contents($complete_save_loc,file_get_contents($thumburl));

        $complete_save_loc=$my_save_dir.$name.'small.jpg';

        file_put_contents($complete_save_loc,file_get_contents($smallurl));

        $complete_save_loc=$my_save_dir.$name.'large.jpg';

        file_put_contents($complete_save_loc,file_get_contents($largeurl));

    }else {
      $largeurl="";
    }

    //Checks for variationdescription which contains colors and sizes attribute of Products
    //If found  inserts colors and sizes to database
    if(isset($outputs['variationDescription'])) {
        $colorid = $outputs['variationDescription'][0]['variationID'];
        $colorname = $outputs['variationDescription'][0]['value'];
        $size = $outputs['variationDescription'][1]['value'];
        $sizeid = $outputs['variationDescription'][1]['variationID'];
    }

    else{
        $colorid=$colorname=$size=$sizeid="";
    }

    if(isset($outputs['parentProductID'])){
        $parentproductid=$outputs['parentProductID'];
    }
    else{
      $parentproductid= "";
    }

    $sql = "SELECT * FROM products WHERE product_id='$productid'";
    $check = mysqli_query($connect, $sql);
    $checkrows = mysqli_num_rows($check);

    $sql = "SELECT * FROM colors WHERE variation_id='$colorid'";
    $check = mysqli_query($connect, $sql);
    $checkrows1 = mysqli_num_rows($check);

    $sql = "SELECT * FROM size WHERE id='$sizeid'";
    $check = mysqli_query($connect, $sql);
    $checkrows2 = mysqli_num_rows($check);

    //Checks for existing color in database, else adds colors into database
    if ($checkrows1 > 0) {
    echo "<br>Product no: $productid=>Color exists<br>";
    } else {
        $sql = "INSERT INTO colors(variation_id,name) VALUES ('$colorid','$colorname')";
        mysqli_query($connect, $sql);
        echo "<br>Product no: $productid=>Color inserted successfully<br>";
    }

      //Checks for existing size in database else adds sizes into database
    if($checkrows2 >0){
        echo "<br>Product no: $productid=>Size exists<br>";
    }
    else{
        $sql = "INSERT INTO size(id,size) VALUES ('$sizeid','$size')";
        mysqli_query($connect, $sql);
        echo "<br>Product no: $productid=>Size inserted successfully<br>";
    }

    //Checks for existing product in database else adds products with all data to database
    if ($checkrows > 0) {
            echo "<br>Product no: $productid=>Data exists<br>";
    } else {
            $sql1 = "INSERT INTO products (product_id, code,name, image,price,cost,pricewithvat,manufacturer,active,color_id,size_id,brand_id,group_id,category_id,quantity,description,longdescription,type,parent_productid) VALUES ('$productid','$code','$name','$largeurl','$price','$wholesale_price','$price_withvat','$manufacturer','$active','$colorid','$sizeid','$brand_id','$group_id','$category_id','','$description','$longdescription','$type','$parentproductid')";
            mysqli_query($connect, $sql1);
            echo "<br>Product no:$productid=>Product inserted successfully =>Type:$type<br>";


    }
}

    }
