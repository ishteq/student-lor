<?php
date_default_timezone_set("Asia/Kolkata");
session_start();
error_reporting(0);
include('../includes/database/dbcon.php');

$studid = $_GET['student_id'] ?? null;
$teaid = $_SESSION['teaclogin'];

if (!$teaid || $_GET['teacher_id'] != $teaid) {
    header('location:../index.php');
    exit();
}
// Handle file deletion
if (isset($_POST['updatepro'])) {
    $student_id = $_POST['student_id'];
    $teacher_id = $_POST['teacher_id'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    try {
        $sql5 = "UPDATE pro_lor SET status=:status, description=:description WHERE stud_id=:stud_id AND teac_id=:teac_id";
        $query5 = $dbh->prepare($sql5);
        $query5->bindParam(':status', $status, PDO::PARAM_STR);  // Changed to PDO::PARAM_INT for the integer status
        $query5->bindParam(':description', $description, PDO::PARAM_STR);
        $query5->bindParam(':stud_id', $student_id, PDO::PARAM_STR);
        $query5->bindParam(':teac_id', $teacher_id, PDO::PARAM_STR);

        $query5->execute();
        header("Location: listprocess.php"); // Redirect to success.php or any other page
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
// Fetch student profile details
$sql = "SELECT student_id, first_name, last_name, department, email 
                    FROM students WHERE id = :studid";
$query = $dbh->prepare($sql);
$query->bindParam(':studid', $studid, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
$student_firstname = $result->first_name;
$student_lastname = $result->last_name;
$gmail = $result->email;
$department = $result->department;

$sql10 = "SELECT * FROM student_docs WHERE stud_id = :student_id AND teac_id = :teacher_id";
$query10 = $dbh->prepare($sql10);
$query10->bindParam(':student_id', $studid, PDO::PARAM_INT);
$query10->bindParam(':teacher_id', $teaid, PDO::PARAM_INT);
$query10->execute();
$documents = $query10->fetchAll(PDO::FETCH_OBJ);

$sql11 = "SELECT status, university, skill FROM pro_lor WHERE stud_id = :student_id AND teac_id = :teacher_id";
$query11 = $dbh->prepare($sql11);
$query11->bindParam(':student_id', $studid, PDO::PARAM_INT);
$query11->bindParam(':teacher_id', $teaid, PDO::PARAM_INT);
$query11->execute();
$sta = $query11->fetch(PDO::FETCH_OBJ);
$status = $sta->status;
$university = $sta->university;
$skill = $sta->skill;
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

                                <li><span><a href="listprocess.php">Application List</a></span></li>
                                <li><span>Application Form</span></li>
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
                        <h2>LOR Application Form</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($query->rowCount() > 0): ?>
                            <form name="update_document" method="POST"
                                action="process_lor.php?student_id=<?php echo $studid; ?>&teacher_id=<?php echo $_GET['teacher_id']; ?>"
                                enctype="multipart/form-data">
                                <!-- Hidden input for student_id -->
                                <input type="hidden" name="student_id" value="<?php echo $studid; ?>">
                                <input type="hidden" name="teacher_id" value="<?php echo $_GET['teacher_id']; ?>">

                                <!-- Form Fields -->
                                <div class="form-group">
                                    <label for="firstName" class="col-form-label">First Name</label>
                                    <input class="form-control" name="firstName"
                                        value="<?php echo htmlentities($student_firstname); ?>" type="text" required
                                        id="firstName" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="lastName" class="col-form-label">Last Name</label>
                                    <input class="form-control" name="lastName"
                                        value="<?php echo htmlentities($student_lastname); ?>" type="text" required
                                        id="lastName" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="university" class="col-form-label">University</label>
                                    <input class="form-control" name="university"
                                        value="<?php echo htmlentities($university); ?>" type="text" required
                                        id="university" readonly>
                                </div>

                                <h4 class="mt-4">Submitted Documents for LOR</h4>
                                <div id="file-group" class="mb-3">
                                    <?php if ($documents): ?>
                                        <?php foreach ($documents as $doc):
                                            $modalId = "proaction-" . $teaid; ?>
                                            <div class="file-item mb-2">
                                                <div class="input-group">
                                                    <input type="text" name="existing_file_names[]" class="form-control mb-3"
                                                        value="<?php echo htmlentities($doc->doc_name); ?>" readonly>
                                                    <input type="text" class="form-control mb-3"
                                                        value="<?php echo htmlentities($doc->date . ' / ' . $doc->time); ?>"
                                                        readonly>
                                                    <a href="<?php echo "view.php?doc_id=$doc->id"; ?>" target="_blank"
                                                        class="btn btn-info btn-sm" style="height: 44px;">View</a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($status != 1) { ?>
                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                        data-target="#<?php echo $modalId; ?>">
                                        ACTION</button>
                                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                        SET ACTION</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <form method="POST" name="updatepro">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="student_id" value="<?php echo $studid; ?>">
                                                        <input type="hidden" name="teacher_id" value="<?php echo $teaid; ?>">
                                                        <select class="custom-select" name="status" required>
                                                            <option value="">Choose...</option>
                                                            <option value="1">Accept</option>
                                                            <option value="-1">Reject</option>
                                                        </select></p>
                                                        <br>
                                                        <p><textarea id="textarea1" name="description" class="form-control"
                                                                placeholder="Description" row="5" maxlength="500"
                                                                required></textarea>
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-success"
                                                            name="updatepro">Commit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger">No student details found.</div>
                        <?php endif; ?>
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