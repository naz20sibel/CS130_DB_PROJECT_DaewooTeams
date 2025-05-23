<?php
include 'partials/nav.php';
// Add this at the beginning of your script
set_time_limit(300); // 300 seconds = 5 minutes

// Database connection
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS";
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

// NEW: Debugging query - check if columns exist
$checkColumns = "
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'PAYMENT_METHODS'
AND COLUMN_NAME IN ('account_number', 'bank_name')";
$checkStmt = sqlsrv_query($conn, $checkColumns);

if ($checkStmt === false) {
    die("Column check failed: " . print_r(sqlsrv_errors(), true));
}

$columnsExist = true;
while ($row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
    if (!in_array($row['COLUMN_NAME'], ['account_number', 'bank_name'])) {
        $columnsExist = false;
    }
}
sqlsrv_free_stmt($checkStmt);

if (!$columnsExist) {
    die("Required columns missing in PAYMENT_METHODS table");
}

// Modified query with fallback
$sql = "
SELECT 
    method_id, 
    method_name, 
    method_type,
    CASE WHEN account_number IS NOT NULL THEN account_number  END AS account_display,
    bank_name,
    is_active
FROM PAYMENT_METHODS 
WHERE is_active = 1";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

$methods = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Format display info
    if ($row['method_type'] == 'Banking' && !empty($row['bank_name'])) {
        $row['display_info'] = "Account Number: {$row['account_display']}, Bank: {$row['bank_name']}";
    } else {
        $row['display_info'] = $row['account_display'] ?? 'N/A';
    }
    $methods[] = $row;
}

sqlsrv_free_stmt($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complete Your Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/BUSPROJECT/css/navbar.css">
    <link rel="stylesheet" href="/BUSPROJECT/css/footer.css">
    <style>
        h5 { color:black; }
        .card { max-width: 600px; margin: 40px auto; }
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
    </style>
</head>
<body>
<div class="card shadow">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">Complete Your Payment</h5>
    </div>
    <div class="card-body">
        <form method="post" action="ticket.php">
            <div class="mb-3">
                <label class="form-label fw-bold">Select Payment Method:</label>
                <?php foreach ($methods as $index => $method): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method"
                               id="method<?= $method['method_id'] ?>"
                               value="<?= $method['method_id'] ?>"
                               data-type="<?= strtolower($method['method_type']) ?>"
                               data-info="<?= htmlspecialchars($method['display_info']) ?>"
                               onclick="updatePaymentDetails(this)"
                               <?= $index === 0 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="method<?= $method['method_id'] ?>">
                            <?= htmlspecialchars($method['method_name']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="paymentDetails" class="mb-3"></div>

            <div class="mb-3">
                <label class="form-label">Amount (PKR)</label>
                <input type="number" name="amount" class="form-control" placeholder="Enter amount" required>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="agree" required>
                <label class="form-check-label" for="agree">
                    I agree to the terms and conditions
                </label>
            </div>

            <button type="submit" class="btn btn-warning">CONFIRM</button>
            <a href="passenger.php" class="btn btn-outline-secondary">BACK</a>
        </form>
    </div>
</div>

<script>
    function updatePaymentDetails(radio) {
        const type = radio.dataset.type;
        const info = radio.dataset.info;
        const container = document.getElementById('paymentDetails');
        container.innerHTML = '';

        if (type === 'online') {
            container.innerHTML = `
                <div class="mb-2">
                    <label class="form-label">Account Info</label>
                    <input type="text" class="form-control" value="${info}" readonly>
                </div>`;
        } else if (type === 'banking') {
            container.innerHTML = `
                <div class="alert alert-info">
                    <h6 class="alert-heading mb-2">Bank Transfer Instructions</h6>
                    <p class="mb-1">Please transfer to the following account:</p>
                    <strong>${info}</strong>
                </div>`;
        } else if (type === 'card') {
            container.innerHTML = `
                <div class="mb-2">
                    <label class="form-label">Card Number</label>
                    <input type="text" class="form-control" placeholder="Enter your card number">
                </div>`;
        } else if (type === 'cash') {
            container.innerHTML = `
                <div class="alert alert-warning">
                    You will pay upon delivery. No further details are needed.
                </div>`;
        }
    }

    // Run on page load
    window.addEventListener('DOMContentLoaded', () => {
        const checked = document.querySelector('input[name="payment_method"]:checked');
        if (checked) updatePaymentDetails(checked);
    });
</script>

</body>
<?php include 'partials/footer.php'; ?>
</html>