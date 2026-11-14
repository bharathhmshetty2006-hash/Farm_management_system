<?php
require_once __DIR__ . '/../../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM labour WHERE labour_id = ?");
$stmt->execute([$id]);
$labour = $stmt->fetch();

if (!$labour) {
    set_flash('danger', 'Labour record not found.');
    redirect('list.php');
}

$crops = $pdo->query("
    SELECT c.crop_id, c.crop_name, f.name as farmer_name
    FROM crop c
    JOIN farmer f ON c.farmer_id = f.farmer_id
    ORDER BY c.crop_name ASC
")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crop_id = intval($_POST['crop_id'] ?? 0);
    $labour_name = trim($_POST['labour_name'] ?? '');
    $working_days = intval($_POST['working_days'] ?? 0);
    $daily_wage = floatval($_POST['daily_wage'] ?? 0);

    if (empty($crop_id) || empty($labour_name) || $working_days <= 0 || $daily_wage <= 0) {
        $error = 'Crop, Worker Name, Working Days (>0), and Daily Wage (>0) are required.';
    } else {
        $update_stmt = $pdo->prepare("UPDATE labour SET crop_id = ?, labour_name = ?, working_days = ?, daily_wage = ? WHERE labour_id = ?");
        if ($update_stmt->execute([$crop_id, $labour_name, $working_days, $daily_wage, $id])) {
            set_flash('success', "Labour record '$labour_name' updated successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to update labour record.';
        }
    }
} else {
    $crop_id = $labour['crop_id'];
    $labour_name = $labour['labour_name'];
    $working_days = $labour['working_days'];
    $daily_wage = $labour['daily_wage'];
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Labour Record</h5>
                <a href="list.php" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="edit.php?id=<?= $id ?>" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Associated Crop <span class="text-danger">*</span></label>
                        <select name="crop_id" class="form-select" required>
                            <option value="">-- Select Crop --</option>
                            <?php foreach ($crops as $c): ?>
                                <option value="<?= $c['crop_id'] ?>" <?= ($crop_id == $c['crop_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['crop_name']) ?> (Farmer: <?= htmlspecialchars($c['farmer_name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Worker / Team Name <span class="text-danger">*</span></label>
                        <input type="text" name="labour_name" class="form-control" value="<?= htmlspecialchars($labour_name) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Working Days <span class="text-danger">*</span></label>
                            <input type="number" min="1" id="working_days" name="working_days" class="form-control" value="<?= htmlspecialchars($working_days) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Daily Wage per Worker (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" id="daily_wage" name="daily_wage" class="form-control" value="<?= htmlspecialchars($daily_wage) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Computed Total Wage</label>
                        <input type="text" id="total_wage_calc" class="form-control bg-light fw-bold text-danger fs-5" value="<?= format_currency($working_days * $daily_wage) ?>" readonly>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Update Labour Record
                        </button>
                        <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
