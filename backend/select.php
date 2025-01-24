<?php

require 'conn.php';

$sql = 'SELECT * FROM products';


$stmt = $conn->prepare($sql);
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// foreach($result as $items) {
//     echo '<br>';
    
//     print_r($items);
//     echo '<br>';
//   }
// if(!$stmt){
//     echo "does not get";
// }


// echo "Got the products";
echo json_encode(["status"=>"true","data"=>$result]);
// print_r($result);
// echo $result;
