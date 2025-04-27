<?php
$hodid = $_SESSION['hodlogin'];
$sql = "SELECT first_name, last_name, hod_id FROM hods WHERE id = :hodid";
$query = $dbh->prepare($sql);
$query->bindParam(':hodid', $hodid, PDO::PARAM_INT);
$query->execute();

// Fetch the count result
$results = $query->fetchAll(PDO::FETCH_OBJ);

$cnt = 1;

if ($query->rowCount() > 0) {
    foreach ($results as $result) { ?>
        <p style="color:white;"><?php echo htmlentities($result->first_name . " " . $result->last_name); ?></p>
        <span><?php echo htmlentities($result->hod_id) ?></span>
    <?php }
}
?>