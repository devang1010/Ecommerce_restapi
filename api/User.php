<?php 
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");

    require "../config/db.php";
    require "../config/authmiddelware.php";

    $method = $_SERVER["REQUEST_METHOD"];

    // GET ALL USERS
    if($method == "GET"){
        if(isset($_GET["id"])){
            $id = intval($_GET["id"]);
            $sql = "SELECT * FROM `users` WHERE `id` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if($row = mysqli_fetch_assoc($result)){
                echo json_encode(["status" => "ok", "data" => $row]);
            }else{
                echo json_encode(["status" => "error", "message" => "User not found"]);
            }
        }else{
            $sql = "SELECT * FROM `users`";
            $result = mysqli_query($conn, $sql);
            $user = [];
            while($row = mysqli_fetch_assoc($result)){
                $user[] = $row;
            }
            echo json_encode(["status" => "ok", "data" =>$user]);
        }
    }

    // Update User
    if($method == "PUT"){
        $data = json_decode(file_get_contents("php://input"), true);

        if(empty($data["id"]) || empty($data["username"]) || empty($data["email"])){
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
        }

        $id = trim(intval($data["id"]));
        $username = trim($data["username"]);
        $email = trim($data["email"]);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo json_encode(["status" => "error", "message" => "Please enter a valid email address"]);
        }

        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $id);
        if(mysqli_stmt_execute($stmt)){
            echo json_encode(["status" => "success", "message" => "User is updated"]);
        }else{
            echo json_encode(["status" => "filed", "message" => "User updation failed"]);
        }
    }

    // DELETE User
    
?>