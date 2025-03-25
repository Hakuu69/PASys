document.addEventListener('DOMContentLoaded', function () {
    let announcedMessages = new Set();
    let availableVoices = [];

    function loadVoices() {
        availableVoices = speechSynthesis.getVoices();
        //console.log("🔊 Available voices:", availableVoices); // CONSOLE LOG FOR AVAILABLE VOICES
    }

    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = loadVoices;
    }
    loadVoices();

    function checkAnnouncements() {
        fetch('check-announcement.php?ts=' + new Date().getTime())
            .then(response => response.json())
            .then(data => {
                console.log("📢 Announcements Data:");
                console.log("🕒 Current Time:", data.currentTime);

                if (data.announcements && data.announcements.length > 0) {
                    data.announcements.forEach(announcement => {
                        if (!announcedMessages.has(announcement.id)) {
                            announcedMessages.add(announcement.id);
                            console.log("🔊 Announcing:", announcement.message, "with voice:", announcement.voice);
                            speakMessage(announcement.message, announcement.voice);

                            setTimeout(() => {
                                showAnnouncementModal(`${announcement.message}`);
                            }, 100);
                        }
                    });
                } else {
                    console.log("⏳ No announcements at this exact time.");
                }

                if (data.upcoming_announcements && data.upcoming_announcements.length > 0) {
                    console.log("📅 Upcoming Announcements:");
                    data.upcoming_announcements.forEach(announcement => {
                        console.log(`   - ${announcement.announce_at}: ${announcement.message}`);
                    });
                } else {
                    console.log("📭 No upcoming announcements.");
                }
            })
            .catch(error => console.error("⚠️ Error checking announcements:", error));
    }

    function speakMessage(message, voiceName) {
        if ('speechSynthesis' in window) {
            let utterance = new SpeechSynthesisUtterance(message);
            let selectedVoice = availableVoices.find(voice => voice.name === voiceName);

            if (selectedVoice) {
                utterance.voice = selectedVoice;
                console.log("✅ Using voice:", selectedVoice.name, "(", selectedVoice.lang, ")");
            } else {
                console.warn("⚠️ Selected voice not found, using default.");
            }

            speechSynthesis.speak(utterance);
        } else {
            console.warn("⚠️ Text-to-Speech not supported in this browser.");
        }
    }

    function startAtExactMinute() {
        let now = new Date();
        let secondsUntilNextMinute = 60 - now.getSeconds();
        console.log(`🕒 Syncing... Checking announcements in ${secondsUntilNextMinute} seconds`);

        setTimeout(() => {
            checkAnnouncements();
            setInterval(checkAnnouncements, 60000);
        }, secondsUntilNextMinute * 1000);
    }

    startAtExactMinute();

    // ===============================
    // New: Weather Alert Functionality
    // ===============================
    // Set this flag to true to simulate weather alerts for testing.
    const testMode = false; //FALSE = NORMAL MODE, TRUE = TEST MODE

    function checkWeatherAlert() {
        if (testMode) {
            const simulatedData = "THIS IS JUST A TEST:<br> Forecasted maximum wind speed in the next 24 hours: 80 km/h.<br>Alert: Tropical Cyclone Wind Signal No. 2";
            console.log("Test mode enabled. Simulated weather alert data:", simulatedData);
            showWeatherModal(simulatedData);
            // Read the weather alert aloud using your existing speakMessage function.
            speakMessage("THIS IS JUST A TEST: Forecasted maximum wind speed in the next 24 hours: 80 km/h. Alert: Tropical Cyclone Wind Signal No. 2", "Default Weather Voice");
            return;
        }
        
        // Normal mode: fetch weather alert from wind_alert.php
        fetch('weather/wind_alert.php')
            .then(response => response.text())
            .then(data => {
                console.log("🌬️ Weather Alert Data:", data);
                if (!data.includes("No Tropical Cyclone Wind Signal")) {
                    showWeatherModal(data);
                    // Use the same speakMessage function to read the weather alert.
                    speakMessage(data, "Default Weather Voice");
                }
            })
            .catch(error => {
                console.error("⚠️ Error fetching weather alert:", error);
            });
    }
    // Check weather alerts immediately and then every 10 minutes (600,000 ms)
    checkWeatherAlert();
    setInterval(checkWeatherAlert, 10 * 60 * 1000);
});

// Functions for Announcement Modal (existing)
function showAnnouncementModal(message) {
    var modal = document.getElementById("announcementModal");
    var modalText = document.getElementById("modalAnnouncementText");
    modalText.textContent = message;
    modal.style.display = "block";
}
  
function hideAnnouncementModal() {
    var modal = document.getElementById("announcementModal");
    modal.style.display = "none";
}
  
document.addEventListener("DOMContentLoaded", function() {
    // When the user clicks on the close button, hide the modal and conditionally refresh
    var closeBtn = document.querySelector("#announcementModal .close");
    if (closeBtn) {
      closeBtn.addEventListener("click", function() {
        hideAnnouncementModal();
        if (window.announceNowRedirect) {
          // For Announce Now modals only: redirect to announcement.php
          window.location.href = 'announcement.php';
        }
        // For other modals (Announce Later/Soon), do nothing extra
      });
    }
    
    // Also hide if clicking outside the modal content
    window.addEventListener("click", function(event) {
      var modal = document.getElementById("announcementModal");
      if (event.target === modal) {
        hideAnnouncementModal();
        if (window.announceNowRedirect) {
          window.location.href = 'announcement.php';
        }
      }
    });
});

// ===============================
// New: Weather Modal Functions
// ===============================
function showWeatherModal(message) {
    var modal = document.getElementById("weatherModal");
    var modalText = document.getElementById("modalWeatherText");

    modalText.innerHTML = message;
    modal.style.display = "block";
}


function hideWeatherModal() {
    var modal = document.getElementById("weatherModal");
    modal.style.display = "none";
}

document.addEventListener("DOMContentLoaded", function() {
    var closeBtn = document.querySelector("#weatherModal .close");
    if (closeBtn) {
        closeBtn.addEventListener("click", function() {
            hideWeatherModal();
        });
    }

    window.addEventListener("click", function(event) {
        var modal = document.getElementById("weatherModal");
        if (event.target === modal) {
            hideWeatherModal();
        }
    });
});
