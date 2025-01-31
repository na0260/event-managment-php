<?php
session_start();
include "../config/database.php";

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $event_id = $event_id;

        $stmt = $conn->prepare("SELECT COUNT(*) FROM event_registrations WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);
        $already_registered = $stmt->fetchColumn() > 0;
    }

    $stmt = $conn->prepare("
        SELECT e.id, e.title, e.description, e.event_date, e.location, e.cover_image, e.thumbnail_image,
               COUNT(er.id) AS attendee_count
        FROM events e
        LEFT JOIN event_registrations er ON e.id = er.event_id
        WHERE e.id = :event_id
        GROUP BY e.id
    ");
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title']); ?> - Event Details</title>
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center"><?= htmlspecialchars($event['title']); ?></h2>
            <img src="<?= $event['cover_image']; ?>" class="img-fluid" alt="<?= htmlspecialchars($event['title']); ?>">

            <div class="mt-4 d-flex justify-content-between">
                <p><strong>Date:</strong> <?= date("F j, Y, g:i A", strtotime($event['event_date'])); ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($event['location']); ?></p>
                <p><strong>Attendees:</strong> <?= $event['attendee_count']; ?></p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <p class="text-danger">Admins cannot register for events. Please login as a user to register.</p>
                        <?php elseif ($already_registered): ?>
                            <p class="text-success">You are already registered for this event.</p>
                        <?php else: ?>
                            <a href="register.php?id=<?= $event['id']; ?>" class="btn btn-primary">Register for Event</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">You must be logged in to register for this event.</p>
                    <?php endif; ?>
            </div>


            <div class="mt-4">
                <h4>Description:</h4>
                <p><?= nl2br(htmlspecialchars($event['description'])); ?></p>
            </div>
        </div>

    </div>
</div>

<script src="../assets/bootstrap.bundle.min.js"></script>
</body>
</html>
