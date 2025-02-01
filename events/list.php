<?php
session_start();
include "../config/database.php";

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
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">
    <h2 class="text-center">All Events</h2>
    <div class="row mt-4">
        <?php foreach ($events as $event): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="ratio ratio-16x9">
                        <img src="../uploads/<?= $event['thumbnail_image']; ?>" class="card-img-top"
                             alt="<?= htmlspecialchars($event['title']); ?>" style="object-fit: cover;">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($event['title']); ?></h5>
                        <p class="text-muted">
                            <strong>Date:</strong> <?= date("F j, Y, g:i A", strtotime($event['event_date'])); ?></p>
                        <p class="text-muted"><strong>Location:</strong> <?= htmlspecialchars($event['location']); ?>
                        </p>
                        <p class="text-muted"><strong>Attendees:</strong> <?= $event['attendee_count']; ?></p>

                        <a href="view.php?id=<?= $event['id']; ?>" class="btn btn-primary w-100">View Details</a>

                        <?php
                        if (isset($_SESSION['user_id'])):
                            $user_id = $_SESSION['user_id'];
                            $event_id = $event['id'];
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM event_registrations WHERE user_id = ? AND event_id = ?");
                            $stmt->execute([$user_id, $event_id]);
                            $already_registered = $stmt->fetchColumn() > 0;

                            if ($already_registered):
                                ?>
                                <button class="btn btn-secondary w-100 mt-2" disabled>Already Registered</button>
                            <?php else: ?>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <button disabled class="btn btn-secondary w-100 mt-2">Login in as user</button>
                                <?php else: ?>
                                        <a href="register.php?id=<?= $event['id']; ?>"
                                           class="btn btn-primary w-100 mt-2">Register for Event</a>
                                <?php endif; ?>
                            <?php endif; ?>


                        <?php else: ?>
                            <p class="text-center text-muted mt-2"><a class="text-decoration-none"
                                                                      href="../auth/login.php"> Login </a> to register
                            </p>
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

<script src="../assets/bootstrap.bundle.min.js"></script>
</body>
</html>
