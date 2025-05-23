<?php
include 'partials/nav.php';
session_start();

// SQL Server connection
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS"; // Adjust if needed
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if (!$conn) {
    die("Database connection failed: " . print_r(sqlsrv_errors(), true));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($message)) {
        $error = "Name and message are required fields";
    } else {
        $query = "INSERT INTO FEEDBACK (name, email, message) VALUES (?, ?, ?)";
        $params = [$name, $email, $message];
        $stmt = sqlsrv_prepare($conn, $query, $params);

        if (sqlsrv_execute($stmt)) {
            $success = "Thank you for your feedback!";
            $_POST = array(); // Clear form
        } else {
            $error = "Failed to submit feedback: " . print_r(sqlsrv_errors(), true);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/BUSPROJECT/css/navbar.css">
    <link rel="stylesheet" href="/BUSPROJECT/css/footer.css">
    <style>
        .feedback-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            background-color: white;
        }
    </style>
</head>
<body class="bg-light">
<div class="container">
    <div class="feedback-container">
        <h3 class="text-center mb-4">Share Your Feedback</h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Your Name *</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Your Feedback *</label>
                <textarea class="form-control" id="message" name="message" rows="4" required><?= 
                    htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-warning px-4 py-2">Submit Feedback</button>
            </div>
        </form>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
</body>
</html>
