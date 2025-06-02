<?php
// 1) Include connection first (so we can delete)
include('./../connection.php');

// 2) Handle deletion BEFORE any HTML is sent
if (isset($_GET['delete_type'], $_GET['delete_id'])) {
    $type = $_GET['delete_type'];
    $id   = intval($_GET['delete_id']);

    if ($type === 'announcement') {
        $tbl = 'announcements';
    } elseif ($type === 'siren') {
        $tbl = 'sirens';
    }

    if (isset($tbl)) {
        $sql = "DELETE FROM `$tbl` WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect back to history.php immediately, before any HTML
        header('Location: history.php');
        exit;
    }
}

// 3) Now we can safely include header.php and start sending HTML
include('includes/header.php');  

// Fetch announcements
function fetchAnnouncements($conn) {
    $rows = [];
    $res = mysqli_query($conn, "
        SELECT
            id,
            firstName,
            lastName,
            contact,
            role,
            message,
            created_at,
            announce_at AS scheduled_at,
            status,
            'Announcement' AS type
        FROM announcements
    ");
    while ($r = mysqli_fetch_assoc($res)) {
        $r['fullName'] = $r['firstName'] . ' ' . $r['lastName'];
        $rows[] = $r;
    }
    return $rows;
}

// Fetch sirens
function fetchSirens($conn) {
    $rows = [];
    $res = mysqli_query($conn, "
        SELECT
            id,
            firstName,
            lastName,
            contact,
            role,
            '' AS message,
            created_at,
            siren_at AS scheduled_at,
            status,
            'Siren' AS type
        FROM sirens
    ");
    while ($r = mysqli_fetch_assoc($res)) {
        $r['fullName'] = $r['firstName'] . ' ' . $r['lastName'];
        $rows[] = $r;
    }
    return $rows;
}

$history = array_merge(fetchAnnouncements($conn), fetchSirens($conn));
usort($history, fn($a,$b) => strcmp($b['scheduled_at'], $a['scheduled_at']));
?>

<div id="main-content">
    <div class="content-header">
        <h2>History</h2>
        <div class="filter-controls">
            <button id="filterAll" class="filter-btn active">All</button>
            <button id="filterAnnouncements" class="filter-btn">Announcements</button>
            <button id="filterSirens" class="filter-btn">Sirens</button>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Role</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Contact</th>
                    <th>Message</th>
                    <th>Created At</th>
                    <th>Scheduled At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $item): ?>
                <tr data-type="<?php echo htmlspecialchars($item['type']); ?>">
                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                    <td><?php echo htmlspecialchars($item['type']); ?></td>
                    <td><?php echo htmlspecialchars($item['role']); ?></td>
                    <td><?php echo htmlspecialchars($item['lastName']); ?></td>
                    <td><?php echo htmlspecialchars($item['firstName']); ?></td>
                    <td><?php echo htmlspecialchars($item['contact']); ?></td>
                    <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        <?php echo htmlspecialchars(str_replace(["\r\n","\r","\n"], ' ', $item['message'])); ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($item['scheduled_at']); ?></td>
                    <td><?php echo htmlspecialchars($item['status']); ?></td>
                    <td data-label="Actions">
                        <a href="#"
                           class="btn btn-edit"
                           onclick='openInfoModal(<?php echo json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);
                                    return false;'>Info</a>
                        |
                        <a href="?delete_type=<?php echo strtolower($item['type']); ?>&delete_id=<?php echo $item['id']; ?>"
                           class="btn btn-delete"
                           onclick="return confirm('Are you sure you want to delete this <?php echo strtolower($item['type']); ?>?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Info Modal (unchanged) -->
<div id="infoModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 id="modalTitle">Info</h2>
    <p id="infoRole"></p>
    <p id="infoContact"></p>
    <p id="infoStatus"></p>
    <hr>
    <p id="infoMessage"></p>
    <hr>
    <p id="infoDates"></p>
  </div>
</div>

<script>
// Filter controls
const btnAll  = document.getElementById('filterAll');
const btnAnn = document.getElementById('filterAnnouncements');
const btnSir = document.getElementById('filterSirens');
const rows    = document.querySelectorAll('tbody tr');

function setFilter(type) {
  rows.forEach(r => {
    r.style.display = (type === 'All' || r.dataset.type === type) ? '' : 'none';
  });
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  if (type === 'All') btnAll.classList.add('active');
  if (type === 'Announcement') btnAnn.classList.add('active');
  if (type === 'Siren') btnSir.classList.add('active');
}
btnAll.addEventListener('click', () => setFilter('All'));
btnAnn.addEventListener('click', () => setFilter('Announcement'));
btnSir.addEventListener('click', () => setFilter('Siren'));

// Info Modal logic (unchanged)
function openInfoModal(item) {
  document.getElementById('modalTitle').textContent = item.type === 'Announcement' ? '📢 Announcement Info' : '🚨 Siren Info';
  document.getElementById('infoRole').innerHTML = `<strong>[${item.role}] ${item.fullName}</strong>`;
  document.getElementById('infoContact').innerHTML = `<em>📞 ${item.contact}</em>`;
  document.getElementById('infoStatus').textContent = `📌 Status: ${item.status}`;
  document.getElementById('infoMessage').innerHTML = item.message ? item.message.replace(/\r\n|\r|\n/g,'<br>') : '(No message)';
  document.getElementById('infoDates').innerHTML = `🗓 Created: ${item.created_at}<br>⏰ Scheduled: ${item.scheduled_at}`;
  document.getElementById('infoModal').style.display = 'block';
}
document.querySelector('#infoModal .close').addEventListener('click', () => document.getElementById('infoModal').style.display = 'none');
window.onclick = e => {
  if (e.target === document.getElementById('infoModal')) {
    document.getElementById('infoModal').style.display = 'none';
  }
};
</script>

<script src="dist/js/script.js"></script>
<script src="dist/js/sortTable.js"></script>
<?php include('includes/footer.php'); ?>
