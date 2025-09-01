<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

$db = new Database();
$pdo = $db->getConnection();

$stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
if ($stmt->execute([$id])) {
    header('Location: index.php');
    exit;
} else {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Error deleting ticket.</div>';
}
?>
