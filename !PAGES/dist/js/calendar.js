document.addEventListener("DOMContentLoaded", function() {
    // Calendar Elements
    let currentMonth, currentYear, announcementDates = [];
    const monthYearElement = document.getElementById("month-year");
    const daysElement = document.getElementById("days");
    const prevButton = document.getElementById("prev");
    const nextButton = document.getElementById("next");
  
    // Initialize calendar functionality
    function initCalendar() {
      if (!monthYearElement || !daysElement || !prevButton || !nextButton) {
        console.warn("Calendar elements not found. Skipping calendar initialization.");
        return;
      }
      const date = new Date();
      currentMonth = date.getMonth();
      currentYear = date.getFullYear();
  
      prevButton.addEventListener("click", () => {
        currentMonth--;
        if (currentMonth < 0) {
          currentMonth = 11;
          currentYear--;
        }
        renderCalendar();
      });
  
      nextButton.addEventListener("click", () => {
        currentMonth++;
        if (currentMonth > 11) {
          currentMonth = 0;
          currentYear++;
        }
        renderCalendar();
      });
  
      // Start by fetching announcements
      fetchAnnouncements();
    }
  
    // Fetch announcements from check-announcement.php and populate announcementDates
    function fetchAnnouncements() {
      fetch("check-announcement.php")
        .then(response => response.json())
        .then(data => {
          const upcoming = data.upcoming_announcements || [];
          announcementDates = upcoming.map(item => {
            let d = new Date(item.announce_at);
            let year = d.getFullYear();
            let month = ("0" + (d.getMonth() + 1)).slice(-2);
            let day = ("0" + d.getDate()).slice(-2);
            return `${year}-${month}-${day}`;
          });
          renderCalendar();
        })
        .catch(err => {
          console.error("Error fetching announcements:", err);
          renderCalendar(); // Render without announcement symbols if error occurs
        });
    }
  
    // Render the calendar grid for the current month and year
    function renderCalendar() {
      if (!monthYearElement || !daysElement) return;
      const newDate = new Date(currentYear, currentMonth);
      monthYearElement.innerText = newDate.toLocaleString("default", { month: "long", year: "numeric" });
      daysElement.innerHTML = "";
  
      const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
      const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);
      const totalDays = lastDayOfMonth.getDate();
      const startDay = firstDayOfMonth.getDay();
  
      // Create empty placeholders before the first day
      for (let i = 0; i < startDay; i++) {
        let placeholder = document.createElement("div");
        placeholder.classList.add("date");
        daysElement.appendChild(placeholder);
      }
  
      // Create a cell for each day of the month
      for (let dayNum = 1; dayNum <= totalDays; dayNum++) {
        let cellDate = new Date(currentYear, currentMonth, dayNum);
        let year = cellDate.getFullYear();
        let month = ("0" + (cellDate.getMonth() + 1)).slice(-2);
        let day = ("0" + cellDate.getDate()).slice(-2);
        let dateStr = `${year}-${month}-${day}`;
  
        let cell = document.createElement("div");
        cell.classList.add("date");
  
        // Sub-element for the day number
        let dayNumber = document.createElement("div");
        dayNumber.textContent = dayNum;
        cell.appendChild(dayNumber);
  
        // If an announcement exists on this date, add an icon (placed below the number)
        if (announcementDates.includes(dateStr)) {
          let symbol = document.createElement("div");
          symbol.classList.add("date-icon");
          symbol.innerHTML = "&#x1F4E2;"; // Megaphone emoji
          cell.appendChild(symbol);
        }
  
        daysElement.appendChild(cell);
      }
    }
  
    // Initialize the calendar when DOM is ready
    initCalendar();
  });
  