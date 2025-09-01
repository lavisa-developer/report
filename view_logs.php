<?php
require_once 'db.php';

$db = new Database();
$pdo = $db->getConnection();

$query = "SELECT logs.*, reports.task_name FROM logs LEFT JOIN reports ON logs.report_id = reports.id ORDER BY logs.updated_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Update Logs</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 0.5rem;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 1rem;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Logs</h1>
        <a href="index.php" class="btn">Back to Dashboard</a>
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Report ID</th>
                    <th>Task Name</th>
                    <th>Action</th>
                    <th>Field</th>
                    <th>Old Value</th>
                    <th>New Value</th>
                    <th>Work Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($logs) > 0): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['updated_at']); ?></td>
                            <td><?php echo htmlspecialchars($log['report_id']); ?></td>
                            <td><?php echo htmlspecialchars($log['task_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                            <td><?php echo htmlspecialchars($log['field_name']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($log['old_value'])); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($log['new_value'])); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($log['work_notes'] ?? '')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">No logs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
