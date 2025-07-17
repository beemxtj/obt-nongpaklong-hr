<?php
// views/layouts/header.php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/login'); exit(); }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'HRM อบต.หนองปากโลง'); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .btn-gradient { background-image: linear-gradient(to right, #4f46e5, #7c3aed); }
    </style>
</head>
<body class="bg-gray-100">
