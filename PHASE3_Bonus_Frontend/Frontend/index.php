<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daewoo-Style Bus Reservation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/BUSPROJECT/css/index.css">
 
<style>

    .hero-section {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                url('assets/hero.jpeg') no-repeat center center fixed;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    width: 100%;
    min-height: 100vh;  /* Increased from 80vh to 90vh */
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
    position: relative;
    margin: 0;
    padding: 0;
}

</style>
</head>
<body>

<div class="noticeboardpreview bg-white mx-3 my-2">
<?php
// DB connection
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS";
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

// Your existing query code
$query = "SELECT n.*, a.full_name as posted_by_name 
          FROM NOTICEBOARD n 
          JOIN ADMINS a ON n.posted_by = a.admin_id 
          ORDER BY n.notice_id DESC";
$stmt = sqlsrv_query($conn, $query);
if ($stmt === false) {
    die("Notices query failed: " . print_r(sqlsrv_errors(), true));
}
$all_notices = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $all_notices[] = $row;
}

// Display notices in h2 elements
foreach ($all_notices as $notice) {
    

    echo '<p class="text-center mb-3 px-3 py-2 bg-light rounded" style="font-size: 1.1rem; border-left: 3px solid #0d6efd;">'
    . '<i class="bi bi-info-circle-fill me-2 text-primary"></i>'
    . htmlspecialchars($notice['message']) 
    . '</p>';
}
?>

</div>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="#">
            <span class="text-warning">DAEWOO</span> EXPRESS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2 rounded" href="index.php">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2 rounded" href="user_login.php">
                        <i class="fas fa-user me-1"></i> Login
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2 rounded" href="signup.php">
                        <i class="fas fa-user-plus me-1"></i> Sign Up
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2 rounded bg-warning text-dark fw-bold" href="admin_login.php">
                        <i class="fas fa-lock me-1"></i> Admin
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">Travel in Comfort & Style</h1>
            <p class="lead mb-5">Book your bus tickets online and enjoy premium services</p>
            <a href="routes.php" class="btn btn-booking">Book Now</a>
        </div>
    </section>

   <!-- About Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">About Daewoo Express</h2>
            <p class="text-muted">Your trusted partner in intercity travel</p>
        </div>
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="assets/indexabout.jpg" alt="Daewoo Bus Service" class="img-fluid rounded shadow-sm">
            </div>
            <div class="col-md-6">
                <h4 class="mb-3">Why Choose Us?</h4>
                <p>Daewoo Express is Pakistan's leading intercity bus service provider. We offer safe, comfortable, and timely travel experiences across all major cities. Our fleet is equipped with the latest amenities to ensure your journey is as smooth as possible.</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Luxurious, air-conditioned buses</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> On-time departures and arrivals</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Experienced and professional staff</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Online booking and e-ticketing system</li>
                </ul>
               
            </div>
        </div>
    </div>
</section>


<footer class="footer-section">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand">
                <h3 class="footer-logo">DAEWOO <span>EXPRESS</span></h3>
                <p>Premium bus services across Pakistan</p>
                
            </div>
            
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="feedback.php">Feedback</a></li>
                </ul>
            </div>
            
       
            
            <div class="footer-contact">
                <h4>Contact Us</h4>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> 123 Bus Terminal, Karachi, Pakistan</li>
                    <li><i class="fas fa-phone-alt"></i> +92 300 1234567</li>
                    <li><i class="fas fa-envelope"></i> info@daewooexpress.com</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2023 Daewoo Express. All Rights Reserved.</p>
        </div>
    </div>
</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>