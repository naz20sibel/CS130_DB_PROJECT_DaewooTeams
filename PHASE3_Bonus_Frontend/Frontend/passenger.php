<?php
include 'partials/nav.php';
include 'partials/dbconnect.php'; // Include your database connection file

// Initialize variables
$error = '';
$success = false;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fullname = $_POST['fullname'] ?? '';
    $cnic = $_POST['cnic'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // Validate inputs (basic validation)
    if (empty($fullname) || empty($cnic) || empty($mobile) || empty($email)) {
        $error = 'All fields are required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
    } else {
        try {
            // Prepare SQL statement to insert passenger data
            $sql = "INSERT INTO PASSENGERS (fullname, cnic, mobile, email) VALUES (?, ?, ?, ?)";
            $params = array($fullname, $cnic, $mobile, $email);
            $stmt = sqlsrv_prepare($conn, $sql, $params);
            
            // Execute the statement
            if (sqlsrv_execute($stmt)) {
                $success = true;
                
                // Store passenger ID in session for later use if needed
                $sql = "SELECT SCOPE_IDENTITY() AS passenger_id";
                $stmt = sqlsrv_query($conn, $sql);
                if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $_SESSION['passenger_id'] = $row['passenger_id'];
                }
                
                // Also store other passenger data in session
                $_SESSION['passenger_data'] = [
                    'fullname' => $fullname,
                    'cnic' => $cnic,
                    'mobile' => $mobile,
                    'email' => $email
                ];
                
                // Redirect to payment page if successful
                header("Location: PAYMENT.PHP");
                exit();
            } else {
                $error = "Error saving passenger data: " . print_r(sqlsrv_errors(), true);
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Get route information from GET parameters
$schedule_id = $_GET['schedule_id'] ?? '';
$from_station_id = $_GET['from_station_id'] ?? '';
$to_station_id = $_GET['to_station_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Information</title>
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
            background-color: #f8f9fa;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 0;
        }
        
        .form-wrapper {
            width: 100%;
            max-width: 600px;
            padding: 0 15px;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 100%;
        }

        .form-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #2c3e50;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #333;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            max-width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.2s ease-in-out;
        }

        input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }

        .back-btn, .continue-btn {
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .back-btn {
            background-color: #f1f3f5;
            color: #333;
            border: 1px solid #ccc;
        }

        .back-btn:hover {
            background-color: #e0e0e0;
        }

        .continue-btn {
            background-color: #FFC107;
            color: black;
            border: none;
        }

        .continue-btn:hover {
            background-color:rgb(234, 142, 5);
        }

        .status-message {
            background-color: #ffe6e6;
            color: #c62828;
            padding: 10px 14px;
            margin-bottom: 20px;
            border-radius: 6px;
            text-align: center;
            font-weight: 500;
        }

        .status-success {
            background-color: #e6ffe6;
            color:rgb(125, 92, 46);
        }
    </style>
</head>
<body>
    <main>
        <div class="form-wrapper">
            <div class="form-container">
                <h2 class="form-title">Passenger Information</h2>

                <?php if (!empty($error)): ?>
                <div class="status-message"><?= htmlspecialchars($error) ?></div>
                <?php elseif ($success): ?>
                <div class="status-message status-success">Passenger added successfully!</div>
                <?php endif; ?>

                <form method="POST" action="">
                    <!-- Hidden fields to preserve route information -->
                    <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id) ?>">
                    <input type="hidden" name="from_station_id" value="<?= htmlspecialchars($from_station_id) ?>">
                    <input type="hidden" name="to_station_id" value="<?= htmlspecialchars($to_station_id) ?>">

                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" required value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="cnic">CNIC</label>
                        <input type="text" id="cnic" name="cnic" placeholder="XXXXX-XXXXXXX-X" required value="<?= isset($_POST['cnic']) ? htmlspecialchars($_POST['cnic']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="mobile">Mobile No.</label>
                        <input type="text" id="mobile" name="mobile" placeholder="03XX-XXXXXXX" required value="<?= isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>

                    <div class="button-group">
                        <a href="seat.php?schedule_id=<?= htmlspecialchars($schedule_id) ?>&from_station_id=<?= htmlspecialchars($from_station_id) ?>&to_station_id=<?= htmlspecialchars($to_station_id) ?>" class="back-btn">BACK</a>
                        <button type="submit" class="continue-btn">CONTINUE</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('cnic').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5);
            }
            if (value.length > 13) {
                value = value.substring(0, 13) + '-' + value.substring(13, 14);
            }
            e.target.value = value;
        });

        document.getElementById('mobile').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) {
                value = value.substring(0, 4) + '-' + value.substring(4, 11);
            }
            e.target.value = value;
        });
    </script>
    <?php include 'partials/footer.php'; ?>
</body>
</html>