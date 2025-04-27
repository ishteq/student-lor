<?php
date_default_timezone_set("Asia/Kolkata");
session_start();
error_reporting(0);
include('../includes/database/dbcon.php');

$studid = $_SESSION['studid'] ?? null;

if (!$studid) {
    header('location:../index.php');
    exit();
}
if (isset($_POST['upload'])) {
    // Fetch student ID and teacher ID from POST request
    $student_id = $_POST['student_id'];
    $teacher_id = $_POST['teacher_id'];
    $selected_uni = $_POST['university'];

    // File description (e.g., "Admit Card")
    $file_name = $_POST['file_names']; // Updated to match input name

    // Uploaded file
    $file = $_FILES['files']; // Match input name

    // Directory where the file will be uploaded
    $upload_dir = "uploads/";

    try {
        // Check if the directory exists; if not, create it
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Get the file's name and target location
        $originalFileName = basename($file['name']);
        $ext = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);

        // Sanitize and make unique
        $safeBaseName = preg_replace('/[^a-zA-Z0-9-_]/', '_', strtolower($baseName));
        $newFileName = $safeBaseName . '_' . time() . '.' . $ext;

        $target_file = $upload_dir . $newFileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update pro_lor status after successful file upload
            $sql2 = "UPDATE pro_lor SET freeze='2', selected_university=:selected_uni, admit_path=:file_path, admit_time=CURTIME(), admit_date=CURDATE() WHERE stud_id=:stud_id AND teac_id=:teac_id";
            $query2 = $dbh->prepare($sql2);
            $query2->bindParam(':file_path', $target_file, PDO::PARAM_STR);
            $query2->bindParam(':stud_id', $student_id, PDO::PARAM_INT);
            $query2->bindParam(':teac_id', $teacher_id, PDO::PARAM_INT);
            $query2->bindParam(':selected_uni', $selected_uni, PDO::PARAM_STR);
            $query2->execute();

            // Optionally, redirect after successful submission
            header('Location: listprocess.php');
            exit(); // Make sure to exit after redirecting
        } else {
            echo "Failed to upload file: " . htmlspecialchars($file_basename);
            // You can also check for specific error codes
            echo " Error Code: " . $file['error'];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();  // Display any PDO exceptions for debugging.
    }
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
                            <h4 class="page-title pull-left">Process LOR</h4>
                            <ul class="breadcrumbs pull-left">

                                <li><span>Application List</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">

                        <?php include '../includes/profile/student-profile.php' ?>

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
                                                    s.first_name AS studfirst, 
                                                    s.last_name AS studlast, 
                                                    t.id AS teacherid,
                                                    t.first_name AS teacfirst, 
                                                    t.last_name AS teaclast, 
                                                    pl.status AS statuz, 
                                                    pl.freeze AS freez, 
                                                    pl.university AS universities,
                                                    pl.selected_university AS selected_university,
                                                    pl.description AS descrip, 
                                                    pl.date AS dat
                                                    FROM pro_lor pl 
                                                    JOIN students s ON pl.stud_id = s.id 
                                                    JOIN teachers t ON pl.teac_id = t.id 
                                                    WHERE pl.stud_id = :studid";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':studid', $studid, PDO::PARAM_STR);
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
                                                <td>Remark</td>
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
                                                        <?php } elseif ($stats == 0 || $stats == 2) { ?>
                                                            <span style="color: blue">Pending <i class="fa fa-spinner"></i></span>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo htmlentities($result->descrip); ?></td>
                                                    <td>
                                                        <?php
                                                        $stats = $result->statuz;
                                                        if ($stats == 1) { ?>
                                                            <span class="btn btn-success">Successful</span>
                                                            <?php
                                                            $freez = $result->freez;
                                                            if ($freez == 0) { ?>
                                                                <form name="update_document" method="POST" action="listprocess.php"
                                                                    enctype="multipart/form-data">
                                                                    <div class="file-item mb-2 mt-2">
                                                                        <p>*<b>Submit Admit Card</b>*</p>
                                                                        <div class="input-group">
                                                                            <input type="hidden" name="student_id"
                                                                                value="<?php echo $studid; ?>">
                                                                            <input type="hidden" name="teacher_id"
                                                                                value="<?php echo $result->teacherid ?>">
                                                                            <input type="text" name="file_names"
                                                                                class="form-control mb-3" value="<?php echo htmlentities($result->studfirst . "_" . $result->studlast);
                                                                                ?>_Admit_Card" required readonly>
                                                                            <input type="file" name="files"
                                                                                class="form-control-file mb-2" accept=".pdf" required
                                                                                style="margin-left: 0px">
                                                                            <label for="university">Select University:</label>
                                                                            <select class="form-control" id="university"
                                                                                name="university" style="width: 100%;">
                                                                                <option value="-1" selected>Select Admitted University
                                                                                </option>
                                                                                <?php
                                                                                $universities = explode(", ", $result->universities);
                                                                                foreach ($universities as $university) { ?>
                                                                                    <option value="<?php echo $university ?>">
                                                                                        <?php echo $university ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                            <button type="submit" id="subbutton" class="btn btn-success"
                                                                                name="upload" style="margin: 5px 0px">Submit</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            <?php } else { ?>
                                                                <br><br>
                                                                <span class="btn btn-success">Freeze</span>
                                                            <?php } ?>
                                                        <?php } elseif ($stats == 2) { ?>
                                                            <span class="btn btn-danger">Submitted</span>
                                                        <?php } else {
                                                            ?>
                                                            <button type="button" class="btn btn-primary set-session-btn"
                                                                data-student="<?php echo $studid; ?>"
                                                                data-teacher="<?php echo $result->teacherid; ?>">Upload</button>
                                                        <?php } ?>

                                                    </td>
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
<script>
    $(document).ready(function () {
        $(".set-session-btn").click(function () {
            var studentId = $(this).data("student");
            var teacherId = $(this).data("teacher");

            $.ajax({
                url: "set_session.php",
                type: "POST",
                data: { student_id: studentId, teacher_id: teacherId },
                success: function (response) {
                    if (response == "success") {
                        window.location.href = "process_lor.php?student_id=" + studentId;
                    } else {
                        alert("Failed to set session. Please try again.");
                    }
                }
            });
        });

    });
    $(document).ready(function () {
        var submitButton = $('#subbutton')
        submitButton.prop('disabled', true);
        submitButton.css('cursor', 'not-allowed');

        $('#university').on('change', function () {
            if ($(this).val() !== '-1') {
                submitButton.prop('disabled', false);
                submitButton.css('cursor', 'pointer');
            } else {
                submitButton.prop('disabled', true);
                submitButton.css('cursor', 'not-allowed');
            }
        });
        $('form#update_document').submit(function (event) {
            if ($('#university').val() == '-1') {
                event.preventDefault();
                $('subbutton').
                    alert('Please select a university');
            }
        });
    });
</script>

</html>