<?php
require_once __DIR__ . '/../../config/db.php';
check_login();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $del_stmt = $pdo->prepare("DELETE FROM sales WHERE sale_id = ?");
    if ($del_stmt->execute([$id])) {
        set_flash('success', "Sale transaction #$id deleted successfully.");
    } else {
        set_flash('danger', "Failed to delete sale transaction.");
    }
}

redirect('list.php');
