<?php 
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");

    require "../config/db.php";
    require "../config/authmiddelware.php";

    $method = $_SERVER["REQUEST_METHOD"];

    // CREATE USER
    if($method == "POST" && isset($_GET['action']) && $_GET['action'] == "signup"){
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit();
        }

        $username = trim($data["username"]);
        $email = trim($data["email"]);
        $password = md5(trim($data["password"]));

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo json_encode(["status" => "error", "message" => "Please enter a valid email address"]);
        }

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
        if(mysqli_stmt_execute($stmt)){
            echo json_encode(["status" => "success", "message" => "User added successfully"]);
        }else{
            echo json_encode(["status" => "error", "message" => "Error occurred while creating user"]);
        }
    }

    // Login 
    elseif($method == "POST" && isset($_GET["action"]) && $_GET["action"] == "login"){
        $data = json_decode(file_get_contents("php://input"), true);

        if( empty($data['email']) || empty($data['password'])){
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
        }

        $email = trim($data["email"]);
        $password = md5(trim($data["password"]));

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if($row = mysqli_fetch_assoc($result)){
            if($password == $row["password"]){
                $token = generateToken();
                $sql = "UPDATE users SET token = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $token, $row["id"]);
                mysqli_stmt_execute($stmt);
                
                echo json_encode(["status" => "success", "message" => "logged in", "token" => $token]);
            }else{
                echo json_encode(["status" => "error", "message" => "password incorrect"]);
            }
        }else{
            echo json_encode(["status" => "error", "message" => "User not found"]);
        }
    }

    // Log out
    elseif($method == "POST" && isset($_GET["action"]) && $_GET["action"] == "logout"){
        $data = json_decode(file_get_contents("php://input"), true);

        if(empty($data["id"])){
            echo json_encode(["status" => "error", "message" => "Enter Id"]);
        }

        $id = trim(intval($data["id"]));
        $sql = "UPDATE users SET token = NULL WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if(mysqli_stmt_execute($stmt)){
            echo json_encode(["status" => "success", "message" => "Logged out"]);
        }else{
            echo json_encode(["status" => "error", "message" => "Error Logging Out"]);
        }
    }
?>