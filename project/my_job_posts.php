<?php
include('db_connect.php'); // DB connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all jobs from the database
$sql = "SELECT j.id, j.title, j.description, j.job_type, j.budget, j.duration, u.username 
        FROM jobs j 
        JOIN users u ON j.freelancer_id = u.id";
$result = $conn->query($sql);

// Fetch the jobs created by the logged-in freelancer
$freelancer_id = $_SESSION['user_id'];
$my_jobs_sql = "SELECT id, title, description, job_type, budget, duration FROM jobs WHERE freelancer_id = ?";
$stmt = $conn->prepare($my_jobs_sql);
$stmt->bind_param('i', $freelancer_id);
$stmt->execute();
$my_jobs_result = $stmt->get_result();
$stmt->close();
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
            padding: 20px; /* Added padding for better spacing */
        }
        .card {
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px; /* Adjusted margin */
            transition: all 0.3s ease-in-out;
            background-color: #fff; /* Set card background to white for contrast */
        }
        .card .card-block {
            padding: 25px;
        }
        .list-group-item {
            border: none; /* Removed border for cleaner look */
            background-color: #f9f9f9; /* Added a slight background for items */
        }
        .list-group-item:hover {
            background-color: #eaeaea; /* Highlight on hover */
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

    <div class="content">
        <h2>Job Listings</h2>
        
        <h3>Your Jobs</h3>
        <ul class="list-group mb-4">
            <?php while ($job = $my_jobs_result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <div class="card">
                        <div class="card-body">
                            <h5><?= $job['title'] ?></h5>
                            <p><?= $job['description'] ?></p>
                            <small>Type: <?= $job['job_type'] ?> | Budget: $<?= $job['budget'] ?> | Duration: <?= $job['duration'] ?></small>
                            <div class="mt-2">
                                <a href="edit_job.php?id=<?= $job['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_job.php?id=<?= $job['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <h3>All Available Jobs</h3>
        <ul class="list-group">
            <?php while ($job = $result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <div class="card">
                        <div class="card-body">
                            <h5><?= $job['title'] ?></h5>
                            <p><?= $job['description'] ?></p>
                            <small>Posted by: <?= $job['username'] ?> | Type: <?= $job['job_type'] ?> | Budget: $<?= $job['budget'] ?> | Duration: <?= $job['duration'] ?></small>
                        </div>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
