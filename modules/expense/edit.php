<?php
require_once __DIR__ . '/../../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM expense WHERE expense_id = ?");
$stmt->execute([$id]);
$expense = $stmt->fetch();

if (!$expense) {
    set_flash('danger', 'Expense record not found.');
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
    $category = trim($_POST['category'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $expense_date = trim($_POST['expense_date'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($crop_id) || empty($category) || $amount <= 0 || empty($expense_date)) {
        $error = 'Crop, Category, Amount (>0), and Expense Date are required.';
    } else {
        $update_stmt = $pdo->prepare("UPDATE expense SET crop_id = ?, category = ?, amount = ?, expense_date = ?, description = ? WHERE expense_id = ?");
        if ($update_stmt->execute([$crop_id, $category, $amount, $expense_date, $description, $id])) {
            set_flash('success', "Expense record updated successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to update expense record.';
        }
    }
} else {
    $crop_id = $expense['crop_id'];
    $category = $expense['category'];
    $amount = $expense['amount'];
    $expense_date = $expense['expense_date'];
    $description = $expense['description'];
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Expense Record</h5>
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
                        <label class="form-label fw-semibold">Select Crop <span class="text-danger">*</span></label>
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
                        <label class="form-label fw-semibold">Expense Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="Seeds" <?= ($category === 'Seeds') ? 'selected' : '' ?>>Seeds</option>
                            <option value="Labour" <?= ($category === 'Labour') ? 'selected' : '' ?>>Labour</option>
                            <option value="Transportation" <?= ($category === 'Transportation') ? 'selected' : '' ?>>Transportation</option>
                            <option value="Other" <?= ($category === 'Other') ? 'selected' : '' ?>>Other (Pesticides, Machinery Fuel, etc.)</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($amount) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="<?= htmlspecialchars($expense_date) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Description / Remarks</label>
                        <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($description) ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Update Expense Record
                        </button>
                        <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
