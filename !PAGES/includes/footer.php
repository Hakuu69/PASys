<!-- Modal Markup for Announcements -->
<div id="announcementModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modalAnnouncementTime" class="modal-time">Loading time...</div>
        <h2 id="modalAnnouncementTitle" class="modal-title">📢 Announcement 📢</h2> 
        <p id="modalAnnouncementText"></p>
    </div>
</div>

<!-- Weather Alert Modal -->
<div id="weatherModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modalWeatherTime" class="modal-time">Loading time...</div> <!-- ✅ Live updating time -->
        <div class="weather-modal-title">🚨 ALERT 🚨</div> 
        <p id="modalWeatherText"></p> 
    </div>
</div>

<div id="sirenModal" class="modal siren-modal">
  <div class="modal-content small-modal top-right">
    <img src="./../ext/img/siren-logo.png" alt="Siren" class="modal-logo">
    <h4>MANUAL SIREN: </h4>
    <p>Ending in: <span id="countdownSeconds">180</span> seconds</p>
    <audio id="sirenAudio" src="./../ext/sounds/siren.mp3" preload="auto" loop></audio>
  </div>
</div>

<!-- SOS Floating Button -->
<button id="sosButton" class="sos-float-btn">SOS</button>

<!-- SOS Modal -->
<div id="manualsirenModal" class="modal siren-modal">
  <div class="modal-content small-modal top-right">
    <!-- ✕ Close Button -->
    <span id="manualCloseBtn" class="close-btn">&times;</span>

    <img src="./../ext/img/siren-logo.png" alt="Siren" class="modal-logo">
    <h4>SCHEDULED SIREN:</h4>
    <p>Ending in: <span id="manualcountdownSeconds">600</span> seconds</p>
    <audio id="manualsirenAudio" src="./../ext/sounds/siren.mp3" preload="auto" loop></audio>
  </div>
</div>

<style>
  /* SOS Floating Button */
  .sos-float-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #e53935;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    z-index: 9999;
    transition: background-color 0.3s ease;
  }
  .sos-float-btn:hover {
    background-color: #c62828;
  }

  /* The modal's background overlay */
  .siren-modal {
    display: none; /* hidden by default */
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.8);
  }

  /* Modal content box */
  .small-modal {
    position: relative;
    background-color: #fff;
    margin: 10% auto; /* center vertically/horizontally */
    padding: 20px;
    border-radius: 8px;
    width: 300px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  }

  /* Position “top-right” if needed */
  .top-right {
    margin-top: 5%;
    margin-left: auto;
    margin-right: 5%;
  }

  .modal-logo {
    width: 40px;
    height: 40px;
    margin-bottom: 10px;
  }

  /* ✕ Close button styling */
  .close-btn {
    position: absolute;
    top: 8px;
    right: 12px;
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    cursor: pointer;
    user-select: none;
  }
  .close-btn:hover {
    color: #000;
  }
</style>


<!-- Include the Modal CSS and JS -->
<link rel="stylesheet" type="text/css" href="dist/css/modal.css">
<script src="dist/js/modal.js"></script>
