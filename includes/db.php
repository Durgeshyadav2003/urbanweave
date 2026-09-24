<?php

$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    die("DATABASE_URL is not configured.");
}

$url = parse_url($databaseUrl);

if ($url === false) {
    die("Invalid DATABASE_URL.");
}

$host = $url["host"] ?? "";
$port = $url["port"] ?? 3306;
$username = $url["user"] ?? "";
$password = $url["pass"] ?? "";
$database = isset($url["path"]) ? ltrim($url["path"], "/") : "";

if (!$host || !$username || !$database) {
    die("Incomplete DATABASE_URL configuration.");
}

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database,
    intval($port)
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>