<?php
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS";
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

$action = $_GET['action'] ?? '';
$bus_id = $_GET['id'] ?? 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_number = $_POST['bus_number'];
    $capacity = $_POST['capacity'];
    $type = $_POST['type'];
    $status = $_POST['status'];

    try {
        if ($action === 'create') {
            $query = "INSERT INTO Buses (bus_number, capacity, type, status) VALUES (?, ?, ?, ?)";
            $params = [$bus_number, $capacity, $type, $status];
        } elseif ($action === 'update') {
            $query = "UPDATE Buses SET bus_number = ?, capacity = ?, type = ?, status = ? WHERE bus_id = ?";
            $params = [$bus_number, $capacity, $type, $status, $bus_id];
        }

        $stmt = sqlsrv_prepare($conn, $query, $params);
        if (sqlsrv_execute($stmt)) {
            $message = 'success=Bus ' . ($action === 'create' ? 'added' : 'updated') . ' successfully';
        } else {
            $message = 'error=' . print_r(sqlsrv_errors(), true);
        }
        header("Location: bus_management.php?$message");
        exit;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

if ($action === 'delete') {
    $query = "DELETE FROM Buses WHERE bus_id = ?";
    $stmt = sqlsrv_prepare($conn, $query, [$bus_id]);
    if (sqlsrv_execute($stmt)) {
        $message = 'success=Bus deleted successfully';
    } else {
        $message = 'error=' . print_r(sqlsrv_errors(), true);
    }
    header("Location: bus_management.php?$message");
    exit;
}

$bus = null;
if ($action === 'edit') {
    $query = "SELECT * FROM Buses WHERE bus_id = ?";
    $stmt = sqlsrv_prepare($conn, $query, [$bus_id]);
    if (sqlsrv_execute($stmt)) {
        $bus = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

$query = "SELECT * FROM Buses ORDER BY bus_id";
$stmt = sqlsrv_query($conn, $query);
if ($stmt === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

$all_buses = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $all_buses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Buses</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#addBusForm">
                        <?= $action === 'edit' ? 'Edit Bus' : 'Add New Bus' ?>
                    </button>
                </div>

                <!-- Add/Edit Form -->
                <div class="collapse <?= $action === 'edit' ? 'show' : '' ?>" id="addBusForm">
                    <div class="card-body border-bottom">
                        <form method="POST" action="bus_management.php?action=<?= $action === 'edit' ? 'update&id='.$bus_id : 'create' ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bus Number*</label>
                                    <input type="text" class="form-control" name="bus_number"
                                           value="<?= htmlspecialchars($bus['bus_number'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Capacity*</label>
                                    <input type="number" class="form-control" name="capacity"
                                           value="<?= htmlspecialchars($bus['capacity'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type*</label>
                                    <select class="form-select" name="type" required>
                                        <?php
                                        $types = ['standard', 'premium', 'luxury'];
                                        foreach ($types as $type) {
                                            $selected = ($bus['type'] ?? '') === $type ? 'selected' : '';
                                            echo "<option value=\"$type\" $selected>" . ucfirst($type) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status*</label>
                                    <select class="form-select" name="status" required>
                                        <?php
                                        $statuses = ['active', 'maintenance', 'inactive'];
                                        foreach ($statuses as $status) {
                                            $selected = ($bus['status'] ?? '') === $status ? 'selected' : '';
                                            echo "<option value=\"$status\" $selected>" . ucfirst($status) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-toggle="collapse" data-bs-target="#addBusForm">Cancel</button>
                                <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Update' : 'Add' ?> Bus</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Feedback Messages -->
                <div class="card-body">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                    <?php endif; ?>

                    <!-- Bus Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bus Number</th>
                                <th>Capacity</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($all_buses as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['bus_id']) ?></td>
                                    <td><?= htmlspecialchars($row['bus_number']) ?></td>
                                    <td><?= htmlspecialchars($row['capacity']) ?></td>
                                    <td><?= ucfirst(htmlspecialchars($row['type'])) ?></td>
                                    <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="bus_management.php?action=edit&id=<?= $row['bus_id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="bus_management.php?action=delete&id=<?= $row['bus_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
