<?php

// Ensure session variable is available
if (isset($_SESSION['alogin'])) {
    
    // Use COUNT() to get the number of accepted requests (status = '1')
    $sql = "SELECT COUNT(*) as accepted_count FROM pro_lor WHERE status = '-1'";
    $query = $dbh->prepare($sql);
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

