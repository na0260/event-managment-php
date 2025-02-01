<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Event ID is required.");
}

$event_id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT u.name, u.email, e.title, er.registration_date
    FROM event_registrations er
    JOIN users u ON er.user_id = u.id
    JOIN events e ON er.event_id = e.id
    WHERE er.event_id = ?
");
$stmt->execute([$event_id]);
$attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendees.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Name', 'Email', 'Event Title', 'Registration Date']);

foreach ($attendees as $attendee) {
    fputcsv($output, $attendee);
}

fclose($output);
exit();
