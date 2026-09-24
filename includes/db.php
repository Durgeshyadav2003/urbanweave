<?php

$host = getenv("MYSQLHOST");
$username = getenv("MYSQLUSER");
$password = getenv("MYSQLPASSWORD");
$database = getenv("MYSQLDATABASE");
$port = getenv("MYSQLPORT");

echo "<pre>";
echo "HOST = " . ($host ?: "EMPTY") . "\n";
echo "USER = " . ($username ?: "EMPTY") . "\n";
echo "DATABASE = " . ($database ?: "EMPTY") . "\n";
echo "PORT = " . ($port ?: "EMPTY") . "\n";
echo "</pre>";

if (!$host || !$username || !$database || !$port) {
    die("Railway MySQL environment variables are missing.");
}

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database,
    intval($port)
);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>