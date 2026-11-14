<?php
require_once __DIR__ . '/../../includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM farmer WHERE farmer_id = ?");
$stmt->execute([$id]);
$farmer = $stmt->fetch();

if (!$farmer) {
    set_flash('danger', 'Farmer record not found.');
    redirect('list.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($name) || empty($phone)) {
        $error = 'Farmer Name and Phone Number are required.';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = 'Please enter a valid 10-digit phone number.';
    } else {
        $update_stmt = $pdo->prepare("UPDATE farmer SET name = ?, phone = ?, address = ? WHERE farmer_id = ?");
        if ($update_stmt->execute([$name, $phone, $address, $id])) {
            set_flash('success', "Farmer '$name' updated successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to update farmer record.';
        }
    }
} else {
    $name = $farmer['name'];
    $phone = $farmer['phone'];
    $address = $farmer['address'];
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-pen text-primary me-2"></i>Edit Farmer Details</h5>
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
                        <label class="form-label fw-semibold">Farmer Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" maxlength="10" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Address / Location</label>
                        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($address) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                            <i class="fa-solid fa-check me-1"></i> Update Farmer
                        </button>
                        <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
