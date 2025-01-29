<?php
session_start();
include "../config/database.php";

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch attendees with event details
$query = "SELECT er.id, u.name AS attendee_name, u.email, 
                 e.title AS event_name, e.event_date, e.location 
          FROM event_registrations er
          JOIN users u ON er.user_id = u.id
          JOIN events e ON er.event_id = e.id
          ORDER BY e.event_date DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendees - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/admin_navbar.php"; ?>

<div class="container mt-4">
    <h2 class="mb-4">Registered Attendees</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Attendee Name</th>
            <th>Email</th>
            <th>Event Name</th>
            <th>Event Date</th>
            <th>Location</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($attendees) > 0): ?>
            <?php foreach ($attendees as $index => $attendee): ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($attendee['attendee_name']); ?></td>
                    <td><?= htmlspecialchars($attendee['email']); ?></td>
                    <td><?= htmlspecialchars($attendee['event_name']); ?></td>
                    <td><?= date("d M Y", strtotime($attendee['event_date'])); ?></td>
                    <td><?= htmlspecialchars($attendee['location']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No attendees registered yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
