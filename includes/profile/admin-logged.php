<?php
$username = $_SESSION['alogin'];
$sql = "SELECT name, id FROM admin WHERE username = :username";
$query = $dbh->prepare($sql);
$query->bindParam(':username', $username, PDO::PARAM_INT);
$query->execute();

// Fetch the count result
$results = $query->fetchAll(PDO::FETCH_OBJ);

$cnt = 1;

if ($query->rowCount() > 0) {
    foreach ($results as $result) { ?>
        <p style="color:white;"><?php echo htmlentities($result->name); ?></p>
    <?php }
}
?>