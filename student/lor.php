<?php
date_default_timezone_set("Asia/Kolkata");
session_start();
error_reporting(0);
include('../includes/database/dbcon.php');
$studid = $_SESSION['studid'];

if (strlen($_SESSION['studlogin']) == 0) {
    header('location:../index.php');
}
if (isset($_POST['apply'])) {

    $teachers = $_POST['teachers'];
    $date = date("Y-m-d");
    $time = date("H:i:s");
    $status = "0";

    foreach ($teachers as $teacid) {
        $sql = "INSERT INTO req_lor (stud_id, teac_id, status, date, time, description)
                VALUES (:studid, :teacid, :status, :date, :time, '-')";
        $query = $dbh->prepare($sql);
        $query->bindParam(':studid', $studid, PDO::PARAM_STR);
        $query->bindParam(':teacid', $teacid, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':date', $date, PDO::PARAM_STR);
        $query->bindParam(':time', $time, PDO::PARAM_STR);

        $query->execute();
    }
    $lastInsertId = $dbh->lastInsertId();

    if ($lastInsertId) {
        $msg = "Your leave application has been applied, Thank You.";
    } else {
        $error = "Sorry, couldnot process this time. Please try again later.";
    }
}
$teacherSql = "SELECT t.id, t.first_name, t.last_name 
FROM teachers as t 
WHERE t.id NOT IN (SELECT r.teac_id FROM req_lor as r WHERE r.stud_id = :studid);";
$teacherQuery = $dbh->prepare($teacherSql);
$teacherQuery->bindParam(':studid', $studid, PDO::PARAM_STR);
$teacherQuery->execute();
$teachers = $teacherQuery->fetchAll(PDO::FETCH_ASSOC);
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
                    $page = 'req-lor';
                    include '../includes/sidebars/student-sidebar.php';
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
                            <h4 class="page-title pull-left">Request LOR</h4>
                            <ul class="breadcrumbs pull-left">

                                <li><span>Student/Request LOR Form</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">

                        <?php include '../includes/profile/student-profile.php' ?>

                    </div>
                </div>
            </div>
            <!-- page title area end -->
            <div class="main-content-inner">
                <div class="row" style="display: flex; flex-wrap: wrap; justify-content: center;">
                    <div class="col-lg-8 col-md-12">
                        <div class="formpart card" style="width: 100%; margin: 20px 0;">
                            <h1 style="margin: 20px;">Apply for LOR</h1>
                            <form action="lor.php" id="reqform" method="post" style="margin: 0 20px;">
                                <?php if (count($teachers) > 0) { ?>
                                    <div class="form-group" id="teachers">
                                        <label>Select Teacher(s):</label><br>
                                        <?php foreach ($teachers as $teacher) { ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="teachers[]"
                                                    value="<?php echo $teacher['id']; ?>"
                                                    id="teacher_<?php echo $teacher['id']; ?>">
                                                <label class="form-check-label" for="teacher_<?php echo $teacher['id']; ?>">
                                                    <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <button id="subbutton" type="submit" name="apply" class="btn btn-primary"
                                        style="margin-bottom: 20px;">Apply</button>

                                <?php } else { ?>
                                    <div style="text-align:center; margin: 50px 0; color: red;">
                                        <h4>No teachers available for LOR request.</h4>
                                    </div>
                                <?php } ?>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-10 col-md-12">
                        <!-- Recent List Section -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-center">
                                    <div class="trd-history-tabs">
                                        <ul class="nav" role="tablist">
                                            <li>
                                                <a class="active" data-toggle="tab" href="dashboard.php"
                                                    role="tab">Recent List</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-hover table-bordered table-striped progress-table text-center">
                                            <tbody>
                                                <?php
                                                $student_id = $_SESSION['studid']; // Assuming student_id is stored in the session upon login
                                                
                                                $sql = "
                                    SELECT 
                                        students.student_id AS stud_id,
                                        students.first_name AS student_first_name,
                                        students.last_name AS student_last_name,
                                        teachers.first_name AS teacher_first_name,
                                        teachers.last_name AS teacher_last_name,
                                        req_lor.description AS remark,
                                        req_lor.status AS reqstatus,
                                        req_lor.date AS reqdate
                                    FROM req_lor
                                    JOIN students ON req_lor.stud_id = students.id
                                    JOIN teachers ON req_lor.teac_id = teachers.id
                                    WHERE req_lor.stud_id = :student_id";

                                                $query = $dbh->prepare($sql);
                                                $query->bindParam(':student_id', $student_id, PDO::PARAM_INT);
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
                                                <td>Applied On</td>
                                                <td>Current Status</td>
                                                <td>Remarks</td>
                                            </tr>
                                        </thead>';

                                                    foreach ($results as $result) { ?>
                                                        <tr class="leave-row">
                                                            <td> <b><?php echo htmlentities($cnt); ?></b></td>
                                                            <td><?php echo htmlentities($result->stud_id); ?></td>
                                                            <td><a><?php echo htmlentities($result->student_first_name . " " . $result->student_last_name); ?></a>
                                                            </td>
                                                            <td><?php echo htmlentities($result->teacher_first_name . " " . $result->teacher_last_name); ?>
                                                            </td>
                                                            <td><?php echo htmlentities($result->reqdate); ?></td>
                                                            <td>
                                                                <?php
                                                                $stats = $result->reqstatus;
                                                                if ($stats == 1) { ?>
                                                                    <span style="color: green">Accepted <i
                                                                            class="fa fa-check-square-o"></i></span>
                                                                <?php } elseif ($stats == -1) { ?>
                                                                    <span style="color: red">Rejected <i
                                                                            class="fa fa-times"></i></span>
                                                                <?php } elseif ($stats == 0) { ?>
                                                                    <span style="color: blue">Pending <i
                                                                            class="fa fa-spinner"></i></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td><?php echo htmlentities($result->remark); ?></td>
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
                        <!-- Recent List Section End -->
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
<script>
    $(document).ready(function () {
        var submitButton = $('#subbutton');
        submitButton.prop('disabled', true); // Disable submit button initially
        submitButton.css('cursor', 'not-allowed'); // Change cursor to not-allowed

        // Enable or disable button when checkboxes change
        $('#teachers input[type="checkbox"]').on('change', function () {
            if ($('#teachers input[type="checkbox"]:checked').length > 0) {
                submitButton.prop('disabled', false);
                submitButton.css('cursor', 'pointer');
            } else {
                submitButton.prop('disabled', true);
                submitButton.css('cursor', 'not-allowed');
            }
        });

        // Prevent form submission if no checkbox is selected
        $('form#reqform').submit(function (event) {
            if ($('#teachers input[type="checkbox"]:checked').length === 0) {
                event.preventDefault();
                alert('Please select at least one teacher.');
            }
        });
    });

</script>

</html>