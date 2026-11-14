<?php
require_once __DIR__ . '/../../config/db.php';
check_login();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT equipment_name FROM equipment WHERE equipment_id = ?");
    $stmt->execute([$id]);
    $equipment = $stmt->fetch();

    if ($equipment) {
        $del_stmt = $pdo->prepare("DELETE FROM equipment WHERE equipment_id = ?");
        if ($del_stmt->execute([$id])) {
            set_flash('success', "Equipment '" . $equipment['equipment_name'] . "' deleted successfully.");
        } else {
            set_flash('danger', "Failed to delete equipment record.");
        }
    } else {
        set_flash('danger', "Equipment record not found.");
    }
}

redirect('list.php');
