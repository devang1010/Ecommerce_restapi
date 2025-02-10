<?php 
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");

    require "../config/db.php";
    require "../config/authmiddelware.php";

    $method = $_SERVER["REQUEST_METHOD"];

    // Get user cart with all products
    if ($method == "GET") {
        if (isset($_GET["id"])) {
            $user_id = intval($_GET["id"]);

            // Query to fetch cart items and product details
            $sql = "SELECT * FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $cart_items = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $cart_items[] = $row;
            }

            if (!empty($cart_items)) {
                echo json_encode(["status" => "ok", "cart" => $cart_items]);
            } else {
                echo json_encode(["status" => "error", "message" => "Cart is empty"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "User ID is required"]);
        }
    }

    // Create cart 
    if($method == "POST"){
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['product']) || empty($data['quantity']) || empty($data['totalprice'])) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit();
        }

        $product = trim($data["product"]);
        $quantity = trim($data["quantity"]);
        $totalprice = trim($data["totalprice"]);

        $sql = "INSERT INTO cart (product, quantity, totalprice) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sii", $product, $quantity, $totalprice);
        if(mysqli_stmt_execute($stmt)){
            echo json_encode(["status" => "success", "message" => "Cart created successfully"]);
        }else{
            echo json_encode(["status" => "error", "message" => "Error occurred while creating cart"]);
        }
    }

    // DELETE Cart
    elseif($method == "DELETE"){
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data["id"])){
            echo json_encode(["status" => "error", "message" => "Enter Id"]);
        }

        $id = trim(intval($data["id"]));
        $sql = "DELETE FROM cart WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "ok", "message" => "Cart deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error deleting Cart"]);
        }
    }
?>
