<?php 

$servername = '172.17.0.1';
$username = 'root';
$password = 'NodeSol786';
$database = 'pos';
$conn = '';


try{
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


}catch(PDOException $e){
    echo "connection Failed" . $e->getMessage();
}

    // $servername = '172.17.0.1';
    // $username = 'root';
    // $password = 'NodeSol786';
    // $database = 'pos';
    // $conn = mysqli_connect($servername, $username , $password, $database);
    // if(!$conn){
    //     echo "connection failed";
    // }

    header("Access-Control-Allow-Origin: http://localhost:9000");
    // header("Access-Control-Allow-Origin: http://localhost:9300");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: Content-Type, Authorization");

?>