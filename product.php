<?php
session_start();

require_once "includes/db.php";
require_once "includes/config.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$productId = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: products.php");
    exit;
}

$product = $result->fetch_assoc();

$sizes = array_filter(array_map('trim', explode(',', $product['size'])));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($product['name']); ?> | UrbanWeave
    </title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .product-page {
            max-width: 1400px;
            margin: auto;
            padding: 70px 35px 120px;
        }

        .product-breadcrumb {
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 40px;
        }

        .product-breadcrumb a {
            color: #777;
        }

        .product-detail {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 70px;
            align-items: start;
        }

        .product-main-image {
            height: 700px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, #d8d4cc, #4b4843);
        }

        .product-main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-main-placeholder {
            position: absolute;
            inset: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            color: white;
            font-size: clamp(35px, 5vw, 70px);
            font-weight: 900;
            letter-spacing: -3px;
            text-align: center;
            padding: 30px;
        }

        .product-featured-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 2;

            background: white;
            color: #111;
            padding: 9px 13px;

            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .product-details {
            padding-top: 20px;
        }

        .product-details-category {
            font-size: 11px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .product-details h1 {
            font-size: clamp(45px, 5vw, 75px);
            line-height: 0.92;
            letter-spacing: -4px;
            margin-bottom: 25px;
        }

        .product-details-price {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .product-description {
            max-width: 520px;
            color: #666;
            line-height: 1.7;
            margin-bottom: 35px;
        }

        .product-option {
            margin-bottom: 28px;
        }

        .product-option-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;

            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .size-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .size-option input {
            display: none;
        }

        .size-option label {
            min-width: 55px;
            height: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #c8c5be;

            font-size: 12px;
            font-weight: 700;

            cursor: pointer;
            transition: 0.25s;
        }

        .size-option input:checked + label {
            background: #111;
            color: white;
            border-color: #111;
        }

        .color-value {
            font-size: 12px;
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
        }

        .quantity-box {
            display: flex;
            width: 140px;
            height: 48px;
            border: 1px solid #bbb;
        }

        .quantity-box button {
            width: 42px;
            border: 0;
            background: transparent;
            font-size: 18px;
        }

        .quantity-box input {
            width: 56px;
            border: 0;
            background: transparent;
            text-align: center;
            outline: none;
        }

        .add-cart-btn {
            width: 100%;
            min-height: 58px;

            border: 0;
            background: #111;
            color: white;

            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;

            margin-top: 10px;
            transition: 0.3s;
        }
.wishlist-btn,
.buy-now-btn {
    width: 100%;
    min-height: 54px;
    margin-top: 10px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.5px;
    cursor: pointer;
    transition: 0.3s;
}

.wishlist-btn {
    background: transparent;
    color: #111;
    border: 1px solid #111;
}

.wishlist-btn:hover {
    background: #111;
    color: white;
}

.buy-now-btn {
    background: #111;
    color: white;
    border: 1px solid #111;
}

.buy-now-btn:hover {
    background: #333;
    transform: translateY(-2px);
}
        .add-cart-btn:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .product-meta {
            margin-top: 35px;
            border-top: 1px solid #d8d5cf;
        }

        .product-meta-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;

            padding: 15px 0;
            border-bottom: 1px solid #d8d5cf;

            font-size: 11px;
        }

        .product-meta-row span:first-child {
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stock-available {
            color: #287a3d;
            font-weight: 700;
        }

        .stock-low {
            color: #a35b00;
            font-weight: 700;
        }

        .stock-out {
            color: #b3261e;
            font-weight: 700;
        }

        .product-message {
            margin-top: 15px;
            font-size: 13px;
            min-height: 20px;
        }

        .product-main-placeholder {
    display: none;
}

        @media (max-width: 900px) {
            .product-detail {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .product-main-image {
                height: 600px;
            }

            .product-details {
                padding-top: 0;
            }
        }

        @media (max-width: 600px) {
            .product-page {
                padding: 45px 20px 80px;
            }

            .product-main-image {
                height: 480px;
            }

            .product-details h1 {
                letter-spacing: -3px;
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


<main class="product-page">

    <div class="product-breadcrumb">
        <a href="products.php">Shop</a>
        &nbsp; / &nbsp;
        <?php echo htmlspecialchars($product['category']); ?>
        &nbsp; / &nbsp;
        <?php echo htmlspecialchars($product['name']); ?>
    </div>


    <section class="product-detail">

        <!-- PRODUCT IMAGE -->

        <div class="product-main-image">

            <?php if ($product['featured'] == 1): ?>
                <div class="product-featured-badge">
                    FEATURED
                </div>
            <?php endif; ?>


            <?php if (!empty($product['image'])): ?>

                <img
                    src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    onerror="this.style.display='none';"
                >

            <?php endif; ?>


            <div class="product-main-placeholder">
                <?php echo strtoupper(htmlspecialchars($product['category'])); ?>
            </div>

        </div>


        <!-- PRODUCT INFORMATION -->

        <div class="product-details">

            <p class="product-details-category">
                <?php echo htmlspecialchars($product['category']); ?>
            </p>


            <h1>
                <?php echo htmlspecialchars($product['name']); ?>
            </h1>


            <p class="product-details-price">
                ₹<?php echo number_format($product['price'], 2); ?>
            </p>


            <p class="product-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>


            <!-- SIZE -->

            <?php if (!empty($sizes)): ?>

                <div class="product-option">

                    <div class="product-option-label">
                        <span>Select Size</span>
                    </div>

                    <div class="size-options">

                        <?php foreach ($sizes as $index => $size): ?>

                            <div class="size-option">

                                <input
                                    type="radio"
                                    name="product_size"
                                    id="size_<?php echo $index; ?>"
                                    value="<?php echo htmlspecialchars($size); ?>"
                                    <?php echo $index === 0 ? 'checked' : ''; ?>
                                >

                                <label for="size_<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($size); ?>
                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            <?php endif; ?>


            <!-- COLOR -->

            <div class="product-option">

                <div class="product-option-label">

                    <span>Color</span>

                    <span class="color-value">
                        <?php echo htmlspecialchars($product['color']); ?>
                    </span>

                </div>

            </div>


            <!-- QUANTITY -->

            <div class="product-option">

                <div class="product-option-label">
                    <span>Quantity</span>
                </div>

                <div class="quantity-box">

                    <button type="button" onclick="changeQuantity(-1)">
                        −
                    </button>

                    <input
                        type="number"
                        id="quantity"
                        value="1"
                        min="1"
                        max="<?php echo (int)$product['stock']; ?>"
                        readonly
                    >

                    <button type="button" onclick="changeQuantity(1)">
                        +
                    </button>

                </div>

            </div>


            <!-- ADD TO CART -->

            <?php if ((int)$product['stock'] > 0): ?>

                <button
                    class="add-cart-btn"
                    id="addToCartBtn"
                    onclick="addToCart()"
                >
                    ADD TO CART →
                    
                </button>
                <button
    class="wishlist-btn"
    id="wishlistBtn"
    onclick="toggleWishlist()"
>
    ♡ ADD TO WISHLIST
</button>

<button
    class="buy-now-btn"
    onclick="buyNow()"
>
    ⚡ BUY NOW
</button>

            <?php else: ?>

                <button class="add-cart-btn" disabled>
                    OUT OF STOCK
                </button>

            <?php endif; ?>


            <p
                class="product-message"
                id="productMessage"
            ></p>


            <!-- PRODUCT META -->

            <div class="product-meta">

                <div class="product-meta-row">
                    <span>Category</span>
                    <span>
                        <?php echo htmlspecialchars($product['category']); ?>
                    </span>
                </div>

                <div class="product-meta-row">
                    <span>Gender</span>
                    <span>
                        <?php echo htmlspecialchars($product['gender']); ?>
                    </span>
                </div>

                <div class="product-meta-row">
                    <span>Color</span>
                    <span>
                        <?php echo htmlspecialchars($product['color']); ?>
                    </span>
                </div>

                <div class="product-meta-row">
                    <span>Stock</span>

                    <?php if ($product['stock'] <= 0): ?>

                        <span class="stock-out">
                            Out of Stock
                        </span>

                    <?php elseif ($product['stock'] <= 5): ?>

                        <span class="stock-low">
                            Only <?php echo (int)$product['stock']; ?> left
                        </span>

                    <?php else: ?>

                        <span class="stock-available">
                            In Stock
                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </div>

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

            <a href="products.php">All Products</a>
            <a href="products.php?category=T-Shirts">T-Shirts</a>
            <a href="products.php?category=Jackets">Jackets</a>
            <a href="products.php?category=Hoodies">Hoodies</a>

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
   WISHLIST
========================================= */

function getWishlist() {

    return JSON.parse(
        localStorage.getItem("urbanweave_wishlist")
    ) || [];

}


/* =========================================
   CHANGE QUANTITY
========================================= */

function changeQuantity(amount) {

    const quantityInput =
        document.getElementById("quantity");

    let quantity =
        parseInt(quantityInput.value) || 1;

    const max =
        parseInt(quantityInput.max) || 10;

    quantity += amount;

    if (quantity < 1) {
        quantity = 1;
    }

    if (quantity > max) {
        quantity = max;
    }

    quantityInput.value = quantity;

}


/* =========================================
   ADD TO CART
========================================= */

function addToCart() {

    const productId =
        <?php echo (int)$product['id']; ?>;

    const productName =
        <?php echo json_encode($product['name']); ?>;

    const price =
        <?php echo (float)$product['price']; ?>;

    const color =
        <?php echo json_encode($product['color']); ?>;

    const quantity =
        parseInt(
            document.getElementById("quantity").value
        ) || 1;

    const selectedSize =
        document.querySelector(
            'input[name="product_size"]:checked'
        );

    const size =
        selectedSize ? selectedSize.value : "";

    let cart =
        JSON.parse(
            localStorage.getItem("urbanweave_cart")
        ) || [];

    const existingIndex =
        cart.findIndex(function(item) {

            return (
                Number(item.productId) === productId &&
                item.size === size
            );

        });


    if (existingIndex !== -1) {

        cart[existingIndex].quantity += quantity;

    } else {

        cart.push({

            productId: productId,
            name: productName,
            price: price,
            color: color,
            size: size,
            quantity: quantity

        });

    }


    localStorage.setItem(
        "urbanweave_cart",
        JSON.stringify(cart)
    );


    updateCartCount();

    alert("Product added to cart!");

}


/* =========================================
   TOGGLE WISHLIST
========================================= */

function toggleWishlist() {

    const productId =
        <?php echo (int)$product['id']; ?>;

    const productName =
        <?php echo json_encode($product['name']); ?>;

    const price =
        <?php echo (float)$product['price']; ?>;

    const color =
        <?php echo json_encode($product['color']); ?>;

    const image =
        <?php echo json_encode($product['image']); ?>;


    let wishlist = getWishlist();


    const existingIndex =
        wishlist.findIndex(function(item) {

            return Number(item.productId) === productId;

        });


    const button =
        document.getElementById("wishlistBtn");


    if (existingIndex !== -1) {

        wishlist.splice(existingIndex, 1);

        button.innerHTML =
            "♡ ADD TO WISHLIST";

    } else {

        wishlist.push({

            productId: productId,
            name: productName,
            price: price,
            color: color,
            image: image

        });

        button.innerHTML =
            "♥ ADDED TO WISHLIST";

    }


    localStorage.setItem(
        "urbanweave_wishlist",
        JSON.stringify(wishlist)
    );


    updateWishlistCount();

}


/* =========================================
   UPDATE WISHLIST BUTTON
========================================= */

function updateWishlistButton() {

    const productId =
        <?php echo (int)$product['id']; ?>;

    const wishlist =
        getWishlist();


    const exists =
        wishlist.some(function(item) {

            return Number(item.productId) === productId;

        });


    const button =
        document.getElementById("wishlistBtn");


    if (!button) return;


    if (exists) {

        button.innerHTML =
            "♥ ADDED TO WISHLIST";

    } else {

        button.innerHTML =
            "♡ ADD TO WISHLIST";

    }

}


/* =========================================
   BUY NOW
========================================= */

function buyNow() {

    const productId =
        <?php echo (int)$product['id']; ?>;

    const productName =
        <?php echo json_encode($product['name']); ?>;

    const price =
        <?php echo (float)$product['price']; ?>;

    const color =
        <?php echo json_encode($product['color']); ?>;

    const quantity =
        parseInt(
            document.getElementById("quantity").value
        ) || 1;


    const selectedSize =
        document.querySelector(
            'input[name="product_size"]:checked'
        );


    const size =
        selectedSize ? selectedSize.value : "";


    const buyNowProduct = {

        productId: productId,
        name: productName,
        price: price,
        color: color,
        size: size,
        quantity: quantity

    };


    localStorage.setItem(
        "urbanweave_buy_now",
        JSON.stringify(buyNowProduct)
    );


    window.location.href =
        "checkout.php";

}


/* =========================================
   CART COUNT
========================================= */

function updateCartCount() {

    const cartCount =
        document.getElementById("cartCount");


    if (!cartCount) return;


    const cart =
        JSON.parse(
            localStorage.getItem("urbanweave_cart")
        ) || [];


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


    const wishlist =
        JSON.parse(
            localStorage.getItem("urbanweave_wishlist")
        ) || [];


    wishlistCount.textContent =
        wishlist.length;

}


/* =========================================
   INITIAL UPDATE
========================================= */

updateCartCount();

updateWishlistCount();

updateWishlistButton();

</script>

<script src="assets/js/script.js"></script>

</body>
</html>