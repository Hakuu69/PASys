
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../connection.php';

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Get current time (with seconds)
$currentTime = date('Y-m-d H:i:s');

// ===============================
// ANNOUNCEMENTS (per minute)
// ===============================
$query = "SELECT * FROM announcements WHERE status = 'pending' AND announce_at = ?";
$stmt = $conn->prepare($query);
$announceMinute = date('Y-m-d H:i:00');
$stmt->bind_param("s", $announceMinute);
$stmt->execute();
$result = $stmt->get_result();

$announcements = [];
while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}

// Fetch upcoming announcements
$upcomingQuery = "SELECT * FROM announcements WHERE status = 'pending' AND announce_at > ? ORDER BY announce_at ASC";
$upcomingStmt = $conn->prepare($upcomingQuery);
$upcomingStmt->bind_param("s", $announceMinute);
$upcomingStmt->execute();
$upcomingResult = $upcomingStmt->get_result();

$upcomingAnnouncements = [];
while ($row = $upcomingResult->fetch_assoc()) {
    $upcomingAnnouncements[] = $row;
}

// Mark announcements as completed if announced
if (!empty($announcements)) {
    $updateQuery = "UPDATE announcements SET status = 'completed' WHERE announce_at = ? AND status = 'pending'";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("s", $announceMinute);
    $updateStmt->execute();
    $updateStmt->close();
}

$stmt->close();
$upcomingStmt->close();

// ===============================
// SIRENS (per second)
// ===============================

// Fetch sirens that should fire NOW (±5 seconds window)
$sirenQuery = "SELECT * FROM sirens WHERE status = 'ongoing' AND siren_at BETWEEN DATE_SUB(?, INTERVAL 5 SECOND) AND DATE_ADD(?, INTERVAL 5 SECOND)";
$sirenStmt = $conn->prepare($sirenQuery);
$sirenStmt->bind_param("ss", $currentTime, $currentTime);
$sirenStmt->execute();
$sirenResult = $sirenStmt->get_result();

$sirens = [];
while ($row = $sirenResult->fetch_assoc()) {
    $sirens[] = $row;
}

// Fetch upcoming sirens
$upcomingSirenQuery = "SELECT siren_at FROM sirens WHERE siren_at > ? ORDER BY siren_at ASC";
$upcomingSirenStmt = $conn->prepare($upcomingSirenQuery);
$upcomingSirenStmt->bind_param("s", $currentTime);
$upcomingSirenStmt->execute();
$upcomingSirenResult = $upcomingSirenStmt->get_result();

$upcoming_sirens = [];
while ($row = $upcomingSirenResult->fetch_assoc()) {
    $upcoming_sirens[] = $row;
}

// Mark sirens as completed if they fired
if (!empty($sirens)) {
    $completeSiren = "UPDATE sirens SET status = 'completed' WHERE status = 'ongoing' AND siren_at BETWEEN DATE_SUB(?, INTERVAL 5 SECOND) AND DATE_ADD(?, INTERVAL 5 SECOND)";
    $completeStmt = $conn->prepare($completeSiren);
    $completeStmt->bind_param("ss", $currentTime, $currentTime);
    $completeStmt->execute();
    $completeStmt->close();
}

$sirenStmt->close();
$upcomingSirenStmt->close();
$conn->close();

// ===============================
// OUTPUT JSON
// ===============================
header('Content-Type: application/json');
echo json_encode([
    "currentTime"             => $currentTime,
    "announcements"           => $announcements,
    "upcoming_announcements"  => $upcomingAnnouncements,
    "sirens"                  => $sirens,
    "upcoming_sirens"         => $upcoming_sirens
]);