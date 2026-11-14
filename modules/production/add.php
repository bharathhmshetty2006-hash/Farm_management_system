<?php
require_once __DIR__ . '/../../includes/header.php';

$crops = $pdo->query("
    SELECT c.crop_id, c.crop_name, f.name as farmer_name
    FROM crop c
    JOIN farmer f ON c.farmer_id = f.farmer_id
    ORDER BY c.crop_name ASC
")->fetchAll();

$error = '';
$crop_id = '';
$quantity_kg = '';
$production_date = date('Y-m-d');
$notes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crop_id = intval($_POST['crop_id'] ?? 0);
    $quantity_kg = floatval($_POST['quantity_kg'] ?? 0);
    $production_date = trim($_POST['production_date'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($crop_id) || $quantity_kg <= 0 || empty($production_date)) {
        $error = 'Crop, Production Quantity (>0 kg), and Production Date are required.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO production (crop_id, quantity_kg, production_date, notes) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$crop_id, $quantity_kg, $production_date, $notes])) {
            set_flash('success', "Production yield of " . number_format($quantity_kg, 2) . " kg recorded successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to record production.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-boxes-stacked text-teal me-2"></i>Record Harvest Production</h5>
                <a href="list.php" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($crops)): ?>
                    <div class="alert alert-warning py-3">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>No crops found! Please <a href="../crop/add.php" class="fw-bold text-dark">Add a Crop</a> before recording harvest yield.
                    </div>
                <?php else: ?>
                    <form action="add.php" method="POST" class="needs-validation" novalidate>
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
                                <label class="form-label fw-semibold">Quantity Harvested (in Kilograms - kg) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="quantity_kg" class="form-control" placeholder="e.g. 1500.00" value="<?= htmlspecialchars($quantity_kg) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Production / Harvest Date <span class="text-danger">*</span></label>
                                <input type="date" name="production_date" class="form-control" value="<?= htmlspecialchars($production_date) ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Production Notes / Grain Quality</label>
                            <input type="text" name="notes" class="form-control" placeholder="e.g. Grade A Sona Masoori Paddy yield" value="<?= htmlspecialchars($notes) ?>">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-farm flex-fill fw-semibold">
                                <i class="fa-solid fa-save me-1"></i> Save Production Record
                            </button>
                            <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
