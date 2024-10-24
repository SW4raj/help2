<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/coreui@4.1.0/dist/css/coreui.min.css" rel="stylesheet">
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
        .card {
            border-radius: 5px;
            box-shadow: 0 1px 2.94px 0.06px rgba(4,26,55,0.16);
            border: none;
            margin-bottom: 30px;
            transition: all 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card .card-block {
            padding: 25px;
        }
        .order-card i {
            font-size: 26px;
        }
        .f-left {
            float: left;
        }
        .f-right {
            float: right;
        }
        .bg-c-blue {
            background: linear-gradient(45deg, #4099ff, #73b4ff);
        }
        .bg-c-green {
            background: linear-gradient(45deg, #2ed8b6, #59e0c5);
        }
        .bg-c-yellow {
            background: linear-gradient(45deg, #FFB64D, #ffcb80);
        }
        .bg-c-pink {
            background: linear-gradient(45deg, #FF5370, #ff869a);
        }
        .container-fluid {
            max-width: 1200px;
        }
        .order-card {
            color: #fff;
            padding: 20px;
            border-radius: 10px;
        }
        .order-card:hover {
            transform: scale(1.05);
        }

        /* Toggle button styles */
        .toggle-btn {
            display: inline-block;
            width: 50px;
            height: 25px;
            background-color: #ddd;
            border-radius: 15px;
            position: relative;
            cursor: pointer;
        }
        .toggle-btn:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 3px;
            width: 20px;
            height: 20px;
            background-color: #fff;
            border-radius: 50%;
            transition: all 0.3s ease;
            transform: translateY(-50%);
        }
        .toggle-btn.active:before {
            left: 27px;
        }
        .toggle-btn:hover {
            background-color: #bbb;
        }
        /* Ensure toggle is off by default */
        .toggle-btn:not(.active):before {
            left: 3px;
        }

        /* Adjust card height to be the same */
        .card {
            height: 100%;
        }
        .card-body {
            flex: 1;
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
    <div class="d-flex">
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

        <!-- Main Content -->
        <div class="content p-4">
            <div class="container-fluid">
                <!-- Profile Completeness Progress Bar -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Profile Completeness</h5>
                                <!-- Profile completeness dynamically calculated -->
                                <div class="progress">
                                    <div class="progress-bar bg-success" id="profile-progress" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>
                                <small id="profile-status" class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row for Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-c-blue">
                            <div class="card-block">
                                <h4 class="card-title">Job Applications</h4>
                                <p class="card-text">You have 5 new job applications to review.</p>
                                <a href="my_applications.php" class="btn btn-light">View Applications</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-c-green">
                            <div class="card-block">
                                <h4 class="card-title">Active Job Posts</h4>
                                <p class="card-text">You currently have 3 active job postings.</p>
                                <a href="my_job_posts.php" class="btn btn-light">Manage Posts</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-c-yellow">
                            <div class="card-block">
                                <h4 class="card-title">Profile Status</h4>
                                <p class="card-text">Your profile is 100% complete.</p>
                                <a href="edit_profile.php" class="btn btn-light">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading Spinner for Job Listings -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status" id="job-listing-spinner">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading your job listings...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS for Dynamic Profile Completeness -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Dummy data - Fetch these values from backend in real app
            let profileFields = {
                photoUploaded: true,
                skillsAdded: true,
                bioFilled: true,
                contactInfoComplete: true,
                portfolioUpdated: false // Field that isn't complete
            };

            // Calculate profile completeness
            let totalFields = Object.keys(profileFields).length;
            let completedFields = Object.values(profileFields).filter(value => value === true).length;
            let completionPercentage = Math.floor((completedFields / totalFields) * 100);

            // Update progress bar and status
            let progressBar = document.getElementById("profile-progress");
            let profileStatus = document.getElementById("profile-status");
            progressBar.style.width = completionPercentage + "%";
            progressBar.setAttribute("aria-valuenow", completionPercentage);
            progressBar.textContent = completionPercentage + "%";

            if (completionPercentage === 100) {
                profileStatus.textContent = "Your profile is fully complete!";
            } else {
                profileStatus.textContent = "Complete your profile to get more visibility!";
            }
        });
    </script>
</body>
</html>
