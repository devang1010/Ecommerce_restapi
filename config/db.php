<?php 
    $servername = "localhost";
    $Username = "root";
    $Password = "";
    $database = "ecommerce";

    $conn = mysqli_connect($servername, $Username, $Password, $database);
    
    if(!$conn){
        echo "Error connecting to database<br>";
    }
?>