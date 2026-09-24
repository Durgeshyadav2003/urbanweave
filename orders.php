<?php

session_start();

require_once "includes/db.php";
require_once "includes/config.php";

$user_id = $_SESSION["user_id"] ?? 1;

/* =========================
   CANCEL ORDER
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cancel_order"])) {

    $cancel_order_id = intval($_POST["cancel_order"]);

    if ($cancel_order_id > 0) {

        $cancel_stmt = $conn->prepare(
            "UPDATE orders
             SET status = 'Cancelled'
             WHERE id = ?
             AND user_id = ?
             AND status IN ('Pending', 'Confirmed')"
        );

        $cancel_stmt->bind_param(
            "ii",
            $cancel_order_id,
            $user_id
        );

        $cancel_stmt->execute();

        $cancel_stmt->close();

        header("Location: orders.php");
        exit;
    }
}


/* =========================
   GET ORDERS
========================= */

$order_stmt = $conn->prepare(
    "SELECT
        id,
        order_number,
        total_amount,
        status,
        payment_method,
        payment_status,
        shipping_name,
        shipping_phone,
        shipping_address,
        shipping_city,
        shipping_state,
        shipping_pincode,
        created_at
     FROM orders
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();

$orders_result = $order_stmt->get_result();


/* =========================
   GET ORDER ITEMS FUNCTION
========================= */

function getOrderItems($conn, $order_id)
{
    $stmt = $conn->prepare(
        "SELECT
            product_id,
            product_name,
            price,
            quantity,
            size,
            color
         FROM order_items
         WHERE order_id = ?"
    );

    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    return $stmt->get_result();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Orders | UrbanWeave</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        .orders-page {
            max-width: 1400px;
            margin: auto;
            padding: 70px 35px 120px;
        }

        .orders-heading {
            margin-bottom: 55px;
        }

        .orders-heading p {
            font-size: 13px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 10px;
            opacity: .65;
        }

        .orders-heading h1 {
            font-size: clamp(55px, 8vw, 120px);
            line-height: .9;
            margin: 0;
            font-weight: 800;
            letter-spacing: -5px;
        }

        .orders-container {
            display: flex;
            flex-direction: column;
            gap: 35px;
        }

        .order-card {
            border: 1px solid #d5d5d5;
            background: #fff;
        }

        .order-header {
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #ddd;
            background: #f5f3ef;
        }

        .order-number {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .order-date {
            font-size: 13px;
            opacity: .65;
            margin-top: 6px;
        }

        .order-status {
            padding: 9px 16px;
            border: 1px solid #222;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .order-body {
            padding: 30px;
        }

        .order-items {
            display: flex;
            flex-direction: column;
        }

        .order-item {
            display: grid;
            grid-template-columns: 90px 1fr auto;
            gap: 20px;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .order-item:first-child {
            padding-top: 0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item-image {
            width: 90px;
            height: 110px;
            background: #f0eeea;
            overflow: hidden;
        }

        .order-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .order-item-info h3 {
            margin: 0 0 8px;
            font-size: 17px;
        }

        .order-item-info p {
            margin: 4px 0;
            font-size: 13px;
            opacity: .65;
        }

        .order-item-price {
            text-align: right;
            font-size: 16px;
            font-weight: 700;
        }

        .order-bottom {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #222;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .shipping-info h3,
        .payment-info h3 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0 0 15px;
        }

        .shipping-info p,
        .payment-info p {
            font-size: 14px;
            line-height: 1.7;
            margin: 3px 0;
        }

        .order-total {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-total span:first-child {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .order-total strong {
            font-size: 25px;
        }

        .empty-orders {
            text-align: center;
            padding: 100px 20px;
            border: 1px solid #ddd;
            background: #f5f3ef;
        }

        .empty-orders h2 {
            font-size: 45px;
            margin: 0 0 15px;
        }

        .empty-orders p {
            opacity: .65;
            margin-bottom: 30px;
        }

        .shop-btn {
            display: inline-block;
            background: #111;
            color: #fff;
            padding: 15px 28px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {

            .orders-page {
                padding: 50px 20px 80px;
            }

            .orders-heading h1 {
                font-size: 60px;
                letter-spacing: -3px;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-body {
                padding: 20px;
            }

            .order-item {
                grid-template-columns: 70px 1fr;
            }

            .order-item-image {
                width: 70px;
                height: 90px;
            }

            .order-item-price {
                grid-column: 2;
                text-align: left;
            }

            .order-bottom {
                grid-template-columns: 1fr;
                gap: 25px;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<header class="navbar">
    <div class="nav-container">

        <a href="index.php" class="logo">
            Urban<span>Weave</span>
        </a>

        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="products.php">Shop</a>
            <a href="orders.php">My Orders</a>
        </nav>

        <div class="nav-actions">

            <button class="icon-btn" id="searchBtn" aria-label="Search">
                🔍
            </button>

            <a href="cart.php" class="cart-btn">
                🛒 <span id="cartCount">0</span>
            </a>

            <a href="wishlist.php" class="cart-btn">
                ♡ <span id="wishlistCount">0</span>
            </a>

           <?php if (isset($_SESSION["user_id"])): ?>

    <a href="orders.php" class="login-link">
        👤 <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
    </a>

    <a href="logout.php" class="register-link">
        Logout
    </a>

<?php else: ?>

    <a href="login.php" class="login-link">
        Login
    </a>

    <a href="register.php" class="register-link">
        Register
    </a>

<?php endif; ?>

            <button class="menu-btn" id="menuBtn">
                ☰
            </button>

        </div>

    </div>
</header>



<!-- =========================
     ORDERS PAGE
========================= -->

<main class="orders-page">

    <div class="orders-heading">

        <p>Your UrbanWeave Account</p>

        <h1>MY ORDERS</h1>

    </div>


    <?php if ($orders_result->num_rows === 0): ?>

        <div class="empty-orders">

            <h2>NO ORDERS YET</h2>

            <p>
                You haven't placed any orders yet.
            </p>

            <a
                href="products.php"
                class="shop-btn"
            >
                START SHOPPING →
            </a>

        </div>

    <?php else: ?>


        <div class="orders-container">

            <?php while ($order = $orders_result->fetch_assoc()): ?>


                <div class="order-card">


                    <!-- ORDER HEADER -->

                    <div class="order-header">

                        <div>

                            <div class="order-number">

                                ORDER #
                                <?php
                                echo htmlspecialchars(
                                    $order["order_number"]
                                );
                                ?>

                            </div>

                            <div class="order-date">

                                <?php
                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $order["created_at"]
                                    )
                                );
                                ?>

                            </div>

                        </div>


                        <div class="order-status">

                            <?php
                            echo htmlspecialchars(
                                $order["status"]
                            );
                            ?>

                        </div>

                    </div>



                    <!-- ORDER BODY -->

                    <div class="order-body">


                        <div class="order-items">


                            <?php

                            $items_result = getOrderItems(
                                $conn,
                                $order["id"]
                            );

                            ?>


                            <?php while ($item = $items_result->fetch_assoc()): ?>


                                <div class="order-item">


                                    <!-- IMAGE -->

                                    <div class="order-item-image">

                                        <?php

                                        $image_stmt = $conn->prepare(
                                            "SELECT image
                                             FROM products
                                             WHERE id = ?"
                                        );

                                        $image_stmt->bind_param(
                                            "i",
                                            $item["product_id"]
                                        );

                                        $image_stmt->execute();

                                        $image_result =
                                            $image_stmt->get_result();

                                        $product_image =
                                            $image_result->fetch_assoc();

                                        $image_stmt->close();

                                        ?>


                                        <?php if (
                                            $product_image &&
                                            !empty(
                                                $product_image["image"]
                                            )
                                        ): ?>

                                            <img
                                                src="assets/images/<?php
                                                echo htmlspecialchars(
                                                    $product_image["image"]
                                                );
                                                ?>"
                                                alt="<?php
                                                echo htmlspecialchars(
                                                    $item["product_name"]
                                                );
                                                ?>"
                                            >

                                        <?php endif; ?>

                                    </div>



                                    <!-- PRODUCT INFO -->

                                    <div class="order-item-info">

                                        <h3>

                                            <?php
                                            echo htmlspecialchars(
                                                $item["product_name"]
                                            );
                                            ?>

                                        </h3>


                                        <p>

                                            Quantity:
                                            <?php
                                            echo (int)$item["quantity"];
                                            ?>

                                        </p>


                                        <?php if (
                                            !empty($item["size"])
                                        ): ?>

                                            <p>

                                                Size:
                                                <?php
                                                echo htmlspecialchars(
                                                    $item["size"]
                                                );
                                                ?>

                                            </p>

                                        <?php endif; ?>


                                        <?php if (
                                            !empty($item["color"])
                                        ): ?>

                                            <p>

                                                Color:
                                                <?php
                                                echo htmlspecialchars(
                                                    $item["color"]
                                                );
                                                ?>

                                            </p>

                                        <?php endif; ?>

                                    </div>



                                    <!-- PRICE -->

                                    <div class="order-item-price">

                                        ₹<?php

                                        echo number_format(
                                            (float)$item["price"] *
                                            (int)$item["quantity"],
                                            2
                                        );

                                        ?>

                                    </div>


                                </div>


                            <?php endwhile; ?>


                        </div>



                        <!-- ORDER DETAILS -->

                        <div class="order-bottom">


                            <!-- SHIPPING -->

                            <div class="shipping-info">

                                <h3>
                                    Delivery Details
                                </h3>

                                <p>
                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $order["shipping_name"]
                                        );
                                        ?>
                                    </strong>
                                </p>

                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $order["shipping_phone"]
                                    );
                                    ?>
                                </p>

                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $order["shipping_address"]
                                    );
                                    ?>
                                </p>

                            </div>



                            <!-- PAYMENT -->

                            <div class="payment-info">

                                <h3>
                                    Payment
                                </h3>

                                <p>

                                    Method:
                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $order["payment_method"]
                                        );
                                        ?>
                                    </strong>

                                </p>


                                <p>

                                    Payment Status:
                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $order["payment_status"]
                                        );
                                        ?>
                                    </strong>

                                </p>

                            </div>


                        </div>



                        <!-- TOTAL -->

                        <div class="order-total">

                        <?php if (
    $order["status"] === "Pending" ||
    $order["status"] === "Confirmed"
): ?>

    <form
        method="POST"
        onsubmit="return confirm('Are you sure you want to cancel this order?');"
        style="margin-top:20px;"
    >

        <input
            type="hidden"
            name="cancel_order"
            value="<?php echo (int)$order["id"]; ?>"
        >

        <button
            type="submit"
            style="
                background:#fff;
                color:#111;
                border:1px solid #111;
                padding:12px 22px;
                font-size:12px;
                font-weight:700;
                letter-spacing:1px;
                cursor:pointer;
            "
        >
            CANCEL ORDER
        </button>

    </form>

<?php endif; ?>

                            <span>
                                Order Total
                            </span>

                            <strong>

                                ₹<?php

                                echo number_format(
                                    (float)$order["total_amount"],
                                    2
                                );

                                ?>

                            </strong>

                        </div>


                    </div>


                </div>


            <?php endwhile; ?>

        </div>


    <?php endif; ?>


</main>



<!-- =========================
     FOOTER
========================= -->

<footer class="footer">

    <div class="footer-container">

        <div class="footer-brand">

            <a href="index.php" class="logo">
                Urban<span>Weave</span>
            </a>

            <p>
                WEAR YOUR IDENTITY.
            </p>

        </div>

        <div class="footer-links">

            <a href="index.php">
                Home
            </a>

            <a href="products.php">
                Shop
            </a>

            <a href="wishlist.php">
                Wishlist
            </a>

            <a href="orders.php">
                Orders
            </a>

        </div>

    </div>

</footer>


<script src="assets/js/script.js"></script>

</body>

</html>

<?php

$order_stmt->close();
$conn->close();

?>