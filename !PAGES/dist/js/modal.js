function showAnnouncementModal(message, title = "📢 Announcement 📢") {
  var modal = document.getElementById("announcementModal");
  var modalTitle = document.getElementById("modalAnnouncementTitle");
  var modalText = document.getElementById("modalAnnouncementText");
  var modalTime = document.getElementById("modalAnnouncementTime");

  // Force update the time when the modal is shown
  var now = new Date();
  var formattedTime = now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
  modalTime.textContent = "🕒 " + formattedTime; // Update time

  modalTitle.textContent = title; // Set the title
  modalText.innerHTML = message; // Use innerHTML to allow <br> to render properly
  modal.style.display = "block";
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
