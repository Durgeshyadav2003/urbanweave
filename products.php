<?php
session_start();

require_once "includes/db.php";
require_once "includes/config.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR description LIKE ? OR category LIKE ?)";
    $searchTerm = "%" . $search . "%";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;

    $types .= "sss";
}

if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$categories = [
    "T-Shirts",
    "Jackets",
    "Hoodies",
    "Bottomwear",
    "Tops"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shop | UrbanWeave</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .shop-page {
            max-width: 1400px;
            margin: auto;
            padding: 70px 35px 110px;
        }

        .shop-header {
            margin-bottom: 45px;
        }

        .shop-header h1 {
            font-size: clamp(55px, 8vw, 110px);
            line-height: 0.9;
            letter-spacing: -6px;
            margin: 20px 0;
        }

        .shop-header h1 span {
            font-weight: 300;
        }

        .shop-header p {
            color: #666;
            max-width: 550px;
        }

        .shop-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 45px;
            padding-bottom: 20px;
            border-bottom: 1px solid #d8d5cf;
        }

        .category-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .category-filters a {
            border: 1px solid #bbb;
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .category-filters a:hover,
        .category-filters a.active {
            background: #111;
            color: white;
            border-color: #111;
        }

        .shop-search {
            display: flex;
            border-bottom: 1px solid #111;
        }

        .shop-search input {
            width: 220px;
            border: 0;
            background: transparent;
            padding: 10px 0;
            outline: none;
        }

        .shop-search button {
            border: 0;
            background: transparent;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .shop-product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 45px 18px;
        }

        .shop-product-card {
            position: relative;
        }

        .shop-product-image {
            height: 500px;
            display: block;
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, #d8d4cc, #4c4944);
        }

        .shop-product-card:nth-child(2n) .shop-product-image {
            background: linear-gradient(145deg, #c7c3bb, #66615a);
        }

        .shop-product-card:nth-child(3n) .shop-product-image {
            background: linear-gradient(145deg, #99948b, #22211f);
        }

        .shop-product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .shop-placeholder {
            position: absolute;
            inset: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            color: rgba(255,255,255,0.9);
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -1px;
            text-align: center;
            padding: 20px;
        }

        .shop-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 2;

            background: white;
            padding: 7px 10px;

            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .shop-product-info {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding-top: 17px;
        }

        .shop-product-category {
            color: #77736d;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .shop-product-info h3 {
            font-size: 16px;
            margin-top: 5px;
        }

        .shop-product-price {
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .shop-view-product {
            display: inline-block;
            margin-top: 13px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            border-bottom: 1px solid #111;
            padding-bottom: 3px;
        }

        .empty-shop {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
        }

        .empty-shop h2 {
            font-size: 40px;
            margin-bottom: 10px;
        }

        @media (max-width: 900px) {
            .shop-product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .shop-controls {
                flex-direction: column;
                align-items: flex-start;
            }

            .shop-search {
                width: 100%;
            }

            .shop-search input {
                width: 100%;
            }
        }

        @media (max-width: 600px) {
            .shop-page {
                padding: 55px 20px 80px;
            }

            .shop-header h1 {
                letter-spacing: -4px;
            }

            .shop-product-grid {
                grid-template-columns: 1fr;
            }

            .shop-product-image {
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
            </a>

            <button class="menu-btn" id="menuBtn">
                ☰
            </button>

        </div>

    </div>
</header>


<main class="shop-page">

    <section class="shop-header">

        <p class="section-label">
            URBANWEAVE COLLECTION
        </p>

        <h1>
            Shop <span>Everything.</span>
        </h1>

        <p>
            Explore our latest collection of modern streetwear,
            everyday essentials and statement pieces.
        </p>

    </section>


    <section class="shop-controls">

        <div class="category-filters">

            <a
                href="products.php"
                class="<?php echo $category === '' ? 'active' : ''; ?>"
            >
                All
            </a>

            <?php foreach ($categories as $cat): ?>

                <a
                    href="products.php?category=<?php echo urlencode($cat); ?>"
                    class="<?php echo $category === $cat ? 'active' : ''; ?>"
                >
                    <?php echo htmlspecialchars($cat); ?>
                </a>

            <?php endforeach; ?>

        </div>


        <form
            action="products.php"
            method="GET"
            class="shop-search"
        >

            <?php if ($category !== ''): ?>

                <input
                    type="hidden"
                    name="category"
                    value="<?php echo htmlspecialchars($category); ?>"
                >

            <?php endif; ?>

            <input
                type="text"
                name="search"
                placeholder="Search products..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">
                SEARCH →
            </button>

        </form>

    </section>


    <section class="shop-product-grid">

        <?php if ($result->num_rows > 0): ?>

            <?php while ($product = $result->fetch_assoc()): ?>

                <article class="shop-product-card">

                    <a
                        href="product.php?id=<?php echo $product['id']; ?>"
                        class="shop-product-image"
                    >

                        <?php if (!empty($product['image'])): ?>

                            <img
                                src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                onerror="this.style.display='none';"
                            >

                        <?php endif; ?>

                        <div class="shop-placeholder">
                            <?php echo strtoupper(htmlspecialchars($product['category'])); ?>
                        </div>

                        <?php if ($product['featured'] == 1): ?>

                            <div class="shop-badge">
                                FEATURED
                            </div>

                        <?php endif; ?>

                    </a>


                    <div class="shop-product-info">

                        <div>

                            <p class="shop-product-category">
                                <?php echo htmlspecialchars($product['category']); ?>
                            </p>

                            <h3>
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h3>

                        </div>

                        <p class="shop-product-price">
                            ₹<?php echo number_format($product['price'], 2); ?>
                        </p>

                    </div>


                    <a
                        href="product.php?id=<?php echo $product['id']; ?>"
                        class="shop-view-product"
                    >
                        VIEW PRODUCT →
                    </a>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty-shop">

                <h2>No products found.</h2>

                <p>
                    Try another search or browse our complete collection.
                </p>

            </div>

        <?php endif; ?>

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


<script src="assets/js/script.js"></script>

</body>
</html>