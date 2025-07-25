<?php
session_start();
include('includes/header.php');  
include('./../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize announcement text
    $message_text = mysqli_real_escape_string($conn, $_POST['announcement-text']);
    
    // Get user info from session if available; otherwise, use empty strings
    if (isset($_SESSION['user'])) {
        $firstName = $_SESSION['user']['firstName'];
        $lastName = $_SESSION['user']['lastName'];
        $contact = $_SESSION['user']['contact'];
        $role = $_SESSION['user']['role'];
    } else {
        $firstName = "";
        $lastName = "";
        $contact = "";
        $role = "";
    }
    
    // Get voice and language from the form
    $language = isset($_POST['language']) ? mysqli_real_escape_string($conn, $_POST['language']) : '';
    $voice = isset($_POST['voice']) ? mysqli_real_escape_string($conn, $_POST['voice']) : '';

    // Determine announce type and set announce_at and status accordingly.
    $announce_type = isset($_POST['announce_type']) ? $_POST['announce_type'] : 'now';
    if ($announce_type === 'later' && !empty($_POST['announce_at'])) {
        // For announce later, ensure announce_at is provided
        $announce_at = mysqli_real_escape_string($conn, $_POST['announce_at']);
        $status = 'pending';

        // Check if an announcement already exists at the same time
        $check_query = "SELECT id FROM announcements WHERE announce_at = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "s", $announce_at);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
                
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
        echo "<script>alert('An announcement is already scheduled for this time. Please choose a different time.');</script>";
        echo "<script>window.history.back();</script>"; // Go back without refreshing
        exit;
        }
        mysqli_stmt_close($check_stmt);
    } else {
        // For announce now, use current time and mark as completed.
        $announce_at = date("Y-m-d H:i:s");
        $status = 'completed';
    }
    
    // Prepare SQL to insert announcement (including language and voice)
    $sql = "INSERT INTO announcements (firstName, lastName, contact, role, message, language, voice, announce_at, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssssss", $firstName, $lastName, $contact, $role, $message_text, $language, $voice, $announce_at, $status);
        if (mysqli_stmt_execute($stmt)) {
        if ($announce_type === 'now') {
        // Prepare the raw message for TTS: replace literal \r\n, \r, and \n with a space.
        $rawMessageTextForTTS = str_replace(array("\\r\\n", "\\r", "\\n"), " ", $message_text);
        // Prepare the formatted message for display: replace literal \r\n, \r, and \n with <br> tags.
        $formattedMessageTextForModal = str_replace(array("\\r\\n", "\\r", "\\n"), "<br>", $message_text);

    echo '<script>
    setTimeout(function(){
        var rawMessageText = ' . json_encode($rawMessageTextForTTS) . ';
        var formattedMessageText = ' . json_encode($formattedMessageTextForModal) . ';
        var now = new Date();
        var formattedTime = now.toLocaleTimeString("en-US", { 
            hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: true 
        });

        // Azure Speech SDK: Speak each sentence, wait for finish, no repeat
        function speakMessage(message, voiceName) {
        let preAnnouncementSound = new Audio("./../ext/sounds/announcementsfx.mp3");

        // Split message into sentences (by . ! ? or line break)
        let phrases = (message.match(/[^.!?]+[.!?]?/g) || [message])
            .map(p => p.trim())
            .filter(p => p.length > 0);

        function speakAllPhrases() {
            let index = 0;
            let synthesizer = null;
            if (window.SpeechSDK) {
                const speechConfig = SpeechSDK.SpeechConfig.fromSubscription("CMo1vqlBhtRiHVg4g6xWcBPQUQCkxWeIfTz262a0jtEb6Ifz6bNQJQQJ99BGACqBBLyXJ3w3AAAYACOGZ5jX", "southeastasia");
                speechConfig.speechSynthesisVoiceName = voiceName;
                const audioConfig = SpeechSDK.AudioConfig.fromDefaultSpeakerOutput();
                synthesizer = new SpeechSDK.SpeechSynthesizer(speechConfig, audioConfig);
            } else {
                alert("Azure Speech SDK not loaded.");
                return;
            }

            function speakNextPhrase() {
                if (index >= phrases.length) {
                    synthesizer.close();
                    return;
                }
                synthesizer.speakTextAsync(
                    phrases[index],
                    function(result) {
                        index++;
                        speakNextPhrase();
                    },
                    function(error) {
                        console.error(error);
                        index++;
                        speakNextPhrase();
                    }
                );
            }
            speakNextPhrase();
        }

        function onPreSoundEnded() {
            preAnnouncementSound.removeEventListener("ended", onPreSoundEnded);
            speakAllPhrases();
        }
        preAnnouncementSound.addEventListener("ended", onPreSoundEnded);

        preAnnouncementSound.play().catch(err => {
            console.error("Error playing pre-announcement sound:", err);
            speakAllPhrases();
        });
    }

        speakMessage(rawMessageText, ' . json_encode($voice) . ');

        window.announceNowRedirect = true;
        showAnnouncementModal(formattedMessageText, formattedTime);

        setTimeout(function(){
            var modalTimeElement = document.getElementById("modalAnnouncementTime");
            if(modalTimeElement){
                modalTimeElement.textContent = "🕒 " + formattedTime;
                console.log("✅ Manually updated modal time: " + formattedTime);
            } else {
                console.error("🚨 Failed to update modal time, element not found!");
            }
        }, 1);
    }, 500);
    </script>';
        exit;
    }else {
                echo "<script>alert('Announcement scheduled successfully!'); window.location.href='announcement.php';</script>";
            }
            exit;
        } else {
            echo "<script>alert('Error submitting announcement: " . addslashes(mysqli_error($conn)) . "'); window.location.href='announcement.php';</script>";
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error preparing statement: " . addslashes(mysqli_error($conn)) . "'); window.location.href='announcement.php';</script>";
        exit;
    }
}

// Pre-fill full name from session if available
$fullName = "";
if (isset($_SESSION['user'])) {
    $fullName = $_SESSION['user']['firstName'] . " " . $_SESSION['user']['lastName'];
}
?>

<div id="main-contents">
    <div class="announcement-container">
        <h1 class="announcement-title">Text and Speech Announcement</h1>
        
        <form id="announcementForm" method="POST" action="">
            <!-- Announce type options -->
            <div class="announce-type">
                <p class="choose-label-header">When would you like to announce?</p>
                <input type="radio" id="announce-now" name="announce_type" value="now" checked>
                <label for="announce-now" class="choose-label">Announce Now</label>
                <input type="radio" id="announce-later" name="announce_type" value="later">
                <label for="announce-later" class="choose-label">Announce Later/Soon</label>
            </div>
            <br><br>
            <label for="username" class="name-label">Your Name:</label>
            <!-- Pre-filled with First Name + Last Name; set to readonly -->
            <input type="text" id="username" name="username" class="name-input" value="<?php echo htmlspecialchars($fullName); ?>" required readonly>

            <label for="announcement-text" class="announcement-label">Announcement:</label>
            <textarea id="announcement-text" name="announcement-text" class="announcement-input" placeholder="Enter your announcement here..." required></textarea>

            <label for="language-select" class="language-label">Choose Language:</label>
            <select id="language-select" name="language" class="language-select">
                <option value="en-US">English</option>
                <option value="fil-PH">Filipino</option>
                <option value="ilo-PH">Iloko</option>
            </select>

            <label for="voice-select" class="voice-label">Choose Voice:</label>
            <select id="voice-select" name="voice" class="voice-select"></select>

            <!-- Datetime picker for Announce Later (hidden by default) -->
            <div id="announce-later-container" class="announce_at-label" style="display:none;">
                <label for="announce_at">Select Date and Time:</label>
                <input type="datetime-local" id="announce_at" name="announce_at" class="announce-at">
            </div>

<div style="display: flex; justify-content: space-between; width: 100%;">
    <button id="speak-button" type="button" class="speak-btn">Speak</button>
    <button type="submit" class="submit-btn">Submit Announcement</button>
</div>

        </form>
    </div>
</div>

<script src="dist/js/voice.js"></script>
<script src="dist/js/script.js"></script>
<script src="dist/js/index.js"></script>
<?php
include('includes/footer.php');
?>