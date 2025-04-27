<?php
session_start();
require '../includes/database/dbcon.php';
error_reporting(0);
// Check if the user is logged in
if (!isset($_SESSION['teaclogin'])) {
    header("Location: index.php");
    exit;
}

// Validate and sanitize doc_id from URL
if (!isset($_GET['stud_id']) || !is_numeric($_GET['stud_id'])) {
    die("Invalid document ID.");
}

$stud_id = (int)$_GET['stud_id'];
$teacid = $_SESSION['teaclogin'];

$sql = "SELECT * FROM pro_lor WHERE stud_id = :stud_id AND teac_id = :teacid";
$query = $dbh->prepare($sql);
$query->bindParam(':stud_id', $stud_id, PDO::PARAM_INT);
$query->bindParam(':teacid', $teacid, PDO::PARAM_INT);
$query->execute();

$doc = $query->fetch(PDO::FETCH_OBJ);

if (!$doc) {
    die("You are not authorized to view this document or it doesn't exist.");
}

// Secure file path (outside htdocs)
$secure_path = "C:/xampp/htdocs/student-lor/student/uploads/" . $doc->admit_path;

if (!file_exists($secure_path)) {
    http_response_code(404);
    exit("File not found.");
}

// Send file headers
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . basename($secure_path) . "\"");
readfile($secure_path);
exit;

