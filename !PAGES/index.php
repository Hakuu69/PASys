<?php
session_start(); // Start the session at the beginning

include('includes/header.php'); 
include('./../connection.php');

// Query to count users using mysqli
$sql = "SELECT COUNT(*) AS user_count FROM users";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $user_count = $row['user_count'];
} else {
    $user_count = 0;
    echo "Error: " . mysqli_error($conn);
}

// Query to count announcements using mysqli
$sql = "SELECT COUNT(*) AS announcement_count FROM announcements";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $announcement_count = $row['announcement_count'];
} else {
    $announcement_count = 0;
    echo "Error: " . mysqli_error($conn);
}

// Query to count pending announcements 
$sql = "SELECT COUNT(*) AS pending_count FROM announcements WHERE status = 'pending'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $pending_count = $row['pending_count'];
} else {
    $pending_count = 0;
    echo "Error: " . mysqli_error($conn);
}
?>

<div id="main-content">

    <div class="welcome-message">
        <div id="time-display" class="time-display"></div>
        <h2>Welcome to the Dashboard, 
            <?php 
            if (isset($_SESSION['user'])) {
                $role = htmlspecialchars($_SESSION['user']['role'], ENT_QUOTES, 'UTF-8');
                $firstName = htmlspecialchars($_SESSION['user']['firstName'], ENT_QUOTES, 'UTF-8');
                $lastName = htmlspecialchars($_SESSION['user']['lastName'], ENT_QUOTES, 'UTF-8');
                echo "$role $firstName $lastName";
            } else {
                echo 'USER NOT LOGGED IN, PLEASE CONTACT THE DEVELOPER';
            }
            ?>!
        </h2>
    </div>

    <div class="w3-row-padding w3-margin-bottom">
        <div class="w3-quarter">
            <div class="w3-container w3-orange">
                <div class="w3-left">
                    <i class="material-icons w3-icon">group</i> <!-- Users icon -->
                </div>
                <div class="w3-right user-info">
                    <h3><?php echo $user_count; ?></h3> <!-- Display the user count -->
                </div>
                <div class="w3-clear"></div>
                <h4>Users</h4>
            </div>
        </div>

        <div class="w3-quarter">
            <div class="w3-container w3-blue">
                <div class="w3-left">
                    <i class="material-icons w3-icon">record_voice_over</i> <!-- Announcement icon -->
                </div>
                <div class="w3-right user-info">
                    <h3><?php echo $announcement_count; ?></h3> <!-- Display announcement count -->
                </div>
                <div class="w3-clear"></div>
                <h4>Announcements</h4>
            </div>
        </div>

        <div class="w3-quarter">
            <div class="w3-container w3-red">
                <div class="w3-left">
                    <i class="material-icons w3-icon">announcement</i> <!-- Pending announcements icon -->
                </div>
                <div class="w3-right user-info">
                    <h3><?php echo $pending_count; ?></h3> <!-- Display pending announcement count -->
                </div>
                <div class="w3-clear"></div>
                <h4>Upcoming Announcements</h4>
            </div>
        </div>

        <div class="w3-quarter">
            <div class="w3-container w3-teal">
                <div class="w3-left">
                    <i class="material-icons w3-icon">phone</i> <!-- Phone icon -->
                </div>
                <div class="w3-right user-info">
                    <h3>09xx-xxx-xxxx</h3> <!-- Placeholder for Contact Number -->
                </div>
                <div class="w3-clear"></div>
                <h4>Developer Contact Num.</h4>
            </div>
        </div>
    </div>

    <div class="containers">
        <div class="calendar">
            <div class="head"> 
                <div class="month">
                    <span id="prev" class="arrow">&#10094;</span>
                    <span id="month-year"></span>
                    <span id="next" class="arrow">&#10095;</span>
                </div>
            </div>
            <div class="days-of-week">
                <div class="day">Sun</div>
                <div class="day">Mon</div>
                <div class="day">Tue</div>
                <div class="day">Wed</div>
                <div class="day">Thu</div>
                <div class="day">Fri</div>
                <div class="day">Sat</div>
            </div>
            <div class="days" id="days"></div>
        </div>
        <div class="map">
            <iframe class="region3" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13586.064340004847!2d120.4479552764402!3d15.660168123993168!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3396b4927f0c4f6d%3A0xca63e6b21abcfe3a!2sTaguiporo%2C%20Tarlac!5e1!3m2!1sen!2sph!4v1729062082838!5m2!1sen!2sph" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>      
    </div>
</div>

<script src="dist/js/script.js"></script>
<script src="dist/js/index.js"></script>
<script src="dist/js/calendar.js"></script>

<?php
include('includes/footer.php');  // Ensure footer.php closes any open tags (if any)
?>
