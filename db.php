
<?php
// db.php - include this in every PHP file that needs DB access
$DB_HOST = "localhost";
$DB_USER = "uei4bkjtcem6s";
$DB_PASS = "wmhalmspfjgz";
$DB_NAME = "dbnefhxadptfti";
 
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
