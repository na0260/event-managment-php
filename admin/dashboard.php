<?php
session_start();
include "../config/database.php";

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch stats
$total_events = $conn->query("SELECT COUNT(*) FROM events")->fetchColumn();
$total_attendees = $conn->query("SELECT COUNT(*) FROM event_registrations")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin.css"> <!-- Custom CSS -->
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content -->
    <div class="container-fluid p-4">
        <h2>Admin Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Events</h5>
                        <h3><?= $total_events; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Attendees</h5>
                        <h3><?= $total_attendees; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Management -->
        <div class="mt-4">
            <h4>Recent Events</h4>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Attendees</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // Fetching events along with the count of attendees
                $stmt = $conn->query("
                    SELECT e.id, e.title, e.event_date, e.location, e.max_capacity, 
                           COUNT(er.id) AS attendee_count
                    FROM events e
                    LEFT JOIN event_registrations er ON e.id = er.event_id
                    GROUP BY e.id
                    ORDER BY e.event_date DESC
                    LIMIT 5
                ");
                while ($event = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title']); ?></td>
                        <td><?= date("F j, Y", strtotime($event['event_date'])); ?></td>
                        <td><?= htmlspecialchars($event['location']); ?></td>
                        <td><?= $event['attendee_count']; ?> / <?= $event['max_capacity']; ?></td>
                        <td>
                            <a href="edit_event.php?id=<?= $event['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_event.php?id=<?= $event['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
