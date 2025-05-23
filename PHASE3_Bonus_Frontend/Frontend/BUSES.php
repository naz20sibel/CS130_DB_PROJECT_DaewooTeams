<?php
include 'partials/nav.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$serverName = "DESKTOP-BC0HPRM\SQLEXPRESS"; // serverName\instanceName
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
    // Get route parameters from URL
    $from_station_id = $_GET['from_station_id'] ?? null;
    $to_station_id = $_GET['to_station_id'] ?? null;

    // Validate that stations are different
    if ($from_station_id && $to_station_id && $from_station_id == $to_station_id) {
        header("Location: routes.php?error=same_station");
        exit();
    }

    // Build the query to fetch bus schedules based on selected route
    $query = "
    SELECT
        bs.schedule_id,
        b.bus_number,
        b.type,
        b.capacity,
        fr.fare,  -- Now getting fare from FARE_RULES instead of BUS_SCHEDULES
        bs.available_seats,
        fs.station_name AS from_station,
        ts.station_name AS to_station,
        fc.city_name AS from_city,
        tc.city_name AS to_city
    FROM
        Bus_Schedules bs
    JOIN Buses b ON bs.bus_id = b.bus_id
    JOIN FARE_RULES fr ON bs.route_id = fr.route_id AND b.type = fr.bus_type  -- New join
    JOIN Routes r ON bs.route_id = r.route_id
    JOIN Stations fs ON r.from_station_id = fs.station_id
    JOIN Stations ts ON r.to_station_id = ts.station_id
    JOIN Cities fc ON fs.city_id = fc.city_id
    JOIN Cities tc ON ts.city_id = tc.city_id
    WHERE r.from_station_id = ?
    AND r.to_station_id = ?
    AND bs.available_seats > 0
    ";

    // Prepare and execute the query
    $stmt = sqlsrv_prepare($conn, $query, array(&$from_station_id, &$to_station_id));
    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true)); // Handle query error
    }

    // Fetch the results
    $buses = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $buses[] = $row;
    }

    // Close the connection
    sqlsrv_close($conn);
} else {
    echo "Connection could not be established.<br />";
    die(print_r(sqlsrv_errors(), true)); // Handle connection error
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Buses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/BUSPROJECT/css/navbar.css">
    <link rel="stylesheet" href="/BUSPROJECT/css/footer.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
       body {
        min-height: 100vh; /* Ensures body takes at least full viewport height */
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif; /* Improved font */
        line-height: 1.6; /* Better text spacing */
    }

    main {
        flex: 1; /* Makes main content expand to fill available space */
        padding: 7rem 0; /* Increased vertical padding (5rem top and bottom, 0 left and right) */
    }
    
    .container.mt-5 {
        padding: 3rem 0; /* Additional padding for the container */
        margin: 2rem auto; /* Added margin for better spacing */
    }
      

    .route-header {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .bus-table {
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .table thead th {
        background-color: #FFCA2C;
        color: black;
        border-bottom: none;
    }

    .no-buses-card {
        max-width: 600px;
        margin: 0 auto;
        border-radius: 8px;
    }
    </style>
</head>
<body>
    <main class="main-content">
        <div class="container">
            <?php if (!empty($buses)): ?>
                <div class="route-header text-center">
                    <h2 class="text-dark mb-3">
                        <?= htmlspecialchars($buses[0]['from_station']) ?> 
                        <small class="text-muted">(<?= htmlspecialchars($buses[0]['from_city']) ?>)</small>
                        <i class="bi bi-arrow-right mx-3"></i>
                        <?= htmlspecialchars($buses[0]['to_station']) ?> 
                        <small class="text-muted">(<?= htmlspecialchars($buses[0]['to_city']) ?>)</small>
                    </h2>
                </div>

                <div class="bus-table">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Bus No</th>
                                <th>Type</th>
                                <th>Fare</th>
                                <th>Seats Left</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($buses as $row): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($row['bus_number']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($row['type'])) ?></td>
                                    <td class="text-success fw-bold">PKR <?= htmlspecialchars(number_format($row['fare'], 2)) ?></td>
                                    <td><?= htmlspecialchars($row['available_seats']) ?></td>
                                    <td>
                                        <a href="seat.php?schedule_id=<?= $row['schedule_id'] ?>&from_station_id=<?= $_GET['from_station_id'] ?>&to_station_id=<?= $_GET['to_station_id'] ?>" 
                                           class="btn btn-sm btn-warning px-3">Book Now</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-buses-card card border-warning">
                    <div class="card-body text-center py-5">
                        <h4 class="text-dark mb-4">No buses available for the selected route</h4>
                        <a href="routes.php" class="btn btn-outline-secondary px-4">Search Again</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'partials/footer.php'; ?>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>