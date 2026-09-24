<?php

session_start();

require_once "includes/db.php";
require_once "includes/config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter your email and password.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];

                header("Location: index.php");
                exit;

            } else {
                $error = "Incorrect email or password.";
            }

        } else {
            $error = "Incorrect email or password.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | UrbanWeave</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8f7f4;
            color: #111;
        }

        .login-page {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 70px 20px;
        }

        .login-box {
            width: 100%;
            max-width: 460px;
            background: #fff;
            padding: 45px;
            box-sizing: border-box;
            border: 1px solid #e5e5e5;
        }

        .login-title {
            font-size: 42px;
            font-weight: 800;
            margin: 0 0 8px;
            letter-spacing: -1px;
        }

        .login-subtitle {
            color: #777;
            font-size: 14px;
            margin-bottom: 35px;
        }

        .login-error {
            background: #fff0f0;
            border: 1px solid #f0caca;
            color: #b00020;
            padding: 12px 14px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            box-sizing: border-box;
            padding: 14px;
            border: 1px solid #d8d8d8;
            outline: none;
            font-size: 14px;
            background: #fff;
        }

        .form-group input:focus {
            border-color: #111;
        }

        .login-button {
            width: 100%;
            padding: 15px;
            border: none;
            background: #111;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-button:hover {
            background: #333;
        }

        .register-text {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #777;
        }

        .register-text a {
            color: #111;
            font-weight: 700;
            text-decoration: underline;
        }

        @media (max-width: 600px) {

            .login-box {
                padding: 30px 22px;
            }

            .login-title {
                font-size: 34px;
            }

        }

    </style>

</head>

<body>

<!-- NAVBAR -->

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


<!-- LOGIN -->

<main class="login-page">

    <div class="login-box">

        <h1 class="login-title">WELCOME BACK.</h1>

        <p class="login-subtitle">
            Login to your UrbanWeave account.
        </p>

        <?php if ($error !== ""): ?>

            <div class="login-error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <div style="text-align:right; margin-top:8px;">
    <a href="forgot-password.php"
       style="font-size:12px; color:#111; text-decoration:underline;">
        Forgot Password?
    </a>
</div>


            <button type="submit" class="login-button">
                LOGIN →
            </button>

        </form>


        <div class="register-text">

            Don't have an account?
            <a href="register.php">Create Account</a>

        </div>

    </div>

</main>


</body>

</html>