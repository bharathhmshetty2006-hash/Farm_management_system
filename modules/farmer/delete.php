<?php
require_once __DIR__ . '/../../config/db.php';
check_login();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Fetch farmer name for confirmation message
    $stmt = $pdo->prepare("SELECT name FROM farmer WHERE farmer_id = ?");
    $stmt->execute([$id]);
    $farmer = $stmt->fetch();

    if ($farmer) {
        $del_stmt = $pdo->prepare("DELETE FROM farmer WHERE farmer_id = ?");
        if ($del_stmt->execute([$id])) {
            set_flash('success', "Farmer '" . $farmer['name'] . "' and all associated records were deleted successfully.");
        } else {
            set_flash('danger', "Failed to delete farmer record.");
        }
    } else {
        set_flash('danger', "Farmer record not found.");
    }
}

redirect('list.php');
