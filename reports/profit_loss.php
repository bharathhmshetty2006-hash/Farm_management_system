<?php
require_once __DIR__ . '/../includes/header.php';

$farmer_filter = intval($_GET['farmer_id'] ?? 0);

// Fetch all farmers for filter dropdown
$farmers = $pdo->query("SELECT farmer_id, name FROM farmer ORDER BY name ASC")->fetchAll();

$query = "
    SELECT c.crop_id, c.crop_name, c.season, c.planting_date, f.name as farmer_name, f.phone as farmer_phone,
           COALESCE(p.tot_produced, 0) as total_produced_kg,
           COALESCE(s.tot_sold, 0) as total_sold_kg,
           COALESCE(s.gross_sales, 0) as total_revenue,
           COALESCE(e.tot_expenses, 0) as total_expenses,
           COALESCE(e.seed_exp, 0) as seed_expenses,
           COALESCE(e.labour_exp, 0) as labour_expenses,
           COALESCE(e.trans_exp, 0) as trans_expenses,
           COALESCE(e.other_exp, 0) as other_expenses
    FROM crop c
    JOIN farmer f ON c.farmer_id = f.farmer_id
    LEFT JOIN (
        SELECT crop_id, SUM(quantity_kg) as tot_produced
        FROM production GROUP BY crop_id
    ) p ON c.crop_id = p.crop_id
    LEFT JOIN (
        SELECT crop_id, SUM(quantity_kg) as tot_sold, SUM(quantity_kg * price_per_kg) as gross_sales
        FROM sales GROUP BY crop_id
    ) s ON c.crop_id = s.crop_id
    LEFT JOIN (
        SELECT crop_id, 
               SUM(amount) as tot_expenses,
               SUM(CASE WHEN category = 'Seeds' THEN amount ELSE 0 END) as seed_exp,
               SUM(CASE WHEN category = 'Labour' THEN amount ELSE 0 END) as labour_exp,
               SUM(CASE WHEN category = 'Transportation' THEN amount ELSE 0 END) as trans_exp,
               SUM(CASE WHEN category = 'Other' THEN amount ELSE 0 END) as other_exp
        FROM expense GROUP BY crop_id
    ) e ON c.crop_id = e.crop_id
";

if ($farmer_filter > 0) {
    $query .= " WHERE c.farmer_id = " . $farmer_filter;
}

$query .= " ORDER BY c.crop_id DESC";

$report_data = $pdo->query($query)->fetchAll();

// Overall Financial Summaries
$grand_expenses = array_sum(array_column($report_data, 'total_expenses'));
$grand_revenue = array_sum(array_column($report_data, 'total_revenue'));
$grand_net_profit = $grand_revenue - $grand_expenses;
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-file-invoice-dollar text-warning me-2"></i>Crop-wise Profit & Loss Analysis</h3>
        <p class="text-muted small mb-0">Detailed breakdown of production yield, category expenses, gross revenue, and net profit per crop.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-dark fw-semibold shadow-sm">
            <i class="fa-solid fa-print me-1"></i> Print / Save PDF
        </button>
    </div>
</div>

<!-- Filter Bar (Hidden on Print) -->
<div class="card shadow-sm border-0 mb-4 d-print-none">
    <div class="card-body py-3">
        <form method="GET" action="profit_loss.php" class="row g-3 align-items-center">
            <div class="col-md-5">
                <label class="form-label fw-semibold small mb-1">Filter by Farmer:</label>
                <select name="farmer_id" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="0">-- All Farmers --</option>
                    <?php foreach ($farmers as $f): ?>
                        <option value="<?= $f['farmer_id'] ?>" <?= ($farmer_filter == $f['farmer_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['name']) ?> (#<?= $f['farmer_id'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($farmer_filter > 0): ?>
                <div class="col-md-2 align-self-end">
                    <a href="profit_loss.php" class="btn btn-outline-secondary w-100">Reset Filter</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Financial Overview Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="p-3 bg-white rounded-3 shadow-sm border-start border-4 border-danger">
            <span class="text-muted small fw-semibold text-uppercase">Total Farm Expenses</span>
            <h3 class="fw-bold text-danger my-1"><?= format_currency($grand_expenses) ?></h3>
            <span class="text-muted small">Sum of all crop operational costs</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 bg-white rounded-3 shadow-sm border-start border-4 border-success">
            <span class="text-muted small fw-semibold text-uppercase">Total Gross Revenue</span>
            <h3 class="fw-bold text-success my-1"><?= format_currency($grand_revenue) ?></h3>
            <span class="text-muted small">Total income from crop sales</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 bg-white rounded-3 shadow-sm border-start border-4 <?= ($grand_net_profit >= 0) ? 'border-success' : 'border-danger' ?>">
            <span class="text-muted small fw-semibold text-uppercase">Overall Net Profit / Loss</span>
            <h3 class="fw-bold my-1 <?= ($grand_net_profit >= 0) ? 'text-success' : 'text-danger' ?>">
                <?= format_currency($grand_net_profit) ?>
            </h3>
            <span class="badge <?= ($grand_net_profit >= 0) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> fw-semibold">
                <?= ($grand_net_profit >= 0) ? 'PROFITABLE HARVEST' : 'NET LOSS INURRED' ?>
            </span>
        </div>
    </div>
</div>

<!-- Detailed Profit & Loss Report Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 fw-bold text-dark d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-table me-2 text-warning"></i>Crop Performance & Financial Statement</span>
        <span class="small text-muted font-monospace">Generated: <?= date('d M Y, h:i A') ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small">
                    <tr>
                        <th>Crop & Farmer</th>
                        <th>Harvest (kg)</th>
                        <th>Sold (kg)</th>
                        <th>Expenses Breakdown (₹)</th>
                        <th>Total Expense</th>
                        <th>Gross Revenue</th>
                        <th>Net Profit / (Loss)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($report_data)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No financial records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($report_data as $row): 
                            $net = $row['total_revenue'] - $row['total_expenses'];
                            $is_warning = ($row['total_sold_kg'] > $row['total_produced_kg'] && $row['total_sold_kg'] > 0);
                        ?>
                            <tr>
                                <td>
                                    <strong class="text-success fs-6"><?= htmlspecialchars($row['crop_name']) ?></strong>
                                    <span class="badge bg-light text-dark border ms-1"><?= htmlspecialchars($row['season']) ?></span>
                                    <div class="small text-muted mt-1">
                                        <i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($row['farmer_name']) ?>
                                    </div>
                                </td>
                                <td class="fw-semibold"><?= number_format($row['total_produced_kg'], 2) ?></td>
                                <td class="fw-semibold"><?= number_format($row['total_sold_kg'], 2) ?></td>
                                <td class="small text-muted">
                                    <div class="d-flex justify-content-between gap-2"><span>Seeds:</span><span><?= format_currency($row['seed_expenses']) ?></span></div>
                                    <div class="d-flex justify-content-between gap-2"><span>Labour:</span><span><?= format_currency($row['labour_expenses']) ?></span></div>
                                    <div class="d-flex justify-content-between gap-2"><span>Trans:</span><span><?= format_currency($row['trans_expenses']) ?></span></div>
                                    <div class="d-flex justify-content-between gap-2"><span>Other:</span><span><?= format_currency($row['other_expenses']) ?></span></div>
                                </td>
                                <td class="fw-bold text-danger"><?= format_currency($row['total_expenses']) ?></td>
                                <td class="fw-bold text-success"><?= format_currency($row['total_revenue']) ?></td>
                                <td class="fw-bold fs-6 <?= ($net >= 0) ? 'text-success' : 'text-danger' ?>">
                                    <?= format_currency($net) ?>
                                </td>
                                <td>
                                    <?php if ($net >= 0): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold"><i class="fa-solid fa-arrow-trend-up me-1"></i>Profit</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold"><i class="fa-solid fa-arrow-trend-down me-1"></i>Loss</span>
                                    <?php endif; ?>

                                    <?php if ($is_warning): ?>
                                        <div class="mt-1"><span class="badge bg-warning text-dark" title="Quantity sold exceeds harvest production yield!"><i class="fa-solid fa-triangle-exclamation me-1"></i>Yield Alert</span></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">Grand Total Summary:</td>
                        <td class="text-danger"><?= format_currency($grand_expenses) ?></td>
                        <td class="text-success"><?= format_currency($grand_revenue) ?></td>
                        <td class="<?= ($grand_net_profit >= 0) ? 'text-success' : 'text-danger' ?>"><?= format_currency($grand_net_profit) ?></td>
                        <td>
                            <span class="badge <?= ($grand_net_profit >= 0) ? 'bg-success' : 'bg-danger' ?>">
                                <?= ($grand_net_profit >= 0) ? 'Overall Profit' : 'Overall Loss' ?>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
