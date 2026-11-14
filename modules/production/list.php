<?php
require_once __DIR__ . '/../../includes/header.php';

$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.crop_name, f.name as farmer_name
        FROM production p
        JOIN crop c ON p.crop_id = c.crop_id
        JOIN farmer f ON c.farmer_id = f.farmer_id
        WHERE c.crop_name LIKE ? OR f.name LIKE ? OR p.notes LIKE ?
        ORDER BY p.production_id DESC
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, c.crop_name, f.name as farmer_name
        FROM production p
        JOIN crop c ON p.crop_id = c.crop_id
        JOIN farmer f ON c.farmer_id = f.farmer_id
        ORDER BY p.production_id DESC
    ");
}
$productions = $stmt->fetchAll();

$total_yield_kg = array_sum(array_column($productions, 'quantity_kg'));
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-boxes-stacked text-teal me-2"></i>Production Management</h3>
        <p class="text-muted small mb-0">Record harvest yield quantities (kg), harvest dates, and crop output logs.</p>
    </div>
    <a href="add.php" class="btn btn-farm shadow-sm">
        <i class="fa-solid fa-plus-circle me-1"></i> Record Harvest Yield
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <form method="GET" action="list.php" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="tableSearchInput" name="search" class="form-control bg-light border-start-0" placeholder="Search by crop, farmer or harvest notes..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <?php if (!empty($search)): ?>
                        <a href="list.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-6 text-md-end text-muted small">
                Total Production Yield: <strong class="text-success fs-6"><?= number_format($total_yield_kg, 2) ?> kg</strong>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-searchable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Crop Harvested</th>
                        <th>Farmer Name</th>
                        <th>Harvest Yield (kg)</th>
                        <th>Production Date</th>
                        <th>Notes / Quality</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No production yield records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productions as $prod): ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?= $prod['production_id'] ?></td>
                                <td class="fw-bold text-success">
                                    <i class="fa-solid fa-wheat-awn me-2"></i><?= htmlspecialchars($prod['crop_name']) ?>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <i class="fa-solid fa-user me-1 text-muted small"></i><?= htmlspecialchars($prod['farmer_name']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success fs-6 fw-bold px-3 py-1 border border-success-subtle">
                                        <?= number_format($prod['quantity_kg'], 2) ?> kg
                                    </span>
                                </td>
                                <td class="small text-muted"><?= format_date($prod['production_date']) ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($prod['notes'] ?: '—') ?></td>
                                <td class="text-end pe-4">
                                    <a href="edit.php?id=<?= $prod['production_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Production">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $prod['production_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item="Production Record #<?= $prod['production_id'] ?>" title="Delete Record">
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
