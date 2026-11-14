<?php
require_once __DIR__ . '/../../includes/header.php';

$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare("
        SELECT c.*, f.name as farmer_name
        FROM crop c
        JOIN farmer f ON c.farmer_id = f.farmer_id
        WHERE c.crop_name LIKE ? OR f.name LIKE ? OR c.season LIKE ?
        ORDER BY c.crop_id DESC
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT c.*, f.name as farmer_name
        FROM crop c
        JOIN farmer f ON c.farmer_id = f.farmer_id
        ORDER BY c.crop_id DESC
    ");
}
$crops = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-seedling text-success me-2"></i>Crop Management</h3>
        <p class="text-muted small mb-0">Track crops planted by farmers, growing seasons, and harvest dates.</p>
    </div>
    <a href="add.php" class="btn btn-farm shadow-sm">
        <i class="fa-solid fa-plus-circle me-1"></i> Add New Crop
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <form method="GET" action="list.php" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="tableSearchInput" name="search" class="form-control bg-light border-start-0" placeholder="Search by crop, farmer name or season..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <?php if (!empty($search)): ?>
                        <a href="list.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-6 text-md-end text-muted small">
                Total Crops Recorded: <strong><?= count($crops) ?></strong>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-searchable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Crop Name</th>
                        <th>Farmer Name</th>
                        <th>Season</th>
                        <th>Planting Date</th>
                        <th>Harvest Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($crops)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No crop records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($crops as $crop): ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?= $crop['crop_id'] ?></td>
                                <td class="fw-bold text-success">
                                    <i class="fa-solid fa-plant-wilt me-2"></i><?= htmlspecialchars($crop['crop_name']) ?>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <i class="fa-solid fa-user me-1 text-muted small"></i><?= htmlspecialchars($crop['farmer_name']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal px-2 py-1">
                                        <?= htmlspecialchars($crop['season']) ?>
                                    </span>
                                </td>
                                <td class="small text-muted"><?= format_date($crop['planting_date']) ?></td>
                                <td class="small text-muted"><?= format_date($crop['harvest_date']) ?></td>
                                <td class="text-end pe-4">
                                    <a href="edit.php?id=<?= $crop['crop_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Crop">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $crop['crop_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item="Crop '<?= htmlspecialchars($crop['crop_name']) ?>'" title="Delete Crop">
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
