<?php
    $id=$_SESSION['teaclogin'];
    $sql = "SELECT first_name,last_name,teacher_id from teachers where id=:id";
    $query = $dbh -> prepare($sql);
    $query->bindParam(':id',$id,PDO::PARAM_STR);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    $cnt=1;

    if($query->rowCount() > 0){
        foreach($results as $result)
    {    ?>
        <p style="color:white;"><?php echo htmlentities($result->first_name." ".$result->last_name);?></p>
        <span><?php echo htmlentities($result->teacher_id)?></span>
<?php }
    } 
?>