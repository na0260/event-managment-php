<?php
include "config/database.php"; // Database connection
include "includes/navbar.php"; // Navbar

// Fetch all events from the database
$stmt = $conn->query("SELECT * FROM events ORDER BY date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="./assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Upcoming Events</h2>

    <div class="row">
        <?php if (count($events) > 0): ?>
            <?php foreach ($events as $event): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                            <p><strong>Date:</strong> <?= date("F d, Y - h:i A", strtotime($event['date'])) ?></p>
                            <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                            <a href="events/register.php?event_id=<?= $event['id'] ?>" class="btn btn-primary">Register</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No upcoming events.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="./assets/bootstrap.bundle.min.js"></script>

</body>
</html>
