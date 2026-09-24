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

    <title>Shopping Cart | UrbanWeave</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        .cart-page {
            max-width: 1400px;
            margin: auto;
            padding: 70px 35px 120px;
        }

        .cart-heading {
            margin-bottom: 50px;
        }

        .cart-heading p {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #777;
            margin-bottom: 12px;
        }

        .cart-heading h1 {
            font-size: clamp(45px, 6vw, 80px);
            line-height: .9;
            letter-spacing: -4px;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 60px;
            align-items: start;
        }

        .cart-items {
            border-top: 1px solid #d8d5cf;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 150px 1fr auto;
            gap: 25px;
            padding: 25px 0;
            border-bottom: 1px solid #d8d5cf;
        }

        .cart-item-image {
            width: 150px;
            height: 180px;
            overflow: hidden;
            background: linear-gradient(145deg, #d8d4cc, #4b4843);
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cart-item-info {
            padding-top: 5px;
        }

        .cart-item-category {
            font-size: 10px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
        }

        .cart-item-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .cart-item-detail {
            font-size: 11px;
            color: #666;
            margin-bottom: 6px;
        }

        .cart-item-price {
            font-size: 16px;
            font-weight: 700;
            margin-top: 15px;
        }

        .cart-item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
        }

        .remove-cart-btn {
            border: 0;
            background: transparent;
            color: #777;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            cursor: pointer;
            text-transform: uppercase;
        }

        .remove-cart-btn:hover {
            color: #111;
        }

        .cart-quantity {
            display: flex;
            width: 120px;
            height: 42px;
            border: 1px solid #bbb;
        }

        .cart-quantity button {
            width: 35px;
            border: 0;
            background: transparent;
            font-size: 17px;
            cursor: pointer;
        }

        .cart-quantity input {
            width: 50px;
            border: 0;
            background: transparent;
            text-align: center;
            outline: none;
            font-size: 12px;
        }

        .move-wishlist-btn {
            border: 0;
            background: transparent;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            cursor: pointer;
            text-transform: uppercase;
        }

        .move-wishlist-btn:hover {
            text-decoration: underline;
        }

        .cart-summary {
            position: sticky;
            top: 30px;
            padding: 30px;
            background: #f5f3ef;
        }

        .cart-summary h2 {
            font-size: 25px;
            margin-bottom: 30px;
            letter-spacing: -1px;
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

        .checkout-btn {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 100%;
            min-height: 58px;
            margin-top: 25px;

            background: #111;
            color: white;
            border: 1px solid #111;

            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-decoration: none;

            transition: .3s;
        }

        .checkout-btn:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 18px;

            color: #111;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-decoration: underline;
        }

        .empty-cart {
            text-align: center;
            padding: 100px 20px;
            border-top: 1px solid #d8d5cf;
        }

        .empty-cart h2 {
            font-size: 45px;
            letter-spacing: -2px;
            margin-bottom: 15px;
        }

        .empty-cart p {
            color: #777;
            margin-bottom: 30px;
        }

        .shop-now-btn {
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

            .cart-layout {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .cart-summary {
                position: static;
            }

        }

        @media (max-width: 650px) {

            .cart-page {
                padding: 45px 20px 80px;
            }

            .cart-item {
                grid-template-columns: 90px 1fr;
                gap: 15px;
            }

            .cart-item-image {
                width: 90px;
                height: 120px;
            }

            .cart-item-actions {
                grid-column: 2;
                align-items: flex-start;
                gap: 15px;
            }

            .cart-item-name {
                font-size: 17px;
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


<main class="cart-page">

    <div class="cart-heading">

        <p>Your Selection</p>

        <h1>SHOPPING CART</h1>

    </div>


    <div id="cartContainer"></div>

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

function getCart() {

    return JSON.parse(
        localStorage.getItem("urbanweave_cart")
    ) || [];

}


function getWishlist() {

    return JSON.parse(
        localStorage.getItem("urbanweave_wishlist")
    ) || [];

}


/* =========================================
   RENDER CART
========================================= */

function renderCart() {

    const container =
        document.getElementById("cartContainer");

    const cart = getCart();

    if (cart.length === 0) {

        container.innerHTML = `

            <div class="empty-cart">

                <h2>Your cart is empty.</h2>

                <p>
                    Looks like you haven't added anything yet.
                </p>

                <a href="products.php" class="shop-now-btn">
                    CONTINUE SHOPPING →
                </a>

            </div>

        `;

        updateCartCount();

        return;

    }


    let subtotal = 0;


    cart.forEach(function(item) {

        subtotal +=
            Number(item.price) *
            Number(item.quantity);

    });


    const shipping =
        subtotal >= 1999 ? 0 : 99;

    const total =
        subtotal + shipping;


    let itemsHTML = "";


    cart.forEach(function(item, index) {

        const itemTotal =
            Number(item.price) *
            Number(item.quantity);


        const imagePath =
            item.image
                ? "assets/images/" + item.image
                : "";


        itemsHTML += `

            <div class="cart-item">

                <div class="cart-item-image">

                    ${
                        imagePath
                        ? `<img
                            src="${imagePath}"
                            alt="${escapeHTML(item.name)}"
                            onerror="this.style.display='none';"
                        >`
                        : ""
                    }

                </div>


                <div class="cart-item-info">

                    <div class="cart-item-category">
                        UrbanWeave
                    </div>

                    <div class="cart-item-name">
                        ${escapeHTML(item.name)}
                    </div>

                    ${
                        item.size
                        ? `
                            <div class="cart-item-detail">
                                Size: ${escapeHTML(item.size)}
                            </div>
                        `
                        : ""
                    }

                    ${
                        item.color
                        ? `
                            <div class="cart-item-detail">
                                Color: ${escapeHTML(item.color)}
                            </div>
                        `
                        : ""
                    }

                    <div class="cart-item-price">
                        ₹${itemTotal.toFixed(2)}
                    </div>

                </div>


                <div class="cart-item-actions">

                    <button
                        class="remove-cart-btn"
                        onclick="removeCartItem(${index})"
                    >
                        REMOVE
                    </button>


                    <div class="cart-quantity">

                        <button
                            type="button"
                            onclick="changeCartQuantity(${index}, -1)"
                        >
                            −
                        </button>

                        <input
                            type="text"
                            value="${Number(item.quantity)}"
                            readonly
                        >

                        <button
                            type="button"
                            onclick="changeCartQuantity(${index}, 1)"
                        >
                            +
                        </button>

                    </div>


                    <button
                        class="move-wishlist-btn"
                        onclick="moveToWishlist(${index})"
                    >
                        ♡ MOVE TO WISHLIST
                    </button>

                </div>

            </div>

        `;

    });


    container.innerHTML = `

        <div class="cart-layout">

            <div class="cart-items">

                ${itemsHTML}

            </div>


            <aside class="cart-summary">

                <h2>ORDER SUMMARY</h2>

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


                <a
                    href="checkout.php"
                    class="checkout-btn"
                    onclick="prepareCheckout(event)"
                >
                    PROCEED TO CHECKOUT →
                </a>


                <a
                    href="products.php"
                    class="continue-shopping"
                >
                    CONTINUE SHOPPING
                </a>

            </aside>

        </div>

    `;


    updateCartCount();

    updateWishlistCount();

}


/* =========================================
   CHANGE QUANTITY
========================================= */

function changeCartQuantity(index, amount) {

    let cart = getCart();

    if (!cart[index]) return;


    let quantity =
        Number(cart[index].quantity) || 1;


    quantity += amount;


    if (quantity < 1) {

        quantity = 1;

    }


    cart[index].quantity = quantity;


    localStorage.setItem(
        "urbanweave_cart",
        JSON.stringify(cart)
    );


    renderCart();

}


/* =========================================
   REMOVE ITEM
========================================= */

function removeCartItem(index) {

    let cart = getCart();

    if (!cart[index]) return;


    cart.splice(index, 1);


    localStorage.setItem(
        "urbanweave_cart",
        JSON.stringify(cart)
    );


    renderCart();

}


/* =========================================
   MOVE TO WISHLIST
========================================= */

function moveToWishlist(index) {

    let cart = getCart();

    let wishlist = getWishlist();


    if (!cart[index]) return;


    const item = cart[index];


    const exists =
        wishlist.some(function(wishlistItem) {

            return Number(wishlistItem.productId) ===
                Number(item.productId);

        });


    if (!exists) {

        wishlist.push({

            productId: item.productId,

            name: item.name,

            price: item.price,

            color: item.color,

            image: item.image || ""

        });

    }


    cart.splice(index, 1);


    localStorage.setItem(
        "urbanweave_cart",
        JSON.stringify(cart)
    );


    localStorage.setItem(
        "urbanweave_wishlist",
        JSON.stringify(wishlist)
    );


    renderCart();

}


/* =========================================
   CHECKOUT
========================================= */

function prepareCheckout(event) {

    const cart = getCart();


    if (cart.length === 0) {

        event.preventDefault();

        alert("Your cart is empty.");

        return;

    }


    localStorage.removeItem("urbanweave_buy_now");

}


/* =========================================
   CART COUNT
========================================= */

function updateCartCount() {

    const cartCount =
        document.getElementById("cartCount");


    if (!cartCount) return;


    const cart = getCart();


    let total = 0;


    cart.forEach(function(item) {

        total +=
            Number(item.quantity) || 1;

    });


    cartCount.textContent = total;

}


/* =========================================
   WISHLIST COUNT
========================================= */

function updateWishlistCount() {

    const wishlistCount =
        document.getElementById("wishlistCount");


    if (!wishlistCount) return;


    const wishlist = getWishlist();


    wishlistCount.textContent =
        wishlist.length;

}


/* =========================================
   SECURITY HELPER
========================================= */

function escapeHTML(value) {

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

}


/* =========================================
   INITIAL LOAD
========================================= */

renderCart();

</script>


<script src="assets/js/script.js"></script>

</body>

</html>