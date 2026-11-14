<?php
require_once __DIR__ . '/../../includes/header.php';

$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare("
        SELECT s.*, c.crop_name, f.name as farmer_name
        FROM seed s
        JOIN crop c ON s.crop_id = c.crop_id
        JOIN farmer f ON c.farmer_id = f.farmer_id
        WHERE s.seed_type LIKE ? OR s.supplier LIKE ? OR c.crop_name LIKE ? OR f.name LIKE ?
        ORDER BY s.seed_id DESC
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT s.*, c.crop_name, f.name as farmer_name
        FROM seed s
        JOIN crop c ON s.crop_id = c.crop_id
        JOIN farmer f ON c.farmer_id = f.farmer_id
        ORDER BY s.seed_id DESC
    ");
}
$seeds = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-jar text-warning me-2"></i>Seed Management</h3>
        <p class="text-muted small mb-0">Record seed varieties, quantities, suppliers, and procurement costs.</p>
    </div>
    <a href="add.php" class="btn btn-farm shadow-sm">
        <i class="fa-solid fa-plus-circle me-1"></i> Add Seed Record
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <form method="GET" action="list.php" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="tableSearchInput" name="search" class="form-control bg-light border-start-0" placeholder="Search by seed type, supplier or crop..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <?php if (!empty($search)): ?>
                        <a href="list.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-6 text-md-end text-muted small">
                Total Seed Records: <strong><?= count($seeds) ?></strong>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-searchable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Seed Variety / Type</th>
                        <th>Crop & Farmer</th>
                        <th>Quantity (kg/units)</th>
                        <th>Supplier / Source</th>
                        <th>Cost</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($seeds)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No seed records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($seeds as $seed): ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?= $seed['seed_id'] ?></td>
                                <td class="fw-bold text-dark">
                                    <i class="fa-solid fa-seedling text-warning me-2"></i><?= htmlspecialchars($seed['seed_type']) ?>
                                </td>
                                <td>
                                    <span class="fw-semibold text-success"><?= htmlspecialchars($seed['crop_name']) ?></span>
                                    <span class="small text-muted d-block">(<?= htmlspecialchars($seed['farmer_name']) ?>)</span>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= number_format($seed['quantity'], 2) ?></span></td>
                                <td class="text-muted"><?= htmlspecialchars($seed['supplier'] ?: 'Local Market') ?></td>
                                <td class="fw-bold text-danger"><?= format_currency($seed['cost']) ?></td>
                                <td class="text-end pe-4">
                                    <a href="edit.php?id=<?= $seed['seed_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Seed">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $seed['seed_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item="Seed '<?= htmlspecialchars($seed['seed_type']) ?>'" title="Delete Seed">
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
