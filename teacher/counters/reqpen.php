<?php
// Ensure session variable is available
if (isset($_SESSION['teaclogin'])) {
    $teachid = $_SESSION['teaclogin']; // Retrieve the teacher ID from session
    
    // Use COUNT() to get the number of accepted requests (status = '1')
    $sql = "SELECT COUNT(*) as accepted_count FROM req_lor WHERE teac_id = :teacid AND status = '0'";
    $query = $dbh->prepare($sql);
    $query->bindParam(':teacid', $teachid, PDO::PARAM_INT);
    $query->execute();
    
    // Fetch the count result
    $result = $query->fetch(PDO::FETCH_OBJ);
    $accepted_count = $result->accepted_count;
    
    // Output the count
    echo htmlentities($accepted_count);
} else {
    // Handle case where the session is not set
    echo "0";
}

