<?php
include('includes/header.php');  
include('./../connection.php');

// Handle announcement deletion via GET parameter
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteSql = "DELETE FROM announcements WHERE id = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);
    if ($deleteStmt) {
        mysqli_stmt_bind_param($deleteStmt, "i", $deleteId);
        if (mysqli_stmt_execute($deleteStmt)) {
            echo "<script>alert('Announcement deleted successfully!'); window.location.href='history.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error deleting announcement: " . addslashes(mysqli_error($conn)) . "'); window.location.href='history.php';</script>";
            exit;
        }
        mysqli_stmt_close($deleteStmt);
    } else {
        echo "<script>alert('Error preparing delete statement: " . addslashes(mysqli_error($conn)) . "'); window.location.href='history.php';</script>";
        exit;
    }
}

// Function to fetch all announcements using mysqli
function fetchAnnouncements($conn) {
    $sql = "SELECT * FROM announcements";
    $result = mysqli_query($conn, $sql);
    $announcements = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $announcements[] = $row;
        }
    }
    return $announcements;
}

// Fetch all announcements
$announcements = fetchAnnouncements($conn);
?>

<div id="main-content">
    <div class="content-header">
        <h2>Announcement History</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Contact</th>
                    <th>Message</th>
                    <th>Created At</th>
                    <th>Announce At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($announcements as $announcement): ?>
                <tr>
                    <td data-label="ID"><?php echo htmlspecialchars($announcement['id']); ?></td>
                    <td data-label="Role"><?php echo htmlspecialchars($announcement['role']); ?></td>
                    <td data-label="Last Name"><?php echo htmlspecialchars($announcement['lastName']); ?></td>
                    <td data-label="First Name"><?php echo htmlspecialchars($announcement['firstName']); ?></td>
                    <td data-label="Contact"><?php echo htmlspecialchars($announcement['contact']); ?></td>
                    <td data-label="Message" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <?php echo htmlspecialchars($announcement['message']); ?>
                    </td>
                    <td data-label="Created At"><?php echo htmlspecialchars($announcement['created_at']); ?></td>
                    <td data-label="Announce At"><?php echo htmlspecialchars($announcement['announce_at']); ?></td>
                    <td data-label="Status"><?php echo htmlspecialchars($announcement['status']); ?></td>
                    <td data-label="Actions">
                        <a href="?delete_id=<?php echo $announcement['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="dist/js/script.js"></script>
<script src="dist/js/sortTable.js"></script>
<?php
include('includes/footer.php');  
?>
