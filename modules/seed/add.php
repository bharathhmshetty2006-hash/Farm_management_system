<?php
require_once __DIR__ . '/../../includes/header.php';

// Fetch crops for dropdown
$crops = $pdo->query("
    SELECT c.crop_id, c.crop_name, f.name as farmer_name
    FROM crop c
    JOIN farmer f ON c.farmer_id = f.farmer_id
    ORDER BY c.crop_name ASC
")->fetchAll();

$error = '';
$crop_id = '';
$seed_type = '';
$quantity = '';
$supplier = '';
$cost = '';
$auto_expense = 1; // Default checked

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crop_id = intval($_POST['crop_id'] ?? 0);
    $seed_type = trim($_POST['seed_type'] ?? '');
    $quantity = floatval($_POST['quantity'] ?? 0);
    $supplier = trim($_POST['supplier'] ?? '');
    $cost = floatval($_POST['cost'] ?? 0);
    $auto_expense = isset($_POST['auto_expense']) ? 1 : 0;

    if (empty($crop_id) || empty($seed_type) || $quantity <= 0 || $cost < 0) {
        $error = 'Crop, Seed Type, valid Quantity (>0) and Cost (>=0) are required.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO seed (crop_id, seed_type, quantity, supplier, cost) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$crop_id, $seed_type, $quantity, $supplier, $cost])) {
            
            // Auto-add expense entry if checked
            if ($auto_expense && $cost > 0) {
                $exp_stmt = $pdo->prepare("INSERT INTO expense (crop_id, category, amount, expense_date, description) VALUES (?, 'Seeds', ?, CURDATE(), ?)");
                $exp_stmt->execute([$crop_id, $cost, "Auto-recorded: Seed purchase ($seed_type - $supplier)"]);
            }

            set_flash('success', "Seed record '$seed_type' added successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to insert seed record.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-jar text-warning me-2"></i>Add Seed Record</h5>
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
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>No crops found! Please <a href="../crop/add.php" class="fw-bold text-dark">Add a Crop</a> before recording seed details.
                    </div>
                <?php else: ?>
                    <form action="add.php" method="POST" class="needs-validation" novalidate>
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
                            <input type="text" name="seed_type" class="form-control" placeholder="e.g. Sona Masoori Hybrid, Bt Cotton" value="<?= htmlspecialchars($seed_type) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quantity (kg / bags) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" placeholder="0.00" value="<?= htmlspecialchars($quantity) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Total Cost (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="cost" class="form-control" placeholder="0.00" value="<?= htmlspecialchars($cost) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Supplier / Depot Name</label>
                            <input type="text" name="supplier" class="form-control" placeholder="e.g. Raitha Samparka Kendra, Syngenta" value="<?= htmlspecialchars($supplier) ?>">
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="auto_expense" value="1" id="autoExpenseCheck" <?= ($auto_expense) ? 'checked' : '' ?>>
                            <label class="form-check-label text-muted small" for="autoExpenseCheck">
                                <i class="fa-solid fa-receipt text-danger me-1"></i> Automatically add cost to <strong>Expense Table</strong> under category 'Seeds'
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-farm flex-fill fw-semibold">
                                <i class="fa-solid fa-save me-1"></i> Save Seed Record
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
