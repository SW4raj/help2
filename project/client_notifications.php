<?php
include('db_connect.php'); // DB connection
session_start();

// Check if the user is logged in (client)
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch notifications for the logged-in client
$client_id = $_SESSION['client_id'];
$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $client_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Mark notifications as read
if (isset($_GET['mark_read'])) {
    $notification_id = $_GET['mark_read'];
    $update_sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('i', $notification_id);
    $update_stmt->execute();
    $update_stmt->close();
    header("Location: client_notifications.php"); // Refresh page after marking as read
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Client Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #FAFAFA;
            margin-top: 20px;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="content">
        <h2>Notifications</h2>
        <?php if ($result->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($notification = $result->fetch_assoc()): ?>
                    <li class="list-group-item <?= $notification['is_read'] == 0 ? 'list-group-item-warning' : '' ?>">
                        <p>
                            <?= $notification['message'] ?>
                            <?php if ($notification['is_read'] == 0): ?>
                                <a href="client_notifications.php?mark_read=<?= $notification['id'] ?>" class="btn btn-sm btn-primary float-end">Mark as read</a>
                            <?php endif; ?>
                        </p>
                        <small class="text-muted">Posted on <?= date('F j, Y, g:i a', strtotime($notification['created_at'])) ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-info">No notifications found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
