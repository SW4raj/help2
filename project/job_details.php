<?php
include('db_connect.php'); // Include DB connection
session_start();

// Check if the user is logged in (freelancer or client)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the job ID from the URL
if (!isset($_GET['job_id'])) {
    header("Location: jobs.php");
    exit;
}

$job_id = $_GET['job_id'];

// Fetch job details
$sql = "SELECT jobs.id, jobs.title, jobs.description, jobs.job_type, jobs.budget, jobs.duration, users.email AS freelancer_email 
        FROM jobs 
        JOIN users ON jobs.freelancer_id = users.id 
        WHERE jobs.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Job not found!";
    exit;
}

$job = $result->fetch_assoc();

// Fetch associated job files
$job_files_sql = "SELECT file_path FROM job_files WHERE job_id = ?";
$stmt_files = $conn->prepare($job_files_sql);
$stmt_files->bind_param("i", $job_id);
$stmt_files->execute();
$files_result = $stmt_files->get_result();

$job_files = [];
if ($files_result->num_rows > 0) {
    while ($file_row = $files_result->fetch_assoc()) {
        $job_files[] = $file_row['file_path'];
    }
}

// Fetch job edit history
$job_edits_sql = "SELECT updated_description, updated_at FROM job_edits WHERE job_id = ? ORDER BY updated_at DESC";
$stmt_edits = $conn->prepare($job_edits_sql);
$stmt_edits->bind_param("i", $job_id);
$stmt_edits->execute();
$edits_result = $stmt_edits->get_result();

$job_edits = [];
if ($edits_result->num_rows > 0) {
    while ($edit_row = $edits_result->fetch_assoc()) {
        $job_edits[] = [
            'updated_description' => $edit_row['updated_description'],
            'updated_at' => $edit_row['updated_at']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Job Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #FAFAFA;
            margin-top: 20px;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            bottom: 0;
            width: 250px;
            background-color: #302B27;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #fff;
        }
        .sidebar .nav-item.active .nav-link {
            background-color: #FF5370;
        }
        .content {
            margin-left: 250px;
        }
        .table {
            margin-top: 20px;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        /* Responsive Sidebar */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                height: auto;
                width: 100%;
            }
            .content {
                margin-left: 0;
            }
            .sidebar .nav-item {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar sidebar-dark bg-dark">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="freelancer_dashboard.php" aria-label="Dashboard">
                    <i class="cil-speedometer" aria-hidden="true"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="create_job.php" aria-label="Create Job">
                    <i class="cil-plus" aria-hidden="true"></i> Create Job
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="jobs.php" aria-label="Job Listings">
                    <i class="cil-briefcase" aria-hidden="true"></i> Job Listings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_applications.php" aria-label="My Applications">
                    <i class="cil-task" aria-hidden="true"></i> My Applications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="edit_profile.php" aria-label="My Profile">
                    <i class="cil-user" aria-hidden="true"></i> My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_job_posts.php" aria-label="My Job Posts">
                    <i class="cil-folder" aria-hidden="true"></i> My Job Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notifications.php" aria-label="Notifications">
                    <i class="cil-bell" aria-hidden="true"></i> Notifications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php" aria-label="Logout">
                    <i class="cil-account-logout" aria-hidden="true"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="container content">
        <h2>Job Details</h2>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><?= htmlspecialchars($job['title']) ?></h4>
                <p class="card-text"><strong>Description:</strong> <?= nl2br(htmlspecialchars($job['description'])) ?></p>
                <p class="card-text"><strong>Job Type:</strong> <?= htmlspecialchars($job['job_type']) ?></p>
                <p class="card-text"><strong>Budget:</strong> $<?= htmlspecialchars($job['budget']) ?></p>
                <p class="card-text"><strong>Duration:</strong> <?= htmlspecialchars($job['duration']) ?> days</p>
                <p class="card-text"><strong>Posted by:</strong> <?= htmlspecialchars($job['freelancer_email']) ?></p>

                <h5>Files</h5>
                <ul>
                    <?php if (!empty($job_files)): ?>
                        <?php foreach ($job_files as $file_path): ?>
                            <li><a href="<?= htmlspecialchars($file_path) ?>" target="_blank">View File</a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No files uploaded.</li>
                    <?php endif; ?>
                </ul>

                <h5>Job Edits</h5>
                <?php if (!empty($job_edits)): ?>
                    <?php foreach ($job_edits as $edit): ?>
                        <div class="alert alert-secondary">
                            <strong>Edited on:</strong> <?= htmlspecialchars($edit['updated_at']) ?><br>
                            <?= nl2br(htmlspecialchars($edit['updated_description'])) ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No edits available.</p>
                <?php endif; ?>

                <a href="jobs.php" class="btn btn-primary">Back to Job Listings</a>
            </div>
        </div>
    </div>
</body>
</html>
