<?php
require_once 'db.php';

$db = new Database();
$pdo = $db->getConnection();

// Fetch all tickets
$stmt = $pdo->prepare("SELECT * FROM tickets ORDER BY created_at DESC");
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="tickets_export.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header
fputcsv($output, ['ID', 'Title', 'Description', 'Status', 'Priority', 'Created At', 'Updated At']);

// Write ticket data
foreach ($tickets as $ticket) {
    fputcsv($output, [
        $ticket['id'],
        $ticket['title'],
        $ticket['description'],
        $ticket['status'],
        $ticket['priority'],
        $ticket['created_at'],
        $ticket['updated_at']
    ]);
}

// Close output stream
fclose($output);
exit;
?>
