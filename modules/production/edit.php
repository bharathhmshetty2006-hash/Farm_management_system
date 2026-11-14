<?php
require_once __DIR__ . '/../../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM production WHERE production_id = ?");
$stmt->execute([$id]);
$production = $stmt->fetch();

if (!$production) {
    set_flash('danger', 'Production record not found.');
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
    $quantity_kg = floatval($_POST['quantity_kg'] ?? 0);
    $production_date = trim($_POST['production_date'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($crop_id) || $quantity_kg <= 0 || empty($production_date)) {
        $error = 'Crop, Production Quantity (>0 kg), and Production Date are required.';
    } else {
        $update_stmt = $pdo->prepare("UPDATE production SET crop_id = ?, quantity_kg = ?, production_date = ?, notes = ? WHERE production_id = ?");
        if ($update_stmt->execute([$crop_id, $quantity_kg, $production_date, $notes, $id])) {
            set_flash('success', "Production record updated successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to update production record.';
        }
    }
} else {
    $crop_id = $production['crop_id'];
    $quantity_kg = $production['quantity_kg'];
    $production_date = $production['production_date'];
    $notes = $production['notes'];
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Production Record</h5>
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
                        <label class="form-label fw-semibold">Select Harvested Crop <span class="text-danger">*</span></label>
                        <select name="crop_id" class="form-select" required>
                            <option value="">-- Select Crop --</option>
                            <?php foreach ($crops as $c): ?>
                                <option value="<?= $c['crop_id'] ?>" <?= ($crop_id == $c['crop_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['crop_name']) ?> (Farmer: <?= htmlspecialchars($c['farmer_name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantity Harvested (in kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="quantity_kg" class="form-control" value="<?= htmlspecialchars($quantity_kg) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Production / Harvest Date <span class="text-danger">*</span></label>
                            <input type="date" name="production_date" class="form-control" value="<?= htmlspecialchars($production_date) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Production Notes / Grain Quality</label>
                        <input type="text" name="notes" class="form-control" value="<?= htmlspecialchars($notes) ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Update Production Record
                        </button>
                        <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
