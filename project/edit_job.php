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

// Fetch job details from the database
$sql = "SELECT * FROM jobs WHERE id = ? AND freelancer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $job_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: jobs.php");
    exit;
}

$job = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $job_type = $_POST['job_type'];
    $budget = $_POST['budget'];
    $duration = $_POST['duration'];

    // Update job in the database
    $update_sql = "UPDATE jobs SET title = ?, description = ?, job_type = ?, budget = ?, duration = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('sssssi', $title, $description, $job_type, $budget, $duration, $job_id);

    if ($update_stmt->execute()) {
        header("Location: jobs.php");
        exit;
    } else {
        $error = "Error updating job.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Edit Job</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Job Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= $job['title'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required><?= $job['description'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="job_type" class="form-label">Job Type</label>
                <select class="form-select" id="job_type" name="job_type" required>
                    <option value="freelance" <?= $job['job_type'] == 'freelance' ? 'selected' : '' ?>>Freelance</option>
                    <option value="part-time" <?= $job['job_type'] == 'part-time' ? 'selected' : '' ?>>Part-Time</option>
                    <option value="full-time" <?= $job['job_type'] == 'full-time' ? 'selected' : '' ?>>Full-Time</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="budget" class="form-label">Budget ($)</label>
                <input type="number" class="form-control" id="budget" name="budget" value="<?= $job['budget'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Duration</label>
                <input type="text" class="form-control" id="duration" name="duration" value="<?= $job['duration'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Job</button>
        </form>
    </div>
</body>
</html>
