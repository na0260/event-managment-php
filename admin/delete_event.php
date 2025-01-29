<?php
session_start();
include "../config/database.php";

// Ensure admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Check if event ID is provided
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Get the event details to delete images
    $stmt = $conn->prepare("SELECT cover_image, thumbnail FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($event) {
        // Delete event images
        if (!empty($event['cover_image']) && file_exists("../uploads/" . $event['cover_image'])) {
            unlink("../uploads/" . $event['cover_image']);
        }
        if (!empty($event['thumbnail']) && file_exists("../uploads/" . $event['thumbnail'])) {
            unlink("../uploads/" . $event['thumbnail']);
        }

        // Delete event registrations first (to maintain database integrity)
        $conn->prepare("DELETE FROM event_registrations WHERE event_id = ?")->execute([$event_id]);

        // Now delete the event
        $deleteStmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        if ($deleteStmt->execute([$event_id])) {
            $_SESSION['success'] = "Event deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete event!";
        }
    } else {
        $_SESSION['error'] = "Event not found!";
    }
} else {
    $_SESSION['error'] = "Invalid request!";
}

// Redirect to events page
header("Location: events.php");
exit();
