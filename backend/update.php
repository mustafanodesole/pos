<?php

require 'conn.php';

try {
    //code...
    $total_items = $_POST['total_items'];
    $purchased_price = $_POST['purchased_price'];
    $retail_price = $_POST['retail_price'];
    $id = $_POST['id'];
    
   
    $sql = "UPDATE products SET retail_price = $retail_price , purchased_price = $purchased_price , total_items = total_items + $total_items WHERE id = $id";
    // $sql =  "UPDATE products SET retail_price = 40, purchased_price = 50 WHERE id = 2";
    
    $stmt = $conn->prepare($sql);

    if($stmt->execute()){
    //    $data =  $conn->prepare("SELECT * FROM products WHERE id = $id")->execute();
    //    $data = $data->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(["message"=>"Successfully Updated "]);
    }else{

        echo json_encode(["message" => "Filed to update the "]);
    }

} catch (PDOException $e) {
    echo json_encode(["message"=>"database error " . $e->getMessage()]);
    http_response_code(500);
}
