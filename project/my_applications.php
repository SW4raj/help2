<?php
session_start();
require_once 'db_connect.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and is a freelancer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'freelancer') {
    header("Location: login.php");
    exit();
}

$freelancer_id = $_SESSION['user_id'];

// Fetch the applications for jobs posted by this freelancer
$sql = "
    SELECT ja.application_id, ja.status, ja.applied_date, j.title AS job_title, 
           f.first_name AS applicant_name, f.email AS applicant_email, j.deadline
    FROM job_applications ja
    JOIN jobs j ON ja.job_id = j.id
    JOIN freelancers f ON ja.freelancer_id = f.id
    WHERE j.freelancer_id = ?
    ORDER BY ja.applied_date DESC";

$stmt = $conn->prepare($sql);

// Check for preparation errors
if (!$stmt) {
    die("SQL prepare error: " . $conn->error);
}

$stmt->bind_param('i', $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();

$applications = [];
while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications | Kat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: #495057;
        }
        .container {
            margin-left: 260px; /* Adjust to accommodate sidebar */
            padding: 20px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem;
            border-radius: 0.25rem;
        }
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .status-approved {
            background-color: #28a745;
            color: #fff;
        }
        .status-rejected {
            background-color: #dc3545;
            color: #fff;
        }
        .card-header {
            background: #8e44ad;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar sidebar-dark bg-dark">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="freelancer_dashboard.php" aria-label="Dashboard">
                <i class="cil-speedometer" aria-hidden="true"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="create_job.php" aria-label="Create Job">
                <i class="cil-plus" aria-hidden="true"></i> Create Job
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="jobs.php" aria-label="Job Listings">
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

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="card border-0 rounded-3">
                <div class="card-header text-center">
                    Applications for Your Jobs
                </div>
                <div class="card-body p-4">
                    <?php if (count($applications) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Applicant</th>
                                    <th>Status</th>
                                    <th>Date Applied</th>
                                    <th>Deadline</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $application): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($application['job_title']); ?></td>
                                        <td>
                                            <?= htmlspecialchars($application['applicant_name']); ?><br>
                                            <small><?= htmlspecialchars($application['applicant_email']); ?></small>
                                        </td>
                                        <td>
                                            <span class="status-badge <?= $application['status'] == 'pending' ? 'status-pending' : ($application['status'] == 'approved' ? 'status-approved' : 'status-rejected'); ?>">
                                                <?= ucfirst($application['status']); ?>
                                            </span>
                                        </td>
                                        <td><?= date('Y-m-d', strtotime($application['applied_date'])); ?></td>
                                        <td><?= date('Y-m-d', strtotime($application['deadline'])); ?></td>
                                        <td>
                                            <a href="approve_application.php?application_id=<?= $application['application_id']; ?>" class="btn btn-sm btn-outline-success">Accept</a>
                                            <a href="reject_application.php?application_id=<?= $application['application_id']; ?>" class="btn btn-sm btn-outline-danger">Reject</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            No applications received for your jobs yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
