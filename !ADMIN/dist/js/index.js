document.addEventListener('DOMContentLoaded', function() {
  // Theme Color Handling (if present)
  const colorPicker = document.getElementById('theme-color');
  const saveColorBtn = document.getElementById('save-color');
  if (colorPicker && saveColorBtn) {
      const savedColor = localStorage.getItem('theme-color');
      if (savedColor) {
          document.documentElement.style.setProperty('--theme-color', savedColor);
          colorPicker.value = savedColor;
      }
      saveColorBtn.addEventListener('click', () => {
          const selectedColor = colorPicker.value;
          document.documentElement.style.setProperty('--theme-color', selectedColor);
          localStorage.setItem('theme-color', selectedColor);
      });
  }

  // Announcement Toggle Functionality (if present)
  const announceNowRadio = document.getElementById('announce-now');
  const announceLaterRadio = document.getElementById('announce-later');
  const announceLaterContainer = document.getElementById('announce-later-container');
  if (announceNowRadio && announceLaterRadio && announceLaterContainer) {
      function toggleAnnounceLater() {
          if (announceLaterRadio.checked) {
              announceLaterContainer.style.display = 'block';
          } else {
              announceLaterContainer.style.display = 'none';
          }
      }
      announceNowRadio.addEventListener('change', toggleAnnounceLater);
      announceLaterRadio.addEventListener('change', toggleAnnounceLater);
      toggleAnnounceLater();
  }

  // Time Display Functionality (if present)
  const timeDisplay = document.getElementById('time-display');
  if (timeDisplay) {
      function updateTime() {
          const now = new Date();
      
          // Adjust time to Manila timezone
          now.setMinutes(now.getMinutes() + now.getTimezoneOffset());
          now.setHours(now.getHours() + 8);
      
          const options = {
              hour: '2-digit',
              minute: '2-digit',
              second: '2-digit',
              hour12: true
          };
      
          timeDisplay.innerText = new Intl.DateTimeFormat('en-US', options).format(now);
      }
      
      setInterval(updateTime, 1000);
      updateTime();
  }

  // Speaker Fetching (if present)
  const speakerElement = document.getElementById('speaker');
  if (speakerElement) {
      // Add your speaker fetching logic here, e.g.:
      // speakerElement.addEventListener('click', someFunction);
  }
});
