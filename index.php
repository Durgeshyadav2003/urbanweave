<?php
session_start();

require_once "includes/db.php";
require_once "includes/config.php";

$productQuery = "SELECT * FROM products WHERE featured = 1 ORDER BY id DESC LIMIT 6";
$productResult = $conn->query($productQuery);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UrbanWeave | Modern Fashion</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- ================= NAVBAR ================= -->

    <!-- ================= NAVBAR ================= -->

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

            <!-- Search -->
            <button class="icon-btn" id="searchBtn" aria-label="Search">
                🔍
            </button>

            <!-- Cart -->
            <a href="cart.php" class="cart-btn">
                🛒 <span id="cartCount">0</span>
            </a>

            <!-- Wishlist -->
            <a href="wishlist.php" class="cart-btn">
                ♡ <span id="wishlistCount">0</span>
            </a>

           <?php if (isset($_SESSION["user_id"])): ?>

    <!-- Logged In User -->
    <a href="orders.php" class="login-link">
        👤 <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
    </a>

    <!-- Logout -->
    <a href="logout.php" class="register-link">
        Logout
    </a>

<?php else: ?>

    <!-- Login -->
    <a href="login.php" class="login-link">
        Login
    </a>

    <!-- Register -->
    <a href="register.php" class="register-link">
        Register
    </a>

<?php endif; ?>

            <!-- Mobile Menu -->
            <button class="menu-btn" id="menuBtn">
                ☰
            </button>

        </div>

    </div>

</header>

    <!-- ================= SEARCH BOX ================= -->

    <div class="search-overlay" id="searchOverlay">

        <div class="search-box">

            <button id="closeSearch" class="close-search">
                ×
            </button>

            <h2>What are you looking for?</h2>

            <form action="products.php" method="GET">

                <input
                    type="text"
                    name="search"
                    placeholder="Search t-shirts, jackets, hoodies..."
                    required
                >

                <button type="submit">
                    Search
                </button>

            </form>

        </div>

    </div>


    <!-- ================= HERO ================= -->

    <main>

        <section class="hero">

            <div class="hero-content">

                <p class="hero-small">
                    NEW SEASON • 2026
                </p>

                <h1>
                    WEAR YOUR
                    <span>IDENTITY.</span>
                </h1>

                <p class="hero-description">
                    Discover modern streetwear designed for people
                    who don't follow the crowd.
                </p>

                <div class="hero-buttons">

                    <a href="products.php" class="btn btn-dark">
                        SHOP COLLECTION
                    </a>

                    <a href="#featured" class="btn btn-light">
                        EXPLORE ↓
                    </a>

                </div>

            </div>

            <div class="hero-visual">

                <div class="hero-card">

                    <div class="hero-fashion-image">

    <img
        src="assets/images/oversized-black.jpg"
        alt="UrbanWeave Black Oversized T-Shirt"
    >

    <div class="fashion-text">
        URBAN<br>WEAVE
    </div>

</div>

                    <div class="floating-tag">
                        EST. 2026
                    </div>

                </div>

            </div>

        </section>


        <!-- ================= MARQUEE ================= -->

        <section class="marquee">

            <div class="marquee-track">

                <span>URBANWEAVE</span>
                <span>NEW COLLECTION</span>
                <span>STREET CULTURE</span>
                <span>MODERN ESSENTIALS</span>
                <span>URBANWEAVE</span>
                <span>NEW COLLECTION</span>
                <span>STREET CULTURE</span>

            </div>

        </section>


        <!-- ================= CATEGORIES ================= -->

        <section class="categories section">

            <div class="section-heading">

                <div>
                    <p class="section-label">SHOP BY STYLE</p>

                    <h2>
                        Find Your
                        <span>Look.</span>
                    </h2>
                </div>

                <a href="products.php" class="view-all">
                    View All →
                </a>

            </div>


            <div class="category-grid">

                <a href="products.php?category=T-Shirts"
   class="category-card category-one">

    <img src="assets/images/oversized-black.jpg" alt="T-Shirts">

    <div>
        <p>01</p>
        <h3>T-Shirts</h3>
        <span>Explore Collection →</span>
    </div>

</a>


                <a href="products.php?category=Jackets"
   class="category-card category-two">

    <img src="assets/images/denim-jacket.jpg" alt="Jackets">

    <div>
        <p>02</p>
        <h3>Jackets</h3>
        <span>Explore Collection →</span>
    </div>

</a>

                <a href="products.php?category=Hoodies"
   class="category-card category-three">

    <img src="assets/images/women-hoodie.jpg" alt="Hoodies">

    <div>
        <p>03</p>
        <h3>Hoodies</h3>
        <span>Explore Collection →</span>
    </div>

</a>

            </div>

        </section>


        <!-- ================= FEATURED PRODUCTS ================= -->

        <section class="featured section" id="featured">

            <div class="section-heading">

                <div>

                    <p class="section-label">
                        CURATED FOR YOU
                    </p>

                    <h2>
                        Featured
                        <span>Pieces.</span>
                    </h2>

                </div>

                <a href="products.php" class="view-all">
                    Shop All →
                </a>

            </div>


            <div class="product-grid">

                <?php if ($productResult && $productResult->num_rows > 0): ?>

                    <?php while ($product = $productResult->fetch_assoc()): ?>

                        <article class="product-card">

                            <a
                                href="product.php?id=<?php echo $product['id']; ?>"
                                class="product-image"
                            >

                                <div class="product-badge">
                                    FEATURED
                                </div>

                                <img
    src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
    alt="<?php echo htmlspecialchars($product['name']); ?>"
    class="product-real-image"
>

                            </a>


                            <div class="product-info">

                                <div>

                                    <p class="product-category">
                                        <?php echo htmlspecialchars($product['category']); ?>
                                    </p>

                                    <h3>
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h3>

                                </div>

                                <p class="product-price">
                                    ₹<?php echo number_format($product['price'], 2); ?>
                                </p>

                            </div>


                            <a
                                href="product.php?id=<?php echo $product['id']; ?>"
                                class="quick-view"
                            >
                                VIEW PRODUCT →
                            </a>

                        </article>

                    <?php endwhile; ?>

                <?php else: ?>

                    <p class="no-products">
                        No featured products available.
                    </p>

                <?php endif; ?>

            </div>

        </section>


        <!-- ================= BRAND STORY ================= -->

        <section class="brand-story">

            <div class="story-content">

                <p class="section-label">
                    THE URBANWEAVE PHILOSOPHY
                </p>

                <h2>
                    Fashion isn't about
                    <em>fitting in.</em>
                </h2>

                <p>
                    UrbanWeave was created for those who create
                    their own path. Minimal designs, confident
                    silhouettes and everyday pieces made for
                    modern urban life.
                </p>

                <a href="products.php" class="btn btn-light">
                    DISCOVER URBANWEAVE
                </a>

            </div>

        </section>


        <!-- ================= NEWSLETTER ================= -->

        <section class="newsletter section">

            <p class="section-label">
                STAY IN THE LOOP
            </p>

            <h2>
                Don't miss the
                <span>drop.</span>
            </h2>

            <p>
                Get first access to new collections,
                exclusive drops and special offers.
            </p>

            <form class="newsletter-form" id="newsletterForm">

                <input
                    type="email"
                    id="newsletterEmail"
                    placeholder="Enter your email address"
                    required
                >

                <button type="submit">
                    JOIN US →
                </button>

            </form>

            <p class="newsletter-message" id="newsletterMessage"></p>

        </section>

    </main>


    <!-- ================= FOOTER ================= -->

    <footer class="footer">

        <div class="footer-grid">

            <div class="footer-brand">

                <a href="index.php" class="logo">
                    Urban<span>Weave</span>
                </a>

                <p>
                    Modern clothing for the
                    modern generation.
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