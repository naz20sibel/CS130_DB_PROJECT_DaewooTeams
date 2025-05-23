<?php
session_start();


if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Bus Reservation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
        }

        .sidebar {
            background-color: var(--primary-color);
            color: white;
            min-height: 100vh;
            width: 25vw;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            background-color: var(--secondary-color);
        }

        .sidebar-menu {
            padding: 0;
            list-style: none;
        }

        .sidebar-menu li a {
            color: white;
            padding: 15px 20px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background-color: var(--secondary-color);
            border-left: 4px solid var(--accent-color);
        }

        .sidebar-menu li a i {
            margin-right: 10px;
        }

        .main-content {
            background-color: #f5f6fa;
        }

        .dashboard-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .staff-card { border-left: 4px solid #3498db; }
        .bus-card { border-left: 4px solid #2ecc71; }
        .route-card { border-left: 4px solid #f39c12; }
        .booking-card { border-left: 4px solid #9b59b6; }
        .user-card { border-left: 4px solid #e74c3c; }

        .navbar-brand {
            font-weight: 700;
        }

        .welcome-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="wrapper d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header text-center">
                <h4>Daewoo Admin</h4>
            </div>
            <ul class="sidebar-menu">
    <li><a href="javascript:void(0)" class="active" onclick="showSection('dashboardall')"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
    <li><a href="javascript:void(0)" onclick="showSection('staff')"><i class="bi bi-people-fill"></i> Staff Management</a></li>
    <li><a href="javascript:void(0)" onclick="showSection('bus')"><i class="bi bi-bus-front"></i> Bus Management</a></li>
    <li><a href="javascript:void(0)" onclick="showSection('maintenance')"><i class="bi bi-tools"></i> Vehicle Maintenance</a></li>
    <li><a href="javascript:void(0)" onclick="showSection('noticeboard')"><i class="bi bi-pin-angle-fill"></i> Noticeboard</a></li>
    <li><a href="javascript:void(0)" onclick="showSection('amenities')"><i class="bi bi-bag-check-fill"></i> Amenities Management</a></li>
    <li><a href="admin_logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
</ul>
        </div>

        <!-- Main Content -->
        <div class="main-content w-100">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                   
                    <div class="navbar-brand ms-3">Admin Dashboard</div>
                    <div class="d-flex align-items-center">
                        <span class="text-white me-3">
                            <i class="bi bi-person-circle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                        </span>
                    </div>
                </div>
            </nav>

            <!-- Dashboard Content -->
            <div class="container-fluid p-4">


            <script>
// Show the default section on page load (optional)
document.addEventListener('DOMContentLoaded', function() {
    // You can set a default section to show here if needed
    // showSection('dashboard');
});

function showSection(sectionId) {
    // Hide all content sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show the selected section
    const activeSection = document.getElementById(sectionId + '-content');
    if (activeSection) {
        activeSection.style.display = 'block';
        dashboardall.style.display='none';
    }
     // Special condition for dashboard
     if (sectionId === 'dashboardall') {
        document.getElementById('dashboardall').style.display = 'block';
    } 
    // Update active menu item
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // Prevent default behavior
    event.preventDefault();
}
</script>
            
    <div id="staff-content" class="content-section" style="display: none;">
        <?php include 'staff_management.php' ?>
    </div>
    <div id="bus-content" class="content-section" style="display: none;">
        <?php include 'bus_management.php' ?>
    </div>
    <div id="maintenance-content" class="content-section" style="display: none;">
        <?php include 'vehicle_maintenance.php' ?>
    </div>
    <div id="noticeboard-content" class="content-section" style="display: none;">
        <?php include 'noticeboard.php' ?>
    </div>
    <div id="amenities-content" class="content-section" style="display: none;">
        <?php include 'bus_amenities.php' ?>
    </div>
            
            
            
    <div id="dashboardall" class="content-section">

                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h4>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h4>
                    <p class="text-muted">Manage your bus reservation system efficiently</p>
                </div>

                <!-- Quick Access Section -->
                <h4 class="mb-3">Quick Access</h4>
                <div class="row">
                    <!-- Bus Management Card -->
                    <div class="col-md-6 col-lg-4">
                        <a href="javascript:void(0)" onclick="showSection('bus')" class="text-decoration-none">                        
                            <div class="card dashboard-card bus-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-bus-front card-icon text-success"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title">Bus Management</h5>
                                            <p class="card-text text-muted">Add, edit or remove buses from the system</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                   <!-- Staff Management Card -->
<div class="col-md-6 col-lg-4">
    <a href="javascript:void(0)" onclick="showSection('staff')" class="text-decoration-none">
        <div class="card dashboard-card staff-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people-fill card-icon text-primary"></i>
                    </div>
                    <div>
                        <h5 class="card-title">Staff Management</h5>
                        <p class="card-text text-muted">Manage staff members and roles</p>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Noticeboard Card -->
<div class="col-md-6 col-lg-4">
<a href="javascript:void(0)" onclick="showSection('noticeboard')" class="text-decoration-none">
        <div class="card dashboard-card route-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-pin-angle-fill card-icon text-warning"></i>
                    </div>
                    <div>
                        <h5 class="card-title">Noticeboard</h5>
                        <p class="card-text text-muted">Post and manage public notices</p>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>
 <!-- Add other feature cards below similarly if needed -->
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
