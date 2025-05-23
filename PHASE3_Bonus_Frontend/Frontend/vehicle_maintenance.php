<?php
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS";
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_id = $_POST['bus_id'];
    $maint_type = $_POST['maint_type'];
    $cost = $_POST['cost'] ?: 0;
    $notes = $_POST['notes'] ?: '';

    try {
        if ($action === 'create') {
            $query = "INSERT INTO VEHICLE_MAINTENANCE (bus_id, maint_type, cost, notes) 
                      VALUES (?, ?, ?, ?)";
            $params = [$bus_id, $maint_type, $cost, $notes];
        } elseif ($action === 'update') {
            $query = "UPDATE VEHICLE_MAINTENANCE SET 
                      bus_id = ?,
                      maint_type = ?,
                      cost = ?,
                      notes = ?
                      WHERE maintenance_id = ?";
            $params = [$bus_id, $maint_type, $cost, $notes, $id];
        }

        $stmt = sqlsrv_prepare($conn, $query, $params);
        if (sqlsrv_execute($stmt)) {
            header("Location: vehicle_maintenance.php?success=1");
            exit;
        } else {
            die("Execution failed: " . print_r(sqlsrv_errors(), true));
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

if ($action === 'delete') {
    try {
        $query = "DELETE FROM VEHICLE_MAINTENANCE WHERE maintenance_id = ?";
        $stmt = sqlsrv_prepare($conn, $query, [$id]);
        if (sqlsrv_execute($stmt)) {
            header("Location: vehicle_maintenance.php?success=1");
            exit;
        } else {
            die("Delete failed: " . print_r(sqlsrv_errors(), true));
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

$maintenance_record = null;
if ($action === 'edit' && $id) {
    $query = "SELECT * FROM VEHICLE_MAINTENANCE WHERE maintenance_id = ?";
    $stmt = sqlsrv_prepare($conn, $query, [$id]);
    if (sqlsrv_execute($stmt)) {
        $maintenance_record = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

$maintenance = [];
$query = "SELECT m.*, b.bus_number 
          FROM VEHICLE_MAINTENANCE m
          JOIN BUSES b ON m.bus_id = b.bus_id
          ORDER BY m.maintenance_id DESC";
$stmt = sqlsrv_query($conn, $query);
if ($stmt === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $maintenance[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Maintenance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styles omitted for brevity – same as your original */
        .management-card {
            width: 900px;
            border-top: 4px solid #4CAF50;
        }
        th { background-color: #f8f9fa; color: #495057; }
        .action-btns .btn {
            width: 80px; padding: 8px; margin-right: 5px;
            font-weight: bold; transition: 0.3s ease;
        }
        .action-btns .btn:hover { transform: scale(1.05); }
        .form-label { font-weight: bold; }
        .form-control { border-radius: 8px; border: 1px solid #ccc; }
        .mb-4, .mt-4 { margin: 2rem 0; }
        .p-3 { padding: 1.5rem; }
        .card-body {
            background-color: #fff; border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #f9f9f9; }
        .btn-sm, .btn { font-size: 1rem; border-radius: 5px; }
        .notes-cell {
            max-width: 200px; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
        }
    </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-start min-vh-100 p-3">
<div class="card management-card shadow-sm mt-4">
    <div class="card-body p-4">
        <h5 class="card-title text-center mb-4">Vehicle Maintenance Management</h5>

        <?php if ($action !== 'edit'): ?>
        <div class="mb-3 text-end">
            <button class="btn btn-success" onclick="toggleForm()">Add New Record</button>
        </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div id="maintenance-form" class="mb-4 p-3 border rounded" style="<?= ($action === 'edit') ? '' : 'display: none;' ?>">
            <h6 class="mb-3"><?= ($action === 'edit') ? 'Edit Maintenance Record' : 'Add New Maintenance Record' ?></h6>
            <form method="POST" action="vehicle_maintenance.php?action=<?= ($action === 'edit') ? 'update&id=' . $id : 'create' ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bus*</label>
                        <select name="bus_id" class="form-control" required>
                            <?php
                            $buses = sqlsrv_query($conn, "SELECT bus_id, bus_number FROM BUSES");
                            if ($buses === false) {
                                die("Buses query failed: " . print_r(sqlsrv_errors(), true));
                            }
                            while ($bus = sqlsrv_fetch_array($buses, SQLSRV_FETCH_ASSOC)) {
                                $selected = ($action === 'edit' && $maintenance_record['bus_id'] == $bus['bus_id']) ? 'selected' : '';
                                echo "<option value='{$bus['bus_id']}' $selected>{$bus['bus_number']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Maintenance Type*</label>
                        <input type="text" name="maint_type" class="form-control"
                               value="<?= ($action === 'edit') ? htmlspecialchars($maintenance_record['maint_type']) : '' ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cost</label>
                        <input type="number" step="0.01" name="cost" class="form-control"
                               value="<?= ($action === 'edit') ? htmlspecialchars($maintenance_record['cost']) : '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control"><?= ($action === 'edit') ? htmlspecialchars($maintenance_record['notes']) : '' ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center">
                        <button type="submit" class="btn btn-<?= ($action === 'edit') ? 'warning' : 'success' ?>">
                            <?= ($action === 'edit') ? 'Update' : 'Add' ?> Record
                        </button>
                        <?php if ($action === 'edit'): ?>
                            <a href="vehicle_maintenance.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Maintenance Records Table -->
        <h6 class="mb-3 mt-4">Maintenance Records</h6>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Bus Number</th>
                    <th>Type</th>
                    <th>Cost</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($maintenance as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['bus_number']) ?></td>
                        <td><?= htmlspecialchars($item['maint_type']) ?></td>
                        <td>$<?= number_format($item['cost'], 2) ?></td>
                        <td class="notes-cell" title="<?= htmlspecialchars($item['notes']) ?>">
                            <?= htmlspecialchars($item['notes']) ?>
                        </td>
                        <td class="action-btns">
                            <a href="?action=edit&id=<?= $item['maintenance_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="?action=delete&id=<?= $item['maintenance_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Toggle Form Script -->
<script>
function toggleForm() {
    const form = document.getElementById('maintenance-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
