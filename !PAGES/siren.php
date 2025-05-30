<?php
session_start();
include('includes/header.php');  
include('./../connection.php');

// Today's date for validation
$today = date('Y-m-d');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user info from session
    $firstName = $_SESSION['user']['firstName'] ?? '';
    $lastName  = $_SESSION['user']['lastName']  ?? '';
    $contact   = $_SESSION['user']['contact']   ?? '';
    $role      = $_SESSION['user']['role']      ?? '';

    // Get inputs
    $fromDate   = $_POST['from_date'] ?? '';
    $untilDate  = $_POST['until_date'] ?? '';
    $weekdays   = $_POST['weekdays']    ?? [];
    $times      = $_POST['times']       ?? [];

    // Server-side validation
    if (!$fromDate || !$untilDate) {
        echo "<script>alert('Please select both From and Until dates.');window.location='siren.php';</script>";
        exit;
    }
    if ($fromDate < $today) {
        echo "<script>alert('From date cannot be earlier than today.');window.location='siren.php';</script>";
        exit;
    }
    if (strtotime($fromDate) > strtotime($untilDate)) {
        echo "<script>alert('From date cannot be later than Until date.');window.location='siren.php';</script>";
        exit;
    }
    if (empty($weekdays)) {
        echo "<script>alert('Please select at least one weekday.');window.location='siren.php';</script>";
        exit;
    }
    if (empty($times)) {
        echo "<script>alert('Please select at least one time slot.');window.location='siren.php';</script>";
        exit;
    }

    // Loop through date range
    $current = strtotime($fromDate);
    $end     = strtotime($untilDate);

    while ($current <= $end) {
        $dayNum = date('w', $current);
        if (in_array($dayNum, $weekdays)) {
            $dateStr = date('Y-m-d', $current);
            foreach ($times as $time) {
                $safeTime = mysqli_real_escape_string($conn, $time);
                $siren_dt = "$dateStr $safeTime:00";

                // Check duplicate for ongoing sirens
                $check = mysqli_prepare($conn, "SELECT id FROM sirens WHERE siren_at = ? AND status = 'ongoing'");
                mysqli_stmt_bind_param($check, 's', $siren_dt);
                mysqli_stmt_execute($check);
                mysqli_stmt_store_result($check);
                if (mysqli_stmt_num_rows($check) === 0) {
                    // Insert as ongoing
                    $insert = mysqli_prepare($conn,
                        "INSERT INTO sirens (firstName, lastName, contact, role, siren_at, status)
                         VALUES (?, ?, ?, ?, ?, 'ongoing')");
                    mysqli_stmt_bind_param($insert, 'sssss', $firstName, $lastName, $contact, $role, $siren_dt);
                    mysqli_stmt_execute($insert);
                    mysqli_stmt_close($insert);
                }
                mysqli_stmt_close($check);
            }
        }
        $current = strtotime('+1 day', $current);
    }

    echo "<script>alert('Siren reminders scheduled!');window.location='siren.php';</script>";
    exit;
}
?>

<div id="main-contents">
    <div class="announcement-container">
        <h1 class="announcement-title">Siren Reminder System</h1>
        <form method="POST" action="siren.php">
            <div class="form-group">
                <label for="from_date">From:</label>
                <input type="date" id="from_date" name="from_date" required min="<?php echo $today; ?>">
            </div>
            <div class="form-group">
                <label for="until_date">Until:</label>
                <input type="date" id="until_date" name="until_date" required min="<?php echo $today; ?>">
            </div>
            <fieldset class="form-group">
                <legend>Select Days of Week:</legend>
                <label><input type="checkbox" name="weekdays[]" value="1"> Monday</label>
                <label><input type="checkbox" name="weekdays[]" value="2"> Tuesday</label>
                <label><input type="checkbox" name="weekdays[]" value="3"> Wednesday</label>
                <label><input type="checkbox" name="weekdays[]" value="4"> Thursday</label>
                <label><input type="checkbox" name="weekdays[]" value="5"> Friday</label>
                <label><input type="checkbox" name="weekdays[]" value="6"> Saturday</label>
                <label><input type="checkbox" name="weekdays[]" value="0"> Sunday</label>
            </fieldset>
            <fieldset class="form-group">
                <legend>Select Time Slots:</legend>
                <label><input type="checkbox" name="times[]" value="07:00"> 7:00 AM</label>
                <label><input type="checkbox" name="times[]" value="12:00"> 12:00 PM</label>
                <label><input type="checkbox" name="times[]" value="17:00"> 5:00 PM</label>
            </fieldset>
            <button type="submit" class="submit-btn">Schedule Sirens</button>
        </form>
    </div>
</div>

<style>
.form-group { margin-bottom: 1rem; }
.announcement-title { margin-bottom: 1rem; }
</style>

<script src="dist/js/script.js"></script>
<?php include('includes/footer.php'); ?>
