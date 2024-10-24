<?php
include('db_connect.php'); // DB connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$sql = "SELECT email, first_name, surname, mobile, address1, address2, postcode, state, area, country, education FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $first_name = $_POST['first_name'];
    $surname = $_POST['surname'];
    $mobile = $_POST['mobile'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $postcode = $_POST['postcode'];
    $state = $_POST['state'];
    $area = $_POST['area'];
    $country = $_POST['country'];
    $education = $_POST['education'];

    // Update the user's profile
    if ($password) {
        $update_sql = "UPDATE users SET email = ?, password = ?, first_name = ?, surname = ?, mobile = ?, address1 = ?, address2 = ?, postcode = ?, state = ?, area = ?, country = ?, education = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('ssssssssssssi', $email, $password, $first_name, $surname, $mobile, $address1, $address2, $postcode, $state, $area, $country, $education, $user_id);
    } else {
        $update_sql = "UPDATE users SET email = ?, first_name = ?, surname = ?, mobile = ?, address1 = ?, address2 = ?, postcode = ?, state = ?, area = ?, country = ?, education = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('sssssssssssi', $email, $first_name, $surname, $mobile, $address1, $address2, $postcode, $state, $area, $country, $education, $user_id);
    }

    if ($stmt->execute()) {
        $success_message = "Profile updated successfully.";
    } else {
        $error_message = "Failed to update profile.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f9fc;
            margin-top: 20px;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            bottom: 0;
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #ffffff;
            transition: background-color 0.2s;
        }
        .sidebar .nav-item.active .nav-link {
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
        }
        .container {
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 1px 10px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            margin-bottom: 30px;
            transition: all 0.3s ease-in-out;
        }
        .form-control {
            border-radius: 5px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert {
            border-radius: 5px;
        }
        .order-card {
            color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: scale(1.05);
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
                <a class="nav-link active" href="freelancer_dashboard.php">
                    <i class="cil-speedometer"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="create_job.php">
                    <i class="cil-plus"></i> Create Job
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="jobs.php">
                    <i class="cil-briefcase"></i> Job Listings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_applications.php">
                    <i class="cil-task"></i> My Applications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="edit_profile.php">
                    <i class="cil-user"></i> My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_job_posts.php">
                    <i class="cil-folder"></i> My Job Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notifications.php">
                    <i class="cil-bell"></i> Notifications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="cil-account-logout"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="content">
        <div class="container mt-5 mb-5 p-4">
            <div class="row">
                <div class="col-md-3 border-right">
                    <div class="d-flex flex-column align-items-center text-center p-3">
                        <img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg">
                        <span class="font-weight-bold"><?= isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User' ?></span>
                        <span class="text-black-50"><?= $user_data['email'] ?></span>
                    </div>
                </div>
                <div class="col-md-5 border-right">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Profile Settings</h4>
                        </div>
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success"><?= $success_message ?></div>
                        <?php elseif (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?= $error_message ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label class="labels">First Name</label>
                                    <input type="text" class="form-control" name="first_name" value="<?= $user_data['first_name'] ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">Surname</label>
                                    <input type="text" class="form-control" name="surname" value="<?= $user_data['surname'] ?>" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="labels">Mobile</label>
                                    <input type="text" class="form-control" name="mobile" value="<?= $user_data['mobile'] ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?= $user_data['email'] ?>" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="labels">Address 1</label>
                                    <input type="text" class="form-control" name="address1" value="<?= $user_data['address1'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">Address 2</label>
                                    <input type="text" class="form-control" name="address2" value="<?= $user_data['address2'] ?>">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="labels">Postcode</label>
                                    <input type="text" class="form-control" name="postcode" value="<?= $user_data['postcode'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">State</label>
                                    <input type="text" class="form-control" name="state" value="<?= $user_data['state'] ?>">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="labels">Area</label>
                                    <input type="text" class="form-control" name="area" value="<?= $user_data['area'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">Country</label>
                                    <input type="text" class="form-control" name="country" value="<?= $user_data['country'] ?>">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="labels">Education</label>
                                    <input type="text" class="form-control" name="education" value="<?= $user_data['education'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">Password</label>
                                    <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <button class="btn btn-primary profile-button" type="submit">Save Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <h4 class="text-right">Order Summary</h4>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="card order-card bg-primary">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-right">Total Orders</h4>
                                            <h5 class="text-right">0</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <i class="cil-shopping-cart" style="font-size: 50px; margin-top: 20px;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card order-card bg-success">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-right">Completed Orders</h4>
                                            <h5 class="text-right">0</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <i class="cil-check-circle" style="font-size: 50px; margin-top: 20px;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card order-card bg-danger">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-right">Pending Orders</h4>
                                            <h5 class="text-right">0</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <i class="cil-warning" style="font-size: 50px; margin-top: 20px;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card order-card bg-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-right">Total Earnings</h4>
                                            <h5 class="text-right">$0</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <i class="cil-money" style="font-size: 50px; margin-top: 20px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
