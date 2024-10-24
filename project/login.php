<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $error_message = "Please fill in both email and password.";
    } else {
        $sql = "SELECT id, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $stored_password, $role);
            $stmt->fetch();
            
            if (password_verify($password, $stored_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;

                // Redirect based on user role
                if ($role == 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($role == 'freelancer') {
                    header("Location: freelancer_dashboard.php");
                } elseif ($role == 'client') {
                    header("Location:client_dashboard.php");
                }
                exit();
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "No account found with this email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Kat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Roboto', sans-serif;
        }
        .card {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            border-radius: 0.5rem;
        }
        .btn-primary {
            background: #3498db;
            border: none;
            transition: background 0.3s;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .form-floating label {
            font-size: 0.9rem;
        }
        .alert {
            margin-top: 1rem;
        }
        .card-header {
            background-color: #3498db;
            color: white;
            font-size: 1.3rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0">
                <div class="card-header">Log In to Your Account 🔐</div>
                <div class="card-body p-4">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?= $error_message ?></div>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Log In</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Don't have an account? <a href="register.php">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
