<?php
require_once __DIR__ . '/../../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM seed WHERE seed_id = ?");
$stmt->execute([$id]);
$seed = $stmt->fetch();

if (!$seed) {
    set_flash('danger', 'Seed record not found.');
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
    $seed_type = trim($_POST['seed_type'] ?? '');
    $quantity = floatval($_POST['quantity'] ?? 0);
    $supplier = trim($_POST['supplier'] ?? '');
    $cost = floatval($_POST['cost'] ?? 0);

    if (empty($crop_id) || empty($seed_type) || $quantity <= 0 || $cost < 0) {
        $error = 'Crop, Seed Type, valid Quantity (>0) and Cost (>=0) are required.';
    } else {
        $update_stmt = $pdo->prepare("UPDATE seed SET crop_id = ?, seed_type = ?, quantity = ?, supplier = ?, cost = ? WHERE seed_id = ?");
        if ($update_stmt->execute([$crop_id, $seed_type, $quantity, $supplier, $cost, $id])) {
            set_flash('success', "Seed record '$seed_type' updated successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to update seed record.';
        }
    }
} else {
    $crop_id = $seed['crop_id'];
    $seed_type = $seed['seed_type'];
    $quantity = $seed['quantity'];
    $supplier = $seed['supplier'];
    $cost = $seed['cost'];
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Seed Record</h5>
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
                        <label class="form-label fw-semibold">Seed Type / Variety <span class="text-danger">*</span></label>
                        <input type="text" name="seed_type" class="form-control" value="<?= htmlspecialchars($seed_type) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantity (kg / bags) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" value="<?= htmlspecialchars($quantity) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Cost (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="cost" class="form-control" value="<?= htmlspecialchars($cost) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Supplier / Depot Name</label>
                        <input type="text" name="supplier" class="form-control" value="<?= htmlspecialchars($supplier) ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Update Seed Record
                        </button>
                        <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
