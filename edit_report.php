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

// Fetch existing report
$stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ?");
$stmt->execute([$id]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $task_name = $_POST['task_name'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $remark = $_POST['remark'];
    $work_notes = $_POST['work_notes'];

    // Fetch old values for comparison
    $stmtOld = $pdo->prepare("SELECT * FROM reports WHERE id = ?");
    $stmtOld->execute([$id]);
    $oldReport = $stmtOld->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("UPDATE reports SET date = ?, task_name = ?, status = ?, description = ?, remark = ? WHERE id = ?");
    if ($stmt->execute([$date, $task_name, $status, $description, $remark, $id])) {
        // Log changes
        $fields = ['date', 'task_name', 'status', 'description', 'remark'];
        $newValues = ['date' => $date, 'task_name' => $task_name, 'status' => $status, 'description' => $description, 'remark' => $remark];
        $hasChanges = false;
        foreach ($fields as $field) {
            $oldValue = $oldReport[$field] ?? '';
            $newValue = $newValues[$field];
            if ($oldValue !== $newValue) {
                $logStmt = $pdo->prepare("INSERT INTO logs (report_id, action, field_name, old_value, new_value, work_notes) VALUES (?, 'update', ?, ?, ?, ?)");
                $logStmt->execute([$id, $field, $oldValue, $newValue, $work_notes]);
                $hasChanges = true;
            }
        }
        // If no field changes but work notes provided, log as work note only
        if (!$hasChanges && !empty($work_notes)) {
            $logStmt = $pdo->prepare("INSERT INTO logs (report_id, action, field_name, old_value, new_value, work_notes) VALUES (?, 'update', 'work_notes', '', '', ?)");
            $logStmt->execute([$id, $work_notes]);
        }
        $message = 'Report updated successfully!';
        header('Location: index.php');
        exit;
    } else {
        $message = 'Error updating report.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Report</h1>
        
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($report['date']); ?>" required>
            
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" value="<?php echo htmlspecialchars($report['task_name']); ?>" required>
            
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="pending" <?php echo $report['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="in_progress" <?php echo $report['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="completed" <?php echo $report['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($report['description']); ?></textarea>

            <label for="remark">Remark:</label>
            <textarea id="remark" name="remark"><?php echo htmlspecialchars($report['remark'] ?? ''); ?></textarea>

            <label for="work_notes">Work Notes:</label>
            <textarea id="work_notes" name="work_notes" placeholder="Add work notes for this update"></textarea>

            <button type="submit" class="btn">Update Report</button>
            <a href="index.php" class="btn">Cancel</a>
        </form>
    </div>
</body>
</html>
