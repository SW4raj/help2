<?php
session_start(); // Start the session

// Check if the user is logged in and has the role of client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php"); // Redirect to login if not logged in as client
    exit();
}

// Database connection
require_once 'db_connect.php';

// Fetch client-specific data, e.g., jobs created by the client
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM jobs WHERE client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$jobs = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1>Client Dashboard</h1>
    <h2>Welcome, <?= $_SESSION['first_name']; ?>!</h2>
    
    <h3>Your Jobs</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jobs)): ?>
                <tr>
                    <td colspan="4">No jobs found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?= htmlspecialchars($job['title']); ?></td>
                        <td><?= htmlspecialchars($job['description']); ?></td>
                        <td><?= htmlspecialchars($job['status']); ?></td>
                        <td>
                            <!-- Add buttons for actions like edit or delete -->
                            <a href="edit_job.php?id=<?= $job['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_job.php?id=<?= $job['id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="create_job.php" class="btn btn-primary">Create New Job</a>
    <a href="logout.php" class="btn btn-secondary">Logout</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
