<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

$total_events = $conn->query("SELECT COUNT(*) FROM events")->fetchColumn();
$total_attendees = $conn->query("SELECT COUNT(*) FROM event_registrations")->fetchColumn();

$limit = 5; // Number of events per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'event_date';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

$search_query = "";
if (!empty($_GET['search'])) {
    $search_query = " AND e.title LIKE :search";
}

$query = "
    SELECT e.id, e.title, e.event_date, e.location, e.max_capacity, 
           COUNT(er.id) AS attendee_count
    FROM events e
    LEFT JOIN event_registrations er ON e.id = er.event_id
    WHERE 1 {$search_query}
    GROUP BY e.id
    ORDER BY {$sort_column} {$sort_order}
    LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($query);
if (!empty($search_query)) {
    $stmt->bindValue(':search', '%' . $_GET['search'] . '%', PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total event count for pagination
$total_rows = $conn->query("SELECT COUNT(*) FROM events")->fetchColumn();
$total_pages = ceil($total_rows / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/admin.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <?php include "sidebar.php"; ?>

    <div class="container-fluid p-4">
        <h2>Admin Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Events</h5>
                        <h3><?= $total_events; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Attendees</h5>
                        <h3><?= $total_attendees; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h4>Recent Events</h4>

            <form method="GET" class="mb-3 d-flex">
                <input type="text" name="search" placeholder="Search by title..." class="form-control me-2">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th><a href="?sort=title&order=<?= $sort_order === 'ASC' ? 'desc' : 'asc' ?>">Title</a></th>
                    <th><a href="?sort=event_date&order=<?= $sort_order === 'ASC' ? 'desc' : 'asc' ?>">Date</a></th>
                    <th><a href="?sort=location&order=<?= $sort_order === 'ASC' ? 'desc' : 'asc' ?>">Location</a></th>
                    <th><a href="?sort=attendee_count&order=<?= $sort_order === 'ASC' ? 'desc' : 'asc' ?>">Attendees</a></th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title']); ?></td>
                        <td><?= date("F j, Y", strtotime($event['event_date'])); ?></td>
                        <td><?= htmlspecialchars($event['location']); ?></td>
                        <td><?= $event['attendee_count']; ?> / <?= $event['max_capacity']; ?></td>
                        <td>
                            <a href="edit_event.php?id=<?= $event['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_event.php?id=<?= $event['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            <a href="export_attendees.php?id=<?= $event['id']; ?>" class="btn btn-sm btn-info">Export CSV</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
</body>
</html>
