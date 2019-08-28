<?php

// include ERPLY API class
include ("EAPI.class.php");

//configuration settings
include ("erply.php");


//fetch brands
function syncBrands(){
    global $api;
    global $connect;
    $result=$api->sendRequest("getBrands",array());

//Default output format is JSON, so we'll decode it inti PHP array
    $outputs=json_decode($result,true);

    $brandno=$outputs['status']['recordsInResponse'];
    echo '<h2>Brands Log</h2>';

//Iterate through all brands
    for($i=0;$i<$brandno;$i++) {

        $brand_id = $outputs['records'][$i]['brandID'];
        $name = $outputs['records'][$i]['name'];
        $sql="SELECT * FROM brands WHERE brand_id='$brand_id'";
        $check=mysqli_query($connect,$sql);
        $checkrows=mysqli_num_rows($check);

//checks if brand exist or not in database else adds brands into database
        if($checkrows>0){
            echo "<br>$i.Brand no: $brand_id=>Data exists<br>";
        }
        else {
            $sql = "INSERT INTO brands (brand_id, name) VALUES ('$brand_id','$name')";
            mysqli_query($connect, $sql);
            echo "<br>$i.Order no: $brand_id=>Data inserted successfully<br>";
        }
    }
}

//fetch categories of products
function syncproductCategories(){
    global $api;
    global $connect;
    $result=$api->sendRequest("getProductCategories",array());

//Default output format is JSON, so we'll decode it inti PHP array
    $outputs=json_decode($result,true);



    $productcategoriesno=$outputs['status']['recordsInResponse'];
    echo '<h2>Product Categories Log</h2>';

    //iterate through all categories
    for($i=0;$i<$productcategoriesno;$i++) {
//
        $category_id = $outputs['records'][$i]['productCategoryID'];
        $name = $outputs['records'][$i]['productCategoryName'];
        $sql="SELECT * FROM categories WHERE category_id='$category_id'";
        $check=mysqli_query($connect,$sql);
        $checkrows=mysqli_num_rows($check);

        //checks if category exist or not in database else add categories into database
        if($checkrows>0){
            echo "<br>$i.Category no: $category_id=>Data exists<br>";
        }
        else {
            $sql = "INSERT INTO categories (category_id, name) VALUES ('$category_id','$name')";
            mysqli_query($connect, $sql);
            echo "<br>$i.Category no: $category_id=>Data inserted successfully<br>";
        }
    }
}

//fetch groups of products
function syncproductGroups()
{
    global $api;
    global $connect;
    global $outputs;
    global $i;
    $result = $api->sendRequest("getProductGroups", array());

//Default output format is JSON, so we'll decode it inti PHP array
    $outputs = json_decode($result, true);

//    print "<pre>";
//    print_r($outputs);
//    print "</pre>";

    $productgroupsno = $outputs['status']['recordsInResponse'];
    echo '<h2>Product Groups Log</h2>';

//iterate through all product groups
    for ($i = 0; $i < $productgroupsno; $i++) {

        $group_id = $outputs['records'][$i]['productGroupID'];
        $name = $outputs['records'][$i]['name'];
        $sql = "SELECT * FROM productgroups WHERE group_id='$group_id'";
        $check = mysqli_query($connect, $sql);
        $checkrows = mysqli_num_rows($check);
        $subcategoriesno = count($outputs['records'][$i]['subGroups']);
        $sql = "INSERT INTO productgroups (group_id, name,level) VALUES ('$group_id','$name','1')";

        //checks if thieir are sub-groups
        if ($subcategoriesno > 0) {
          //checks if sub-group exists or not in database
            if ($checkrows > 0) {
                echo "<br>$i.Group no: $group_id=>Data exists<br>";
                syncSub2ProductGroupss($outputs['records'][$i]['subGroups']);

            } else {

                mysqli_query($connect, $sql);
                echo "<br>$i.Group no: $group_id=>Data inserted successfully<br>";
                syncSub2ProductGroupss($outputs['records'][$i]['subGroups']);
            }
        }
        elseif ($checkrows > 0) {
            echo "<br>$i.Group no: $group_id=>Data exists<br>";
        }
        else {
            mysqli_query($connect, $sql);
            echo "<br>$i.Group no: $group_id=>Data inserted successfully<br>";
        }


}
}


//fetch sub-gropus of products
function syncSub2ProductGroupss($subgroup)
{
    global $connect;
//    print "<pre>";
//    print_r($subgroup);
//   print "<hr>pass";

    $subgroupno = count($subgroup);
    // checks if sub-groups consists of sub-groups
    if ($subgroupno > 0)
        foreach ($subgroup as $sub) {
            $subc = count($sub['subGroups']);

            if ($subc > 0) {
                syncSub2ProductGroupss($sub['subGroups']);
                echo '(consists sub groups)';

            }
        }

    $id = $subgroup[0]['id'];
    $name = $subgroup[0]['name'];
    $parent_id = $subgroup[0]['parentGroupID'];
    $sql = "SELECT * FROM productgroups WHERE group_id='$id'";
    $check = mysqli_query($connect, $sql);
    $checkrows = mysqli_num_rows($check);
    if ($checkrows > 0) {
        echo "<br>Sub-group no: $id=>Data exists<br>";

    } else {
        $sql = "INSERT INTO productgroups (group_id, parent_id,name,level) VALUES ('$id','$parent_id','$name','1')";
        mysqli_query($connect, $sql);
        echo "<br>Group no: $id=>Data inserted successfully<br>";
    }


}





syncBrands();
syncproductCategories();
syncproductGroups();
?>
