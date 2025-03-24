<?php
include('includes/header.php');  
include('./../connection.php');

// Check if the form is submitted for adding a user
if (isset($_POST['add_user'])) {
    // Get form data
    $firstName = $_POST['firstName'];
    $lastName  = $_POST['lastName'];
    $contact   = $_POST['contact'];
    $email     = $_POST['email'];
    $password  = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
    $role      = $_POST['role'];

    // Prepare SQL query to insert user into the database using mysqli
    $sql = "INSERT INTO users (firstName, lastName, contact, role, email, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        // Bind parameters: "ssssss" indicates six strings
        mysqli_stmt_bind_param($stmt, "ssssss", $firstName, $lastName, $contact, $role, $email, $password);

        // Execute the query and check if the user is added
        if (mysqli_stmt_execute($stmt)) {
            $message = "User added successfully!";
        } else {
            $message = "Error adding user: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Function to fetch all users from the database using mysqli
function fetchUsers($conn) {
    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql);
    $users = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    return $users;
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // Check if the current logged in user is trying to delete their own account.
    if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $deleteId) {
        echo "<script>
                alert('This is your logged in account, you cannot delete it!');
                window.location.href = 'user-management.php';
              </script>";
        exit();
    }

    $deleteSql = "DELETE FROM users WHERE id = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);
    if ($deleteStmt) {
        mysqli_stmt_bind_param($deleteStmt, "i", $deleteId);
        if (mysqli_stmt_execute($deleteStmt)) {
            echo "<script>
                    alert('User deleted successfully!');
                    window.location.href = 'user-management.php';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Error deleting user: " . mysqli_error($conn) . "');
                    window.location.href = 'user-management.php';
                  </script>";
            exit();
        }
        mysqli_stmt_close($deleteStmt);
    } else {
        echo "<script>
                alert('Error preparing delete statement: " . mysqli_error($conn) . "');
                window.location.href = 'user-management.php';
              </script>";
        exit();
    }
}

// Fetch all users
$users = fetchUsers($conn);
?>

<div id="main-content">
    <div class="content-header">
        <h2>User Management</h2>
    </div>
    <?php if (isset($message)) : ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td data-label="ID"><?php echo htmlspecialchars($user['id']); ?></td>
                    <td data-label="Role"><?php echo htmlspecialchars($user['role']); ?></td>
                    <td data-label="Last Name"><?php echo htmlspecialchars($user['lastName']); ?></td>
                    <td data-label="First Name"><?php echo htmlspecialchars($user['firstName']); ?></td>
                    <td data-label="Contact"><?php echo htmlspecialchars($user['contact']); ?></td>
                    <td data-label="Email"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td data-label="Created At"><?php echo htmlspecialchars($user['created_at']); ?></td>
                    <td data-label="Actions">
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-edit">Edit</a> |
                        <a href="?delete_id=<?php echo $user['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="dist/js/script.js"></script>
<?php
include('includes/footer.php');  
?>
