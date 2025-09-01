<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

$db = new Database();
$pdo = $db->getConnection();

// Log deletion before deleting
$logStmt = $pdo->prepare("INSERT INTO logs (report_id, action, field_name, old_value, new_value) VALUES (?, 'delete', 'report', 'Report deleted', '')");
$logStmt->execute([$id]);

$stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
if ($stmt->execute([$id])) {
    header('Location: index.php');
    exit;
} else {
    echo 'Error deleting report.';
}
?>
