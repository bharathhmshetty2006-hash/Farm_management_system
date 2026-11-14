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
$buyer_name = '';
$quantity_kg = '';
$price_per_kg = '';
$sale_date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crop_id = intval($_POST['crop_id'] ?? 0);
    $buyer_name = trim($_POST['buyer_name'] ?? '');
    $quantity_kg = floatval($_POST['quantity_kg'] ?? 0);
    $price_per_kg = floatval($_POST['price_per_kg'] ?? 0);
    $sale_date = trim($_POST['sale_date'] ?? '');

    if (empty($crop_id) || empty($buyer_name) || $quantity_kg <= 0 || $price_per_kg <= 0 || empty($sale_date)) {
        $error = 'Crop, Buyer Name, Quantity (>0 kg), Price per kg (>0), and Sale Date are required.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO sales (crop_id, buyer_name, quantity_kg, price_per_kg, sale_date) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$crop_id, $buyer_name, $quantity_kg, $price_per_kg, $sale_date])) {
            $total_income = $quantity_kg * $price_per_kg;
            
            // Check if sold quantity exceeds total production
            $prod_check = $pdo->prepare("SELECT COALESCE(SUM(quantity_kg), 0) FROM production WHERE crop_id = ?");
            $prod_check->execute([$crop_id]);
            $total_produced = $prod_check->fetchColumn();

            $sold_check = $pdo->prepare("SELECT COALESCE(SUM(quantity_kg), 0) FROM sales WHERE crop_id = ?");
            $sold_check->execute([$crop_id]);
            $total_sold = $sold_check->fetchColumn();

            if ($total_sold > $total_produced) {
                set_flash('warning', "Sale of " . format_currency($total_income) . " recorded! Note: Total quantity sold ($total_sold kg) exceeds recorded harvest production ($total_produced kg).");
            } else {
                set_flash('success', "Sale transaction of " . format_currency($total_income) . " recorded successfully!");
            }

            redirect('list.php');
        } else {
            $error = 'Failed to record sale transaction.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-coins text-success me-2"></i>Record Crop Sale</h5>
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
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>No crops found! Please <a href="../crop/add.php" class="fw-bold text-dark">Add a Crop</a> before recording sales.
                    </div>
                <?php else: ?>
                    <form action="add.php" method="POST" class="needs-validation" novalidate>
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
                            <input type="text" name="buyer_name" class="form-control" placeholder="e.g. APMC Hassan Wholesaler, Local Grain Merchant" value="<?= htmlspecialchars($buyer_name) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quantity Sold (in kg) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" id="quantity_kg" name="quantity_kg" class="form-control" placeholder="e.g. 1000.00" value="<?= htmlspecialchars($quantity_kg) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price per kg (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" id="price_per_kg" name="price_per_kg" class="form-control" placeholder="e.g. 24.50" value="<?= htmlspecialchars($price_per_kg) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Computed Total Revenue / Income</label>
                            <input type="text" id="total_sales_calc" class="form-control bg-light fw-bold text-success fs-5" value="₹0.00" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Sale Date <span class="text-danger">*</span></label>
                            <input type="date" name="sale_date" class="form-control" value="<?= htmlspecialchars($sale_date) ?>" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-farm flex-fill fw-semibold">
                                <i class="fa-solid fa-save me-1"></i> Save Sale Record
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
