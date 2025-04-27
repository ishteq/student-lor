<?php
session_start();
error_reporting(0);
include('../includes/database/dbcon.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
}
$error = "";
$msg = "";
if (isset($_POST['add'])) {
    $studid = $_POST['studid'];
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $department = $_POST['department'];
    $designation = $_POST['designation'];
    $status = 1;

    $sql = "INSERT INTO teachers(teacher_id, first_name, last_name, email, password,department, designation) VALUES(:studid,:fname,:lname,:email,:password,:department, :designation)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':studid', $studid, PDO::PARAM_STR);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':lname', $lname, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->bindParam(':designation', $designation, PDO::PARAM_STR);
    $query->bindParam(':department', $department, PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();
    if ($lastInsertId) {
        $msg = "Record has been added Successfully";
    } else {
        $error = "ERROR";
    }
    header('location:teacher.php');
}

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Panel - Student LOR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="../assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/css/metisMenu.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/slicknav.min.css">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css"
        media="all" />
    <!-- others css -->
    <link rel="stylesheet" href="../assets/css/typography.css">
    <link rel="stylesheet" href="../assets/css/default-css.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <!-- modernizr css -->
    <script src="../assets/js/vendor/modernizr-2.8.3.min.js"></script>

    <!-- Custom form script -->
    <script type="text/javascript">
        function valid() {
            if (document.addemp.password.value != document.addemp.confirmpassword.value) {
                alert("Password and Confirm Password Field do not match  !!");
                document.addemp.confirmpassword.focus();
                return false;
            } return true;
        }
    </script>
</head>

<body>
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->

    <div class="page-container">
        <!-- sidebar menu area start -->
        <div class="sidebar-menu">
            <div class="sidebar-header" style="width: 250px;">
                <?php include '../includes/icons/dbit-logo.php'; ?>
            </div>
            <div class="main-menu">
                <div class="menu-inner">
                    <?php
                    $page = 'teacher';
                    include '../includes/sidebars/admin-sidebar.php';
                    ?>
                </div>
            </div>
        </div>
        <!-- sidebar menu area end -->
        <!-- main content area start -->
        <div class="main-content">
            <!-- header area start -->
            <div class="header-area">
                <div class="row align-items-center">
                    <!-- nav and search button -->
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn pull-left">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>

                    </div>
                    <!-- profile info & task notification -->
                    <div class="col-md-6 col-sm-4 clearfix">
                        <ul class="notification-area pull-right">
                            <li id="full-view"><i class="ti-fullscreen"></i></li>
                            <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- header area end -->
            <!-- page title area start -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Add Teacher Section</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="teacher.php">Teacher</a></li>
                                <li><span>Add</span></li>

                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-6 clearfix">
                        <?php include '../includes/profile/admin-profile.php'; ?>
                    </div>
                </div>
            </div>
            <!-- page title area end -->
            <div class="main-content-inner">
                <!-- row area start -->
                <div class="row">
                    <div class="col-lg-6 col-ml-12">
                        <div class="row">
                            <!-- Input form start -->
                            <div class="col-12 mt-5">
                                <?php if ($error) { ?>
                                    <div class="alert alert-danger alert-dismissible fade show"><strong>Info:
                                        </strong><?php echo htmlentities($error); ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>

                                    </div><?php } else if ($msg) { ?>
                                        <div class="alert alert-success alert-dismissible fade show"><strong>Info:
                                            </strong><?php echo htmlentities($msg); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div><?php } ?>
                                <div class="card">
                                    <form name="addemp" method="POST">

                                        <div class="card-body">
                                            <p class="text-muted font-14 mb-4">Please fill up the form in order to add
                                                teacher records</p>

                                            <div class="form-group">
                                                <label for="studid" class="col-form-label">Teacher ID</label>
                                                <input class="form-control" name="studid" type="text" autocomplete="off"
                                                    required id="studid" onBlur="">
                                            </div>


                                            <div class="form-group">
                                                <label for="firstname" class="col-form-label">First Name</label>
                                                <input class="form-control" name="firstname" type="text" required
                                                    id="firstname">
                                            </div>

                                            <div class="form-group">
                                                <label for="lastname" class="col-form-label">Last Name</label>
                                                <input class="form-control" name="lastname" type="text"
                                                    autocomplete="off" required id="lastname">
                                            </div>

                                            <div class="form-group">
                                                <label for="email" class="col-form-label">Email</label>
                                                <input class="form-control" name="email" type="email" autocomplete="off"
                                                    required id="email">
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Department</label>
                                                <select class="custom-select" name="department" autocomplete="off">
                                                    <option value="">Choose Department...</option>
                                                    <option value="Comps">Comps</option>
                                                    <option value="IT">IT</option>
                                                    <option value="Mech">Mech</option>
                                                    <option value="Extc">Extc</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="designation" class="col-form-label">Designation</label>
                                                <input class="form-control" name="designation" type="text" autocomplete="off"
                                                    required id="designation" placeholder="eg: Professor, Computer Department">
                                            </div>

                                            <h4>Set Password for Teacher Login</h4>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">Password</label>
                                                <input class="form-control" name="password" type="password"
                                                    autocomplete="off" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">Confirmation
                                                    Password</label>
                                                <input class="form-control" name="confirmpassword" type="password"
                                                    autocomplete="off" required>
                                            </div>



                                            <button class="btn btn-primary" name="add" id="update" type="submit"
                                                onclick="return valid();">PROCEED</button>

                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- Input Form Ending point -->

                </div>
                <!-- row area end -->

            </div>
            <!-- row area start-->
        </div>
        <?php include '../includes/footer/set-footer.php' ?>
        <!-- footer area end-->
    </div>
    <!-- main content area end -->


    </div>
    <!-- jquery latest version -->
    <script src="../assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/metisMenu.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.slicknav.min.js"></script>

    <!-- start chart js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    <!-- start highcharts js -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <!-- start zingchart js -->
    <script src="https://cdn.zingchart.com/zingchart.min.js"></script>
    <script>
        zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
        ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "ee6b7db5b51705a13dc2339db3edaf6d"];
    </script>
    <!-- all line chart activation -->
    <script src="assets/js/line-chart.js"></script>
    <!-- all pie chart -->
    <script src="assets/js/pie-chart.js"></script>

    <!-- others plugins -->
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/scripts.js"></script>
</body>

</html>