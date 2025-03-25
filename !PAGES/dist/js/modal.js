function showAnnouncementModal(message, title = "📢 Announcement 📢") {
  var modal = document.getElementById("announcementModal");
  var modalTitle = document.getElementById("modalAnnouncementTitle");
  var modalText = document.getElementById("modalAnnouncementText");

  modalTitle.textContent = title; // Set the title
  modalText.textContent = message; // Set the message
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
  