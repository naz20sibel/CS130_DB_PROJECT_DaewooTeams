<?php
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS"; // Note the double backslash
$connectionInfo = array("Database" => "Project");

$conn = sqlsrv_connect($serverName, $connectionInfo);


$action = $_GET['action'] ?? '';
$staff_id = $_GET['id'] ?? 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    try {
        if ($action === 'create') {
            $query = "INSERT INTO STAFF (first_name, last_name, email, phone, role, status) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$first_name, $last_name, $email, $phone, $role, $status];
        } elseif ($action === 'update') {
            $query = "UPDATE STAFF SET 
                      first_name = ?,
                      last_name = ?,
                      email = ?,
                      phone = ?,
                      role = ?,
                      status = ?
                      WHERE staff_id = ?";
            $params = [$first_name, $last_name, $email, $phone, $role, $status, $staff_id];
        }

        $stmt = sqlsrv_prepare($conn, $query, $params);
        if (sqlsrv_execute($stmt)) {
            $message = 'success=Staff ' . ($action === 'create' ? 'added' : 'updated') . ' successfully';
        } else {
            $message = 'error=' . print_r(sqlsrv_errors(), true);
        }
        header("Location: staff_management.php?$message");
        exit;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

if ($action === 'delete') {
    try {
        $query = "DELETE FROM STAFF WHERE staff_id = ?";
        $stmt = sqlsrv_prepare($conn, $query, [$staff_id]);
        if (sqlsrv_execute($stmt)) {
            $message = 'success=Staff deleted successfully';
        } else {
            $message = 'error=' . print_r(sqlsrv_errors(), true);
        }
        header("Location: staff_management.php?$message");
        exit;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

$staff = null;
if ($action === 'edit') {
    $query = "SELECT * FROM STAFF WHERE staff_id = ?";
    $stmt = sqlsrv_prepare($conn, $query, [$staff_id]);
    if (sqlsrv_execute($stmt)) {
        $staff = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

$query = "SELECT * FROM STAFF ORDER BY staff_id";
$stmt = sqlsrv_query($conn, $query);
if ($stmt === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

$all_staff = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $all_staff[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Staff</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#addStaffForm">
                        <?= $action === 'edit' ? 'Edit Staff' : 'Add New Staff' ?>
                    </button>
                </div>

                <!-- Add/Edit Form -->
                <div class="collapse <?= $action === 'edit' ? 'show' : '' ?>" id="addStaffForm">
                    <div class="card-body border-bottom">
                        <form method="POST" action="staff_management.php?action=<?= $action === 'edit' ? 'update&id='.$staff_id : 'create' ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name*</label>
                                    <input type="text" class="form-control" name="first_name"
                                           value="<?= htmlspecialchars($staff['first_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name*</label>
                                    <input type="text" class="form-control" name="last_name"
                                           value="<?= htmlspecialchars($staff['last_name'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email*</label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?= htmlspecialchars($staff['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone*</label>
                                    <input type="tel" class="form-control" name="phone"
                                           value="<?= htmlspecialchars($staff['phone'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role*</label>
                                    <select class="form-select" name="role" required>
                                        <?php
                                        $roles = ['driver', 'conductor', 'manager', 'admin'];
                                        foreach ($roles as $role) {
                                            $selected = ($staff['role'] ?? '') === $role ? 'selected' : '';
                                            echo "<option value=\"$role\" $selected>" . ucfirst($role) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status*</label>
                                    <select class="form-select" name="status" required>
                                        <?php
                                        $statuses = ['active', 'on_leave', 'inactive'];
                                        foreach ($statuses as $status) {
                                            $selected = ($staff['status'] ?? '') === $status ? 'selected' : '';
                                            echo "<option value=\"$status\" $selected>" . ucfirst(str_replace('_', ' ', $status)) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-toggle="collapse" data-bs-target="#addStaffForm">Cancel</button>
                                <button type="submit" class="btn btn-success"><?= $action === 'edit' ? 'Update' : 'Add' ?> Staff</button>
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

                    <!-- Staff Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($all_staff as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['staff_id']) ?></td>
                                    <td><?= htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= ucfirst(htmlspecialchars($row['role'])) ?></td>
                                    <td><?= ucfirst(str_replace('_', ' ', htmlspecialchars($row['status']))) ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="staff_management.php?action=edit&id=<?= $row['staff_id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="staff_management.php?action=delete&id=<?= $row['staff_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</a>
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