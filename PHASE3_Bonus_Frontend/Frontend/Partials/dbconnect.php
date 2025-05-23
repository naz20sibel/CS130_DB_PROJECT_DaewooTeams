<?php
$serverName = "DESKTOP-BC0HPRM\\SQLEXPRESS"; 
$connectionInfo = array("Database" => "Project");
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}
?>
