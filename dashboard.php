<?php
require_once __DIR__ . '/includes/header.php';

// Fetch Aggregate Statistics
$total_farmers = $pdo->query("SELECT COUNT(*) FROM farmer")->fetchColumn();
$total_crops = $pdo->query("SELECT COUNT(*) FROM crop")->fetchColumn();
$total_expenses = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM expense")->fetchColumn();

// Sales Total: SUM(quantity_kg * price_per_kg)
$total_sales = $pdo->query("SELECT COALESCE(SUM(quantity_kg * price_per_kg), 0) FROM sales")->fetchColumn();

$net_profit = $total_sales - $total_expenses;

$total_equipment = $pdo->query("SELECT COUNT(*) FROM equipment")->fetchColumn();
$total_production_kg = $pdo->query("SELECT COALESCE(SUM(quantity_kg), 0) FROM production")->fetchColumn();

// Fetch Recent Sales
$recent_sales_stmt = $pdo->query("
    SELECT s.*, c.crop_name, (s.quantity_kg * s.price_per_kg) as total_amount
    FROM sales s
    JOIN crop c ON s.crop_id = c.crop_id
    ORDER BY s.sale_date DESC LIMIT 5
");
$recent_sales = $recent_sales_stmt->fetchAll();

// Fetch Recent Expenses
$recent_expenses_stmt = $pdo->query("
    SELECT e.*, c.crop_name
    FROM expense e
    JOIN crop c ON e.crop_id = c.crop_id
    ORDER BY e.expense_date DESC LIMIT 5
");
$recent_expenses = $recent_expenses_stmt->fetchAll();

// Production vs Sales Warning Check
$warnings_stmt = $pdo->query("
    SELECT c.crop_name, f.name as farmer_name,
           COALESCE(p.tot_prod, 0) as total_produced,
           COALESCE(s.tot_sold, 0) as total_sold
    FROM crop c
    JOIN farmer f ON c.farmer_id = f.farmer_id
    LEFT JOIN (SELECT crop_id, SUM(quantity_kg) as tot_prod FROM production GROUP BY crop_id) p ON c.crop_id = p.crop_id
    LEFT JOIN (SELECT crop_id, SUM(quantity_kg) as tot_sold FROM sales GROUP BY crop_id) s ON c.crop_id = s.crop_id
    WHERE COALESCE(s.tot_sold, 0) > COALESCE(p.tot_prod, 0)
");
$production_warnings = $warnings_stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-dark mb-1"><i class="fa-solid fa-gauge-high text-success me-2"></i>Farm Operations Dashboard</h2>
        <p class="text-muted">Overview of farmers, crops, production, finances, and key metrics.</p>
    </div>
    <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end gap-2">
        <a href="reports/profit_loss.php" class="btn btn-warning fw-semibold shadow-sm">
            <i class="fa-solid fa-file-invoice-dollar me-1"></i> Profit Report
        </a>
    </div>
</div>

<?php if (!empty($production_warnings)): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm mb-4" role="alert">
        <h5 class="alert-heading fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i>Yield Discrepancy Warning</h5>
        <p class="mb-1">The following crops have total quantity sold exceeding recorded harvest production:</p>
        <ul class="mb-0">
            <?php foreach ($production_warnings as $warn): ?>
                <li><strong><?= htmlspecialchars($warn['crop_name']) ?></strong> (Farmer: <?= htmlspecialchars($warn['farmer_name']) ?>) - Sold: <strong><?= number_format($warn['total_sold'], 2) ?> kg</strong> | Produced: <strong><?= number_format($warn['total_produced'], 2) ?> kg</strong></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Metric Cards Row -->
<div class="row g-3 mb-4">
    <!-- Total Farmers -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-metric p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold small text-uppercase">Total Farmers</span>
                    <h3 class="fw-bold text-dark my-1"><?= number_format($total_farmers) ?></h3>
                    <a href="modules/farmer/list.php" class="text-primary text-decoration-none small">Manage Farmers &rarr;</a>
                </div>
                <div class="metric-icon icon-farmer">
                    <i class="fa-solid fa-user-group"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Crops -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-metric p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold small text-uppercase">Active Crops</span>
                    <h3 class="fw-bold text-dark my-1"><?= number_format($total_crops) ?></h3>
                    <a href="modules/crop/list.php" class="text-success text-decoration-none small">View Crop Records &rarr;</a>
                </div>
                <div class="metric-icon icon-crop">
                    <i class="fa-solid fa-seedling"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Expenses -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-metric p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold small text-uppercase">Total Expenses</span>
                    <h3 class="fw-bold text-danger my-1"><?= format_currency($total_expenses) ?></h3>
                    <a href="modules/expense/list.php" class="text-danger text-decoration-none small">Track Expenses &rarr;</a>
                </div>
                <div class="metric-icon icon-expense">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Sales & Profit -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-metric p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold small text-uppercase">Total Revenue</span>
                    <h3 class="fw-bold text-success my-1"><?= format_currency($total_sales) ?></h3>
                    <span class="badge <?= ($net_profit >= 0) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> fw-semibold">
                        Net: <?= format_currency($net_profit) ?>
                    </span>
                </div>
                <div class="metric-icon icon-sales">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Metrics Bar -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="p-3 bg-white rounded-3 shadow-sm d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="metric-icon icon-equipment"><i class="fa-solid fa-tractor"></i></div>
                <div>
                    <h6 class="mb-0 text-muted small">Registered Equipment</h6>
                    <span class="fs-5 fw-bold"><?= number_format($total_equipment) ?> Units</span>
                </div>
            </div>
            <a href="modules/equipment/list.php" class="btn btn-sm btn-outline-primary">View</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="p-3 bg-white rounded-3 shadow-sm d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="metric-icon icon-production"><i class="fa-solid fa-boxes-stacked"></i></div>
                <div>
                    <h6 class="mb-0 text-muted small">Total Harvested Yield</h6>
                    <span class="fs-5 fw-bold"><?= number_format($total_production_kg, 2) ?> kg</span>
                </div>
            </div>
            <a href="modules/production/list.php" class="btn btn-sm btn-outline-success">View</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="p-3 bg-white rounded-3 shadow-sm d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="metric-icon icon-profit"><i class="fa-solid fa-scale-balanced"></i></div>
                <div>
                    <h6 class="mb-0 text-muted small">Overall Profit Margin</h6>
                    <span class="fs-5 fw-bold <?= ($net_profit >= 0) ? 'text-success' : 'text-danger' ?>">
                        <?= ($total_sales > 0) ? number_format(($net_profit / $total_sales) * 100, 1) . '%' : 'N/A' ?>
                    </span>
                </div>
            </div>
            <a href="reports/profit_loss.php" class="btn btn-sm btn-outline-warning">Analysis</a>
        </div>
    </div>
</div>

<!-- Recent Activities Row -->
<div class="row g-4">
    <!-- Recent Sales Table -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-sack-dollar text-success me-2"></i>Recent Crop Sales</span>
                <a href="modules/sales/list.php" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Crop</th>
                                <th>Buyer</th>
                                <th>Qty (kg)</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_sales)): ?>
                                <tr><td colspan="5" class="text-center py-3 text-muted">No sales recorded yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_sales as $sale): ?>
                                    <tr>
                                        <td class="fw-semibold text-success"><?= htmlspecialchars($sale['crop_name']) ?></td>
                                        <td><?= htmlspecialchars($sale['buyer_name']) ?></td>
                                        <td><?= number_format($sale['quantity_kg'], 2) ?></td>
                                        <td class="fw-bold text-dark"><?= format_currency($sale['total_amount']) ?></td>
                                        <td class="small text-muted"><?= format_date($sale['sale_date']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Expenses Table -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-receipt text-danger me-2"></i>Recent Farming Expenses</span>
                <a href="modules/expense/list.php" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Crop</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_expenses)): ?>
                                <tr><td colspan="4" class="text-center py-3 text-muted">No expenses recorded yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_expenses as $exp): ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($exp['crop_name']) ?></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary fw-semibold"><?= htmlspecialchars($exp['category']) ?></span></td>
                                        <td class="fw-bold text-danger"><?= format_currency($exp['amount']) ?></td>
                                        <td class="small text-muted"><?= format_date($exp['expense_date']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
