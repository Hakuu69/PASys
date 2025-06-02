<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Calendar</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .calendar {
            width: 300px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
            text-align: center;
        }
        .calendar-header {
            background: #3b5998;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }
        .calendar-table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background: #ddd;
        }
    </style>
</head>
<body>
    <div class="calendar">
        <div class="calendar-header">
            <button id="prev-month">&#9665;</button>
            <h2 id="month-year"></h2>
            <button id="next-month">&#9655;</button>
        </div>
        <table class="calendar-table">
            <thead>
                <tr>
                    <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                </tr>
            </thead>
            <tbody id="calendar-body">
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const monthYear = document.getElementById("month-year");
            const calendarBody = document.getElementById("calendar-body");
            const prevMonthBtn = document.getElementById("prev-month");
            const nextMonthBtn = document.getElementById("next-month");

            let currentDate = new Date();

            function renderCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                
                monthYear.textContent = currentDate.toLocaleDateString("en-US", { month: "long", year: "numeric" });
                calendarBody.innerHTML = "";
                
                let row = document.createElement("tr");
                for (let i = 0; i < firstDay; i++) {
                    row.appendChild(document.createElement("td"));
                }
                
                for (let day = 1; day <= daysInMonth; day++) {
                    if (row.children.length === 7) {
                        calendarBody.appendChild(row);
                        row = document.createElement("tr");
                    }
                    const cell = document.createElement("td");
                    cell.textContent = day;
                    row.appendChild(cell);
                }
                
                if (row.children.length > 0) {
                    calendarBody.appendChild(row);
                }
            }
            
            prevMonthBtn.addEventListener("click", () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            });
            
            nextMonthBtn.addEventListener("click", () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            });
            
            renderCalendar();
        });
    </script>
</body>
</html>
