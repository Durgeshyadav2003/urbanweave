<?php
session_start();

require_once "includes/db.php";
require_once "includes/config.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout | UrbanWeave</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        .checkout-page {
            max-width: 1400px;
            margin: auto;
            padding: 70px 35px 120px;
        }

        .checkout-heading {
            margin-bottom: 50px;
        }

        .checkout-heading p {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #777;
            margin-bottom: 12px;
        }

        .checkout-heading h1 {
            font-size: clamp(45px, 6vw, 80px);
            line-height: .9;
            letter-spacing: -4px;
        }

        .checkout-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 60px;
            align-items: start;
        }

        .checkout-section {
            margin-bottom: 40px;
        }

        .checkout-section h2 {
            font-size: 22px;
            letter-spacing: -1px;
            margin-bottom: 25px;
        }

        .checkout-form {
            border-top: 1px solid #d8d5cf;
            padding-top: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.3px;
            margin-bottom: 9px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            min-height: 50px;
            padding: 13px 14px;

            border: 1px solid #bbb;
            background: white;

            font-family: inherit;
            font-size: 13px;
            outline: none;
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 110px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #111;
        }

        .payment-options {
            border-top: 1px solid #d8d5cf;
            padding-top: 25px;
        }

        .payment-option {
            position: relative;
            margin-bottom: 12px;
        }

        .payment-option input {
            position: absolute;
            opacity: 0;
        }

        .payment-option label {
            display: flex;
            align-items: center;
            justify-content: space-between;

            min-height: 65px;
            padding: 15px 18px;

            border: 1px solid #c8c5be;
            cursor: pointer;

            transition: .25s;
        }

        .payment-option input:checked + label {
            border-color: #111;
            background: #f5f3ef;
        }

        .payment-left {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .payment-radio {
            width: 17px;
            height: 17px;
            border: 1px solid #777;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .payment-option input:checked + label .payment-radio::after {
            content: "";
            width: 8px;
            height: 8px;
            background: #111;
            border-radius: 50%;
        }

        .payment-name {
            font-size: 12px;
            font-weight: 700;
        }

        .payment-description {
            font-size: 10px;
            color: #777;
            margin-top: 3px;
        }

        .payment-icon {
            font-size: 20px;
        }

        .checkout-summary {
            position: sticky;
            top: 30px;
            padding: 30px;
            background: #f5f3ef;
        }

        .checkout-summary h2 {
            font-size: 24px;
            margin-bottom: 28px;
        }

        .summary-products {
            border-top: 1px solid #d8d5cf;
            margin-bottom: 15px;
        }

        .summary-product {
            display: flex;
            justify-content: space-between;
            gap: 15px;

            padding: 16px 0;
            border-bottom: 1px solid #d8d5cf;
        }

        .summary-product-name {
            font-size: 12px;
            font-weight: 700;
        }

        .summary-product-info {
            font-size: 10px;
            color: #777;
            margin-top: 5px;
        }

        .summary-product-price {
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;

            padding: 14px 0;
            border-bottom: 1px solid #d8d5cf;

            font-size: 12px;
        }

        .summary-row span:first-child {
            color: #666;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;

            padding-top: 22px;

            font-size: 18px;
            font-weight: 700;
        }

        .place-order-btn {
            width: 100%;
            min-height: 58px;

            margin-top: 25px;

            border: 1px solid #111;
            background: #111;
            color: white;

            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;

            cursor: pointer;
            transition: .3s;
        }

        .place-order-btn:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .place-order-btn:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
        }

        .secure-message {
            margin-top: 18px;
            text-align: center;

            font-size: 10px;
            color: #777;
            line-height: 1.5;
        }

        .checkout-message {
            margin-top: 15px;
            font-size: 12px;
            min-height: 20px;
        }

        .empty-checkout {
            text-align: center;
            padding: 100px 20px;
            border-top: 1px solid #d8d5cf;
        }

        .empty-checkout h2 {
            font-size: 40px;
            letter-spacing: -2px;
            margin-bottom: 15px;
        }

        .empty-checkout p {
            color: #777;
            margin-bottom: 30px;
        }

        .shop-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            min-height: 52px;
            padding: 0 30px;

            background: #111;
            color: white;
            text-decoration: none;

            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
        }

        @media (max-width: 950px) {

            .checkout-layout {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .checkout-summary {
                position: static;
            }

        }

        @media (max-width: 600px) {

            .checkout-page {
                padding: 45px 20px 80px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-group.full {
                grid-column: auto;
            }

        }

    </style>

</head>


<body>

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


<main class="checkout-page">

    <div class="checkout-heading">

        <p>Almost There</p>

        <h1>CHECKOUT</h1>

    </div>


    <div id="checkoutContainer"></div>

</main>


<footer class="footer">

    <div class="footer-grid">

        <div class="footer-brand">

            <a href="index.php" class="logo">
                Urban<span>Weave</span>
            </a>

            <p>
                Modern clothing for the modern generation.
            </p>

        </div>


        <div class="footer-column">

            <h4>SHOP</h4>

            <a href="products.php">All Products</a>

            <a href="products.php?category=T-Shirts">
                T-Shirts
            </a>

            <a href="products.php?category=Jackets">
                Jackets
            </a>

            <a href="products.php?category=Hoodies">
                Hoodies
            </a>

        </div>


        <div class="footer-column">

            <h4>HELP</h4>

            <a href="#">Contact Us</a>

            <a href="#">Shipping</a>

            <a href="#">Returns</a>

            <a href="#">FAQ</a>

        </div>


        <div class="footer-column">

            <h4>FOLLOW</h4>

            <a href="#">Instagram</a>

            <a href="#">Facebook</a>

            <a href="#">Pinterest</a>

        </div>

    </div>


    <div class="footer-bottom">

        <p>
            © 2026 UrbanWeave. All rights reserved.
        </p>

        <p>
            Designed for the streets.
        </p>

    </div>

</footer>


<script>

/* =========================================
   CART
========================================= */

function getCart() {

    return JSON.parse(
        localStorage.getItem("urbanweave_cart")
    ) || [];

}


/* =========================================
   BUY NOW
========================================= */

function getBuyNow() {

    const product =
        localStorage.getItem("urbanweave_buy_now");

    if (!product) {
        return null;
    }

    try {

        return JSON.parse(product);

    } catch (error) {

        return null;

    }

}


/* =========================================
   ESCAPE HTML
========================================= */

function escapeHTML(value) {

    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

}


/* =========================================
   GET CHECKOUT ITEMS
========================================= */

function getCheckoutItems() {

    const buyNow =
        getBuyNow();

    if (buyNow) {

        return [buyNow];

    }

    return getCart();

}


/* =========================================
   RENDER CHECKOUT
========================================= */

function renderCheckout() {

    const container =
        document.getElementById("checkoutContainer");

    const items =
        getCheckoutItems();


    if (items.length === 0) {

        container.innerHTML = `

            <div class="empty-checkout">

                <h2>Your cart is empty.</h2>

                <p>
                    Add some products before checking out.
                </p>

                <a
                    href="products.php"
                    class="shop-btn"
                >
                    GO TO SHOP →
                </a>

            </div>

        `;

        return;

    }


    let subtotal = 0;


    items.forEach(function(item) {

        subtotal +=
            Number(item.price) *
            Number(item.quantity);

    });


    const shipping =
        subtotal >= 1999 ? 0 : 99;


    const total =
        subtotal + shipping;


    let productsHTML = "";


    items.forEach(function(item) {

        const itemTotal =
            Number(item.price) *
            Number(item.quantity);


        productsHTML += `

            <div class="summary-product">

                <div>

                    <div class="summary-product-name">

                        ${escapeHTML(item.name)}

                    </div>

                    <div class="summary-product-info">

                        Qty: ${Number(item.quantity)}

                        ${
                            item.size
                            ? " • Size: " +
                              escapeHTML(item.size)
                            : ""
                        }

                        ${
                            item.color
                            ? " • Color: " +
                              escapeHTML(item.color)
                            : ""
                        }

                    </div>

                </div>

                <div class="summary-product-price">

                    ₹${itemTotal.toFixed(2)}

                </div>

            </div>

        `;

    });


    container.innerHTML = `

        <form
            class="checkout-layout"
            id="checkoutForm"
        >

            <div>

                <div class="checkout-section">

                    <h2>DELIVERY DETAILS</h2>

                    <div class="checkout-form">

                        <div class="form-grid">

                            <div class="form-group">

                                <label for="name">
                                    Full Name
                                </label>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    placeholder="Enter your full name"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="phone">
                                    Mobile Number
                                </label>

                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    placeholder="10 digit mobile number"
                                    maxlength="10"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="email">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="you@example.com"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="pincode">
                                    Pincode
                                </label>

                                <input
                                    type="text"
                                    id="pincode"
                                    name="pincode"
                                    placeholder="6 digit pincode"
                                    maxlength="6"
                                    required
                                >

                            </div>


                            <div class="form-group full">

                                <label for="address">
                                    Full Address
                                </label>

                                <textarea
                                    id="address"
                                    name="address"
                                    placeholder="House no., street, area..."
                                    required
                                ></textarea>

                            </div>


                            <div class="form-group">

                                <label for="city">
                                    City
                                </label>

                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    placeholder="Your city"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="state">
                                    State
                                </label>

                                <select
                                    id="state"
                                    name="state"
                                    required
                                >

                                    <option value="">
                                        Select State
                                    </option>

                                    <option>Haryana</option>
                                    <option>Delhi</option>
                                    <option>Punjab</option>
                                    <option>Rajasthan</option>
                                    <option>Uttar Pradesh</option>
                                    <option>Maharashtra</option>
                                    <option>Gujarat</option>
                                    <option>Madhya Pradesh</option>
                                    <option>Bihar</option>
                                    <option>West Bengal</option>
                                    <option>Karnataka</option>
                                    <option>Tamil Nadu</option>
                                    <option>Telangana</option>
                                    <option>Kerala</option>
                                    <option>Andhra Pradesh</option>
                                    <option>Odisha</option>
                                    <option>Other</option>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="checkout-section">

                    <h2>PAYMENT METHOD</h2>

                    <div class="payment-options">


                        <div class="payment-option">

                            <input
                                type="radio"
                                id="cod"
                                name="payment"
                                value="COD"
                                checked
                            >

                            <label for="cod">

                                <div class="payment-left">

                                    <span class="payment-radio"></span>

                                    <div>

                                        <div class="payment-name">
                                            Cash on Delivery
                                        </div>

                                        <div class="payment-description">
                                            Pay when your order arrives
                                        </div>

                                    </div>

                                </div>

                                <span class="payment-icon">
                                    💵
                                </span>

                            </label>

                        </div>


                        <div class="payment-option">

                            <input
                                type="radio"
                                id="online"
                                name="payment"
                                value="ONLINE"
                            >

                            <label for="online">

                                <div class="payment-left">

                                    <span class="payment-radio"></span>

                                    <div>

                                        <div class="payment-name">
                                            Online Payment
                                        </div>

                                        <div class="payment-description">
                                            UPI / Card / Net Banking
                                        </div>

                                    </div>

                                </div>

                                <span class="payment-icon">
                                    💳
                                </span>

                            </label>

                        </div>


                    </div>

                </div>

            </div>


            <aside class="checkout-summary">

                <h2>ORDER SUMMARY</h2>


                <div class="summary-products">

                    ${productsHTML}

                </div>


                <div class="summary-row">

                    <span>Subtotal</span>

                    <span>
                        ₹${subtotal.toFixed(2)}
                    </span>

                </div>


                <div class="summary-row">

                    <span>Shipping</span>

                    <span>

                        ${
                            shipping === 0
                            ? "FREE"
                            : "₹" + shipping.toFixed(2)
                        }

                    </span>

                </div>


                <div class="summary-total">

                    <span>Total</span>

                    <span>
                        ₹${total.toFixed(2)}
                    </span>

                </div>


                <button
                    type="submit"
                    class="place-order-btn"
                    id="placeOrderBtn"
                >
                    PLACE ORDER →
                </button>


                <p
                    class="checkout-message"
                    id="checkoutMessage"
                ></p>


                <p class="secure-message">

                    🔒 Your information is securely handled.
                    <br>
                    UrbanWeave secure checkout.

                </p>

            </aside>

        </form>

    `;


    document
        .getElementById("checkoutForm")
        .addEventListener(
            "submit",
            placeOrder
        );

}


/* =========================================
   PLACE ORDER — MYSQL BACKEND
========================================= */

async function placeOrder(event) {

    event.preventDefault();


    const form =
        document.getElementById("checkoutForm");

    const message =
        document.getElementById("checkoutMessage");

    const button =
        document.getElementById("placeOrderBtn");


    if (!form.checkValidity()) {

        form.reportValidity();

        return;

    }


    const name =
        document.getElementById("name")
        .value
        .trim();


    const phone =
        document.getElementById("phone")
        .value
        .trim();


    const email =
        document.getElementById("email")
        .value
        .trim();


    const address =
        document.getElementById("address")
        .value
        .trim();


    const city =
        document.getElementById("city")
        .value
        .trim();


    const state =
        document.getElementById("state")
        .value;


    const pincode =
        document.getElementById("pincode")
        .value
        .trim();


    /* =====================================
       VALIDATION
    ===================================== */

    if (!/^[0-9]{10}$/.test(phone)) {

        message.textContent =
            "Please enter a valid 10 digit mobile number.";

        return;

    }


    if (!/^[0-9]{6}$/.test(pincode)) {

        message.textContent =
            "Please enter a valid 6 digit pincode.";

        return;

    }


    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {

        message.textContent =
            "Please enter a valid email address.";

        return;

    }


    const payment =
        document.querySelector(
            'input[name="payment"]:checked'
        ).value;


    const items =
        getCheckoutItems();


    if (items.length === 0) {

        message.textContent =
            "Your cart is empty.";

        return;

    }


    /* =====================================
       DISABLE BUTTON
    ===================================== */

    button.disabled = true;

    button.textContent =
        "PROCESSING...";

    message.textContent = "";


    /* =====================================
       FORM DATA
    ===================================== */

    const formData =
        new FormData();


    formData.append(
        "name",
        name
    );


    formData.append(
        "phone",
        phone
    );


    formData.append(
        "email",
        email
    );


    formData.append(
        "address",
        address
    );


    formData.append(
        "city",
        city
    );


    formData.append(
        "state",
        state
    );


    formData.append(
        "pincode",
        pincode
    );


    /*
       IMPORTANT:

       API expects:
       COD
       ONLINE

       We are sending exactly those values.
    */

    formData.append(
        "payment_method",
        payment
    );


    /*
       Product price will NOT be trusted
       by PHP.

       create_order.php verifies the
       product price directly from MySQL.
    */

    formData.append(
        "items",
        JSON.stringify(items)
    );


    try {


        /* =================================
           SEND TO PHP API
        ================================= */

        const response =
            await fetch(
                "api/create_order.php",
                {
                    method: "POST",
                    body: formData
                }
            );


        const result =
            await response.json();


        /* =================================
           API ERROR
        ================================= */

        if (!result.success) {

            message.textContent =
                result.message ||
                "Unable to place order.";

            button.disabled = false;

            button.textContent =
                "PLACE ORDER →";

            return;

        }


        /* =================================
           SAVE LAST ORDER INFO
        ================================= */

        localStorage.setItem(
            "urbanweave_last_order",
            JSON.stringify({

                orderNumber:
                    result.order_number,

                orderId:
                    result.order_id,

                total:
                    result.total,

                customerName:
                    name,

                createdAt:
                    new Date().toISOString()

            })
        );


        /* =================================
           CLEAR CART
        ================================= */

        localStorage.removeItem(
            "urbanweave_cart"
        );


        localStorage.removeItem(
            "urbanweave_buy_now"
        );


        /* =================================
           SUCCESS
        ================================= */

        if (payment === "ONLINE") {

            alert(
                "Order created successfully!\n\n" +
                "Order No: " +
                result.order_number +
                "\n\n" +
                "Online payment gateway will be connected next."
            );

        } else {

            alert(
                "Order placed successfully! 🎉\n\n" +
                "Order No: " +
                result.order_number
            );

        }


        /* =================================
           GO TO ORDERS
        ================================= */

        window.location.href =
            "orders.php";


    } catch (error) {

        console.error(error);


        message.textContent =
            "Something went wrong. Please try again.";


        button.disabled = false;


        button.textContent =
            "PLACE ORDER →";

    }

}


/* =========================================
   NAVBAR CART COUNT
========================================= */

function updateCartCount() {

    const cartCount =
        document.getElementById("cartCount");

    if (!cartCount) return;


    const cart =
        JSON.parse(
            localStorage.getItem("urbanweave_cart")
        ) || [];


    const count =
        cart.reduce(
            function(total, item) {

                return total +
                    Number(item.quantity || 0);

            },
            0
        );


    cartCount.textContent =
        count;

}


/* =========================================
   NAVBAR WISHLIST COUNT
========================================= */

function updateWishlistCount() {

    const wishlistCount =
        document.getElementById("wishlistCount");

    if (!wishlistCount) return;


    const wishlist =
        JSON.parse(
            localStorage.getItem("urbanweave_wishlist")
        ) || [];


    wishlistCount.textContent =
        wishlist.length;

}


/* =========================================
   INITIAL LOAD
========================================= */

renderCheckout();

updateCartCount();

updateWishlistCount();

</script>


<script src="assets/js/script.js"></script>

</body>

</html>