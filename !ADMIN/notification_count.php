<?php
// notification_count.php
include('./../connection.php');
$sql = "SELECT COUNT(*) AS count FROM notifications";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
echo $row['count'];
?>
