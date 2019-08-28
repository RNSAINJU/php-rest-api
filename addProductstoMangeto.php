<?php

include 'local.php';

    $requestUrl=$Url.'/products';

//sql to fetch all product details as well as product groups detais using table join
    $sql = "SELECT products.id,products.product_id, products.code, products.name,products.image, products.price, products.cost, products.manufacturer,products.active,products.group_id, products.quantity,products.description,
products.longdescription,products.magento_id, products.status, products.type, productgroups.group_id,
 productgroups.magentocategory_id,productgroups.magentoparentcategory_id FROM products INNER JOIN productgroups ON products.group_id=productgroups.group_id WHERE products.status='0' AND products.type='PRODUCT' LIMIT 1";
    $product = mysqli_query($connect, $sql);
    $products = mysqli_fetch_assoc($product);

//sql to fetch magento color-id using table join
    $sql1="SELECT products.color_id,products.status,colors.variation_id,colors.magento_id FROM products INNER JOIN colors ON products.color_id=colors.variation_id WHERE products.status='0'";
    $allcolors = mysqli_query($connect, $sql1);
    $colors_magentoid = mysqli_fetch_assoc($allcolors);

//sql to fetch magento size-id using table join
    $sql2="SELECT products.size_id,products.status,size.id,size.magento_id FROM products INNER JOIN size ON products.size_id=size.id WHERE products.status='0'";
    $allsizes = mysqli_query($connect, $sql2);
    $size_magentoid = mysqli_fetch_assoc($allsizes);

    $id=$products['id'];
    $product_id = $products['product_id'];
    $code = $products['code'];
    $name = $products['name'];
    $urlkey=substr($name,0,5);
    $price = $products['price'];
    $cost=$products['cost'];
    $status = $products['active'];
    $description = $products['description'];
    $longdescription=$products['longdescription'];
    $category_id = $products['magentocategory_id'];
    $childcategory_id=$products['magentoparentcategory_id'];
    $quantity=$products['quantity'];
    $type=$products['type'];
    $colors=$colors_magentoid['magento_id'];
    $size=$size_magentoid['magento_id'];
    $imageurl=$products['image'];
    $manufacturer=$products['manufacturer'];
    echo $name;
    echo $manufacturer;


    $productData = array(
        'id'=>$product_id,
        'sku' => $code,
        'name' => $name,
        'visibility' => 4, /*'1-not visible, 2-catalog', 3-search, 4-catalog and search*/
        'type_id' => 'simple',
        'price' => $price,
        'status' => $status,
        'attribute_set_id' => 4,
        'weight' => 0,
        'extension_attributes' => array(
            "stock_item" => array(
                // 'item_id'=>$product_id,
                // 'product_id'=>$product_id,
                // 'stock_id'=>981,
                "qty" => $quantity,
                "is_in_stock" => true,
            )
        ),
        'custom_attributes' => array(
            array('attribute_code' => 'category_ids', 'value' => ["$category_id", "$childcategory_id"]),
            array('attribute_code' => 'description', 'value' => $description),
            array('attribute_code' => 'details_and_care', 'value' => $longdescription),
            array('attribute_code' => 'size', 'value' => $size), //5=small
            array('attribute_code' => 'color', 'value' => $colors), //8-red
            array('attribute_code' => 'cost', 'value' => $cost),
            // array('attribute_code' => 'manufacturer', 'value' => $manufacturer),
            // array('attribute_code'=> 'image','value'=>'https://erply.s3.amazonaws.com/457184/pictures/14214_5be7fec42b9ce5.10554447_3M-Wrap-Film-Series-1080-Gloss-Blue-Raspberry-G378_large.jpg'),
            // array('attribute_code'=> 'small_image','value'=>'https://erply.s3.amazonaws.com/457184/pictures/14214_5be7fec42b9ce5.10554447_3M-Wrap-Film-Series-1080-Gloss-Blue-Raspberry-G378_large.jpg'),
            // array('attribute_code'=> 'thumbnail','value'=>'https://erply.s3.amazonaws.com/457184/pictures/14214_5be7fec42b9ce5.10554447_3M-Wrap-Film-Series-1080-Gloss-Blue-Raspberry-G378_large.jpg'),
    )
  );


    $productData = json_encode(array('product' => $productData));


    $setHaders = array('Content-Type:application/json', 'Authorization:Bearer ' . $token);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $requestUrl);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $productData);

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

    $magento_id = $output->id;
    $magento_type=$output->type_id;
    $sql = "UPDATE products SET magento_id='$magento_id'WHERE product_id='$product_id'";
    $query = mysqli_query($connect, $sql);
    $sql = "UPDATE products SET status='1' WHERE product_id='$product_id'";
    $query = mysqli_query($connect, $sql);
//
// $path = 'Matt_T-Shirtlarge.jpg';
// $type = pathinfo($path, PATHINFO_EXTENSION);
// $data = base64_encode(file_get_contents($path));
// $base64 = base64_encode($data);
//
//
// echo $base64;
//
//
//
//     $newrequestUrl=$Url.'/products/SAMPLE005ALL/media';
//
//       $newproductData =   array(
//       'media_type'=>'image',
//       'label'=>'sd',
//       'position'=>0,
//       'disabled'=>false,
//       'types'=>array(
//         'image',
//         'small_image',
//         'thumbnail'
//       ),
//       'file'=>'Matt_T-Shirtlarge.jpg',
//       'content'=>array(
//         'base64_encoded_data'=>$base64,
//         'type'=>'image/jpeg',
//         'name'=>'Matt_T-Shirtlarge.jpg'
//       )
//     );
//
//
//           $newproductData = json_encode(array('entry' => $newproductData));
//
//
//           $setHaders = array('Content-Type:application/json', 'Authorization:Bearer ' . $token);
//
//
//           $ch = curl_init();
//           curl_setopt($ch, CURLOPT_URL, $newrequestUrl);
//
//           curl_setopt($ch, CURLOPT_POSTFIELDS, $newproductData);
//
//           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//           curl_setopt($ch, CURLOPT_HTTPHEADER, $setHaders);
//           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//           if (curl_exec($ch) === false) {
//               echo "Curl error: " . curl_error($ch) . "\n";
//           } else {
//               $newresponse = curl_exec($ch) ?: "";
//           }
//
//           curl_close($ch);
//
//           $output = json_decode($newresponse);
//           print "<pre>";
//           print_r($output);
//           print "</pre>";


    ?>
