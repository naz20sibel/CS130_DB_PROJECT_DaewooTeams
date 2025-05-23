<?php


session_start();

// SQL Server connection
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS"; // Escape backslash
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Database connection failed: " . print_r(sqlsrv_errors(), true));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // ⚠️ In production, hash passwords
    $phone = $_POST['phone'];

    try {
        // Check if email already exists
        $check_email_sql = "SELECT COUNT(*) AS count FROM Signup WHERE email = ?";
        $check_stmt = sqlsrv_prepare($conn, $check_email_sql, array($email));
        if (!sqlsrv_execute($check_stmt)) {
            throw new Exception("Failed to check email.");
        }
        $row = sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC);
        if ($row['count'] > 0) {
            throw new Exception("Email already registered.");
        }

        // Insert new user
        $insert_sql = "INSERT INTO Signup (first_name, last_name, email, password, phone) 
                       VALUES (?, ?, ?, ?, ?)";
        $params = array($first_name, $last_name, $email, $password, $phone);
        $insert_stmt = sqlsrv_prepare($conn, $insert_sql, $params);
        if (!sqlsrv_execute($insert_stmt)) {
            throw new Exception("Registration failed.");
        }

        $success = "Registration successful!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .signup-card {
            width: 500px;
            border-top: 4px solid #4CAF50;
        }
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="card signup-card shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title text-center mb-4">Create New Account</h5>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?>
                    <p class="mt-2">You can now <a href="user_login.php">login</a></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">First Name*</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Name*</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address*</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password*</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number*</label>
                    <input type="tel" class="form-control" name="phone" required>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-warning">Register</button>
                </div>
                <div class="text-center">
                    <p class="small text-muted">Already have an account?
                        <a href="user_login.php" class="text-decoration-none">Log in</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
