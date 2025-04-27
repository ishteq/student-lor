<?php
session_start();
require '../includes/database/dbcon.php';
error_reporting(0);
// Check if the user is logged in
if (!isset($_SESSION['studlogin'])) {
    header("Location: ../index.php");
    exit;
}

// Validate and sanitize doc_id from URL
if (!isset($_GET['doc_id']) || !is_numeric($_GET['doc_id'])) {
    die("Invalid document ID.");
}

$doc_id = (int)$_GET['doc_id'];
$studid = $_SESSION['studid'];

$sql = "SELECT * FROM student_docs WHERE id = :doc_id AND stud_id = :studid";
$query = $dbh->prepare($sql);
$query->bindParam(':doc_id', $doc_id, PDO::PARAM_INT);
$query->bindParam(':studid', $studid);
$query->execute();

$doc = $query->fetch(PDO::FETCH_OBJ);

if (!$doc) {
    die("You are not authorized to view this document or it doesn't exist.");
}

// Secure file path (outside htdocs)
$secure_path = "C:/uploads/" . $doc->doc_path;

if (!file_exists($secure_path)) {
    http_response_code(404);
    exit("File not found.");
}

// Send file headers
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . basename($secure_path) . "\"");
readfile($secure_path);
exit;
