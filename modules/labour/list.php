<?php
require_once __DIR__ . '/../../includes/header.php';

$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare("
        SELECT l.*, (l.working_days * l.daily_wage) as total_wage, c.crop_name, f.name as farmer_name
        FROM labour l
        JOIN crop c ON l.crop_id = c.crop_id
        JOIN farmer f ON c.farmer_id = f.farmer_id
        WHERE l.labour_name LIKE ? OR c.crop_name LIKE ? OR f.name LIKE ?
        ORDER BY l.labour_id DESC
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT l.*, (l.working_days * l.daily_wage) as total_wage, c.crop_name, f.name as farmer_name
        FROM labour l
        JOIN crop c ON l.crop_id = c.crop_id
        JOIN farmer f ON c.farmer_id = f.farmer_id
        ORDER BY l.labour_id DESC
    ");
}
$labours = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-person-digging text-purple me-2"></i>Labour Management</h3>
        <p class="text-muted small mb-0">Manage farm workers, working days, daily wages, and calculated total labor costs.</p>
    </div>
    <a href="add.php" class="btn btn-farm shadow-sm">
        <i class="fa-solid fa-plus-circle me-1"></i> Add Labour Record
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <form method="GET" action="list.php" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="tableSearchInput" name="search" class="form-control bg-light border-start-0" placeholder="Search by worker name, crop or farmer..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <?php if (!empty($search)): ?>
                        <a href="list.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-6 text-md-end text-muted small">
                Total Labour Records: <strong><?= count($labours) ?></strong>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-searchable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Worker / Team Name</th>
                        <th>Crop & Farmer</th>
                        <th>Working Days</th>
                        <th>Daily Wage</th>
                        <th>Total Wage (Days × Wage)</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($labours)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No labour records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($labours as $labour): ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?= $labour['labour_id'] ?></td>
                                <td class="fw-bold text-dark">
                                    <i class="fa-solid fa-user-gear me-2 text-purple"></i><?= htmlspecialchars($labour['labour_name']) ?>
                                </td>
                                <td>
                                    <span class="fw-semibold text-success"><?= htmlspecialchars($labour['crop_name']) ?></span>
                                    <span class="small text-muted d-block">(<?= htmlspecialchars($labour['farmer_name']) ?>)</span>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= $labour['working_days'] ?> Days</span></td>
                                <td><?= format_currency($labour['daily_wage']) ?>/day</td>
                                <td class="fw-bold text-danger"><?= format_currency($labour['total_wage']) ?></td>
                                <td class="text-end pe-4">
                                    <a href="edit.php?id=<?= $labour['labour_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Labour">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $labour['labour_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item="Labour '<?= htmlspecialchars($labour['labour_name']) ?>'" title="Delete Labour">
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
