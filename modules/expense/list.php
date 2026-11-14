<?php
require_once __DIR__ . '/../../includes/header.php';

$search = trim($_GET['search'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

$sql = "
    SELECT e.*, c.crop_name, f.name as farmer_name
    FROM expense e
    JOIN crop c ON e.crop_id = c.crop_id
    JOIN farmer f ON c.farmer_id = f.farmer_id
    WHERE 1=1
";
$params = [];

if (!empty($category_filter)) {
    $sql .= " AND e.category = ?";
    $params[] = $category_filter;
}

if (!empty($search)) {
    $sql .= " AND (c.crop_name LIKE ? OR f.name LIKE ? OR e.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY e.expense_date DESC, e.expense_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll();

// Total Expenses Sum
$total_expense_sum = array_sum(array_column($expenses, 'amount'));
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-receipt text-danger me-2"></i>Expense Management</h3>
        <p class="text-muted small mb-0">Track all operational costs including seeds, labour, transport, and equipment maintenance.</p>
    </div>
    <a href="add.php" class="btn btn-farm shadow-sm">
        <i class="fa-solid fa-plus-circle me-1"></i> Record New Expense
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <form method="GET" action="list.php" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="tableSearchInput" name="search" class="form-control bg-light border-start-0" placeholder="Search by crop, farmer or description..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- All Categories --</option>
                    <option value="Seeds" <?= ($category_filter === 'Seeds') ? 'selected' : '' ?>>Seeds</option>
                    <option value="Labour" <?= ($category_filter === 'Labour') ? 'selected' : '' ?>>Labour</option>
                    <option value="Transportation" <?= ($category_filter === 'Transportation') ? 'selected' : '' ?>>Transportation</option>
                    <option value="Other" <?= ($category_filter === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-4 text-md-end text-muted small">
                Total Expenses Listed: <strong class="text-danger fs-6"><?= format_currency($total_expense_sum) ?></strong>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-searchable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Crop & Farmer</th>
                        <th>Category</th>
                        <th>Description / Details</th>
                        <th>Amount</th>
                        <th>Expense Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No expense records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenses as $exp): ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?= $exp['expense_id'] ?></td>
                                <td>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($exp['crop_name']) ?></span>
                                    <span class="small text-muted d-block">(<?= htmlspecialchars($exp['farmer_name']) ?>)</span>
                                </td>
                                <td>
                                    <?php
                                    $cat_badge = 'bg-secondary';
                                    if ($exp['category'] === 'Seeds') $cat_badge = 'bg-warning text-dark';
                                    elseif ($exp['category'] === 'Labour') $cat_badge = 'bg-purple text-white';
                                    elseif ($exp['category'] === 'Transportation') $cat_badge = 'bg-info text-dark';
                                    ?>
                                    <span class="badge <?= $cat_badge ?> fw-semibold"><?= htmlspecialchars($exp['category']) ?></span>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($exp['description'] ?: '—') ?></td>
                                <td class="fw-bold text-danger"><?= format_currency($exp['amount']) ?></td>
                                <td class="small text-muted"><?= format_date($exp['expense_date']) ?></td>
                                <td class="text-end pe-4">
                                    <a href="edit.php?id=<?= $exp['expense_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Expense">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $exp['expense_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" data-item="Expense #<?= $exp['expense_id'] ?>" title="Delete Expense">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
