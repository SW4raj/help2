<?php
include('db_connect.php'); // DB connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if job ID is provided
if (!isset($_GET['id'])) {
    header("Location: jobs.php");
    exit;
}

$job_id = $_GET['id'];

// Delete job from the database
$sql = "DELETE FROM jobs WHERE id = ? AND freelancer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $job_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    header("Location: jobs.php");
    exit;
} else {
    $error = "Error deleting job.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Delete Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php else: ?>
            <div class="alert alert-success">Job deleted successfully!</div>
        <?php endif; ?>
        <a href="jobs.php" class="btn btn-primary">Back to Job Listings</a>
    </div>
</body>
</html>
