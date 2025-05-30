<?php
require_once '../connection.php';

// Set timezone to ensure correct time comparison
date_default_timezone_set('Asia/Manila');

// Get the exact current server time (rounded to the nearest minute)
$currentTime = date('Y-m-d H:i:00');

// ——————————————
// ANNOUNCEMENTS
// ——————————————

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

// ——————————————
// SIRENS
// ——————————————

// Fetch sirens that should fire NOW
$sirenQuery = "SELECT * FROM sirens WHERE status = 'ongoing' AND siren_at = ?";
$sirenStmt = $conn->prepare($sirenQuery);
$sirenStmt->bind_param("s", $currentTime);
$sirenStmt->execute();
$sirenResult = $sirenStmt->get_result();

$sirens = [];
while ($row = $sirenResult->fetch_assoc()) {
    $sirens[] = $row;
}

$upcomingSirenResult = mysqli_query($conn, "
    SELECT siren_at FROM sirens 
    WHERE siren_at > NOW()
    ORDER BY siren_at ASC
");

$upcoming_sirens = [];
while ($row = mysqli_fetch_assoc($upcomingSirenResult)) {
    $upcoming_sirens[] = $row;
}

// Only mark sirens as completed if they actually fired
if (!empty($sirens)) {
    $completeSiren = "UPDATE sirens SET status = 'completed' WHERE siren_at = ? AND status = 'ongoing'";
    $compStmt = $conn->prepare($completeSiren);
    $compStmt->bind_param("s", $currentTime);
    $compStmt->execute();
    $compStmt->close();
}

$sirenStmt->close();
$conn->close();

// ——————————————
// OUTPUT JSON
// ——————————————
echo json_encode([
    "currentTime"             => $currentTime,
    "announcements"           => $announcements,
    "upcoming_announcements"  => $upcomingAnnouncements,
    "sirens"                  => $sirens,
    "upcoming_sirens"         => $upcoming_sirens 
]);
