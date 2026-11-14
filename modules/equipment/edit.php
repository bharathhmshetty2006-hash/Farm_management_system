<?php
require_once __DIR__ . '/../../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM equipment WHERE equipment_id = ?");
$stmt->execute([$id]);
$equipment = $stmt->fetch();

if (!$equipment) {
    set_flash('danger', 'Equipment record not found.');
    redirect('list.php');
}

$farmers = $pdo->query("SELECT farmer_id, name FROM farmer ORDER BY name ASC")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = intval($_POST['farmer_id'] ?? 0);
    $equipment_name = trim($_POST['equipment_name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $purchase_date = trim($_POST['purchase_date'] ?? '');
    $cost = floatval($_POST['cost'] ?? 0);

    if (empty($farmer_id) || empty($equipment_name) || empty($purchase_date) || $cost < 0) {
        $error = 'Farmer, Equipment Name, Purchase Date, and valid Cost are required.';
    } else {
        $update_stmt = $pdo->prepare("UPDATE equipment SET farmer_id = ?, equipment_name = ?, type = ?, purchase_date = ?, cost = ? WHERE equipment_id = ?");
        if ($update_stmt->execute([$farmer_id, $equipment_name, $type, $purchase_date, $cost, $id])) {
            set_flash('success', "Equipment '$equipment_name' updated successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to update equipment record.';
        }
    }
} else {
    $farmer_id = $equipment['farmer_id'];
    $equipment_name = $equipment['equipment_name'];
    $type = $equipment['type'];
    $purchase_date = $equipment['purchase_date'];
    $cost = $equipment['cost'];
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Equipment Record</h5>
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
                        <label class="form-label fw-semibold">Select Farmer (Owner) <span class="text-danger">*</span></label>
                        <select name="farmer_id" class="form-select" required>
                            <option value="">-- Select Farmer --</option>
                            <?php foreach ($farmers as $f): ?>
                                <option value="<?= $f['farmer_id'] ?>" <?= ($farmer_id == $f['farmer_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f['name']) ?> (#<?= $f['farmer_id'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Equipment Name / Model <span class="text-danger">*</span></label>
                        <input type="text" name="equipment_name" class="form-control" value="<?= htmlspecialchars($equipment_name) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Equipment Type</label>
                        <select name="type" class="form-select">
                            <option value="Tractor" <?= ($type === 'Tractor') ? 'selected' : '' ?>>Tractor</option>
                            <option value="Tillage" <?= ($type === 'Tillage') ? 'selected' : '' ?>>Tillage / Rotavator / Power Tiller</option>
                            <option value="Irrigation System" <?= ($type === 'Irrigation System') ? 'selected' : '' ?>>Irrigation System</option>
                            <option value="Sprayer" <?= ($type === 'Sprayer') ? 'selected' : '' ?>>Sprayer</option>
                            <option value="Harvester" <?= ($type === 'Harvester') ? 'selected' : '' ?>>Harvester</option>
                            <option value="Hand Tools" <?= ($type === 'Hand Tools') ? 'selected' : '' ?>>Hand Tools</option>
                            <option value="Other" <?= ($type === 'Other') ? 'selected' : '' ?>>Other Machinery</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control" value="<?= htmlspecialchars($purchase_date) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cost (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="cost" class="form-control" value="<?= htmlspecialchars($cost) ?>" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Update Equipment Record
                        </button>
                        <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
