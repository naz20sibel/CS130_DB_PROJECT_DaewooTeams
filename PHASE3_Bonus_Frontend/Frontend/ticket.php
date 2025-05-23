<?php
include 'partials/nav.php';
session_start();
// Just show confirmation without database
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/BUSPROJECT/css/navbar.css">
    <link rel="stylesheet" href="/BUSPROJECT/css/footer.css">
    <style>
        .confirmation-icon {
            font-size: 4rem;
            color: #28a745;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="confirmation-icon mb-3">✓</div>
                <h2 class="text-success mb-3">Booking Confirmed!</h2>
                <p class="lead">Your e-ticket has been sent to your registered email.</p>
                
                <div class="card mt-4">
                    <div class="card-body text-start">
                        <h5>Next Steps:</h5>
                        <ul>
                            <li>Check your email for the e-ticket</li>
                            <li>Arrive 30 minutes before departure</li>
                            <li>Bring your CNIC for verification</li>
                        </ul>
                    </div>
                </div>
                
                <a href="index.php" class="btn btn-warning mt-4">Back to Home</a>
            </div>
        </div>
    </div>
</body>
<?php include 'partials/footer.php'; ?>
</html>