<?php
require_once __DIR__ . '/../../includes/header.php';

// Fetch farmers with search filter
$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT f.*, COUNT(c.crop_id) as total_crops FROM farmer f LEFT JOIN crop c ON f.farmer_id = c.farmer_id WHERE f.name LIKE ? OR f.phone LIKE ? OR f.address LIKE ? GROUP BY f.farmer_id ORDER BY f.farmer_id DESC");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT f.*, COUNT(c.crop_id) as total_crops FROM farmer f LEFT JOIN crop c ON f.farmer_id = c.farmer_id GROUP BY f.farmer_id ORDER BY f.farmer_id DESC");
}
$farmers = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-group text-success me-2"></i>Farmer Management</h3>
        <p class="text-muted small mb-0">Maintain farmer records, contact details, and registered crops.</p>
    </div>
    <a href="add.php" class="btn btn-farm shadow-sm">
        <i class="fa-solid fa-plus-circle me-1"></i> Add New Farmer
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <form method="GET" action="list.php" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="tableSearchInput" name="search" class="form-control bg-light border-start-0" placeholder="Search by name, phone or address..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <?php if (!empty($search)): ?>
                        <a href="list.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-6 text-md-end text-muted small">
                Total Farmers Recorded: <strong><?= count($farmers) ?></strong>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-searchable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Farmer Name</th>
                        <th>Phone Number</th>
                        <th>Address / Location</th>
                        <th>Crops</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($farmers)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No farmer records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($farmers as $farmer): ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?= $farmer['farmer_id'] ?></td>
                                <td class="fw-semibold text-dark">
                                    <i class="fa-solid fa-circle-user text-success me-2"></i><?= htmlspecialchars($farmer['name']) ?>
                                </td>
                                <td>
                                    <a href="tel:<?= htmlspecialchars($farmer['phone']) ?>" class="text-decoration-none text-dark">
                                        <i class="fa-solid fa-phone text-muted me-1 small"></i><?= htmlspecialchars($farmer['phone']) ?>
                                    </a>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($farmer['address'] ?: 'N/A') ?></td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">
                                        <?= $farmer['total_crops'] ?> Crop(s)
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="edit.php?id=<?= $farmer['farmer_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Farmer">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $farmer['farmer_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item="Farmer '<?= htmlspecialchars($farmer['name']) ?>'" title="Delete Farmer">
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
