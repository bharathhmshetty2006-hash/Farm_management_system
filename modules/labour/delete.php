<?php
require_once __DIR__ . '/../../config/db.php';
check_login();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT labour_name FROM labour WHERE labour_id = ?");
    $stmt->execute([$id]);
    $labour = $stmt->fetch();

    if ($labour) {
        $del_stmt = $pdo->prepare("DELETE FROM labour WHERE labour_id = ?");
        if ($del_stmt->execute([$id])) {
            set_flash('success', "Labour record '" . $labour['labour_name'] . "' deleted successfully.");
        } else {
            set_flash('danger', "Failed to delete labour record.");
        }
    } else {
        set_flash('danger', "Labour record not found.");
    }
}

redirect('list.php');
