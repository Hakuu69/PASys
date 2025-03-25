<?php
// notification_count.php
include('./../connection.php');
session_start();

$userRole = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'guest';

if ($userRole === 'Admin') {
    // Admin sees all notifications.
    $sql = "SELECT COUNT(*) AS count FROM notifications";
} else {
    // Non-admins (Kapitan and Kagawad) see notifications only from users that are not Admin.
    $sql = "SELECT COUNT(*) AS count 
            FROM notifications n 
            JOIN users u ON n.user_id = u.id 
            WHERE u.role != 'Admin'";
}

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
echo $row['count'];
?>
