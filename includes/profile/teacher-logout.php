<?php
    session_start();
    unset($_SESSION['teaclogin']);
    header("location:../../teacher/index.php"); 