<?php
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $date = $_POST['date'];
    $task_name = $_POST['task_name'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $remark = $_POST['remark'];

    $stmt = $pdo->prepare("INSERT INTO reports (date, task_name, status, description, remark) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$date, $task_name, $status, $description, $remark])) {
        $reportId = $pdo->lastInsertId();
        // Log creation
        $logStmt = $pdo->prepare("INSERT INTO logs (report_id, action, field_name, old_value, new_value) VALUES (?, 'create', 'report', '', 'Report created')");
        $logStmt->execute([$reportId]);
        $message = 'Report added successfully!';
        header('Location: index.php');
        exit;
    } else {
        $message = 'Error adding report.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Add New Report</h1>
        
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
            
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" required>
            
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <label for="remark">Remark:</label>
            <textarea id="remark" name="remark"></textarea>

            <button type="submit" class="btn">Add Report</button>
            <a href="index.php" class="btn">Cancel</a>
        </form>
    </div>
</body>
</html>
