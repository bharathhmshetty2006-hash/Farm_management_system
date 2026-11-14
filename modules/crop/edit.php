<?php
require_once __DIR__ . '/../../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM crop WHERE crop_id = ?");
$stmt->execute([$id]);
$crop = $stmt->fetch();

if (!$crop) {
    set_flash('danger', 'Crop record not found.');
    redirect('list.php');
}

$farmers = $pdo->query("SELECT farmer_id, name FROM farmer ORDER BY name ASC")->fetchAll();
$error = '';

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
        $h_date = !empty($harvest_date) ? $harvest_date : NULL;
        $update_stmt = $pdo->prepare("UPDATE crop SET farmer_id = ?, crop_name = ?, season = ?, planting_date = ?, harvest_date = ? WHERE crop_id = ?");
        if ($update_stmt->execute([$farmer_id, $crop_name, $season, $planting_date, $h_date, $id])) {
            set_flash('success', "Crop '$crop_name' updated successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to update crop record.';
        }
    }
} else {
    $farmer_id = $crop['farmer_id'];
    $crop_name = $crop['crop_name'];
    $season = $crop['season'];
    $planting_date = $crop['planting_date'];
    $harvest_date = $crop['harvest_date'];
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Crop Record</h5>
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
                        <input type="text" name="crop_name" class="form-control" value="<?= htmlspecialchars($crop_name) ?>" required>
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
                            <label class="form-label fw-semibold">Harvest Date</label>
                            <input type="date" name="harvest_date" class="form-control" value="<?= htmlspecialchars($harvest_date) ?>">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Update Crop Record
                        </button>
                        <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
