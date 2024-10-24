<?php
session_start(); // Start session
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role']; // freelancer or client

    // New variables for additional fields
    $first_name = trim($_POST['first_name']);
    $surname = trim($_POST['surname']);
    $mobile = trim($_POST['mobile']);
    $address1 = trim($_POST['address1']);
    $address2 = trim($_POST['address2']);
    $postcode = trim($_POST['postcode']);
    $state = trim($_POST['state']);
    $area = trim($_POST['area']);
    $country = trim($_POST['country']);
    $education = trim($_POST['education']);

    // Basic validation
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if the email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Email is already registered.";
        } else {
            // Insert the new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (email, password, role, first_name, surname, mobile, address1, address2, postcode, state, area, country, education) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssssssssss', $email, $hashed_password, $role, $first_name, $surname, $mobile, $address1, $address2, $postcode, $state, $area, $country, $education);

            if ($stmt->execute()) {
                // After successful registration, log the user in
                $user_id = $stmt->insert_id; // Get the last inserted ID
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;

                // Redirect based on user role
                if ($role == 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($role == 'freelancer') {
                    header("Location: freelancer_dashboard.php");
                } elseif ($role == 'client') {
                    header("Location: client_dashboard.php");
                }
                exit();
            } else {
                $error_message = "An error occurred. Try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Kat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-floating label {
            font-weight: bold;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a3795 0%, #713081 100%);
        }
        .form-check-label {
            font-size: 0.9rem;
        }
        .alert {
            border-radius: 0.5rem;
            margin-top: 1rem;
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

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 rounded-3">
                <div class="card-header text-center">
                    Create an Account 🚀
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?= $error_message ?></div>
                    <?php endif; ?>
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success"><?= $success_message ?></div>
                    <?php endif; ?>
                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Select Role</label>
                            <select name="role" class="form-control" id="role" required>
                                <option value="freelancer">Freelancer</option>
                                <option value="client">Client</option>
                            </select>
                        </div>

                        <!-- Additional fields -->
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" id="first_name" placeholder="Enter first name" required>
                        </div>
                        <div class="mb-3">
                            <label for="surname" class="form-label">Surname</label>
                            <input type="text" name="surname" class="form-control" id="surname" placeholder="Enter surname" required>
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input type="text" name="mobile" class="form-control" id="mobile" placeholder="Enter mobile number" required>
                        </div>
                        <div class="mb-3">
                            <label for="address1" class="form-label">Address Line 1</label>
                            <input type="text" name="address1" class="form-control" id="address1" placeholder="Enter address" required>
                        </div>
                        <div class="mb-3">
                            <label for="address2" class="form-label">Address Line 2</label>
                            <input type="text" name="address2" class="form-control" id="address2" placeholder="Enter address line 2">
                        </div>
                        <div class="mb-3">
                            <label for="postcode" class="form-label">Postcode</label>
                            <input type="text" name="postcode" class="form-control" id="postcode" placeholder="Enter postcode" required>
                        </div>
                        <div class="mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" name="state" class="form-control" id="state" placeholder="Enter state" required>
                        </div>
                        <div class="mb-3">
                            <label for="area" class="form-label">Area</label>
                            <input type="text" name="area" class="form-control" id="area" placeholder="Enter area" required>
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" id="country" placeholder="Enter country" required>
                        </div>
                        <div class="mb-3">
                            <label for="education" class="form-label">Education</label>
                            <input type="text" name="education" class="form-control" id="education" placeholder="Enter education level" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
