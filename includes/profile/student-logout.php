<?php
    session_start();
    unset($_SESSION['studlogin']);
    header("location:../../index.php");