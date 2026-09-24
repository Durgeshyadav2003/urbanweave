<?php

session_start();

require_once "includes/db.php";
require_once "includes/config.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    /* =========================
       VALIDATION
    ========================= */

    if ($name === "" || $email === "" || $password === "" || $confirm_password === "") {

        $message = "Please fill all fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } else {

        /* =========================
           CHECK EXISTING USER
        ========================= */

        $check_stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();

        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {

            $message = "An account with this email already exists.";
            $message_type = "error";

        } else {

            /* =========================
               SECURE PASSWORD
            ========================= */

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /* =========================
               CREATE USER
            ========================= */

            $insert_stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password)
                VALUES (?, ?, ?)"
            );

            $insert_stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashed_password
            );


            if ($insert_stmt->execute()) {

                $new_user_id = $conn->insert_id;

                /* Automatically login */

                $_SESSION["user_id"] = $new_user_id;
                $_SESSION["user_name"] = $name;
                $_SESSION["user_email"] = $email;

                header("Location: index.php");
                exit;

            } else {

                $message = "Unable to create account. Please try again.";
                $message_type = "error";
            }

            $insert_stmt->close();
        }

        $check_stmt->close();
    }
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

    <title>Create Account | UrbanWeave</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        .auth-page {
            min-height: calc(100vh - 90px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 70px 25px 100px;
        }

        .auth-container {
            width: 100%;
            max-width: 520px;
        }

        .auth-heading {
            margin-bottom: 45px;
        }

        .auth-heading p {
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            opacity: .6;
            margin-bottom: 12px;
        }

        .auth-heading h1 {
            margin: 0;
            font-size: clamp(55px, 9vw, 100px);
            line-height: .85;
            letter-spacing: -5px;
            font-weight: 800;
        }

        .auth-form {
            border-top: 1px solid #222;
            padding-top: 35px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
            margin-bottom: 9px;
        }

        .form-group input {
            width: 100%;
            box-sizing: border-box;
            padding: 16px 15px;
            border: 1px solid #aaa;
            background: #fff;
            color: #111;
            font-family: inherit;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #111;
        }

        .auth-button {
            width: 100%;
            border: none;
            background: #111;
            color: #fff;
            padding: 17px 20px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            cursor: pointer;
            margin-top: 5px;
        }

        .auth-button:hover {
            opacity: .85;
        }

        .auth-message {
            padding: 14px 16px;
            margin-bottom: 25px;
            font-size: 13px;
            line-height: 1.5;
        }

        .auth-message.error {
            background: #f9e8e8;
            border: 1px solid #d7a5a5;
        }

        .auth-message.success {
            background: #e8f5e9;
            border: 1px solid #a5c9a7;
        }

        .auth-switch {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            opacity: .7;
        }

        .auth-switch a {
            color: #111;
            font-weight: 700;
            text-decoration: underline;
        }

        

        @media (max-width: 600px) {

            .auth-page {
                padding: 50px 20px 80px;
            }

            .auth-heading h1 {
                font-size: 65px;
                letter-spacing: -3px;
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

            <a href="login.php" class="login-link">
                <span>Login</span>
            </a>

            <a href="register.php" class="register-link">
                <span>Register</span>
            </a>

            <button class="menu-btn" id="menuBtn">
                ☰
            </button>

        </div>

    </div>
</header>



<!-- =========================
     REGISTER
========================= -->

<main class="auth-page">

    <div class="auth-container">

        <div class="auth-heading">

            <p>Join UrbanWeave</p>

            <h1>CREATE<br>ACCOUNT</h1>

        </div>


        <?php if ($message !== ""): ?>

            <div class="auth-message <?php echo $message_type; ?>">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            class="auth-form"
            autocomplete="off"
        >

            <!-- NAME -->

            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your name"
                    value="<?php
                    echo htmlspecialchars($_POST["name"] ?? "");
                    ?>"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php
                    echo htmlspecialchars($_POST["email"] ?? "");
                    ?>"
                    required
                >

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimum 6 characters"
                    required
                >

            </div>


            <!-- CONFIRM PASSWORD -->

            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Re-enter your password"
                    required
                >

            </div>


            <button
                type="submit"
                class="auth-button"
            >
                CREATE ACCOUNT →
            </button>

        </form>


        <div class="auth-switch">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </div>

    </div>

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
$conn->close();
?>