<?php
session_start();
include "config/database.php";

$stmt = $conn->query("
    SELECT e.id, e.title, e.description, e.event_date, e.location, e.thumbnail_image, 
           COUNT(er.id) AS attendee_count
    FROM events e
    LEFT JOIN event_registrations er ON e.id = er.event_id
    GROUP BY e.id
    ORDER BY e.event_date ASC
");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link href="./assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "./includes/navbar.php"; ?>

<div class="container mt-5">
    <h2 class="text-center">Upcoming Events</h2>
    <div class="row mt-4">
        <?php foreach ($events as $event): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="uploads/<?= $event['thumbnail_image']; ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($event['title']); ?></h5>
                        <p class="card-text"><?= substr(htmlspecialchars($event['description']), 0, 100); ?>...</p>
                        <p class="text-muted"><strong>Date:</strong> <?= date("F j, Y, g:i A", strtotime($event['event_date'])); ?></p>
                        <p class="text-muted"><strong>Location:</strong> <?= htmlspecialchars($event['location']); ?></p>
                        <p class="text-muted"><strong>Attendees:</strong> <?= $event['attendee_count']; ?></p>
                        <a href="./events/view.php?id=<?= $event['id']; ?>" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($events)): ?>
        <p class="text-center text-muted">No upcoming events.</p>
    <?php endif; ?>
</div>

<script src="./assets/bootstrap.bundle.min.js"></script>
</body>
</html>
