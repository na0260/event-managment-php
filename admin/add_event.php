<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $location = trim($_POST['location']);
    $max_capacity = (int)$_POST['max_capacity'];
    $created_by = $_SESSION['user_id'];

    if (empty($title) || empty($description) || empty($event_date) || empty($location) || $max_capacity <= 0) {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    } else {
        $upload_dir = "../uploads/";
        $cover_image = $_FILES['cover_image'];
        $thumbnail_image = $_FILES['thumbnail_image'];

        $cover_path = "";
        $thumbnail_path = "";

        if ($cover_image['error'] == 0 && $thumbnail_image['error'] == 0) {
            $cover_path = $upload_dir . time() . "_cover_" . basename($cover_image['name']);
            $thumbnail_path = $upload_dir . time() . "_thumb_" . basename($thumbnail_image['name']);

            move_uploaded_file($cover_image['tmp_name'], $cover_path);
            move_uploaded_file($thumbnail_image['tmp_name'], $thumbnail_path);
        }

        $query = "INSERT INTO events (title, description, event_date, location, max_capacity, cover_image, thumbnail_image, created_by)
                  VALUES (:title, :description, :event_date, :location, :max_capacity, :cover_image, :thumbnail_image, :created_by)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':event_date' => $event_date,
            ':location' => $location,
            ':max_capacity' => $max_capacity,
            ':cover_image' => $cover_path,
            ':thumbnail_image' => $thumbnail_path,
            ':created_by' => $created_by
        ]);

        $message = "<div class='alert alert-success'>Event added successfully!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - Admin</title>
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/admin_navbar.php"; ?>

<div class="container mt-4">
    <h2>Add New Event</h2>
    <?= $message; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="border p-4 rounded shadow">
        <div class="mb-3">
            <label class="form-label">Event Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description:</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Event Date:</label>
            <input type="datetime-local" name="event_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location:</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Max Capacity:</label>
            <input type="number" name="max_capacity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cover Image:</label>
            <input type="file" name="cover_image" class="form-control" accept="image/*" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Thumbnail Image:</label>
            <input type="file" name="thumbnail_image" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Event</button>
    </form>
</div>
</body>
</html>
