<?php
require_once __DIR__ . '/../config/db.php';
check_login();

// Calculate relative path offset for CSS/JS assets
$script_path = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$depth = (strpos($script_path, '/modules/') !== false || strpos($script_path, '/reports/') !== false) ? '../../' : './';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Management System - VTU Mini Project</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= $depth ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar Included via PHP -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper" class="w-100">
        <!-- Top Navigation Bar -->
        <nav class="navbar navbar-expand-lg top-navbar px-4 py-3">
            <div class="container-fluid">
                <span class="navbar-text text-dark fw-bold me-auto">
                    <i class="fa-solid fa-leaf text-success me-2"></i>Farm Management System
                </span>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border shadow-sm rounded-pill px-3" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-circle text-success fs-5"></i>
                            <span class="fw-semibold"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin User') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2" aria-labelledby="userDropdown">
                            <li><span class="dropdown-item-text text-muted small"><i class="fa-solid fa-shield-halved me-2"></i>Role: <?= ucfirst($_SESSION['role'] ?? 'Admin') ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= $depth ?>logout.php"><i class="fa-solid fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Body Container -->
        <div class="container-fluid p-4">
            <?php display_flash(); ?>
