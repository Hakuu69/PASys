<?php
session_start();
include('includes/header.php');  
include('./../connection.php');

// Check if the user id is provided in the GET parameter.
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('User ID is missing.');
            window.location.href = 'users_list.php';
          </script>";
    exit();
}

$user_id = $_GET['id'];

// Fetch the current user data from the database
$sql = "SELECT id, firstName, lastName, contact, email, password, role FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "<script>
            alert('User not found.');
            window.location.href = 'users_list.php';
          </script>";
    exit();
}

if (isset($_POST['submit'])) {
    // Get form data
    $firstName      = $_POST['firstName'];
    $lastName       = $_POST['lastName'];
    $contact        = $_POST['contact'];
    $email          = $_POST['email'];
    $password_input = $_POST['password']; // if provided, then update password
    $role           = $_POST['role'];
    
    // First, check if the new email is already registered to another user.
    $emailCheckSql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $checkStmt = mysqli_prepare($conn, $emailCheckSql);
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "si", $email, $user_id);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            echo "<script>
                    alert('Email already registered, please use a different Email Address!');
                    window.location.href = 'edit_user.php?id=$user_id';
                  </script>";
            exit();
        }
        mysqli_stmt_close($checkStmt);
    } else {
        echo "<script>
                alert('Error checking email: " . mysqli_error($conn) . "');
                window.location.href = 'edit_user.php?id=$user_id';
              </script>";
        exit();
    }
    
    // Determine if any changes were made.
    // If the password field is blank, assume no change for the password.
    $changesMade = false;
    if ($firstName !== $user['firstName'] ||
        $lastName !== $user['lastName'] ||
        $contact !== $user['contact'] ||
        $email !== $user['email'] ||
        $role !== $user['role'] ||
        !empty($password_input)) {
            $changesMade = true;
    }
    
    if (!$changesMade) {
        echo "<script>
                alert('No changes were made.');
                window.location.href = 'user-management.php';
              </script>";
        exit();
    }
    
    // Handle password update: if new password provided, hash it; otherwise, keep the current password.
    if (!empty($password_input)) {
        $password_hashed = password_hash($password_input, PASSWORD_BCRYPT);
    } else {
        $password_hashed = $user['password'];
    }
    
    // Update query
    $update_sql = "UPDATE users SET firstName = ?, lastName = ?, contact = ?, email = ?, password = ?, role = ? WHERE id = ?";
    $updateStmt = mysqli_prepare($conn, $update_sql);
    if ($updateStmt) {
        mysqli_stmt_bind_param($updateStmt, "ssssssi", $firstName, $lastName, $contact, $email, $password_hashed, $role, $user_id);
        if (mysqli_stmt_execute($updateStmt)) {
            // If the updated user is the currently logged-in user, update session data.
            if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $user_id) {
                $refreshSql = "SELECT id, firstName, lastName, contact, email, password, role FROM users WHERE id = ?";
                $refreshStmt = mysqli_prepare($conn, $refreshSql);
                mysqli_stmt_bind_param($refreshStmt, "i", $user_id);
                mysqli_stmt_execute($refreshStmt);
                $result = mysqli_stmt_get_result($refreshStmt);
                if ($result && mysqli_num_rows($result) > 0) {
                    $newUser = mysqli_fetch_assoc($result);
                    $_SESSION['user'] = $newUser;  // Refresh session data with complete record.
                    $user = $newUser; // Also update the local $user variable.
                }
                mysqli_stmt_close($refreshStmt);
            }
            echo "<script>
                    alert('User updated successfully!');
                    window.location.href = 'user-management.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error updating user: " . mysqli_error($conn) . "');
                    window.location.href = 'user-management.php';
                  </script>";
        }
        mysqli_stmt_close($updateStmt);
    } else {
        echo "<script>
                alert('Error preparing statement: " . mysqli_error($conn) . "');
                window.location.href = 'user-management.php';
              </script>";
    }
}
?>

<div id="main-content">
    <div class="container">
        <div class="half-container">
            <div class="image-side">
                <img src="https://logodix.com/logo/1984369.png" alt="Descriptive Image"/>
            </div>
            <div class="form-side">
                <form method="POST">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" class="form-input" required
                           value="<?php echo htmlspecialchars($user['firstName']); ?>"><br>

                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" class="form-input" required
                           value="<?php echo htmlspecialchars($user['lastName']); ?>"><br>

                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" class="form-input" required
                           value="<?php echo htmlspecialchars($user['contact']); ?>"
                           maxlength="11" pattern="\d{11}" title="Please enter exactly 11 digits"><br>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-input" required
                           value="<?php echo htmlspecialchars($user['email']); ?>"><br>

                    <label for="password">Password:</label>
                    <!-- Leave blank if not changing the password -->
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="Enter new password (leave blank to keep current)"><br>

                    <label for="role">Role:</label>
                    <br>
                    <select id="role" name="role" class="form-select" required>
                        <option value="Admin" <?php if($user['role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                        <option value="Kapitan" <?php if($user['role'] == 'Kapitan') echo 'selected'; ?>>Kapitan</option>
                        <option value="Kagawad" <?php if($user['role'] == 'Kagawad') echo 'selected'; ?>>Kagawad</option>
                    </select><br>

                    <input type="submit" name="submit" class="form-submit" value="Update User">
                </form>
            </div>
        </div>
    </div>
</div>

<script src="dist/js/script.js"></script>
<?php
include('includes/footer.php');
?>
