<?php
// Determine current script name to mark active menu items
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Depth offset helper for links
$depth = (strpos(str_replace('\\', '/', $_SERVER['SCRIPT_NAME']), '/modules/') !== false || strpos(str_replace('\\', '/', $_SERVER['SCRIPT_NAME']), '/reports/') !== false) ? '../../' : './';
?>
<div id="sidebar-wrapper">
    <div class="sidebar-heading d-flex align-items-center justify-content-between">
        <span><i class="fa-solid fa-wheat-awn me-2"></i>FARM SYS</span>
        <span class="badge bg-success small">v1.0</span>
    </div>
    
    <div class="list-group list-group-flush py-2">
        <a href="<?= $depth ?>dashboard.php" class="sidebar-link <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>

        <div class="sidebar-section-title">Core Modules</div>

        <a href="<?= $depth ?>modules/farmer/list.php" class="sidebar-link <?= ($current_dir === 'farmer') ? 'active' : '' ?>">
            <i class="fa-solid fa-user-group"></i> Farmers
        </a>

        <a href="<?= $depth ?>modules/crop/list.php" class="sidebar-link <?= ($current_dir === 'crop') ? 'active' : '' ?>">
            <i class="fa-solid fa-seedling"></i> Crops
        </a>

        <a href="<?= $depth ?>modules/seed/list.php" class="sidebar-link <?= ($current_dir === 'seed') ? 'active' : '' ?>">
            <i class="fa-solid fa-jar"></i> Seeds
        </a>

        <a href="<?= $depth ?>modules/labour/list.php" class="sidebar-link <?= ($current_dir === 'labour') ? 'active' : '' ?>">
            <i class="fa-solid fa-person-digging"></i> Labour
        </a>

        <a href="<?= $depth ?>modules/expense/list.php" class="sidebar-link <?= ($current_dir === 'expense') ? 'active' : '' ?>">
            <i class="fa-solid fa-receipt"></i> Expenses
        </a>

        <a href="<?= $depth ?>modules/equipment/list.php" class="sidebar-link <?= ($current_dir === 'equipment') ? 'active' : '' ?>">
            <i class="fa-solid fa-tractor"></i> Equipment
        </a>

        <a href="<?= $depth ?>modules/production/list.php" class="sidebar-link <?= ($current_dir === 'production') ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-stacked"></i> Production
        </a>

        <a href="<?= $depth ?>modules/sales/list.php" class="sidebar-link <?= ($current_dir === 'sales') ? 'active' : '' ?>">
            <i class="fa-solid fa-coins"></i> Sales
        </a>

        <div class="sidebar-section-title">Reports & Analysis</div>

        <a href="<?= $depth ?>reports/profit_loss.php" class="sidebar-link <?= ($current_page === 'profit_loss.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice-dollar text-warning"></i> Profit & Loss Report
        </a>
    </div>

    <div class="p-3 mt-auto text-center small text-muted border-top border-secondary">
        <div>VTU 5th Sem Mini Project</div>
        <div class="fw-bold text-light">Govt Engg College, Hassan</div>
    </div>
</div>
