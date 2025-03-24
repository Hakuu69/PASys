<?php
session_start();
include('includes/header.php');  // Contains necessary HTML structure and login-check logic
include('./../connection.php');

// Ensure the user is logged in via the 'user' session array.
if (!isset($_SESSION['user'])) {
    die("Session user is not set. Please log in again.");
}

// Retrieve the user id from the session user array.
$user_id = $_SESSION['user']['id'];

// Fetch current user information from the database (including password, though it will be hidden).
$sql = "SELECT firstName, lastName, contact, email, password, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("User not found. Please check that the session user_id (" . htmlspecialchars($user_id) . ") matches a valid user in the database.");
}

$message = "";
if (isset($_POST['submit'])) {
    // Retrieve and sanitize inputs.
    $contact      = $conn->real_escape_string($_POST['contact']);
    $email        = $conn->real_escape_string($_POST['email']);
    $new_password = trim($_POST['password']); // New password input

    // Check for duplicate email for any user other than the current one.
    $email_check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $email_check_stmt = $conn->prepare($email_check_sql);
    $email_check_stmt->bind_param("si", $email, $user_id);
    $email_check_stmt->execute();
    $email_check_result = $email_check_stmt->get_result();
    if ($email_check_result->num_rows > 0) {
        $message = "Email already registered, please use different Email Address!";
    } else {
        // Determine if any changes were made.
        $changesMade = false;
        if ($contact !== $user['contact'] || $email !== $user['email'] || !empty($new_password)) {
            $changesMade = true;
        }
        
        if (!$changesMade) {
            $message = "No changes were made.";
        } else {
            // If a new password is provided, hash it; otherwise, keep the current hashed password.
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            } else {
                $hashed_password = $user['password'];
            }
            
            // Update query
            $update_sql = "UPDATE users SET contact = ?, email = ?, password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssi", $contact, $email, $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                // Optionally re-fetch updated user information.
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $message = "Profile updated successfully!";
            } else {
                $message = "Error updating profile: " . $conn->error;
            }
        }
    }
}
?>

<?php
// If there's a message, output it in a JavaScript alert.
if (!empty($message)) {
    echo "<script>alert('" . addslashes($message) . "');</script>";
}
?>

<div id="main-content">
    <div class="container">
        <div class="half-container">
            <div class="image-side">
                <img src="https://th.bing.com/th/id/OIP.qpYPAeI5kot5rub3PLUIqwHaHa?rs=1&pid=ImgDetMain" alt="Descriptive Image" />
            </div>
            <div class="form-side">
                <form method="POST">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" class="form-input" 
                           value="<?php echo htmlspecialchars($user['firstName']); ?>" readonly><br>

                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" class="form-input" 
                           value="<?php echo htmlspecialchars($user['lastName']); ?>" readonly><br>

                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" class="form-input" 
                           value="<?php echo htmlspecialchars($user['contact']); ?>" 
                           required maxlength="11" pattern="\d{11}" title="Please enter exactly 11 digits"><br>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

                    <label for="password">New Password:</label>
                    <!-- Password field is hidden and left blank; if a new password is provided, it will be updated -->
                    <input type="password" id="password" name="password" class="form-input" 
                           placeholder="Enter new password (leave blank to keep current)"><br>

                    <label for="role">Role:</label>
                    <input type="text" id="role" name="role" class="form-input" 
                           value="<?php echo htmlspecialchars($user['role']); ?>" readonly><br>

                    <input type="submit" name="submit" class="form-submit" value="Update Profile">
                </form>
            </div>
        </div>
    </div>
</div>

<script src="dist/js/script.js"></script>
<?php include('includes/footer.php'); ?>
