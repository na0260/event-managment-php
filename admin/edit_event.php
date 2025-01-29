<?php
session_start();
include "../config/database.php";

// Ensure admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Check if event ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: events.php");
    exit();
}

$event_id = $_GET['id'];

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    $_SESSION['error'] = "Event not found!";
    header("Location: events.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $event_date = $_POST["event_date"];
    $location = trim($_POST["location"]);

    // Handle file uploads (cover & thumbnail)
    $upload_dir = "../uploads/";
    $cover_image = $event['cover_image'];
    $thumbnail = $event['thumbnail_image'];

    // Upload cover image if new one is provided
    if (!empty($_FILES["cover_image"]["name"])) {
        $cover_image = time() . "_cover_" . $_FILES["cover_image"]["name"];
        move_uploaded_file($_FILES["cover_image"]["tmp_name"], $upload_dir . $cover_image);

        // Delete old cover image if exists
        if (!empty($event['cover_image']) && file_exists($upload_dir . $event['cover_image'])) {
            unlink($upload_dir . $event['cover_image']);
        }
    }

    // Upload thumbnail image if new one is provided
    if (!empty($_FILES["thumbnail_image"]["name"])) {
        $thumbnail = time() . "_thumb_" . $_FILES["thumbnail_image"]["name"];
        move_uploaded_file($_FILES["thumbnail_image"]["tmp_name"], $upload_dir . $thumbnail);

        // Delete old thumbnail image if exists
        if (!empty($event['thumbnail_image']) && file_exists($upload_dir . $event['thumbnail_image'])) {
            unlink($upload_dir . $event['thumbnail_image']);
        }
    }

    // Update event details with new cover and thumbnail images
    $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, location = ?, cover_image = ?, thumbnail_image = ? WHERE id = ?");
    if ($stmt->execute([$title, $description, $event_date, $location, $cover_image, $thumbnail, $event_id])) {
        $_SESSION['success'] = "Event updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update event!";
    }

    // Redirect to events list
    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/admin_navbar.php"; ?>
<div class="container mt-5">
    <h2>Edit Event</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Event Title</label>
            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($event['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" required><?= htmlspecialchars($event['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Event Date</label>
            <input type="datetime-local" class="form-control" name="event_date" value="<?= $event['event_date']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($event['location']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Cover Image (Leave empty to keep current)</label>
            <input type="file" class="form-control" name="cover_image">
            <?php if ($event['cover_image']): ?>
                <img src="../uploads/<?= $event['cover_image']; ?>" alt="Cover Image" class="img-thumbnail mt-2" width="200">
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Thumbnail (Leave empty to keep current)</label>
            <input type="file" class="form-control" name="thumbnail_image">
            <?php if ($event['thumbnail_image']): ?>
                <img src="../uploads/<?= $event['thumbnail_image']; ?>" alt="Thumbnail" class="img-thumbnail mt-2" width="100">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update Event</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
