<?php
date_default_timezone_set("Asia/Kolkata");
session_start();
error_reporting(0);
include('../includes/database/dbcon.php');

$studid = $_SESSION['studid'] ?? null;
$teaid = $_SESSION['selectTeac'] ?? null;

if (!$studid || !$teaid) {
    header('location:../index.php');
    exit();
}
$sqlstud = "SELECT id, student_id, first_name, last_name, department, email 
                    FROM students WHERE id = :studid";
$query = $dbh->prepare($sqlstud);
$query->bindParam(':studid', $studid, PDO::PARAM_STR);
$query->execute();
$resultStud = $query->fetch(PDO::FETCH_OBJ);

$sqlteac = "SELECT first_name, last_name, designation FROM teachers WHERE id = :teacid";
$query = $dbh->prepare($sqlteac);
$query->bindParam(':teacid', $teaid, PDO::PARAM_STR);
$query->execute();
$resultTeac = $query->fetch(PDO::FETCH_OBJ);

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
                            <h4 class="page-title pull-left">Generate LOR</h4>
                            <ul class="breadcrumbs pull-left">

                                <li><span><a href="listprocess.php">Application List</a></span></li>
                                <li><span><a href="process_lor.php?student_id=<?php echo $studid ?>">Application
                                            Form</a></span></li>
                                <li><span>Generate LOR</span></li>
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
                        <h2>Generate LOR and Save as PDF</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($query->rowCount() > 0): ?>
                            <form id="lorForm">
                                <div class="form-group">
                                    <label for="student_name" class="col-form-label">Name</label>
                                    <input class="form-control" name="student_name"
                                        value="<?php echo $resultStud->first_name . ' ' . $resultStud->last_name; ?>"
                                        type="text" required placeholder="Name" id="student_name">
                                </div>
                                <div class="form-group">
                                    <label for="recommender_name" class="col-form-label">Recommender Name:</label>
                                    <input class="form-control" name="recommender_name"
                                        value="<?php echo $resultTeac->first_name . ' ' . $resultTeac->last_name; ?>"
                                        type="text" required placeholder="Recommender Name" id="recommender_name">
                                </div>
                                <div class="form-group">
                                    <label for="recommender_designation" class="col-form-label">Recommender
                                        Designation:</label>
                                    <input class="form-control" name="recommender_designation"
                                        value="<?php echo $resultTeac->designation; ?>" type="text" required
                                        placeholder="Recommender Designation" id="recommender_designation">
                                </div>
                                <div class="form-group">
                                    <label for="institution" class="col-form-label">Institution:</label>
                                    <input class="form-control" name="institution" value="Don Bosco Institute of Technology"
                                        type="text" required placeholder="Institution" id="institution">
                                </div>
                                <div class="form-group">
                                    <label for="academic_subjects" class="col-form-label">Academic Subjects
                                        (comma-separated):</label>
                                    <input class="form-control" name="academic_subjects" value="" type="text" required
                                        placeholder="Eg: Algorithms, Cloud Computing, Cybersecurity..."
                                        id="academic_subjects">
                                </div>
                                <h3>Achievements:</h3>
                                <div id="achievements">
                                    <div class="achievement">
                                        <div class="form-group">
                                            <label for="project_name[]" class="col-form-label">Project Name:</label>
                                            <input class="form-control" name="project_name[]" value="" type="text" required
                                                placeholder="Eg: Secure Cloud Storage System" id="project_name[]">
                                        </div>
                                        <div class="form-group">
                                            <label for="description[]" class="col-form-label">Description:</label>
                                            <input class="form-control" name="description[]" value="" type="text" required
                                                placeholder="Eg: Developed a secure cloud storage system with end-to-end encrypt..."
                                                id="description[]">
                                        </div>
                                        <div class="form-group">
                                            <label for="technologies_used[]" class="col-form-label">Technologies Used
                                                (comma-separated):</label>
                                            <input class="form-control" name="technologies_used[]" value="" type="text"
                                                required placeholder="Eg: AWS, Node.js, React.js, Encryption Algori..."
                                                id="technologies_used[]">
                                        </div>
                                        <div class="form-group">
                                            <label for="outcome[]" class="col-form-label">Outcome:</label>
                                            <input class="form-control" name="outcome[]" value="" type="text" required
                                                placeholder="Eg: Selected for presentation at the International Cloud Computing Conference 2023..."
                                                id="outcome[]">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" onclick="addAchievement()" class="btn btn-primary">Add
                                    Achievement</button><br><br>
                                <div class="form-group">
                                    <label for="extra_curricular_activities" class="col-form-label">Extra-Curricular
                                        Activities (comma-separated):</label>
                                    <input class="form-control" name="extra_curricular_activities" value="" type="text"
                                        required
                                        placeholder="Eg: Core member of the cybersecurity club, Organized workshops on ethical hacking..."
                                        id="extra_curricular_activities">
                                </div>
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <button type="submit" class="btn btn-success">Generate LOR</button>
                            </form>
                            <br>
                            <div id="generatedLOR"></div>
                            <br>
                            <h3>Generated LOR:</h3>
                            <textarea class="form-control" name="fullLOR" rows="10" required id="lorTextarea"
                                placeholder="LOR will be generated here and edit it (if required) before saving as PDF..."
                                style="white-space: pre-wrap;"></textarea>
                            <br>
                            <button id="downloadPDF" class="btn btn-primary">Download as PDF</button>
                            <a href="process_lor.php?student_id=<?php echo $resultStud->id; ?>"><button
                                    class="btn btn-success">Go Back</button></a>
                        <?php else: ?>
                            <div class="alert alert-danger">Error generating LOR!!!.</div>
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
    <div id="loading-overlay" style="
    display: none;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    text-align: center;
    color: white;
    font-size: 24px;
    padding-top: 20%;
">
        <div class="spinner-border text-light" role="status">
            <span class="sr-only">Generating LOR...</span>
        </div>
        <p style="color: white">Generating LOR, please wait...</p>
    </div>

</body>
<script>
    function addAchievement() {
        $("#achievements").append(`
                <div class="achievement">
                <hr class="my-4" style="width: 100%; height: 2px;background-color: black;">
                                                <div class="form-group">
                                                    <label for="project_name[]" class="col-form-label">Project Name:</label>
                                                    <input class="form-control" name="project_name[]" value="" type="text"
                                                        required placeholder="Eg: AI-Based Intrusion Detection System..." id="project_name[]">
                                                </div>
                                                <div class="form-group">
                                                    <label for="description[]" class="col-form-label">Description:</label>
                                                    <input class="form-control" name="description[]" value="" type="text"
                                                        required placeholder="Eg: Created an AI model to detect and prevent cyber attacks in real-time..." id="description[]">
                                                </div>
                                                <div class="form-group">
                                                    <label for="technologies_used[]" class="col-form-label">Technologies Used
                                                        (comma-separated):</label>
                                                    <input class="form-control" name="technologies_used[]" value="" type="text"
                                                        required placeholder="Eg: Python, TensorFlow, Keras..."
                                                        id="technologies_used[]">
                                                </div>
                                                <div class="form-group">
                                                    <label for="outcome[]" class="col-form-label">Outcome:</label>
                                                    <input class="form-control" name="outcome[]" value="" type="text" required
                                                        placeholder="Eg: Published in the Journal of Cybersecurity 2023..." id="outcome[]">
                                                </div>
                                                <button type="button" class="btn btn-danger"
                                                    onclick="removeAchievement(this)">Remove</button><br><br>
                                            </div>
            `);
    }

    function removeAchievement(element) {
        $(element).closest(".achievement").remove();
    }

    $("#lorForm").submit(function (event) {
        event.preventDefault(); // Prevent form from reloading the page
        $("#loading-overlay").fadeIn();
        $.ajax({
            type: "POST",
            url: "get_lor.php", // PHP file handling the request
            data: $(this).serialize(),
            success: function (response) {
                $("#loading-overlay").fadeOut();
                let formattedResponse = $("<div>").html(response).text() // Convert HTML entities
                    .replace(/\r\n|\r/g, "\n") // Normalize line breaks
                    .replace(/\n{3,}/g, "\n\n") // Ensure no more than two consecutive new lines
                    .trim(); // Trim leading/trailing spaces
                $("textarea[name='fullLOR']").val(formattedResponse);
            },
            error: function (xhr, status, error) {
                $("#loading-overlay").fadeOut();
                let errorMessage = xhr.responseText || "Error generating LOR";
                $("#generatedLOR").html(`<p style='color:red;'>${errorMessage}</p>`);
            }
        });
    });

    document.getElementById("downloadPDF").addEventListener("click", function () {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF();

        let lorText = document.getElementById("lorTextarea").value;

        // Add college letterhead (Replace 'college_logo.png' with your image)
        let img = new Image();
        img.src = 'test.png'; // Your college letterhead image
        img.onload = function () {
            doc.addImage(img, 'PNG', 10, 10, 190, 40); // Adjust dimensions
            generatePDF(doc, lorText);
        };
    });

    function generatePDF(doc, lorText) {
        doc.setFont("times"); // Set font to Times New Roman
        doc.setFontSize(12);
        doc.setFont("times", "bold");

        doc.text("Letter of Recommendation", 105, 60, { align: "center" });
        doc.setFont("times", "normal");
        doc.setFontSize(11);

        // Add the LOR content with proper line wrapping
        let marginLeft = 20;
        let marginTop = 70;
        let maxWidth = 170;

        doc.text(lorText, marginLeft, marginTop, { maxWidth });

        // Save the PDF
        doc.save("Letter_of_Recommendation.pdf");
    }
</script>


</html>