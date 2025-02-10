<?php
header("Content-Type: application/json");

echo json_encode([
    "status" => "success",
    "message" => "Welcome to Blog API",
    "user endpoints" => [
        "POST /api/authUser.php" => "Create a User user",
        "GET /api/User.php" => "Get all users",
        "GET /api/user.php?id=user_ID" => "Get a single user",
        "PUT /api/user.php?id=user_ID" => "Update a user",
        "DELETE /api/user.php?id=user_ID" => "Delete a user"
    ],
    "Product endpoints" =>[
        "POST /api/Product.php" => "Create a blog Product",
        "GET /api/Product.php" => "Get all Product",
        "GET /api/Product.php?id=Product_ID" => "Get a single Product",
        "PUT /api/Product.php?id=Product_ID" => "Update a Product",
        "DELETE /api/Product.php?id=Product_ID" => "Delete a Product"
    ],
    "Order endpoints" =>[
        "POST /api/Order.php" => "Create a Ordert",
        "GET /api/Order.php" => "Get all Order",
        "GET /api/Order.php?id=Order_ID" => "Get a single Order",
        "PUT /api/Order.php?id=Order_ID" => "Update a Order",
        "DELETE /api/Order.php?id=Order_ID" => "Delete a Order"
    ],
    "Cart endpoints" =>[
        "POST /api/Cart.php" => "Create a Cart",
        "GET /api/Cart.php" => "Get all Products of cart",
        "DELETE /api/Cart.php?id=Cart" => "Delete a Cart"
    ]
]);