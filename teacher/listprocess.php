<?php
date_default_timezone_set("Asia/Kolkata");
session_start();
error_reporting(0);
include('../includes/database/dbcon.php');

$teacid = $_SESSION['teaclogin'] ?? null;

if (!$teacid) {
    header('location:../index.php');
    exit();
}
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Letter of Recommendation System for DBIT Student’s Higher Studies</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="shortcut icon" type="image/png" href="../assets/images/icon/favicon.ico"> -->
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- preloader area start -->
    <div id="preloader">
            <div class="loader"></div>
        </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">
        <!-- sidebar menu area start -->
        <div class="sidebar-menu">
            <div class="sidebar-header" style="width: 250px;">
                <?php include '../includes/icons/dbit-logo.php'; ?>
            </div>
            <div class="main-menu">
                <div class="menu-inner">
                    <?php
                    $page = 'pro-lor';
                    include '../includes/sidebars/teacher-sidebar.php';
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
                            <h4 class="page-title pull-left">Process LOR</h4>
                            <ul class="breadcrumbs pull-left">

                                <li><span>Application List</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <?php include '../includes/profile/teacher-profile.php' ?>
                    </div>
                </div>
            </div>
            <!-- page title area end -->
            <div class="container mt-5">
                <div class="card">
                    <div class="card-header">
                        <h2>LOR Application List</h2>
                    </div>
                    <div class="card-body">
                        <div class="single-table">
                            <div class="table-responsive">
                                <table
                                    class="table table-hover table-bordered table-striped progress-table text-center">
                                    <tbody>
                                        <?php
                                        // Fetch student profile details
                                        $sql = "SELECT s.student_id AS studentid, 
                                                    s.id AS studid, 
                                                    s.first_name AS studfirst, 
                                                    s.last_name AS studlast, 
                                                    t.id AS teacherid,
                                                    t.first_name AS teacfirst, 
                                                    t.last_name AS teaclast, 
                                                    pl.status AS statuz, 
                                                    pl.description AS descrip, 
                                                    pl.date AS dat,
                                                    pl.freeze AS freeze,
                                                    pl.admit_path AS admit_path
                                                    FROM pro_lor pl 
                                                    JOIN students s ON pl.stud_id = s.id 
                                                    JOIN teachers t ON pl.teac_id = t.id 
                                                    WHERE pl.teac_id = :teacid AND (pl.status=2 OR pl.status=1)";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':teacid', $teacid, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;

                                        if ($query->rowCount() > 0) {
                                            echo '
                                        <thead class="text-uppercase">
                                            <tr>
                                                <td>S.N</td>
                                                <td>Student ID</td>
                                                <td width="120">Full Name</td>
                                                <td>Teacher Name</td>
                                                <td>Date</td>
                                                <td>Current Status</td>
                                                <td>Process</td>
                                            </tr>
                                        </thead>';

                                            foreach ($results as $result) { ?>
                                                <tr class="leave-row">
                                                    <td> <b><?php echo htmlentities($cnt); ?></b></td>
                                                    <td><?php echo htmlentities($result->studentid); ?></td>
                                                    <td><a><?php echo htmlentities($result->studfirst . " " . $result->studlast); ?></a>
                                                    </td>
                                                    <td><?php echo htmlentities($result->teacfirst . " " . $result->teaclast); ?>
                                                    </td>
                                                    <td><?php echo htmlentities($result->dat); ?></td>
                                                    <td>
                                                        <?php
                                                        $stats = $result->statuz;
                                                        if ($stats == 1) { ?>
                                                            <span style="color: green">Accepted <i
                                                                    class="fa fa-check-square-o"></i></span>
                                                        <?php } elseif ($stats == -1) { ?>
                                                            <span style="color: red">Rejected <i class="fa fa-times"></i></span>
                                                        <?php } elseif ($stats == 2) { ?>
                                                            <span style="color: blue">Pending <i class="fa fa-spinner"></i></span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $stats = $result->statuz;
                                                        if ($stats == 1) { ?>
                                                            <a
                                                                href="process_lor.php?student_id=<?php echo $result->studid; ?>&teacher_id=<?php echo $result->teacherid; ?>"><button
                                                                    type="button" class="btn btn-success">Successful</button></a>
                                                            <?php
                                                            if ($result->freeze == 0) {
                                                                ?>
                                                                <span class="btn btn-success">Not Freeze</span>
                                                            <?php } else {
                                                                ?>
                                                                <a href="<?php echo "admit_card.php?stud_id=$result->studid"; ?>"
                                                                    target="_blank" class="btn btn-info btn-sm"
                                                                    style="height: 44px;">Freeze</a>
                                                            <?php }
                                                            ?>
                                                        </td>
                                                        <?php
                                                        } else { ?>
                                                        <a
                                                            href="process_lor.php?student_id=<?php echo $result->studid; ?>&teacher_id=<?php echo $result->teacherid; ?>"><button
                                                                type="button" class="btn btn-success">Verify</button></a></td>
                                                    <?php } ?>
                                                </tr>
                                                <?php
                                                $cnt++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'>You have no request history yet.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- main content area end -->
    <!-- footer area start-->
    <?php include '../includes/footer/set-footer.php' ?>
    <!-- footer area end-->
    </div>
    <!-- page container area end -->
    <!-- offset area start -->
    <div class="offset-area">
        <div class="offset-close"><i class="ti-close"></i></div>


    </div>
    <!-- offset area end -->
    <!-- jquery latest version -->
    <script src="../assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
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