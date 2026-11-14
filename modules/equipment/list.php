<?php
require_once __DIR__ . '/../../includes/header.php';

$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare("
        SELECT e.*, f.name as farmer_name
        FROM equipment e
        JOIN farmer f ON e.farmer_id = f.farmer_id
        WHERE e.equipment_name LIKE ? OR e.type LIKE ? OR f.name LIKE ?
        ORDER BY e.equipment_id DESC
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT e.*, f.name as farmer_name
        FROM equipment e
        JOIN farmer f ON e.farmer_id = f.farmer_id
        ORDER BY e.equipment_id DESC
    ");
}
$equipments = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-tractor text-primary me-2"></i>Equipment Management</h3>
        <p class="text-muted small mb-0">Maintain records of farm machinery, tools, purchase dates, and capital costs.</p>
    </div>
    <a href="add.php" class="btn btn-farm shadow-sm">
        <i class="fa-solid fa-plus-circle me-1"></i> Add Equipment
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <form method="GET" action="list.php" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="tableSearchInput" name="search" class="form-control bg-light border-start-0" placeholder="Search equipment name, type or farmer..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <?php if (!empty($search)): ?>
                        <a href="list.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-6 text-md-end text-muted small">
                Total Machinery Listed: <strong><?= count($equipments) ?></strong>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-searchable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Equipment Name</th>
                        <th>Type / Category</th>
                        <th>Owner (Farmer)</th>
                        <th>Purchase Date</th>
                        <th>Cost</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($equipments)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No equipment records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($equipments as $eq): ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?= $eq['equipment_id'] ?></td>
                                <td class="fw-bold text-dark">
                                    <i class="fa-solid fa-gear text-primary me-2"></i><?= htmlspecialchars($eq['equipment_name']) ?>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($eq['type']) ?></span></td>
                                <td class="fw-semibold text-dark">
                                    <i class="fa-solid fa-user me-1 text-muted small"></i><?= htmlspecialchars($eq['farmer_name']) ?>
                                </td>
                                <td class="small text-muted"><?= format_date($eq['purchase_date']) ?></td>
                                <td class="fw-bold text-dark"><?= format_currency($eq['cost']) ?></td>
                                <td class="text-end pe-4">
                                    <a href="edit.php?id=<?= $eq['equipment_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Equipment">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $eq['equipment_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item="Equipment '<?= htmlspecialchars($eq['equipment_name']) ?>'" title="Delete Equipment">
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
