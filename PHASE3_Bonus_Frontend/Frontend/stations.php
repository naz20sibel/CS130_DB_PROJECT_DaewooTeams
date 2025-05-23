<?php
require_once 'C:\xampp\htdocs\BUSPROJECT\partials\dbconnect.php';

// Verify database connection
if (!$conn) {
    $e = oci_error();
    die("<div class='alert alert-danger'>Connection failed: " . htmlentities($e['message']) . "</div>");
}

$action = $_GET['action'] ?? '';
$station_id = $_GET['id'] ?? 0;
$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['station_name'];
    $location = $_POST['location'];
    $city_id = $_POST['city_id'];
    $contact = $_POST['contact_number'];

    if ($action === 'create') {
        $query = "INSERT INTO Stations (station_name, location, city_id, contact_number) 
                  VALUES (:name, :location, :city_id, :contact)";
    } elseif ($action === 'update') {
        $query = "UPDATE Stations SET 
                  station_name = :name,
                  location = :location,
                  city_id = :city_id,
                  contact_number = :contact
                  WHERE station_id = :id";
    }

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':location', $location);
    oci_bind_by_name($stmt, ':city_id', $city_id);
    oci_bind_by_name($stmt, ':contact', $contact);
    
    if ($action === 'update') {
        oci_bind_by_name($stmt, ':id', $station_id);
    }

    if (oci_execute($stmt)) {
        $message = 'success=Station ' . ($action === 'create' ? 'added' : 'updated') . ' successfully';
    } else {
        $e = oci_error($stmt);
        $message = 'error=' . $e['message'];
    }
    header("Location: station_management.php?$message");
    exit;
}

// Handle delete action
if ($action === 'delete') {
    $query = "DELETE FROM Stations WHERE station_id = :id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':id', $station_id);
    
    if (oci_execute($stmt)) {
        $message = 'success=Station deleted successfully';
    } else {
        $e = oci_error($stmt);
        $message = 'error=' . $e['message'];
    }
    header("Location: station_management.php?$message");
    exit;
}

// Get station data for editing
$station = null;
if ($action === 'edit') {
    $query = "SELECT s.*, c.city_name 
              FROM Stations s 
              JOIN Cities c ON s.city_id = c.city_id 
              WHERE s.station_id = :id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':id', $station_id);
    
    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        die("<div class='alert alert-danger'>Edit query failed: " . htmlentities($e['message']) . "</div>");
    }
    
    $station = oci_fetch_array($stmt, OCI_ASSOC);
}

// Get all stations with city names
$query = "SELECT s.station_id, s.station_name, s.location, 
                 c.city_name as city, s.contact_number
          FROM Stations s
          JOIN Cities c ON s.city_id = c.city_id
          ORDER BY s.station_id";
$stmt = oci_parse($conn, $query);

if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die("<div class='alert alert-danger'>Stations query failed: " . htmlentities($e['message']) . "</div>");
}

$all_stations = [];
while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
    $all_stations[] = $row;
}

// Get cities for dropdown
$city_query = "SELECT city_id, city_name FROM Cities ORDER BY city_name";
$city_stmt = oci_parse($conn, $city_query);

if (!oci_execute($city_stmt)) {
    $e = oci_error($city_stmt);
    die("<div class='alert alert-danger'>Cities query failed: " . htmlentities($e['message']) . "</div>");
}

// Store cities in array for reuse
$cities = [];
while ($city = oci_fetch_array($city_stmt, OCI_ASSOC)) {
    $cities[] = $city;
}

oci_free_statement($stmt);
oci_free_statement($city_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Station Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow form-card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Stations</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#addStationForm">
                        <?= $action === 'edit' ? 'Edit Station' : 'Add New Station' ?>
                    </button>
                </div>

                <!-- Add/Edit Form -->
                <div class="collapse <?= $action === 'edit' ? 'show' : '' ?>" id="addStationForm">
                    <div class="card-body border-bottom">
                        <form method="POST" action="station_management.php?action=<?= $action === 'edit' ? 'update&id='.$station_id : 'create' ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Station Name*</label>
                                    <input type="text" class="form-control" name="station_name"
                                           value="<?= htmlspecialchars($station['STATION_NAME'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Location*</label>
                                    <input type="text" class="form-control" name="location"
                                           value="<?= htmlspecialchars($station['LOCATION'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City*</label>
                                    <select name="city_id" class="form-select" required>
                                        <option value="">Select City</option>
                                        <?php foreach ($cities as $city): ?>
                                            <?php $selected = ($station['CITY_ID'] ?? '') == $city['CITY_ID'] ? 'selected' : ''; ?>
                                            <option value="<?= htmlspecialchars($city['CITY_ID']) ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($city['CITY_NAME']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" name="contact_number"
                                           value="<?= htmlspecialchars($station['CONTACT_NUMBER'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-toggle="collapse" data-bs-target="#addStationForm">Cancel</button>
                                <button type="submit" class="btn btn-success"><?= $action === 'edit' ? 'Update' : 'Add' ?> Station</button>
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

                    <!-- Stations Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>ID</th>
                                    <th>Station Name</th>
                                    <th>Location</th>
                                    <th>City</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($all_stations)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No stations found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($all_stations as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['STATION_ID']) ?></td>
                                        <td><?= htmlspecialchars($row['STATION_NAME']) ?></td>
                                        <td><?= htmlspecialchars($row['LOCATION']) ?></td>
                                        <td><?= htmlspecialchars($row['CITY']) ?></td>
                                        <td><?= htmlspecialchars($row['CONTACT_NUMBER']) ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="station_management.php?action=edit&id=<?= $row['STATION_ID'] ?>" 
                                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="station_management.php?action=delete&id=<?= $row['STATION_ID'] ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this station?')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
<?php
oci_close($conn);
?>