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
    <h4>SIREN</h4>
    <p>Ending in: <span id="countdownSeconds">60</span> seconds</p>
    <audio id="sirenAudio" src="./../ext/sounds/siren.mp3" preload="auto"></audio>
  </div>
</div>

<!-- SOS Floating Button -->
<button id="sosButton" class="sos-float-btn">SOS</button>

<!-- SOS Modal -->
<div id="sosModal" class="sos-modal">
  <div class="sos-modal-content">
    <img src="./../ext/img/siren-logo.png" alt="Siren" class="sos-modal-logo">
    <h4>SOS SIREN</h4>
    <p>Ending in: <span id="sosCountdownSeconds">60</span> seconds</p>
    <audio id="sosAudio" src="./../ext/img/siren-logo.png   " preload="auto"></audio>
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

/* SOS Modal Styles */
.sos-modal {
  display: none; /* Initially hidden */
  position: fixed;
  z-index: 10000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.8);
}

.sos-modal-content {
  background-color: #fff;
  margin: 15% auto;
  padding: 20px;
  border-radius: 8px;
  width: 300px;
  text-align: center;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.sos-modal-logo {
  width: 40px;
  height: 40px;
  margin-bottom: 10px;
}
</style>


<!-- Include the Modal CSS and JS -->
<link rel="stylesheet" type="text/css" href="dist/css/modal.css">
<script src="dist/js/modal.js"></script>
