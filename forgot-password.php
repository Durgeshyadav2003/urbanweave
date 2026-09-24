<?php

session_start();

require_once "includes/db.php";
require_once "includes/config.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($email === "" || $new_password === "" || $confirm_password === "") {

        $error = "Please fill all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email.";

    } elseif (strlen($new_password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($new_password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {

            $error = "No account found with this email.";

        } else {

            $user = $result->fetch_assoc();

            $hashed_password = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );

            $update = $conn->prepare(
                "UPDATE users SET password = ? WHERE id = ?"
            );

            $update->bind_param(
                "si",
                $hashed_password,
                $user["id"]
            );

            if ($update->execute()) {

                $message = "Password reset successfully. You can login now.";

            } else {

                $error = "Something went wrong. Please try again.";
            }

            $update->close();
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

    <title>Forgot Password | UrbanWeave</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        body {
            margin: 0;
            background: #f5f3ef;
            color: #111;
        }

        .forgot-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .forgot-box {
            width: 100%;
            max-width: 430px;
            background: #fff;
            padding: 45px;
            box-sizing: border-box;
            border: 1px solid #dedbd4;
        }

        .forgot-logo {
            text-align: center;
            font-size: 30px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .forgot-logo span {
            font-weight: 400;
        }

        .forgot-title {
            text-align: center;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #666;
            margin-bottom: 35px;
        }

        .forgot-group {
            margin-bottom: 20px;
        }

        .forgot-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .forgot-group input {
            width: 100%;
            box-sizing: border-box;
            padding: 14px;
            border: 1px solid #ccc;
            outline: none;
            font-size: 14px;
        }

        .forgot-group input:focus {
            border-color: #111;
        }

        .forgot-btn {
            width: 100%;
            padding: 15px;
            border: none;
            background: #111;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            cursor: pointer;
        }

        .forgot-btn:hover {
            opacity: 0.85;
        }

        .forgot-error {
            background: #ffe9e9;
            color: #b00020;
            padding: 12px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
        }

        .forgot-success {
            background: #e9f8ed;
            color: #16733a;
            padding: 12px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
        }

        .back-login {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #111;
            font-size: 12px;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="forgot-page">

    <div class="forgot-box">

        <div class="forgot-logo">
            Urban<span>Weave</span>
        </div>

        <div class="forgot-title">
            Reset Your Password
        </div>

        <?php if ($error !== ""): ?>

            <div class="forgot-error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <?php if ($message !== ""): ?>

            <div class="forgot-success">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="forgot-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your registered email"
                    required
                >

            </div>

            <div class="forgot-group">

                <label>New Password</label>

                <input
                    type="password"
                    name="new_password"
                    placeholder="Minimum 6 characters"
                    required
                >

            </div>

            <div class="forgot-group">

                <label>Confirm Password</label>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm new password"
                    required
                >

            </div>

            <button type="submit" class="forgot-btn">
                RESET PASSWORD
            </button>

        </form>

        <a href="login.php" class="back-login">
            ← Back to Login
        </a>

    </div>

</div>

</body>
</html>