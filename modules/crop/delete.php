<?php
require_once __DIR__ . '/../../config/db.php';
check_login();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT crop_name FROM crop WHERE crop_id = ?");
    $stmt->execute([$id]);
    $crop = $stmt->fetch();

    if ($crop) {
        $del_stmt = $pdo->prepare("DELETE FROM crop WHERE crop_id = ?");
        if ($del_stmt->execute([$id])) {
            set_flash('success', "Crop '" . $crop['crop_name'] . "' and all related records were deleted successfully.");
        } else {
            set_flash('danger', "Failed to delete crop record.");
        }
    } else {
        set_flash('danger', "Crop record not found.");
    }
}

redirect('list.php');
