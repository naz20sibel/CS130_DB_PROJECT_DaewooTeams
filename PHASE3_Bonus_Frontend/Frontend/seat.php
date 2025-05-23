<?php
include 'partials/nav.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$serverName = "DESKTOP-BC0HPRM\SQLEXPRESS"; // serverName\instanceName
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Connection could not be established.<br />" . print_r(sqlsrv_errors(), true));
}

// Get parameters from URL
$schedule_id = $_GET['schedule_id'] ?? null;
$from_station_id = $_GET['from_station_id'] ?? null;
$to_station_id = $_GET['to_station_id'] ?? null;

if (!$schedule_id) {
    die("Schedule ID not provided. Please select a bus schedule first.");
}

// Fetch fare from FARE_RULES via BUS_SCHEDULES and BUSES
$query = "
    SELECT fr.fare
    FROM BUS_SCHEDULES bs
    JOIN BUSES b ON bs.bus_id = b.bus_id
    JOIN FARE_RULES fr ON bs.route_id = fr.route_id AND b.type = fr.bus_type
    WHERE bs.schedule_id = ?
";

$params = array($schedule_id);
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$fare = $row['fare'] ?? 0;

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Seat Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/BUSPROJECT/css/navbar.css">
    <link rel="stylesheet" href="/BUSPROJECT/css/footer.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        main {
            flex: 1;
            padding: 7rem 0;
        }
        
        .container.mt-5 {
            padding: 3rem 0;
            margin: 2rem auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-black">
                        <h5 class="mb-0 text-center">Book Your Seat</h5>
                    </div>
                    <div class="card-body">
                        <form id="bookingForm">
                            <!-- Number of Seats -->
                            <div class="mb-3">
                                <label class="form-label">Number of Seats</label>
                                <select class="form-select" id="seatCount">
                                    <option value="1">1 Seat</option>
                                    <option value="2">2 Seats</option>
                                    <option value="3">3 Seats</option>
                                    <option value="4">4 Seats</option>
                                </select>
                            </div>
                            
                            <!-- Price Display -->
                            <div class="alert alert-info">
                                <h6 class="mb-0">Total Price: <span id="totalPrice">PKR 0</span></h6>
                            </div>
                            
                            <!-- Confirm Booking button with schedule_id -->
                            <a href="passenger.php?schedule_id=<?= $schedule_id ?>&from_station_id=<?= $from_station_id ?>&to_station_id=<?= $to_station_id ?>" 
                               id="confirmBookingBtn" 
                               class="btn btn-warning d-grid gap-2 col-6 mx-auto">
                                Confirm Booking
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const seatPrice = <?= $fare ?>;

    function calculatePrice() {
        const seatCount = document.getElementById('seatCount').value;
        const total = seatCount * seatPrice;
        document.getElementById('totalPrice').textContent = `PKR ${total}`;
        
        // Update the href with seat count
        const confirmBtn = document.getElementById('confirmBookingBtn');
        let href = confirmBtn.getAttribute('href').split('&seat_count=')[0];
        confirmBtn.setAttribute('href', href + '&seat_count=' + seatCount);
    }

    document.getElementById('seatCount').addEventListener('change', calculatePrice);
    window.addEventListener('DOMContentLoaded', calculatePrice);
    </script>
    
    <?php include 'partials/footer.php'; ?>
</body>
</html>