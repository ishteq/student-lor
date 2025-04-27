<?php
session_start();
error_reporting(0);
include('../includes/database/dbcon.php');

if (isset($_POST['signin'])) {
    $hod_id = $_POST['hod_id'];
    $password = $_POST['password'];

    if (!empty($hod_id) && !empty($password)) {
        try {
            $sql = "SELECT id, hod_id, password FROM hods WHERE hod_id = :hod_id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':hod_id', $hod_id, PDO::PARAM_STR);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_OBJ);

            if ($result && $password == $result->password) {
                $_SESSION['hodlogin'] = $result->id;

                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid HOD ID or Password!";
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please enter both HOD ID and Password.";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/css/metisMenu.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/slicknav.min.css">
    <link rel="stylesheet" href="../assets/css/typography.css">
    <link rel="stylesheet" href="../assets/css/default-css.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <script src="../assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->

    <!-- login area start -->
    <div class="login-area">
        <div class="container">
            <div class="login-box ptb--100">
                <form method="POST">
                    <div class="login-form-head">
                        <h4>HOD LOGIN</h4>
                        <p>Letter of Recommendation System for DBIT Student’s Higher Studies</p>
                    </div>
                    <div class="login-form-body">
                        <!-- Display error message if any -->
                        <?php if (isset($error_message)) { ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php } ?>

                        <!-- HOD ID input -->
                        <div class="form-gp">
                            <label for="hod_id">HOD ID</label>
                            <input type="text" id="hod_id" name="hod_id" autocomplete="off" required>
                            <i class="ti-user"></i>
                        </div>

                        <!-- Password input -->
                        <div class="form-gp">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" autocomplete="off" required>
                            <i class="ti-lock"></i>
                        </div>

                        <!-- Submit button -->
                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit" name="signin">Submit <i
                                    class="ti-arrow-right"></i></button>
                        </div>

                        <!-- Go back link -->
                        <div class="form-footer text-center mt-5">
                            <p class="text-muted"><a href="../index.php"><i class="ti-arrow-left"></i> Go Back</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- login area end -->

    <!-- jquery latest version -->
    <script src="../assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/metisMenu.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.slicknav.min.js"></script>

    <!-- others plugins -->
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/scripts.js"></script>
</body>

</html>