<?php
include('db_connect.php'); // Include DB connection
session_start();

// Check if the user is logged in (freelancer or client)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all jobs from the database along with job files and edits
$sql = "SELECT jobs.id, jobs.title, jobs.description, jobs.job_type, jobs.budget, jobs.duration, users.email AS freelancer_email 
        FROM jobs 
        JOIN users ON jobs.freelancer_id = users.id";
$result = $conn->query($sql);

// Fetch job files and job edits separately
$job_files_sql = "SELECT job_id, file_path FROM job_files";
$job_files_result = $conn->query($job_files_sql);

$job_edits_sql = "SELECT job_id, updated_description, updated_at FROM job_edits";
$job_edits_result = $conn->query($job_edits_sql);

// Prepare arrays to store the job files and job edits
$job_files = [];
$job_edits = [];

if ($job_files_result->num_rows > 0) {
    while ($file_row = $job_files_result->fetch_assoc()) {
        $job_files[$file_row['job_id']][] = $file_row['file_path'];
    }
}

if ($job_edits_result->num_rows > 0) {
    while ($edit_row = $job_edits_result->fetch_assoc()) {
        $job_edits[$edit_row['job_id']][] = [
            'updated_description' => $edit_row['updated_description'],
            'updated_at' => $edit_row['updated_at']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Job Listings</title>
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
        <h2>Job Listings</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Description</th>
                    <th>Job Type</th>
                    <th>Budget ($)</th>
                    <th>Duration</th>
                    <th>Posted By</th>
                    <th>Files</th>
                    <th>Edits</th>
                    <th>Actions</th>
                </tr>
            </thead>
            
<tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['job_type']) ?></td>
                <td><?= htmlspecialchars($row['budget']) ?></td>
                <td><?= htmlspecialchars($row['duration']) ?></td>
                <td><?= htmlspecialchars($row['freelancer_email']) ?></td>
                <td>
                    <?php if (isset($job_files[$row['id']])): ?>
                        <?php foreach ($job_files[$row['id']] as $file_path): ?>
                            <a href="<?= htmlspecialchars($file_path) ?>" target="_blank">View File</a><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        No files.
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($job_edits[$row['id']])): ?>
                        <?php foreach ($job_edits[$row['id']] as $edit): ?>
                            <strong>Edited on: <?= htmlspecialchars($edit['updated_at']) ?></strong><br>
                            <?= nl2br(htmlspecialchars($edit['updated_description'])) ?><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        No edits.
                    <?php endif; ?>
                </td>
                <td>
                    <a href="job_details.php?job_id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                    <?php if ($_SESSION['role'] == 'freelancer' && isset($row['freelancer_id']) && $_SESSION['user_id'] == $row['freelancer_id']): ?>
                        <a href="edit_job.php?job_id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_job.php?job_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="9">No jobs found.</td>
        </tr>
    <?php endif; ?>
</tbody>

        </table>
    </div>
</body>
</html>
