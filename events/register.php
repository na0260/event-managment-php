<?php
include "../config/database.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$event_id = $_GET["id"];
$stmt = $conn->prepare("INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)");
$stmt->execute([$_SESSION["user_id"], $event_id]);

header("Location: list.php");
exit();
?>
