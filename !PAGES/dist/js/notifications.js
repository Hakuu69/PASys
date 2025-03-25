document.addEventListener("DOMContentLoaded", function() {

    // Delete notification function to be used by the "X" button
    window.deleteNotification = function(notificationId) {
        if (!confirm("Are you sure you want to delete this notification?")) {
            return;
        }
        
        fetch('notifications.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ notification_id: notificationId })
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                // Remove the card from the DOM if deletion was successful.
                const cardElement = document.getElementById(`notification-card-${notificationId}`);
                if (cardElement) {
                    cardElement.remove();
                }
                // Update both the sidebar and footer notification counts.
                updateNotifCount();
                updateNoNotificationMessage();
            } else {
                console.error("Error deleting notification:", data);
                alert("An error occurred while deleting the notification.");
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            alert("An error occurred. Please try again.");
        });
    };

    // Function to update the notifications count in the sidebar and footer.
    function updateNotifCount() {
        fetch('notification_count.php')
        .then(response => response.text())
        .then(count => {
            const notifSpan = document.getElementById("notif-count");
            const footerNotifBadge = document.getElementById("footerNotificationCount");

            if (notifSpan) {
                notifSpan.textContent = parseInt(count, 10) > 0 ? `Notifications (${count})` : "Notifications";
            }

            if (footerNotifBadge) {
                if (parseInt(count, 10) > 0) {
                    footerNotifBadge.textContent = count;
                    footerNotifBadge.style.display = "inline-block";
                } else {
                    footerNotifBadge.style.display = "none";
                }
            }
        })
        .catch(error => console.error("Error updating notification count:", error));
    }

    // Function to update the "No notifications available." message in the main content.
    function updateNoNotificationMessage() {
        const container = document.getElementById("main-content");
        const cards = container.querySelectorAll('.card');
        // If there are no cards, ensure the "No notifications available." message is shown.
        if (cards.length === 0) {
            if (!container.querySelector('.no-notifications')) {
                const msg = document.createElement("p");
                msg.className = "no-notifications";
                msg.textContent = "No notifications available.";
                container.appendChild(msg);
            }
        } else {
            // If cards exist, remove the message if it exists.
            const msg = container.querySelector('.no-notifications');
            if (msg) {
                msg.remove();
            }
        }
    }

    // Call updateNotifCount() on page load to ensure counts are updated.
    updateNotifCount();
});
