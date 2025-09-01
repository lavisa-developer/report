<?php
require_once 'db.php';

$db = new Database();
$pdo = $db->getConnection();

$message = '';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Fetch existing ticket
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $work_notes = $_POST['work_notes'];

    // Fetch old values for comparison
    $stmtOld = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
    $stmtOld->execute([$id]);
    $oldTicket = $stmtOld->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("UPDATE tickets SET title = ?, description = ?, status = ?, priority = ? WHERE id = ?");
    if ($stmt->execute([$title, $description, $status, $priority, $id])) {
        // Log work notes if provided
        if (!empty($work_notes)) {
            $noteStmt = $pdo->prepare("INSERT INTO work_notes (ticket_id, note) VALUES (?, ?)");
            $noteStmt->execute([$id, $work_notes]);
        }
        $message = 'Ticket updated successfully!';
        header('Location: view_ticket.php?id=' . $id);
        exit;
    } else {
        $message = 'Error updating ticket.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Ticket</h1>

        <?php if ($message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($ticket['title']); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($ticket['description']); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="open" <?php echo $ticket['status'] == 'open' ? 'selected' : ''; ?>>Open</option>
                    <option value="in_progress" <?php echo $ticket['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="resolved" <?php echo $ticket['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="closed" <?php echo $ticket['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                <select id="priority" name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="low" <?php echo $ticket['priority'] == 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo $ticket['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo $ticket['priority'] == 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="urgent" <?php echo $ticket['priority'] == 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="work_notes" class="block text-sm font-medium text-gray-700 mb-2">Work Notes</label>
                <textarea id="work_notes" name="work_notes" placeholder="Add work notes for this update" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Update Ticket</button>
                <a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
