<?php
include('db_connect.php'); // DB connection
session_start();

// Check if the user is logged in as a freelancer
if ($_SESSION['role'] != 'freelancer') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $job_type = $_POST['job_type'];
    $budget = $_POST['budget'];
    $duration = $_POST['duration'];
    $freelancer_id = $_SESSION['user_id']; // Assuming session has user_id

    // Insert the job into the database
    $sql = "INSERT INTO jobs (freelancer_id, title, description, job_type, budget, duration) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isssds', $freelancer_id, $title, $description, $job_type, $budget, $duration);

    if ($stmt->execute()) {
        $job_id = $conn->insert_id;
        // Handling file uploads if any
        if (isset($_FILES['job_files'])) {
            foreach ($_FILES['job_files']['tmp_name'] as $key => $tmp_name) {
                $file_name = basename($_FILES['job_files']['name'][$key]);
                $file_path = 'uploads/' . $file_name;
                if (move_uploaded_file($tmp_name, $file_path)) {
                    // Insert file path into job_files table
                    $sql = "INSERT INTO job_files (job_id, file_path) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('is', $job_id, $file_path);
                    $stmt->execute();
                }
            }
        }
        header("Location: my_job_posts.php");
        exit;
    } else {
        $error = "Error creating job.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job</title>
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
            transition: width 0.3s;
        }
        .sidebar .nav-link {
            color: #fff;
        }
        .sidebar .nav-item.active .nav-link {
            background-color: #FF5370;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
        .card {
            border-radius: 5px;
            box-shadow: 0 1px 2.94px 0.06px rgba(4,26,55,0.16);
            border: none;
            margin-bottom: 30px;
            transition: all 0.3s ease-in-out;
        }
        .container-fluid {
            max-width: 1200px;
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
    <div class="sidebar">
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

    <!-- Main content -->
    <div class="container content">
        <h2 class="mb-4">Create a New Job</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Job Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="job_type" class="form-label">Job Type</label>
                <select class="form-select" id="job_type" name="job_type" required>
                    <option value="freelance">Freelance</option>
                    <option value="part-time">Part-Time</option>
                    <option value="full-time">Full-Time</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="budget" class="form-label">Budget (₹)</label>
                <input type="number" class="form-control" id="budget" name="budget" required>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Duration</label>
                <input type="text" class="form-control" id="duration" name="duration" required>
            </div>
            <div class="mb-3">
                <label for="job_files" class="form-label">Upload Files</label>
                <input type="file" class="form-control" id="job_files" name="job_files[]" multiple>
            </div>
            <button type="submit" class="btn btn-primary">Create Job</button>
        </form>
    </div>
</body>
</html>
