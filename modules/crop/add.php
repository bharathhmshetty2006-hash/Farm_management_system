<?php
require_once __DIR__ . '/../../includes/header.php';

// Fetch farmers for dropdown
$farmers = $pdo->query("SELECT farmer_id, name FROM farmer ORDER BY name ASC")->fetchAll();

$error = '';
$farmer_id = '';
$crop_name = '';
$season = 'Kharif';
$planting_date = date('Y-m-d');
$harvest_date = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = intval($_POST['farmer_id'] ?? 0);
    $crop_name = trim($_POST['crop_name'] ?? '');
    $season = trim($_POST['season'] ?? '');
    $planting_date = trim($_POST['planting_date'] ?? '');
    $harvest_date = trim($_POST['harvest_date'] ?? '');

    if (empty($farmer_id) || empty($crop_name) || empty($planting_date)) {
        $error = 'Farmer, Crop Name, and Planting Date are required.';
    } elseif (!empty($harvest_date) && strtotime($harvest_date) < strtotime($planting_date)) {
        $error = 'Harvest Date cannot be earlier than Planting Date.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO crop (farmer_id, crop_name, season, planting_date, harvest_date) VALUES (?, ?, ?, ?, ?)");
        $h_date = !empty($harvest_date) ? $harvest_date : NULL;
        if ($stmt->execute([$farmer_id, $crop_name, $season, $planting_date, $h_date])) {
            set_flash('success', "Crop '$crop_name' added successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to insert crop record.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-seedling text-success me-2"></i>Add New Crop</h5>
                <a href="list.php" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($farmers)): ?>
                    <div class="alert alert-warning py-3">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>No farmers found in the system! You must <a href="../farmer/add.php" class="fw-bold text-dark">Add a Farmer</a> before creating crop records.
                    </div>
                <?php else: ?>
                    <form action="add.php" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Farmer <span class="text-danger">*</span></label>
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
                            <label class="form-label fw-semibold">Crop Name <span class="text-danger">*</span></label>
                            <input type="text" name="crop_name" class="form-control" placeholder="e.g. Paddy (Rice), Sugarcane, Maize" value="<?= htmlspecialchars($crop_name) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Growing Season</label>
                            <select name="season" class="form-select">
                                <option value="Kharif" <?= ($season === 'Kharif') ? 'selected' : '' ?>>Kharif (Monsoon / Summer)</option>
                                <option value="Rabi" <?= ($season === 'Rabi') ? 'selected' : '' ?>>Rabi (Winter)</option>
                                <option value="Zaid" <?= ($season === 'Zaid') ? 'selected' : '' ?>>Zaid (Spring / Summer)</option>
                                <option value="Annual" <?= ($season === 'Annual') ? 'selected' : '' ?>>Annual (Year-round)</option>
                                <option value="Perennial" <?= ($season === 'Perennial') ? 'selected' : '' ?>>Perennial</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Planting Date <span class="text-danger">*</span></label>
                                <input type="date" name="planting_date" class="form-control" value="<?= htmlspecialchars($planting_date) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Harvest Date (Expected/Actual)</label>
                                <input type="date" name="harvest_date" class="form-control" value="<?= htmlspecialchars($harvest_date) ?>">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-farm flex-fill fw-semibold">
                                <i class="fa-solid fa-save me-1"></i> Save Crop Record
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
