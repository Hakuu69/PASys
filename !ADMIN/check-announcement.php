<?php
require_once '../connection.php';

// Set timezone to ensure correct time comparison
date_default_timezone_set('Asia/Manila');

// Get the exact current server time (rounded to the nearest minute)
$currentTime = date('Y-m-d H:i:00');

// Fetch announcements that should be announced NOW
$query = "SELECT * FROM announcements WHERE status = 'pending' AND announce_at = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $currentTime);
$stmt->execute();
$result = $stmt->get_result();

$announcements = [];
while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}

// Fetch upcoming announcements (only future announcements that are pending)
$upcomingQuery = "SELECT * FROM announcements WHERE status = 'pending' AND announce_at > ? ORDER BY announce_at ASC";
$upcomingStmt = $conn->prepare($upcomingQuery);
$upcomingStmt->bind_param("s", $currentTime);
$upcomingStmt->execute();
$upcomingResult = $upcomingStmt->get_result();

$upcomingAnnouncements = [];
while ($row = $upcomingResult->fetch_assoc()) {
    $upcomingAnnouncements[] = $row;
}

// Only mark announcements as completed if they were actually announced
if (!empty($announcements)) {
    $updateQuery = "UPDATE announcements SET status = 'completed' WHERE announce_at = ? AND status = 'pending'";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("s", $currentTime);
    $updateStmt->execute();
    $updateStmt->close();
}

$stmt->close();
$upcomingStmt->close();
$conn->close();

echo json_encode([
    "announcements" => $announcements,
    "upcoming_announcements" => $upcomingAnnouncements,
    "currentTime" => $currentTime
]);
?>
