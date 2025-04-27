<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
error_reporting(0);
include '../includes/database/dbcon.php';
if (strlen($_SESSION['hodlogin']) == 0) {
    header('location:index.php');
}
// Prepare the SQL query without search functionality
$sql1 = "SELECT s.id as studid,s.first_name AS student_first, s.last_name AS student_last, 
    s.email AS student_email, s.department AS student_department,
    t.first_name AS teacher_first, t.last_name AS teacher_last,
    lor.status AS lor_status, lor.description AS lor_description, lor.selected_university AS lor_selected_university,
    lor.date AS lor_date, lor.time AS lor_time, lor.university AS lor_university, lor.skill AS lor_skill
    FROM students s
    INNER JOIN pro_lor lor ON s.id = lor.stud_id
    LEFT JOIN teachers t ON lor.teac_id = t.id";

try {
    $stmt = $dbh->prepare($sql1);
    $stmt->execute();
    $student_teacher_data = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit();
}

// Status mapping for display purposes
$status_map = [
    0 => 'Pending',
    1 => 'Approved',
    -1 => 'Rejected',
    3 => 'In Progress'
];

?>

<!doctype html>
<html class="no-js" lang="en">
<style>
    /* Custom styles for the status timeline */
    .status-timeline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        position: relative;
    }

    /* Custom styles for the status timeline */
    .status-timeline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        position: relative;
    }

    /* Style for each timeline step (circle) */
    .timeline-step {
        display: inline-block;
        text-align: center;
        width: 35px;
        /* Circle width */
        height: 35px;
        /* Circle height */
        line-height: 35px;
        /* Centers text vertically */
        border-radius: 50%;
        /* Keeps the shape circular */
        background-color: #f0f0f0;
        color: #333;
        position: relative;
        margin: 0 15px;
        /* Spacing between the circles */
        font-size: 12px;
        /* Adjusted font size for better fit */
        white-space: nowrap;
        /* Prevents text wrapping */
        overflow: hidden;
        /* Prevents overflow */
        text-overflow: ellipsis;
        /* Adds ellipsis if text is too long */
    }

    /* Active status */
    .timeline-step.pending {
        background-color: #007bff;
        color: white;
    }

    .timeline-step.progrez {
        background-color: #f0ad4e;
        color: white;
    }

    .timeline-step.accepted {
        background-color: #5cb85c;
        color: white;
    }

    .timeline-step.rejected {
        background-color: #d9534f;
        color: white;
    }

    .freeze {
        background-color: #0275d8;
        color: white;
    }

    /* Blue connecting line between circles */
    .timeline-step::before {
        content: '';
        position: absolute;
        width: 100px;
        /* Increased width for connecting line */
        height: 2px;
        background-color: #007bff;
        /* Blue line color */
        top: 50%;
        /* Adjusted to align with the center of the circle */
        left: -100px;
        /* Adjusted to maintain proper spacing */
        z-index: -1;
    }

    /* Hide the line for the first circle */
    .timeline-step:first-child::before {
        display: none;
    }

    /* Optional: Handle last circle line */
    .timeline-step:last-child::before {
        width: 50px;
        /* Adjusted to shorten the last line */
    }

    /* Timeline text */
    .timeline-text {
        font-size: 10px;
        /* Smaller font size for better fit */
        white-space: nowrap;
        /* Prevents text wrapping */
        overflow: hidden;
        /* Hides overflow text */
        text-overflow: ellipsis;
        /* Adds ellipsis if text is too long */
    }

    .status-container {
        max-width: 300px;
        /* Limit the width of the status column */
        word-wrap: break-word;
        /* Prevent text overflow */
    }

    .status-description {
        font-size: 13px;
        margin-top: 5px;
    }

    .status-date {
        font-size: 12px;
        color: #555;
        margin-top: 2px;
    }
</style>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>HOD Panel - Student LOR</title>
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
                <div class="menu-inner" <?php $page = "dashboard";
                include '../includes/sidebars/hod-sidebar.php';
                ?> </div>
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
                                    <li><span>HOD's Dashboard</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 clearfix">
                            <?php include '../includes/profile/hod-profile.php';?>
                        </div>
                    </div>
                </div>
                <!-- page title area end -->
                <div class="main-content-inner">

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
                                                        role="tab">Student LOR Information</a>
                                                </li>
                                            </ul>
                                        </div>
                                        </select>
                                    </div>
                                    <!-- <h4 class="header-title"></h4> -->
                                    <div class="table-container">
                                        <!-- Results Table -->
                                        <table class="table table-bordered mt-4">
                                            <thead>
                                                <tr>
                                                    <th>Student Name</th>
                                                    <th>Student Email</th>
                                                    <th>Student Department</th>
                                                    <th>Teacher Name</th>
                                                    <th>University</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($student_teacher_data)) { ?>
                                                    <?php foreach ($student_teacher_data as $record) { ?>
                                                        <tr>
                                                            <td><a
                                                                    href="student_profile.php?studid=<?php echo htmlspecialchars($record->studid) ?>"><?php echo htmlspecialchars($record->student_first . " " . $record->student_last); ?></a>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($record->student_email); ?></td>
                                                            <td><?php echo htmlspecialchars($record->student_department); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($record->teacher_first . " " . $record->teacher_last); ?>
                                                            </td>
                                                            <td><?php if ($record->lor_status != 0) {
                                                                $universities = explode(", ", $record->lor_university); // Convert string back to an array
                                                                echo "<ul>";
                                                                foreach ($universities as $university) {
                                                                    if ($university == $record->lor_selected_university)
                                                                        echo "<li class='freeze'>" . htmlspecialchars($university) . "</li>";
                                                                    else
                                                                        echo "<li>" . htmlspecialchars($university) . "</li>";
                                                                }
                                                                echo "</ul>";
                                                            } else
                                                                echo htmlspecialchars('-'); ?>
                                                            </td>
                                                            <td>
                                                                <div class="status-container">
                                                                    <div class="status-timeline">
                                                                        <div
                                                                            class="timeline-step <?php echo ($record->lor_status == 0) ? 'pending' : ''; ?>">
                                                                            <span class="timeline-text">Pending</span>
                                                                        </div>

                                                                        <div
                                                                            class="timeline-step <?php echo ($record->lor_status == 2) ? 'progrez' : ''; ?>">
                                                                            <span class="timeline-text">In Progress</span>
                                                                        </div>
                                                                        <div
                                                                            class="timeline-step <?php echo ($record->lor_status == 1) ? 'accepted' : ''; ?>">
                                                                            <span class="timeline-text">Approved</span>
                                                                        </div>
                                                                        <div
                                                                            class="timeline-step <?php echo ($record->lor_status == -1) ? 'rejected' : ''; ?>">
                                                                            <span class="timeline-text">Rejected</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="status-description">
                                                                        Description:
                                                                        <?php echo htmlspecialchars($record->lor_description); ?>
                                                                    </div>
                                                                    <div class="status-date">
                                                                        Date: <?php echo htmlspecialchars($record->lor_date); ?>
                                                                        |
                                                                        Time:
                                                                        <?php echo htmlspecialchars($record->lor_time); ?>
                                                                    </div>
                                                                </div>
                                                            </td>

                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="5">No records found.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
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
    <script src="../assets/js/line-chart.js"></script>
    <!-- all pie chart -->
    <script src="../assets/js/pie-chart.js"></script>

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