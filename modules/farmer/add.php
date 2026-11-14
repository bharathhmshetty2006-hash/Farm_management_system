<?php
require_once __DIR__ . '/../../includes/header.php';

$error = '';
$name = '';
$phone = '';
$address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Server-side validation
    if (empty($name) || empty($phone)) {
        $error = 'Farmer Name and Phone Number are required fields.';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = 'Please enter a valid 10-digit phone number.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO farmer (name, phone, address) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $phone, $address])) {
            set_flash('success', "Farmer '$name' added successfully!");
            redirect('list.php');
        } else {
            $error = 'Failed to insert farmer record into database.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-plus text-success me-2"></i>Add New Farmer</h5>
                <a href="list.php" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="add.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Farmer Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Ramesh Kumar" value="<?= htmlspecialchars($name) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="10-digit mobile number (e.g. 9845012345)" value="<?= htmlspecialchars($phone) ?>" maxlength="10" required>
                        <div class="form-text">Must be a valid 10-digit phone number.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Address / Location</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Village, Taluk, District..."><?= htmlspecialchars($address) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-farm flex-fill fw-semibold">
                            <i class="fa-solid fa-save me-1"></i> Save Farmer Record
                        </button>
                        <a href="list.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
