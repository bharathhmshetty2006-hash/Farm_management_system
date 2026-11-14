<?php
require_once __DIR__ . '/../../includes/header.php';

$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare("
        SELECT s.*, (s.quantity_kg * s.price_per_kg) as total_income, c.crop_name, f.name as farmer_name
        FROM sales s
        JOIN crop c ON s.crop_id = c.crop_id
        JOIN farmer f ON c.farmer_id = f.farmer_id
        WHERE s.buyer_name LIKE ? OR c.crop_name LIKE ? OR f.name LIKE ?
        ORDER BY s.sale_id DESC
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT s.*, (s.quantity_kg * s.price_per_kg) as total_income, c.crop_name, f.name as farmer_name
        FROM sales s
        JOIN crop c ON s.crop_id = c.crop_id
        JOIN farmer f ON c.farmer_id = f.farmer_id
        ORDER BY s.sale_id DESC
    ");
}
$sales = $stmt->fetchAll();

$total_revenue_sum = array_sum(array_column($sales, 'total_income'));
$total_sold_kg = array_sum(array_column($sales, 'quantity_kg'));
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-coins text-success me-2"></i>Sales & Revenue Management</h3>
        <p class="text-muted small mb-0">Record crop sales transactions, buyer details, unit rates, and total sales income.</p>
    </div>
    <a href="add.php" class="btn btn-farm shadow-sm">
        <i class="fa-solid fa-plus-circle me-1"></i> Record New Sale
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <form method="GET" action="list.php" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="tableSearchInput" name="search" class="form-control bg-light border-start-0" placeholder="Search buyer name, crop or farmer..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <?php if (!empty($search)): ?>
                        <a href="list.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-6 text-md-end text-muted small">
                Total Sold Volume: <strong><?= number_format($total_sold_kg, 2) ?> kg</strong> | Gross Income: <strong class="text-success fs-6"><?= format_currency($total_revenue_sum) ?></strong>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-searchable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Crop & Farmer</th>
                        <th>Buyer Name / Market</th>
                        <th>Quantity Sold</th>
                        <th>Price / kg</th>
                        <th>Total Income</th>
                        <th>Sale Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No sales transactions recorded.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?= $sale['sale_id'] ?></td>
                                <td>
                                    <span class="fw-bold text-success"><?= htmlspecialchars($sale['crop_name']) ?></span>
                                    <span class="small text-muted d-block">(<?= htmlspecialchars($sale['farmer_name']) ?>)</span>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <i class="fa-solid fa-store text-muted me-1 small"></i><?= htmlspecialchars($sale['buyer_name']) ?>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= number_format($sale['quantity_kg'], 2) ?> kg</span></td>
                                <td><?= format_currency($sale['price_per_kg']) ?>/kg</td>
                                <td class="fw-bold text-success fs-6"><?= format_currency($sale['total_income']) ?></td>
                                <td class="small text-muted"><?= format_date($sale['sale_date']) ?></td>
                                <td class="text-end pe-4">
                                    <a href="edit.php?id=<?= $sale['sale_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Sale">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $sale['sale_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item="Sale Record #<?= $sale['sale_id'] ?>" title="Delete Sale">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
