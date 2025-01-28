<?php

require 'conn.php';

try {
    //code...
    $soldItems = $_POST['soldItems'];
    $id = $_POST['id'];
    
   
    $sql = "UPDATE products SET total_items = total_items - $soldItems WHERE id = $id";
    // $sql = "UPDATE products SET total_items = total_items - 2 WHERE id = 5;";
    
    $stmt = $conn->prepare($sql);

    if($stmt->execute()){
    //    $data =  $conn->prepare("SELECT * FROM products WHERE id = $id")->execute();
    //    $data = $data->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(["message"=>"Successfully Updated total items"]);
    }else{

        echo json_encode(["message" => "Filed to update the total items"]);
    }

} catch (PDOException $e) {
    echo json_encode(["message"=>"database error " . $e->getMessage()]);
    http_response_code(500);
}
