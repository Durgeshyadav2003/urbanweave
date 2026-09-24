<?php
session_start();

require_once "includes/db.php";
require_once "includes/config.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Wishlist | UrbanWeave</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        .wishlist-page {
            max-width: 1400px;
            margin: auto;
            padding: 70px 35px 120px;
        }

        .wishlist-header {
            margin-bottom: 50px;
        }

        .wishlist-header h1 {
            font-size: clamp(55px, 8vw, 110px);
            line-height: 0.9;
            letter-spacing: -6px;
            margin: 20px 0;
        }

        .wishlist-header h1 span {
            font-weight: 300;
        }

        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px 18px;
        }

        .wishlist-card {
            position: relative;
        }

        .wishlist-image {
            height: 470px;
            background:
                linear-gradient(
                    145deg,
                    #d8d4cc,
                    #4a4742
                );
            position: relative;
            overflow: hidden;
        }

        .wishlist-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .wishlist-placeholder {
            position: absolute;
            inset: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            color: white;
            font-size: 30px;
            font-weight: 900;
            text-align: center;
        }

        .remove-wishlist {
            position: absolute;
            top: 15px;
            right: 15px;

            width: 40px;
            height: 40px;

            border: 0;
            border-radius: 50%;

            background: white;
            font-size: 20px;

            z-index: 2;
        }

        .wishlist-info {
            display: flex;
            justify-content: space-between;
            gap: 20px;

            padding-top: 17px;
        }

        .wishlist-category {
            color: #77736d;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .wishlist-info h3 {
            font-size: 16px;
            margin-top: 5px;
        }

        .wishlist-price {
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .wishlist-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .wishlist-actions a,
        .wishlist-actions button {
            min-height: 44px;
            padding: 0 18px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;

            cursor: pointer;
        }

        .wishlist-actions a {
            background: #111;
            color: white;
        }

        .wishlist-actions button {
            background: white;
            color: #111;
            border: 1px solid #111;
        }

        .empty-wishlist {
            text-align: center;
            padding: 100px 20px;
        }

        .empty-wishlist h2 {
            font-size: 45px;
            margin-bottom: 15px;
        }

        .empty-wishlist p {
            color: #666;
            margin-bottom: 30px;
        }

        .empty-wishlist a {
            display: inline-flex;
            min-height: 50px;
            padding: 0 25px;
            align-items: center;
            background: #111;
            color: white;

            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        @media (max-width: 900px) {

            .wishlist-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .wishlist-page {
                padding: 55px 20px 80px;
            }

            .wishlist-grid {
                grid-template-columns: 1fr;
            }

            .wishlist-image {
                height: 430px;
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


<main class="wishlist-page">


    <section class="wishlist-header">

        <p class="section-label">
            YOUR SAVED COLLECTION
        </p>

        <h1>
            My <span>Wishlist.</span>
        </h1>

        <p>
            Keep the pieces you love close.
        </p>

    </section>


    <section
        class="wishlist-grid"
        id="wishlistGrid"
    >

    </section>


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

            <a href="products.php">
                All Products
            </a>

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

            <a href="#">
                Contact Us
            </a>

            <a href="#">
                Shipping
            </a>

            <a href="#">
                Returns
            </a>

            <a href="#">
                FAQ
            </a>

        </div>


        <div class="footer-column">

            <h4>FOLLOW</h4>

            <a href="#">
                Instagram
            </a>

            <a href="#">
                Facebook
            </a>

            <a href="#">
                Pinterest
            </a>

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

function getWishlist() {

    return JSON.parse(
        localStorage.getItem(
            "urbanweave_wishlist"
        )
    ) || [];

}


function renderWishlist() {

    const grid =
        document.getElementById(
            "wishlistGrid"
        );

    const wishlist =
        getWishlist();


    if (wishlist.length === 0) {

        grid.innerHTML = `

            <div class="empty-wishlist">

                <h2>
                    Your wishlist is empty.
                </h2>

                <p>
                    Save products you love and
                    find them here later.
                </p>

                <a href="products.php">
                    EXPLORE COLLECTION →
                </a>

            </div>

        `;

        return;

    }


    grid.innerHTML =
        wishlist.map(item => `

            <article
                class="wishlist-card"
            >

                <div
                    class="wishlist-image"
                >

                    <button
                        class="remove-wishlist"
                        onclick="removeWishlist(${item.productId})"
                    >
                        ×
                    </button>

                    ${
                        item.image
                        ? `
                            <img
                                src="assets/images/${escapeHtml(item.image)}"
                                onerror="this.style.display='none'"
                                alt="${escapeHtml(item.name)}"
                            >
                        `
                        : ""
                    }

                    <div
                        class="wishlist-placeholder"
                    >
                        URBANWEAVE
                    </div>

                </div>


                <div
                    class="wishlist-info"
                >

                    <div>

                        <p
                            class="wishlist-category"
                        >
                            ${escapeHtml(item.color)}
                        </p>

                        <h3>
                            ${escapeHtml(item.name)}
                        </h3>

                    </div>

                    <p
                        class="wishlist-price"
                    >
                        ₹${Number(item.price).toLocaleString(
                            "en-IN",
                            {
                                minimumFractionDigits: 2
                            }
                        )}
                    </p>

                </div>


                <div
                    class="wishlist-actions"
                >

                    <a
                        href="product.php?id=${item.productId}"
                    >
                        VIEW PRODUCT
                    </a>

                    <button
                        onclick="moveToCart(${item.productId})"
                    >
                        ADD TO CART
                    </button>

                </div>

            </article>

        `).join("");

}


function removeWishlist(productId) {

    let wishlist =
        getWishlist();

    wishlist =
        wishlist.filter(
            item =>
                item.productId !== productId
        );

    localStorage.setItem(
        "urbanweave_wishlist",
        JSON.stringify(wishlist)
    );

    renderWishlist();

}


function moveToCart(productId) {

    const wishlist =
        getWishlist();

    const item =
        wishlist.find(
            item =>
                item.productId === productId
        );


    if (!item) {
        return;
    }


    let cart =
        JSON.parse(
            localStorage.getItem(
                "urbanweave_cart"
            )
        ) || [];


    const existing =
        cart.find(
            cartItem =>
                cartItem.productId === productId
        );


    if (existing) {

        existing.quantity += 1;

    } else {

        cart.push({

            productId: item.productId,
            name: item.name,
            price: item.price,
            size: "",
            color: item.color,
            quantity: 1

        });

    }


    localStorage.setItem(
        "urbanweave_cart",
        JSON.stringify(cart)
    );


    alert(
        "✓ Product added to cart."
    );

}


function escapeHtml(text) {

    const div =
        document.createElement("div");

    div.textContent =
        text || "";

    return div.innerHTML;

}


function updateCartCount() {

    const cart =
        JSON.parse(
            localStorage.getItem(
                "urbanweave_cart"
            )
        ) || [];


    const count =
        cart.reduce(
            (total, item) =>
                total + item.quantity,
            0
        );


    const cartCount =
        document.getElementById(
            "cartCount"
        );


    if (cartCount) {
        cartCount.textContent = count;
    }

}


renderWishlist();
updateCartCount();


</script>


<script src="assets/js/script.js"></script>

</body>

</html>