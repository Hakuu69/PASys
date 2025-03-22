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

    // Calendar Functionality (if present)
    const monthYearElement = document.getElementById('month-year');
    const daysElement = document.getElementById('days');
    const prevButton = document.getElementById('prev');
    const nextButton = document.getElementById('next');
    if (monthYearElement && daysElement && prevButton && nextButton) {
        let date = new Date();
        let currentMonth = date.getMonth();
        let currentYear = date.getFullYear();

        function renderCalendar() {
            monthYearElement.innerText = `${date.toLocaleString('default', { month: 'long' })} ${currentYear}`;
            daysElement.innerHTML = '';

            const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
            const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);
            const totalDays = lastDayOfMonth.getDate();
            const startDay = firstDayOfMonth.getDay();

            for (let i = 0; i < startDay; i++) {
                daysElement.innerHTML += `<div class="date"></div>`;
            }
            for (let i = 1; i <= totalDays; i++) {
                daysElement.innerHTML += `<div class="date">${i}</div>`;
            }
        }

        prevButton.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });

        nextButton.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });

        renderCalendar();
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
        
            document.getElementById('time-display').innerText = new Intl.DateTimeFormat('en-US', options).format(now);
        }
        
        setInterval(updateTime, 1000);
        updateTime();        
    }
});


//Speaker Fetchingg