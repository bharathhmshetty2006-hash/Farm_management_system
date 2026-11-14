<?php
require_once __DIR__ . '/../../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM sales WHERE sale_id = ?");
$stmt->execute([$id]);
$sale = $stmt->fetch();

if (!$sale) {
    set_flash('danger', 'Sale record not found.');
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
    $buyer_name = trim($_POST['buyer_name'] ?? '');
    $quantity_kg = floatval($_POST['quantity_kg'] ?? 0);
    $price_per_kg = floatval($_POST['price_per_kg'] ?? 0);
    $sale_date = trim($_POST['sale_date'] ?? '');

    if (empty($crop_id) || empty($buyer_name) || $quantity_kg <= 0 || $price_per_kg <= 0 || empty($sale_date)) {
        $error = 'Crop, Buyer Name, Quantity (>0 kg), Price per kg (>0), and Sale Date are required.';
    } else {
        $update_stmt = $pdo->prepare("UPDATE sales SET crop_id = ?, buyer_name = ?, quantity_kg = ?, price_per_kg = ?, sale_date = ? WHERE sale_id = ?");
        if ($update_stmt->execute([$crop_id, $buyer_name, $quantity_kg, $price_per_kg, $sale_date, $id])) {
            set_flash('success', "Sale record updated successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to update sale record.';
        }
    }
} else {
    $crop_id = $sale['crop_id'];
    $buyer_name = $sale['buyer_name'];
    $quantity_kg = $sale['quantity_kg'];
    $price_per_kg = $sale['price_per_kg'];
    $sale_date = $sale['sale_date'];
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Sale Transaction</h5>
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
                        <label class="form-label fw-semibold">Select Sold Crop <span class="text-danger">*</span></label>
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
                        <label class="form-label fw-semibold">Buyer / Market Name <span class="text-danger">*</span></label>
                        <input type="text" name="buyer_name" class="form-control" value="<?= htmlspecialchars($buyer_name) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantity Sold (in kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" id="quantity_kg" name="quantity_kg" class="form-control" value="<?= htmlspecialchars($quantity_kg) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Price per kg (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" id="price_per_kg" name="price_per_kg" class="form-control" value="<?= htmlspecialchars($price_per_kg) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Computed Total Revenue / Income</label>
                        <input type="text" id="total_sales_calc" class="form-control bg-light fw-bold text-success fs-5" value="<?= format_currency($quantity_kg * $price_per_kg) ?>" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Sale Date <span class="text-danger">*</span></label>
                        <input type="date" name="sale_date" class="form-control" value="<?= htmlspecialchars($sale_date) ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Update Sale Record
                        </button>
                        <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
