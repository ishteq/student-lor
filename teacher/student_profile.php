<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
error_reporting(0);
include('../includes/database/dbcon.php');

if (strlen($_SESSION['teaclogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    $teacid = $_SESSION['teaclogin'];
    $sql = "SELECT first_name AS fname, last_name AS lname FROM teachers WHERE id = :teacid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':teacid', $teacid, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_OBJ);

    // Sanitize and validate studid
    $studid = $_GET['studid'];

    if ($studid) {
        try {
            $sql2 = "SELECT student_id, first_name, last_name, department, email, batch
                    FROM students WHERE id = :studid";
            $query2 = $dbh->prepare($sql2);
            $query2->bindParam(':studid', $studid, PDO::PARAM_STR);
            $query2->execute();
            $result2 = $query2->fetch(PDO::FETCH_OBJ);

            if (!$result2) {
                throw new Exception('No student found with the provided ID.');
            }

            // Fetch academic details (CGPA)
            $academic_query = "SELECT semester, cgpa FROM student_academics WHERE stud_id = :studid";
            $stmt = $dbh->prepare($academic_query);
            $stmt->bindParam(':studid', $studid, PDO::PARAM_INT);
            $stmt->execute();
            $academic_result = $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo "<p>Error fetching data: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Invalid student ID provided.</p>";
        exit();
    }
    ?>

    <!doctype html>
    <html class="no-js" lang="en">
    <style>
        .container-fluid {
            padding: 0;
        }

        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #343a40;
            padding-top: 20px;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
        }

        .sidebar a:hover {
            background-color: #007bff;
            color: white;
        }

        .profile-container {
            margin: 0px 50px;
            /* Account for the sidebar width */
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 30px;
        }

        .profile-header {
            text-align: center;
            padding-bottom: 20px;
        }

        .profile-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .profile-header p {
            font-size: 16px;
            color: #555;
        }

        .academic-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .academic-table th,
        .academic-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .academic-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .academic-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .academic-table td {
            font-size: 14px;
        }

        .academic-title {
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        .btn-back {
            margin-top: 20px;
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Teacher Panel - Student LOR</title>
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
                                    <li><span>Student Profile</span></li>
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
                    <div class="sales-report-area mt-5 mb-5">
                        <div class="profile-container">
                            <div class="profile-header">
                                <h1><?php echo htmlspecialchars($result2->first_name . " " . $result2->last_name); ?></h1>
                                <p>Email: <?php echo htmlspecialchars($result2->email); ?></p>
                                <p>Department: <?php echo htmlspecialchars($result2->department); ?></p>
                                <p>Batch: <?php echo htmlspecialchars($result2->batch); ?></p>
                            </div>

                            <!-- Academic Information -->
                            <h2 class="academic-title">Academic Information</h2>
                            <table class="academic-table">
                                <thead>
                                    <tr>
                                        <th>Semester</th>
                                        <th>CGPA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($academic_result)) {
                                        foreach ($academic_result as $academic) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($academic->semester); ?></td>
                                                <td><?php echo htmlspecialchars($academic->cgpa); ?></td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="2">No academic records available.</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
                        </div>
                    </div>
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
        <script>
            $(document).ready(function () {
                $('.freeze-btn').click(function (e) {
                    e.preventDefault();
                    var row = $(this).closest('.leave-row');
                    row.addClass('freeze-row');
                });
            });
        </script>

    </body>

    </html>

<?php } ?>