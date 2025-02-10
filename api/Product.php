<?php 
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");

    require "../config/db.php";
    require "../config/authmiddelware.php";

    $method = $_SERVER["REQUEST_METHOD"];

    // GET ALL PRODUCTS
    if($method == "GET"){
        if(isset($_GET["id"])){
            $id = intval($_GET["id"]);
            $sql = "SELECT * FROM `products` WHERE `id` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if($row = mysqli_fetch_assoc($result)){
                echo json_encode(["status" => "ok", "data" => $row]);
            }else{
                echo json_encode(["status" => "error", "message" => "Product not found"]);
            }
        }else{
            $sql = "SELECT * FROM `products`";
            $result = mysqli_query($conn, $sql);
            $product = [];
            while($row = mysqli_fetch_assoc($result)){
                $product[] = $row;
            }
            echo json_encode(["status" => "ok", "data" =>$product]);
        }
    }

    // Add Product
    if($method == "POST"){
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['title']) || empty($data['discription']) || empty($data['size']) || empty($data['color']) || empty($data['price'])) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit();
        }

        $title = trim($data["title"]);
        $discription = trim($data["discription"]);
        $size = trim($data["size"]);
        $color = trim($data["color"]);
        $price = trim($data["price"]);


        $sql = "INSERT INTO products (title, discription, size, color, price) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $title, $discription, $size, $color, $price);
        if(mysqli_stmt_execute($stmt)){
            echo json_encode(["status" => "success", "message" => "Product added successfully"]);
        }else{
            echo json_encode(["status" => "error", "message" => "Error occurred while creating product"]);
        }
    }

    // Update Product
    elseif($method == "PUT"){
            $data = json_decode(file_get_contents("php://input"), true);
    
            if(empty($data["id"]) || empty($data["title"]) || empty($data["discription"]) || empty($data["size"]) || empty($data["color"]) || empty($data["price"])){
                echo json_encode(["status" => "error", "message" => "All fields are required"]);
            }
    
            $id = trim(intval($data["id"]));
            $title = trim($data["title"]);
            $discription = trim($data["discription"]);
            $size = trim($data["size"]);
            $color = trim($data["color"]);
            $price = trim($data["price"]);
    
            $sql = "UPDATE products SET title = ?, discription = ?, size = ?, color = ?, price = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssi", $title, $discription, $size, $color, $price, $id);
            if(mysqli_stmt_execute($stmt)){
                echo json_encode(["status" => "success", "message" => "Product is updated"]);
            }else{
                echo json_encode(["status" => "filed", "message" => "Product updation failed"]);
            }
        }

    // DELETE Product
    elseif($method == "DELETE"){
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data["id"])){
            echo json_encode(["status" => "error", "message" => "Enter Id"]);
        }

        $id = trim(intval($data["id"]));
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "ok", "message" => "Product deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error deleting Product"]);
        }
    }
?>