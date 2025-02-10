<?php 
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");

    require "../config/db.php";
    require "../config/authmiddelware.php";

    $method = $_SERVER["REQUEST_METHOD"];

     // GET ALL ORDERS
     if($method == "GET"){
        if(isset($_GET["id"])){
            $id = intval($_GET["id"]);
            $sql = "SELECT * FROM `orderdetails` WHERE `id` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if($row = mysqli_fetch_assoc($result)){
                echo json_encode(["status" => "ok", "data" => $row]);
            }else{
                echo json_encode(["status" => "error", "message" => "Order not found"]);
            }
        }else{
            $sql = "SELECT * FROM `orderdetails`";
            $result = mysqli_query($conn, $sql);
            $order = [];
            while($row = mysqli_fetch_assoc($result)){
                $order[] = $row;
            }
            echo json_encode(["status" => "ok", "data" =>$order]);
        }
    }

    // Create Order
    if($method == "POST"){
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['product']) || empty($data['quantity']) || empty($data['price']) || empty($data['address']) || empty($data['status'])) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit();
        }

        $product = trim($data["product"]);
        $quantity = trim($data["quantity"]);
        $price = trim($data["price"]);
        $address = trim($data["address"]);
        $status = trim($data["status"]);

        $sql = "INSERT INTO orderdetails (product, quantity, price, address, status) VALUES (?, ?,? , ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "siiss", $product, $quantity, $price, $address, $status);
        if(mysqli_stmt_execute($stmt)){
            echo json_encode(["status" => "success", "message" => "Order created successfully"]);
        }else{
            echo json_encode(["status" => "error", "message" => "Error occurred while creating order"]);
        }
    }

    // Update Order
    elseif($method == "PUT"){
        $data = json_decode(file_get_contents("php://input"), true);

        if(empty($data["id"]) || empty($data["product"]) || empty($data["quantity"]) || empty($data["price"]) || empty($data["address"]) || empty($data["status"])){
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
        }

        $id = trim(intval($data["id"]));
        $product = trim($data["product"]);
        $quantity = trim($data["quantity"]);
        $price = trim($data["price"]);
        $address = trim($data["address"]);
        $status = trim($data["status"]);

        $sql = "UPDATE orderdetails SET product = ?, quantity = ?, price = ?, address = ?, status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "siissi", $product, $quantity, $price, $address, $status, $id);
        if(mysqli_stmt_execute($stmt)){
            echo json_encode(["status" => "success", "message" => "Product is updated"]);
        }else{
            echo json_encode(["status" => "filed", "message" => "Product updation failed"]);
        }
    }

    // Delete Order
    elseif($method == "DELETE"){
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data["id"])){
            echo json_encode(["status" => "error", "message" => "Enter Id"]);
        }

        $id = trim(intval($data["id"]));
        $sql = "DELETE FROM orderdetails WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "ok", "message" => "Order deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error deleting Order"]);
        }
    }
?>