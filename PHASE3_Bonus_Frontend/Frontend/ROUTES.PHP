<?php
include 'partials/nav.php';
$serverName = "DESKTOP-BC0HPRM\SQLEXPRESS"; //serverName\instanceName
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
    // Fetch stations with city names
    $query = "SELECT s.station_id, s.station_name, c.city_name 
              FROM Stations s
              JOIN Cities c ON s.city_id = c.city_id
              ORDER BY c.city_name, s.station_name";
    
    $stmt = sqlsrv_query($conn, $query);
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle query error
    }
    
    $stations = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $stations[] = $row;
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
    <title>Select Your Route</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/BUSPROJECT/css/navbar.css">
    <link rel="stylesheet" href="/BUSPROJECT/css/footer.css">
    <style>
        .form-card {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="form-card bg-white">
        <h2 class="text-center mb-4">Select Your Route</h2>
        <form method="GET" action="BUSES.php">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">From Station:</label>
                    <select name="from_station_id" class="form-select" required>
                        <option value="">Select Departure Station</option>
                        <?php foreach ($stations as $station): ?>
                            <option value="<?= htmlspecialchars($station['station_id']) ?>">
                                <?= htmlspecialchars($station['station_name']) ?> (<?= htmlspecialchars($station['city_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">To Station:</label>
                    <select name="to_station_id" class="form-select" required>
                        <option value="">Select Arrival Station</option>
                        <?php foreach ($stations as $station): ?>
                            <option value="<?= htmlspecialchars($station['station_id']) ?>">
                                <?= htmlspecialchars($station['station_name']) ?> (<?= htmlspecialchars($station['city_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Travel Date:</label>
                <input type="date" name="travel_date" class="form-control" 
                       min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-warning px-4">Search Buses</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromSelect = document.querySelector('select[name="from_station_id"]');
    const toSelect = document.querySelector('select[name="to_station_id"]');
    const form = document.querySelector('form');
    
    function validateStations() {
        if (fromSelect.value && fromSelect.value === toSelect.value) {
            alert("Departure and arrival stations cannot be the same!");
            return false;
        }
        return true;
    }
    
    // Validate on form submission
    form.addEventListener('submit', function(e) {
        if (!validateStations()) {
            e.preventDefault();
        }
    });
    
    // Optional: Disable already selected option in the other dropdown
    fromSelect.addEventListener('change', function() {
        Array.from(toSelect.options).forEach(option => {
            option.disabled = (option.value === this.value && option.value !== '' );
        });
    });
    
    toSelect.addEventListener('change', function() {
        Array.from(fromSelect.options).forEach(option => {
            option.disabled = (option.value === this.value && option.value !== '' );
        });
    });
});
</script>
</body>
<?php include 'partials/footer.php'; ?>
</html>
