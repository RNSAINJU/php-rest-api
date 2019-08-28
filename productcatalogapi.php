<?php


$connect=mysqli_connect("localhost","root","","printful");


$requestUrl='https://api.printful.com/products';

function request($requesturl){
$tokens='l2m6cwam-rg7x-bkf0:yd8e-4wdb09drfod9';
$token=base64_encode($tokens);
$setHaders = array('Content-Type:application/json', 'Authorization:Basic'.' '. $token);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $requesturl);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $productData);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
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



return $output;
}

function insertupdatevariants($variants){
  global $connect;
    $allvariants=$variants->variants;

    foreach ($allvariants as $variant){
      print "<pre>";
      print_r($variant);
      print "</pre>";

    //   $id=$variant->id;
    //   $parentid=$variant->sync_product_id;
    //    $name=$variant->name;
    //   $price=$variant->retail_price;
    //   $currency=$variant->currency;
    //   echo $image=$variant->files[1]->preview_url;
    //
    //   echo '<br>'.'name=>'.$name.'<br>';
    //   echo '<br>'.'price=>'.$price.'<br>';
    //   echo '<br>'.'currency=>'.$currency.'<br>';
    //   echo '<br>'.'imagelink=>'.$image.'<br>';
    //
    //   $sql="SELECT * FROM products WHERE printful_id='$id'";
    //   $check=mysqli_query($connect,$sql);
    //   $checkrows=mysqli_num_rows($check);
    //
    //   if($checkrows>0){
    //     $sql="UPDATE products SET name='$name',price='$price', currency='$currency', image='$image' WHERE printful_id='$id'";
    //     mysqli_query($connect,$sql);
    //   }
    //     else{
    //   echo $sql="INSERT INTO products (printful_id, name, price, currency, image,parent_id) VALUES ($id, '$name', $price, '$currency', '$image',$parentid)";
    //   $qy=mysqli_query($connect, $sql);
    // }
}
}


$printful=request($requestUrl);

// print "<pre>";
// print_r($printful);
// print "</pre>";

$results=$printful->result;
$count=count($results);
echo '<br>'.'Total products'.$count.'<br>';
foreach ($results as $result) {
  $id=$result->id;
  $newrequestUrl='https://api.printful.com/products/'.$id;


 $productid=$result->type;
 // $name=$result->name;
 $type=$result->type;
 $brand=$result->brand;
 $model=$result->model;
 $image=$result->image;
 $variantstotal=$result->variant_count;
 $currency=$result->currency;
 $description=$result->description;

echo '<br>'.'id=>'.$id.'<br>';
echo '<br>'.'type=>'.$type.'<br>';
echo '<br>'.'brand=>'.$brand.'<br>';
echo '<br>'.'model=>'.$model.'<br>';
echo '<br>'.'image link=>'.$image.'<br>';
echo '<br>'.'Variants=>'.$variantstotal.'<br>';
echo '<br>'.'description=>'.$description.'<br>';

$newprintful=request($newrequestUrl);
$allvariants=$newprintful->result->variants;
// print "<pre>";
// print_r($allvariants);
// print "</pre>";

  $sql="SELECT * FROM products WHERE printful_id='$id' AND variant_id=0";
  $check=mysqli_query($connect,$sql);
  $checkrows=mysqli_num_rows($check);

  if($checkrows>0){
    // $sql="UPDATE products SET name='$name',price='$price', currency='$currency', image='$image' WHERE printful_id='$id'";
    // mysqli_query($connect,$sql);
  }
    else{
  echo $sql="INSERT INTO products (printful_id, variant_id,name, type,brand,size,color,color_code,color_code_2,price, currency, image,description)
   VALUES ($id, '','$model', '$type','$brand', '','','','','', '$currency', '$image','".addslashes($description)."')";
  $qy=mysqli_query($connect, $sql);
}

foreach ($allvariants as $variants){
$variantid=$variants->id;
$productid=$variants->product_id;
$name=$variants->name;
$size=$variants->size;
$color=$variants->color;
$colorcode=$variants->color_code;
$color_code2=$variants->color_code2;
$variantimage=$variants->image;
$price=$variants->price;
$stock=$variants->in_stock;

echo '<br>'.'---------------variant id=>'.$variantid.'<br>';
echo '<br>'.'name=>'.$name.'<br>';
echo '<br>'.'size=>'.$size.'<br>';
echo '<br>'.'color=>'.$color.'<br>';
echo '<br>'.'color=>'.$colorcode.'<br>';
echo '<br>'.'color code 2=>'.$color_code2.'<br>';
echo '<br>'.'image link=>'.$variantimage.'<br>';
echo '<br>'.'price=>'.$price.'<br>';
echo '<br>'.'stock=>'.$stock.'<br>';
echo '<br>'.'---------------->'.'<br>';

$sql="SELECT * FROM products WHERE variant_id='$variantid'";
$check=mysqli_query($connect,$sql);
$checkrows=mysqli_num_rows($check);

if($checkrows>0){
  // $sql="UPDATE products SET name='$name',price='$price', currency='$currency', image='$image' WHERE printful_id='$id'";
  // mysqli_query($connect,$sql);
}
  else{
echo $sql="INSERT INTO products (printful_id, variant_id, name, type,brand,size,color,color_code,color_code_2,price, currency, image,description,stock)
 VALUES ($productid,$variantid, '$name', '','', '$size','$color','$colorcode','$color_code2',$price, '', '$variantimage','','$stock')";
$qy=mysqli_query($connect, $sql);
}

}
}
