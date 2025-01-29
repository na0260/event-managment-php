<?php
session_start();
include "../config/database.php";

// Fetch events
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
    <title>Event List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <h2 class="text-center">All Events</h2>
    <div class="row mt-4">
        <?php foreach ($events as $event): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="../uploads/<?= $event['thumbnail_image']; ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($event['title']); ?></h5>
                        <p class="card-text"><?= substr(htmlspecialchars($event['description']), 0, 100); ?>...</p>
                        <p class="text-muted"><strong>Date:</strong> <?= date("F j, Y, g:i A", strtotime($event['event_date'])); ?></p>
                        <p class="text-muted"><strong>Location:</strong> <?= htmlspecialchars($event['location']); ?></p>
                        <p class="text-muted"><strong>Attendees:</strong> <?= $event['attendee_count']; ?></p>

                        <a href="view.php?id=<?= $event['id']; ?>" class="btn btn-primary w-100">View Details</a>

                        <?php
                        // Check if the user is logged in
                        if (isset($_SESSION['user_id'])):

                            // Check if the user has already registered for the event
                            $user_id = $_SESSION['user_id'];
                            $event_id = $event['id'];

                            // Query to check if the user is already registered
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM event_registrations WHERE user_id = ? AND event_id = ?");
                            $stmt->execute([$user_id, $event_id]);
                            $already_registered = $stmt->fetchColumn() > 0;

                            if ($already_registered):
                                ?>
                                <!-- Show 'Already Registered' button if the user is already registered -->
                                <button class="btn btn-secondary w-100 mt-2" disabled>Already Registered</button>
                            <?php else: ?>
                                <!-- If not registered, show the register button -->
                                <form action="register.php" method="POST" class="mt-2">
                                    <input type="hidden" name="event_id" value="<?= $event['id']; ?>">
                                    <button type="submit" class="btn btn-success w-100">Register</button>
                                </form>
                            <?php endif; ?>

                        <?php else: ?>
                            <p class="text-center text-muted mt-2"><a class="text-decoration-none" href="../auth/login.php"> Login </a> to register</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($events)): ?>
        <p class="text-center text-muted">No events available.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
