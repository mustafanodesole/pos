<?php  

require 'conn.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // print_r($_POST) ;
    // echo json_encode(["status"=>"successful","message"=>$_POST]);
    $product_name = $_POST['product_name'];
    $purchased_price = $_POST['purchased_price'];
    $retail_price = $_POST['retail_price'];
    $total_items = $_POST['total_items'];
    
    
    $sql = "INSERT INTO products (product_name, purchased_price , retail_price, total_items) VALUES ('$product_name' , $purchased_price , $retail_price , $total_items)";
    // $sql = "INSERT INTO products (product_name, purchased , retail_price, sold, remaining_items) VALUES ('Panadol Dark' , 205 , 210 , 240 , 20)";

    
    $query = $conn->prepare($sql);
    $execute = $query->execute();
    if(!$execute){
        // echo "data has not been inserted";
        echo json_encode(["message" => "data does not inserted"]);
    }else{
    // echo "data inserted successfully";
    echo json_encode(["message" => "data  inserted successfully"]);
    
}
}

// $result = $query->fetch()