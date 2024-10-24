<?php
// client_jobs.php

// Include your database connection
include('db_connection.php'); // Adjust the path to your actual DB connection file

session_start();

// Assuming the client is logged in and their ID is stored in the session
$client_id = $_SESSION['client_id']; // Adjust based on how you manage sessions

// Fetch all jobs created by the freelancer
$query = "SELECT * FROM jobs WHERE client_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Job Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Job Listings</h1>
        <div class="row">
            <?php while ($job = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($job['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($job['description']); ?></p>
                            <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
                            <p class="card-text"><strong>Budget:</strong> ₹<?php echo htmlspecialchars(number_format($job['budget'], 2)); ?></p>
                            <p class="card-text"><strong>Duration:</strong> <?php echo htmlspecialchars($job['duration']); ?></p>
                            <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($job['status']); ?></p>
                            <!-- Link updated to pass job ID to job_details.php -->
                            <a href="job_details.php?job_id=<?php echo $job['id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
