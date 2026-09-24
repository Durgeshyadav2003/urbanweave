<?php

session_start();

header("Content-Type: application/json");

require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}


/* =========================
   GET CUSTOMER DETAILS
========================= */

$name = trim($_POST["name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$email = trim($_POST["email"] ?? "");
$pincode = trim($_POST["pincode"] ?? "");
$address = trim($_POST["address"] ?? "");
$city = trim($_POST["city"] ?? "");
$state = trim($_POST["state"] ?? "");
$payment_method = trim($_POST["payment_method"] ?? "");

$items_json = $_POST["items"] ?? "";


/* =========================
   VALIDATION
========================= */

if (
    $name === "" ||
    $phone === "" ||
    $pincode === "" ||
    $address === "" ||
    $city === "" ||
    $state === "" ||
    $payment_method === ""
) {
    echo json_encode([
        "success" => false,
        "message" => "Please fill all required fields."
    ]);
    exit;
}

if (!preg_match("/^[0-9]{10}$/", $phone)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid 10-digit mobile number."
    ]);
    exit;
}

if (!preg_match("/^[0-9]{6}$/", $pincode)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid 6-digit pincode."
    ]);
    exit;
}

if (!in_array($payment_method, ["COD", "ONLINE"])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid payment method."
    ]);
    exit;
}

if ($items_json === "") {
    echo json_encode([
        "success" => false,
        "message" => "Your cart is empty."
    ]);
    exit;
}

$items = json_decode($items_json, true);

if (!is_array($items) || count($items) === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid cart data."
    ]);
    exit;
}


/* =========================
   START TRANSACTION
========================= */

$conn->begin_transaction();

try {

    $total_amount = 0;

    $validated_items = [];


    /* =========================
       CHECK PRODUCTS + STOCK
    ========================= */

    $product_stmt = $conn->prepare(
        "SELECT id, name, price, stock
         FROM products
         WHERE id = ?
         FOR UPDATE"
    );

    if (!$product_stmt) {
        throw new Exception("Product query failed.");
    }


    foreach ($items as $item) {

        $product_id = intval($item["productId"] ?? 0);
        $quantity = intval($item["quantity"] ?? 0);

        $size = trim($item["size"] ?? "");
        $color = trim($item["color"] ?? "");


        if ($product_id <= 0 || $quantity <= 0) {
            throw new Exception("Invalid product or quantity.");
        }


        $product_stmt->bind_param(
            "i",
            $product_id
        );

        $product_stmt->execute();

        $result = $product_stmt->get_result();

        $product = $result->fetch_assoc();


        if (!$product) {
            throw new Exception("Product not found.");
        }


        if ($product["stock"] < $quantity) {

            throw new Exception(
                $product["name"] . " does not have enough stock."
            );

        }


        $price = (float)$product["price"];

        $item_total = $price * $quantity;

        $total_amount += $item_total;


        $validated_items[] = [
            "product_id" => $product_id,
            "name" => $product["name"],
            "price" => $price,
            "quantity" => $quantity,
            "size" => $size,
            "color" => $color
        ];
    }


    $product_stmt->close();


    /* =========================
       SHIPPING
    ========================= */

    $shipping_charge = ($total_amount >= 1999) ? 0 : 99;

    $final_amount = $total_amount + $shipping_charge;


    /* =========================
       ORDER NUMBER
    ========================= */

    $order_number =
        "UW" .
        date("YmdHis") .
        rand(100, 999);


    /* =========================
       USER ID
    ========================= */

    /* =========================
   USER ID
========================= */

if (!isset($_SESSION["user_id"]) || intval($_SESSION["user_id"]) <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Please login before placing an order."
    ]);

    exit;
}

$user_id = intval($_SESSION["user_id"]);


    /* =========================
       PAYMENT STATUS
    ========================= */

    if ($payment_method === "COD") {
        $payment_status = "Pending";
        $payment_db = "Cash on Delivery";
    } else {
        $payment_status = "Pending";
        $payment_db = "Online Payment";
    }


    /* =========================
       SHIPPING ADDRESS
    ========================= */

    $shipping_address =
        $address .
        ", " .
        $city .
        ", " .
        $state .
        " - " .
        $pincode;


    /* =========================
       INSERT ORDER
    ========================= */

    $order_stmt = $conn->prepare(
        "INSERT INTO orders
        (
            order_number,
            user_id,
            total_amount,
            status,
            payment_method,
            payment_status,
            shipping_name,
            shipping_phone,
            shipping_address,
            shipping_city,
            shipping_state,
            shipping_pincode
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );


    if (!$order_stmt) {
        throw new Exception("Order query failed.");
    }


    $status = "Pending";


    $order_stmt->bind_param(
        "sidsssssssss",
        $order_number,
        $user_id,
        $final_amount,
        $status,
        $payment_db,
        $payment_status,
        $name,
        $phone,
        $shipping_address,
        $city,
        $state,
        $pincode
    );


    if (!$order_stmt->execute()) {
        throw new Exception(
            "Unable to create order: " .
            $order_stmt->error
        );
    }


    $order_id = $conn->insert_id;


    $order_stmt->close();


    /* =========================
       SAVE ORDER ITEMS
    ========================= */

    $item_stmt = $conn->prepare(
        "INSERT INTO order_items
        (
            order_id,
            product_id,
            product_name,
            price,
            quantity,
            size,
            color
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?)"
    );


    if (!$item_stmt) {
        throw new Exception("Order items query failed.");
    }


    /* =========================
       UPDATE STOCK
    ========================= */

    $stock_stmt = $conn->prepare(
        "UPDATE products
         SET stock = stock - ?
         WHERE id = ?"
    );


    if (!$stock_stmt) {
        throw new Exception("Stock update query failed.");
    }


    foreach ($validated_items as $item) {


        $item_stmt->bind_param(
            "iisdiss",
            $order_id,
            $item["product_id"],
            $item["name"],
            $item["price"],
            $item["quantity"],
            $item["size"],
            $item["color"]
        );


        if (!$item_stmt->execute()) {
            throw new Exception(
                "Unable to save order item."
            );
        }


        $stock_stmt->bind_param(
            "ii",
            $item["quantity"],
            $item["product_id"]
        );


        if (!$stock_stmt->execute()) {
            throw new Exception(
                "Unable to update stock."
            );
        }

    }


    $item_stmt->close();

    $stock_stmt->close();


    /* =========================
       COMMIT
    ========================= */

    $conn->commit();


    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully.",
        "order_id" => $order_id,
        "order_number" => $order_number,
        "total" => $final_amount
    ]);


} catch (Exception $e) {


    $conn->rollback();


    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}


$conn->close();

?>