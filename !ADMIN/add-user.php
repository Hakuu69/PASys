<?php
include('includes/header.php');  
include('./../connection.php');

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get form data
    $firstName = $_POST['firstName'];
    $lastName  = $_POST['lastName'];
    $contact   = $_POST['contact'];
    $email     = $_POST['email'];
    $password  = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
    $role      = $_POST['role'];

    // Prepare SQL query to insert user into the database using mysqli
    $sql = "INSERT INTO users (firstName, lastName, contact, email, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        // Bind parameters: "ssssss" indicates six strings
        mysqli_stmt_bind_param($stmt, "ssssss", $firstName, $lastName, $contact, $email, $password, $role);

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
?>

<div id="main-content">
    <?php
    // Display success or error message
    if (isset($message)) {
        echo "<p class='alert-message'>$message</p>";
    }
    ?>

    <div class="container">
        <div class="half-container">
            <div class="image-side">
                <img src="https://i.ibb.co/c85Hv6B/3530040-64619.jpg" alt="Descriptive Image" />
            </div>
            <div class="form-side">
                <form method="POST">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" class="form-input" required><br>

                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" class="form-input" required><br>

                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" class="form-input" required><br>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-input" required><br>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-input" required><br>

                    <label for="role">Role:</label>
                    <br>
                    <select id="role" name="role" class="form-select" required>
                        <option value="Admin">Admin</option>
                        <option value="Kapitan">Kapitan</option>
                        <option value="Kagawad">Kagawad</option>
                    </select><br>

                    <input type="submit" name="submit" class="form-submit" value="Add User">
                </form>
            </div>
        </div>
    </div>
</div>

<script src="dist/js/script.js"></script>
<?php
include('includes/footer.php');  // Ensure footer.php closes any open tags (if any)
?>
