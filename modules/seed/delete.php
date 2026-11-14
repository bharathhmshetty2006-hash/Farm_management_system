<?php
require_once __DIR__ . '/../../config/db.php';
check_login();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT seed_type FROM seed WHERE seed_id = ?");
    $stmt->execute([$id]);
    $seed = $stmt->fetch();

    if ($seed) {
        $del_stmt = $pdo->prepare("DELETE FROM seed WHERE seed_id = ?");
        if ($del_stmt->execute([$id])) {
            set_flash('success', "Seed record '" . $seed['seed_type'] . "' deleted successfully.");
        } else {
            set_flash('danger', "Failed to delete seed record.");
        }
    } else {
        set_flash('danger', "Seed record not found.");
    }
}

redirect('list.php');
