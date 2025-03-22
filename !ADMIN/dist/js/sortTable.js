document.addEventListener("DOMContentLoaded", function() {
    const table = document.querySelector("table");
    const headers = table.querySelectorAll("th");

    headers.forEach((header, index) => {
        header.style.cursor = "pointer"; // visually indicate it's clickable
        header.addEventListener("click", function() {
            sortTableByColumn(table, index);
        });
    });
});

function parseCustomDate(dateString) {
    // Assumes the format "YYYY-MM-DD HH:MM:SS"
    const parts = dateString.split(' ');
    if (parts.length < 2) return new Date(dateString); // fallback if format is different

    const dateParts = parts[0].split('-');
    const timeParts = parts[1].split(':');

    if (dateParts.length !== 3 || timeParts.length < 2) return new Date(dateString);

    const year = parseInt(dateParts[0], 10);
    const month = parseInt(dateParts[1], 10) - 1; // JavaScript months are 0-indexed
    const day = parseInt(dateParts[2], 10);
    const hour = parseInt(timeParts[0], 10);
    const minute = parseInt(timeParts[1], 10);
    const second = timeParts[2] ? parseInt(timeParts[2], 10) : 0;

    return new Date(year, month, day, hour, minute, second);
}

function sortTableByColumn(table, columnIndex) {
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));
    const header = table.querySelectorAll("th")[columnIndex];
    const isAscending = header.getAttribute("data-order") !== "asc";

    // A regex to detect date strings in the format "YYYY-MM-DD"
    const dateRegex = /^\d{4}-\d{2}-\d{2}/;

    rows.sort((a, b) => {
        const aCell = a.querySelector(`td:nth-child(${columnIndex + 1})`);
        const bCell = b.querySelector(`td:nth-child(${columnIndex + 1})`);
        const aText = aCell ? aCell.innerText.trim() : "";
        const bText = bCell ? bCell.innerText.trim() : "";

        // If both values look like dates, use date comparison
        if (dateRegex.test(aText) && dateRegex.test(bText)) {
            const aDate = parseCustomDate(aText);
            const bDate = parseCustomDate(bText);
            return isAscending ? aDate - bDate : bDate - aDate;
        }

        // Otherwise, check if they are purely numeric (and not a date string)
        // Note: We check that the text does not contain a dash which is typical for dates.
        const aNum = parseFloat(aText);
        const bNum = parseFloat(bText);
        if (!isNaN(aNum) && !isNaN(bNum) && !aText.includes("-") && !bText.includes("-")) {
            return isAscending ? aNum - bNum : bNum - aNum;
        }

        // Fallback to text sorting
        return isAscending ? aText.localeCompare(bText) : bText.localeCompare(aText);
    });

    // Remove existing rows and append sorted rows
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }
    rows.forEach(row => tbody.appendChild(row));

    // Toggle sort order for next click
    header.setAttribute("data-order", isAscending ? "asc" : "desc");
}
