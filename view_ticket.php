<?php
require_once 'db.php';

$db = new Database();
$pdo = $db->getConnection();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Fetch ticket
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header('Location: index.php');
    exit;
}

// Fetch work notes
$stmtNotes = $pdo->prepare("SELECT * FROM work_notes WHERE ticket_id = ? ORDER BY created_at DESC");
$stmtNotes->execute([$id]);
$work_notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Ticket Details</h1>
            <a href="index.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4"><?php echo htmlspecialchars($ticket['title']); ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">ID</p>
                    <p class="text-lg text-gray-900"><?php echo htmlspecialchars($ticket['id']); ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        <?php 
                        switch($ticket['status']) {
                            case 'open': echo 'bg-green-100 text-green-800'; break;
                            case 'in_progress': echo 'bg-yellow-100 text-yellow-800'; break;
                            case 'resolved': echo 'bg-blue-100 text-blue-800'; break;
                            case 'closed': echo 'bg-gray-100 text-gray-800'; break;
                        }
                        ?>">
                        <?php echo htmlspecialchars($ticket['status']); ?>
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Priority</p>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        <?php 
                        switch($ticket['priority']) {
                            case 'low': echo 'bg-gray-100 text-gray-800'; break;
                            case 'medium': echo 'bg-yellow-100 text-yellow-800'; break;
                            case 'high': echo 'bg-orange-100 text-orange-800'; break;
                            case 'urgent': echo 'bg-red-100 text-red-800'; break;
                        }
                        ?>">
                        <?php echo htmlspecialchars($ticket['priority']); ?>
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Created</p>
                    <p class="text-lg text-gray-900"><?php echo htmlspecialchars($ticket['created_at']); ?></p>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-2">Description</p>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Work Notes</h3>
            <?php if (count($work_notes) > 0): ?>
                <div class="space-y-4">
                    <?php foreach ($work_notes as $note): ?>
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                            <p class="text-sm font-medium text-gray-500 mb-2"><?php echo htmlspecialchars($note['created_at']); ?></p>
                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($note['note'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">No work notes yet.</p>
            <?php endif; ?>
        </div>

        <div class="flex justify-end">
            <a href="edit_ticket.php?id=<?php echo $ticket['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Ticket
            </a>
        </div>
    </div>
</body>
</html>
