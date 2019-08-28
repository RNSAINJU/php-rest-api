<?php
/**
 * Created by PhpStorm.
 * User: Demon
 * Date: 10/23/2018
 * Time: 10:23 AM
 */

include ("live.php");

//Inserts magento color and size id if magento color and size matches with erply
function fetchcolorsandsize(){
    global $connect;
    global $token;
    global $Url;

    $headers = array("Authorization: Bearer $token");

    $requestUrl = $Url.'/products/attribute-sets/9/attributes';

    $ch = curl_init();
    $ch = curl_init($requestUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $results = curl_exec($ch);
    $results = json_decode($results, true);

//    live=37
    //size=34
    $colors=$results[37]['options'];

    foreach ($colors as $color){
        $colorname=$color['label'];
        $colorvalue=$color['value'];

        $sql="UPDATE colors SET magento_id='$colorvalue' WHERE name='$colorname'";
        mysqli_query($connect,$sql);
        echo "<br>Color fetched successfully<br>";
    }

    $sizes=$results[34]['options'];
    foreach ($sizes as $size){
        $size_name=$size['label'];
        $size_value=$size['value'];


        $sql="UPDATE size SET magento_id='$size_value' WHERE size='$size_name'";
        mysqli_query($connect,$sql);

        echo "<br>Size fetched successfully<br>";
    }
    print "<pre>";
    print_r($results);
    print "</pre>";

}

//Inserts category id if magento category matches with erply
function fetchcategories()
{
    global $connect;
    global $token;
    global $Url;


    $headers = array("Authorization: Bearer $token");

    $requestUrl = $Url.'/categories?searchCriteria';

    $ch = curl_init();
    $ch = curl_init($requestUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $results = curl_exec($ch);
    $results = json_decode($results, true);
    $outputs = $results;

    print "<pre>";
    print_r($results);
    print "</pre>";
    $categoriesno = count($results['children_data']);

    for ($i = 0; $i < $categoriesno; $i++) {

        $id = $results['children_data'][$i]['id'];
        $name = $results['children_data'][$i]['name'];
        $sub_category = $results['children_data'][$i]['children_data'];
        $sub_categoryno = count($results['children_data'][$i]['children_data']);

        $sql = "UPDATE productgroups SET magentocategory_id='$id' WHERE name='$name'";

        //checks if sub_category exists or not else adds subcategory into database
        if ($sub_categoryno < 1) {
            echo "<br>$i.Group no: $id=>Data fetched<br>";
                mysqli_query($connect, $sql);
        } else {
            mysqli_query($connect, $sql);
            echo "<br>$i.Group no: $id=>Data fetched with subcategories<br>";
            fetchsubcategories($results['children_data'][$i]['children_data']);
        }
    }
}

//Inserts sub-category id if magento sub-category matches with erply
function fetchsubcategories($subgroup)
{
        global $connect;
//    print "<pre>";
//    print_r($subgroup);
//   print "<hr>pass";

        $subgroupno = count($subgroup);


        if ($subgroupno > 0)
            foreach ($subgroup as $sub) {
                $subc = count($sub['children_data']);

                if ($subc > 0) {
                    fetchsubcategories($sub['children_data']);
                    echo '(consists sub groups)';

                }
            }

            $id = $subgroup[0]['id'];
            $parent_id = $subgroup[0]['parent_id'];
            $name = $subgroup[0]['name'];

        $sql = "UPDATE productgroups SET magentocategory_id='$id' WHERE name='$name'";
        mysqli_query($connect,$sql);
        $sql = "UPDATE productgroups SET magentoparentcategory_id='$parent_id' WHERE name='$name'";
        mysqli_query($connect,$sql);
    }



fetchcategories();
fetchcolorsandsize();
