<?php
session_start();
include('./../connection.php');

// If this is an AJAX deletion request, process it and exit before any HTML is sent.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    // (Optional) Check for authentication if needed:
    if (!isset($_SESSION['user'])) {
        echo "error";
        exit;
    }
    $notificationId = (int) $_POST['notification_id'];
    $query = "DELETE FROM notifications WHERE id = $notificationId";
    if (mysqli_query($conn, $query)) {
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

// For normal page requests, continue with your header and display the page.
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

include('includes/header.php');

// Retrieve notifications along with user info (including role).
$sql = "SELECT n.id AS notif_id, n.created_at, u.firstName, u.lastName, u.role
        FROM notifications AS n
        JOIN users AS u ON n.user_id = u.id
        ORDER BY n.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div id="main-content" class="container-notification mt-4">
    <h2 class="mb-4">Notifications</h2>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div id="notification-card-<?php echo $row['notif_id']; ?>" class="card mb-3">
                <!-- Plain "X" button positioned at top right -->
                <button 
                    type="button" 
                    class="close-btn" 
                    aria-label="Close" 
                    onclick="deleteNotification(<?php echo $row['notif_id']; ?>)">
                    X
                </button>
                <div class="card-body">
                    <h5 class="card-title">
                        [<?php echo htmlspecialchars($row['role']); ?>]
                        <?php echo htmlspecialchars($row['firstName'] . ' ' . $row['lastName']); ?>
                    </h5>
                    <p class="card-text">
                        Logged in on <?php echo date('Y-m-d H:i:s', strtotime($row['created_at'])); ?>
                    </p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications available.</p>
    <?php endif; ?>
</div>

<script src="dist/js/script.js"></script>
<script src="dist/js/notifications.js"></script>
<?php
include('includes/footer.php');
?>
