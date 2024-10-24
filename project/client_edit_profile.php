<?php
include('db_connect.php'); // DB connection
session_start();

// Check if the user is logged in (client)
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch client details
$client_id = $_SESSION['client_id'];
$sql = "SELECT * FROM clients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();

// Update client profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $update_sql = "UPDATE clients SET first_name = ?, last_name = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('sssi', $first_name, $last_name, $email, $client_id);
    $update_stmt->execute();
    header("Location: client_edit_profile.php"); // Redirect to avoid form resubmission
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #FAFAFA;
            margin-top: 20px;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="content">
        <h2>Edit Profile</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($client['first_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($client['last_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</body>
</html>
