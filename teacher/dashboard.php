<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
error_reporting(0);
include('../includes/database/dbcon.php');
if (strlen($_SESSION['teaclogin']) == 0) {
    header('location:index.php');
}
$teacid = $_SESSION['teaclogin'];
$sql = "SELECT first_name AS fname, last_name AS lname FROM teachers WHERE id = :teacid";
$query = $dbh->prepare($sql);
$query->bindParam(':teacid', $teacid, PDO::PARAM_INT);
$query->execute();

// Fetch the count result
$result = $query->fetch(PDO::FETCH_OBJ);

if (isset($_POST['updatereq'])) {
    $student_id = $_POST['student_id'];
    $teacher_id = $_POST['teacher_id'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    try {
        $sql5 = "UPDATE req_lor SET status=:status, description=:description WHERE stud_id=:stud_id AND teac_id=:teac_id";
        $query5 = $dbh->prepare($sql5);
        $query5->bindParam(':status', $status, PDO::PARAM_STR);  // Changed to PDO::PARAM_INT for the integer status
        $query5->bindParam(':description', $description, PDO::PARAM_STR);
        $query5->bindParam(':stud_id', $student_id, PDO::PARAM_STR);
        $query5->bindParam(':teac_id', $teacher_id, PDO::PARAM_STR);

        $query5->execute();
        if ($status == 1) {
            $date = date("Y-m-d");
            $time = date("H:i:s");
            $description = '-';
            $sql4 = "INSERT INTO pro_lor (stud_id, teac_id, description, university, skill, status, date, time) 
            VALUES (:student_id, :teacher_id, :description,'', '', 0, :date, :time)";
            $query4 = $dbh->prepare($sql4);

            // Bind the parameters to the query to prevent SQL injection
            $query4->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $query4->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
            $query4->bindParam(':description', $description, PDO::PARAM_STR);
            $query4->bindParam(':date', $date, PDO::PARAM_STR);
            $query4->bindParam(':time', $time, PDO::PARAM_STR);
            $query4->execute();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Teacher Panel - Student LOR</title>
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
                    $page = 'dashboard';
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
                            <h4 class="page-title pull-left">Dashboard</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="dashboard.php">Home</a></li>
                                <li><span>Teacher's Dashboard</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <?php include '../includes/profile/teacher-profile.php' ?>
                    </div>
                </div>
            </div>
            <!-- page title area end -->
            <div class="main-content-inner">
                <!-- sales report area start -->
                <div class="sales-report-area mt-5 mb-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="single-report mb-xs-30">
                                <div class="s-report-inner pr--20 pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-spinner"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Pending LOR Request</h4>

                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
                                        <h1><?php include 'counters/reqpen.php' ?></h1>
                                        <span>Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="single-report mb-xs-30">
                                <div class="s-report-inner pr--20 pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-times"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Rejected LOR Request</h4>

                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
                                        <h1><?php include 'counters/reqrej.php' ?></h1>
                                        <span>Rejected</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="single-report">
                                <div class="s-report-inner pr--20 pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-check-square-o"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Accepted LOR Request</h4>

                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
                                        <h1><?php include 'counters/reqacc.php' ?></h1>
                                        <span>Accepted</span>
                                    </div>
                                </div>
                                <!-- <canvas id="coin_sales3" height="100"></canvas> -->
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="single-report mb-xs-30">
                                <div class="s-report-inner pr--20 pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-spinner"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Pending Application</h4>

                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
                                        <h1><?php include 'counters/propen.php' ?></h1>
                                        <span>Pending</span>
                                    </div>
                                </div>
                                <!-- <canvas id="coin_sales1" height="100"></canvas> -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="single-report mb-xs-30">
                                <div class="s-report-inner pr--20 pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-times"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Declined Application</h4>

                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
                                        <h1><?php include 'counters/prorej.php' ?></h1>
                                        <span>Declined</span>
                                    </div>
                                </div>
                                <!-- <canvas id="coin_sales2" height="100"></canvas> -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="single-report">
                                <div class="s-report-inner pr--20 pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-check-square-o"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Approved Application</h4>

                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
                                        <h1><?php include 'counters/proacc.php' ?></h1>
                                        <span>Approved</span>
                                    </div>
                                </div>
                                <!-- <canvas id="coin_sales3" height="100"></canvas> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- sales report area end -->

                <!-- row area start -->
                <div class="row">

                    <!-- trading history area start -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-center">
                                    <!-- <h4 class="header-title">Employee Leave History</h4> -->
                                    <div class="trd-history-tabs">
                                        <ul class="nav" role="tablist">
                                            <li>
                                                <a class="active" data-toggle="tab" href="dashboard.php"
                                                    role="tab">Requested LOR list</a>
                                            </li>
                                        </ul>
                                    </div>
                                    </select>
                                </div>
                                <!-- <h4 class="header-title"></h4> -->
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-hover table-bordered table-striped progress-table text-center">

                                            <tbody>
                                                <?php

                                                $sql = "
                                                    SELECT 
                                                        req_lor.stud_id AS stud_id,
                                                        students.student_id AS coll_id,
                                                        students.first_name AS student_first_name,
                                                        students.last_name AS student_last_name,
                                                        teachers.first_name AS teacher_first_name,
                                                        teachers.last_name AS teacher_last_name,
                                                        req_lor.description AS remark,
                                                        req_lor.status AS reqstatus,
                                                        req_lor.date AS reqdate,
                                                        students.department AS department
                                                    FROM req_lor
                                                    JOIN students ON req_lor.stud_id = students.id
                                                    JOIN teachers ON req_lor.teac_id = teachers.id
                                                    WHERE req_lor.teac_id = :teacher_id";  // Filter by the logged-in teacher's ID
                                                // Filter by the logged-in student
                                                
                                                $query = $dbh->prepare($sql);
                                                $query->bindParam(':teacher_id', $teacid, PDO::PARAM_INT); // Binding the student ID for security
                                                $query->execute();
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                $cnt = 1;

                                                // Check if there are any results
                                                if ($query->rowCount() > 0) {
                                                    // Only display the table header if there are results
                                                    echo '
                    <thead class="text-uppercase">
                        <tr>
                            <td>S.N</td>
                            <td>Student ID</td>
                            <td width="120">Full Name</td>
                            <td>Department</td>
                            <td>Applied On</td>
                            <td>Current Status</td>
                            <td>Accept / Reject</td>
                        </tr>
                    </thead>';

                                                    // Loop through the results and display them
                                                    foreach ($results as $result) {
                                                        $stuid = $result->stud_id;
                                                        $modalId = "proaction-" . $stuid;
                                                        ?>
                                                        <tr class="leave-row">
                                                            <td> <b><?php echo htmlentities($cnt); ?></b></td>
                                                            <td><?php echo htmlentities($result->coll_id); ?></td>
                                                            <td><a
                                                                    href="student_profile.php?studid=<?php echo htmlentities($stuid); ?>"><?php echo htmlentities($result->student_first_name . " " . $result->student_last_name); ?></a>
                                                            </td>
                                                            <td><?php echo htmlentities($result->department); ?>
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
                                                            <td>
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-toggle="modal"
                                                                    data-target="#<?php echo $modalId; ?>">SET
                                                                    ACTION</button>
                                                                <div class="modal fade" id="<?php echo $modalId; ?>"
                                                                    tabindex="-1" role="dialog"
                                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="exampleModalLabel">
                                                                                    SET ACTION</h5>
                                                                                <button type="button" class="close"
                                                                                    data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>

                                                                            <form method="POST" name="updatereq">
                                                                                <div class="modal-body">
                                                                                    <input type="hidden" name="student_id"
                                                                                        value="<?php echo $stuid; ?>">
                                                                                    <input type="hidden" name="teacher_id"
                                                                                        value="<?php echo $teacid; ?>">
                                                                                    <select class="custom-select" name="status"
                                                                                        required>
                                                                                        <option value="">Choose...</option>
                                                                                        <option value="1">Accept</option>
                                                                                        <option value="-1">Reject</option>
                                                                                    </select></p>
                                                                                    <br>
                                                                                    <p><textarea id="textarea1"
                                                                                            name="description"
                                                                                            class="form-control"
                                                                                            placeholder="Description" row="5"
                                                                                            maxlength="500" required></textarea>
                                                                                    </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-danger"
                                                                                        data-dismiss="modal">Close</button>
                                                                                    <button type="submit"
                                                                                        class="btn btn-success"
                                                                                        name="updatereq">Commit</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                        <?php
                                                        $cnt++;
                                                    }
                                                } else {
                                                    // No records found, display a friendly message
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
                    <!-- trading history area end -->
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>

</html>