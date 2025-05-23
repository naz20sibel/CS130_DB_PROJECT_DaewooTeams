<?php
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS"; // Note the double backslash
$connectionInfo = array("Database" => "Project");

$conn = sqlsrv_connect($serverName, $connectionInfo);


// Define default values to avoid "undefined variable" warnings
$action = $_GET['action'] ?? '';
$amenity_id = $_GET['id'] ?? null;
$amenity = [];
$all_buses = [];
$all_amenities = [];

// 1. Fetch all buses for dropdown
$busQuery = "SELECT BUS_ID, BUS_NUMBER, CAPACITY, TYPE FROM BUSES";
$busResult = sqlsrv_query($conn, $busQuery);
if ($busResult !== false) {
    while ($row = sqlsrv_fetch_array($busResult, SQLSRV_FETCH_ASSOC)) {
        $all_buses[] = $row;
    }
}

// 2. If editing, fetch the amenity to edit
if ($action === 'edit' && $amenity_id) {
    $editQuery = "SELECT * FROM BUS_AMENITIES WHERE AMENITY_ID = ?";
    $editResult = sqlsrv_query($conn, $editQuery, [$amenity_id]);
    if ($editResult && $row = sqlsrv_fetch_array($editResult, SQLSRV_FETCH_ASSOC)) {
        $amenity = $row;
    }
}

// 3. Fetch all amenities to list in the table (JOIN to get bus number)
$amenitiesQuery = "
    SELECT A.AMENITY_ID, A.AMENITY_NAME, A.AMENITY_DESCRIPTION, A.IS_AVAILABLE,
           B.BUS_NUMBER
    FROM BUS_AMENITIES A
    JOIN BUSES B ON A.BUS_ID = B.BUS_ID
";
$amenitiesResult = sqlsrv_query($conn, $amenitiesQuery);
if ($amenitiesResult !== false) {
    while ($row = sqlsrv_fetch_array($amenitiesResult, SQLSRV_FETCH_ASSOC)) {
        $all_amenities[] = $row;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_id = $_POST['bus_id'];
    $amenity_name = $_POST['amenity_name'];
    $description = $_POST['amenity_description'] ?? '';
    $is_available = $_POST['is_available'];

    if ($action === 'create') {
        $insertQuery = "INSERT INTO BUS_AMENITIES (BUS_ID, AMENITY_NAME, AMENITY_DESCRIPTION, IS_AVAILABLE) VALUES (?, ?, ?, ?)";
        $params = [$bus_id, $amenity_name, $description, $is_available];
        $stmt = sqlsrv_query($conn, $insertQuery, $params);

        if ($stmt) {
            header("Location: bus_amenities.php?success=Amenity added successfully");
            exit;
        } else {
            header("Location: bus_amenities.php?error=Failed to add amenity");
            exit;
        }

    } elseif ($action === 'update' && $amenity_id) {
        $updateQuery = "UPDATE BUS_AMENITIES SET BUS_ID = ?, AMENITY_NAME = ?, AMENITY_DESCRIPTION = ?, IS_AVAILABLE = ? WHERE AMENITY_ID = ?";
        $params = [$bus_id, $amenity_name, $description, $is_available, $amenity_id];
        $stmt = sqlsrv_query($conn, $updateQuery, $params);

        if ($stmt) {
            header("Location: bus_amenities.php?success=Amenity updated successfully");
            exit;
        } else {
            header("Location: bus_amenities.php?error=Failed to update amenity");
            exit;
        }
    }
}

// Handle delete
if ($action === 'delete' && $amenity_id) {
    $deleteQuery = "DELETE FROM BUS_AMENITIES WHERE AMENITY_ID = ?";
    $stmt = sqlsrv_query($conn, $deleteQuery, [$amenity_id]);

    if ($stmt) {
        header("Location: bus_amenities.php?success=Amenity deleted successfully");
        exit;
    } else {
        header("Location: bus_amenities.php?error=Failed to delete amenity");
        exit;
    }
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Amenities Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Bus Amenities</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#addAmenityForm">
                        <?= $action === 'edit' ? 'Edit Amenity' : 'Add New Amenity' ?>
                    </button>
                </div>

                <!-- Add/Edit Form -->
                <div class="collapse <?= $action === 'edit' ? 'show' : '' ?>" id="addAmenityForm">
                    <div class="card-body border-bottom">
                        <form method="POST" action="bus_amenities.php?action=<?= $action === 'edit' ? 'update&id='.$amenity_id : 'create' ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bus*</label>
                                    <select class="form-select" name="bus_id" required>
                                        <option value="">Select Bus</option>
                                        <?php foreach ($all_buses as $bus): ?>
                                            <option value="<?= $bus['BUS_ID'] ?>" 
                                                <?= ($amenity['BUS_ID'] ?? '') == $bus['BUS_ID'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($bus['BUS_NUMBER']) ?> 
                                                (<?= $bus['CAPACITY'] ?> seats, <?= $bus['TYPE'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Amenity Name*</label>
                                    <input type="text" class="form-control" name="amenity_name"
                                           value="<?= $amenity['AMENITY_NAME'] ?? '' ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="amenity_description" rows="2"><?= $amenity['AMENITY_DESCRIPTION'] ?? '' ?></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Availability*</label>
                                    <select class="form-select" name="is_available" required>
                                        <option value="Y" <?= ($amenity['IS_AVAILABLE'] ?? 'Y') === 'Y' ? 'selected' : '' ?>>Available</option>
                                        <option value="N" <?= ($amenity['IS_AVAILABLE'] ?? '') === 'N' ? 'selected' : '' ?>>Not Available</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-toggle="collapse" data-bs-target="#addAmenityForm">Cancel</button>
                                <button type="submit" class="btn btn-success"><?= $action === 'edit' ? 'Update' : 'Add' ?> Amenity</button>
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

                    <!-- Amenities Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bus Number</th>
                                <th>Amenity Name</th>
                                <th>Description</th>
                                <th>Availability</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($all_amenities as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['AMENITY_ID']) ?></td>
                                    <td><?= htmlspecialchars($row['BUS_NUMBER']) ?></td>
                                    <td><?= htmlspecialchars($row['AMENITY_NAME']) ?></td>
                                    <td><?= htmlspecialchars($row['AMENITY_DESCRIPTION'] ?? 'N/A') ?></td>
                                    <td><?= $row['IS_AVAILABLE'] === 'Y' ? 'Available' : 'Not Available' ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="bus_amenities.php?action=edit&id=<?= $row['AMENITY_ID'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="bus_amenities.php?action=delete&id=<?= $row['AMENITY_ID'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</a>
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