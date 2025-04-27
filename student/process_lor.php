<?php
date_default_timezone_set("Asia/Kolkata");
session_start();
error_reporting(0);
include('../includes/database/dbcon.php');

$studid = $_SESSION['studid'] ?? null;
$teaid = $_SESSION['selectTeac'] ?? null;

if (!$studid || $_GET['student_id'] != $studid || !$teaid) {
    header('location:../index.php');
    exit();
}
// Handle file deletion
if (isset($_POST['delete'])) {
    $doc_id = $_POST['delete'];

    // Fetch the file path of the document to delete
    $sql = "SELECT doc_path FROM student_docs WHERE id = :doc_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':doc_id', $doc_id, PDO::PARAM_INT);
    $query->execute();
    $document = $query->fetch(PDO::FETCH_OBJ);

    if ($document) {
        // Delete the file from the server
        if (file_exists($document->doc_path)) {
            unlink($document->doc_path);
        }

        // Delete the record from the database
        $sql = "DELETE FROM student_docs WHERE id = :doc_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':doc_id', $doc_id, PDO::PARAM_INT);
        $query->execute();

        echo "Document deleted successfully.";
    }
}
if (isset($_POST['submit'])) {

    // Mark form as submitted to prevent duplicate submission
    $_SESSION['form_submitted'] = true;
    $student_id = $_POST['student_id']; // Fetch student ID from POST request
    $file_names = $_POST['file_names']; // File descriptions (e.g., GRE Score)
    $files = $_FILES['files'];          // Uploaded files
    $teacher_id = $_POST['teacher_id'];                    // Assuming teacher ID is constant for now
    $universities = implode(", ", $_POST['universities']);
    $skill = $_POST['skill'];
    // Directory where files will be uploaded
    $upload_dir = "uploads/";
    try {
        // Process each uploaded file
        for ($i = 0; $i < count($files['name']); $i++) {
            $originalFileName = basename($files['name'][$i]);
            $tempFilePath = $files['tmp_name'][$i];

            $ext = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);

            // Make it unique and safe
            $safeBaseName = preg_replace('/[^a-zA-Z0-9-_]/', '_', strtolower($baseName)); // replace spaces/special chars with "_"
            $newFileName = $safeBaseName . '_' . time() . '_' . $i . '.' . $ext; // timestamp + index to avoid conflict

            $target_file = $upload_dir . $newFileName;
            // Move the file to the server
            if (move_uploaded_file($tempFilePath, $target_file)) {
                // Insert file information into student_docs table
                $sql = "INSERT INTO student_docs (stud_id, teac_id, doc_name, doc_path,date , time) 
                            VALUES (:student_id, :teacher_id, :file_name, :file_path, CURDATE(), CURTIME())";
                $query = $dbh->prepare($sql);
                $query->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $query->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
                $query->bindParam(':file_name', $file_names[$i], PDO::PARAM_STR);
                $query->bindParam(':file_path', $target_file, PDO::PARAM_STR);
                $query->execute();
            } else {
                echo "Failed to upload file: " . $file_name;
            }
        }
        $sql5 = "UPDATE pro_lor SET status='2', university=:university, skill=:skill WHERE stud_id=:stud_id AND teac_id=:teac_id";
        $query5 = $dbh->prepare($sql5);
        $query5->bindParam(':stud_id', $student_id, PDO::PARAM_STR);
        $query5->bindParam(':teac_id', $teacher_id, PDO::PARAM_STR);
        $query5->bindParam(':university', $universities, PDO::PARAM_STR);
        $query5->bindParam(':skill', $skill, PDO::PARAM_STR);

        $query5->execute();
        //Optionally, redirect after successful submission.
        header('location:listprocess.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();  // Display any PDO exceptions for debugging.
    }
}
// Fetch student profile details
$sql = "SELECT student_id, first_name, last_name, department, email 
                    FROM students WHERE id = :studid";
$query = $dbh->prepare($sql);
$query->bindParam(':studid', $studid, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
$gmail = $result->email;
$department = $result->department;
$sql10 = "SELECT * FROM student_docs WHERE stud_id = :student_id AND teac_id = :teacher_id";
$query10 = $dbh->prepare($sql10);
$query10->bindParam(':student_id', $studid, PDO::PARAM_INT);
$query10->bindParam(':teacher_id', $teaid, PDO::PARAM_INT);
$query10->execute();
$documents = $query10->fetchAll(PDO::FETCH_OBJ);
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

                                <li><span><a href="listprocess.php">Application List</a></span></li>
                                <li><span>Application Form</span></li>
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
                        <h2>LOR Application Form</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($query->rowCount() > 0): ?>
                            <form name="update_document" method="POST"
                                action="process_lor.php?student_id=<?php echo $studid; ?>" enctype="multipart/form-data">
                                <!-- Hidden input for student_id -->
                                <input type="hidden" name="student_id" value="<?php echo $studid; ?>">
                                <input type="hidden" name="teacher_id" value="<?php echo $teaid; ?>">

                                <!-- Form Fields -->
                                <div class="form-group">
                                    <label for="fullName" class="col-form-label">Full Name</label>
                                    <input class="form-control" name="fullName"
                                        value="<?php echo htmlentities($result->first_name . ' ' . $result->last_name); ?>"
                                        type="text" required readonly id="fullName">
                                </div>
                                <div class="form-group">
                                    <p class="col-form-label">Universities</p>
                                    <div id="university-group">
                                        <div class="university-input">
                                            <input type="text" name="universities[]" class="form-control university-field"
                                                placeholder="Enter University Name" required>
                                        </div>
                                    </div>
                                    <button type="button" id="add-university" class="btn btn-primary mt-2">Add More</button>
                                </div>
                                <div class="form-group">
                                    <label for="objective" class="col-form-label">Generate LOR from here and Upload it
                                        below: &nbsp;</label>
                                    <a href="generate_lor.php"><button type="button" name="genlor"
                                            class="btn btn-success">Generate LOR Page</button></a>
                                </div>
                                <!-- File Upload Section -->
                                <h4 class="mt-4">Submit LOR and Documents</h4>
                                <div id="file-group" class="mb-3">
                                    <!-- Display previously uploaded files -->
                                    <?php if ($documents): ?>
                                        <?php foreach ($documents as $doc): ?>
                                            <div class="file-item mb-2">
                                                <div class="input-group">
                                                    <input type="text" name="existing_file_names[]" class="form-control mb-3"
                                                        value="<?php echo htmlentities($doc->doc_name); ?>" readonly>
                                                    <a href="<?php echo "view.php?doc_id=" . $doc->id; ?>" target="_blank"
                                                        class="btn btn-info btn-sm" style="height: 44px;">View</a>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="removeDocument(<?php echo $doc->id; ?>)"
                                                        style="height: 44px;">Remove</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <!-- New file upload input -->
                                    <div class="file-item mb-2 mt-2">
                                        <div class="input-group">
                                            <input type="text" name="file_names[]" class="form-control mb-3"
                                                placeholder="Document Name (e.g., LOR_{YourName})" <?php echo empty($documents) ? 'required' : ''; ?>>
                                            <input type="file" name="files[]" class="form-control-file mb-2" accept=".pdf"
                                                <?php echo empty($documents) ? 'required' : ''; ?>">
                                            <?php if ($documents): ?>
                                                <button type="button" class="btn btn-danger btn-sm remove-file">Remove</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Another File Button -->
                                <button type="button" class="btn btn-primary" id="add-file-btn">Add More Documents</button>
                                <button type="submit" name="submit" class="btn btn-success">Submit Documents</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger">No student details found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Add a hidden form to handle the delete action -->
            <form id="deleteForm" method="POST" action="process_lor.php?student_id=<?php echo $studid; ?>"
                style="display:none;">
                <input type="hidden" name="delete" id="deleteDocId">
            </form>
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
        // Add new file input group
        $('#add-file-btn').on('click', function () {
            const newFileItem = `
                <div class="file-item mb-2 mt-2">
                    <div class="input-group">
                        <input type="text" name="file_names[]" class="form-control mb-3" placeholder="Document Name (e.g., GRE Score)" required>
                        <input type="file" name="files[]" class="form-control-file mb-2" required>
                        <button type="button" class="btn btn-danger btn-sm remove-file">Remove</button>
                    </div>
                </div>
            `;
            $('#file-group').append(newFileItem);
        });

        // Remove dynamically added file input group
        $('#file-group').on('click', '.remove-file', function () {
            $(this).closest('.file-item').remove();
        });

        // Remove previously uploaded file (existing documents)
        $('#file-group').on('click', '.remove-existing-file', function () {
            const docId = $(this).data('doc-id');
            if (confirm('Are you sure you want to remove this document?')) {
                $('#deleteDocId').val(docId);
                $('#deleteForm').submit();
            }
        });

        // File validation before form submit
        $('form').on('submit', function (e) {
            let isValid = true;
            const allowedExtensions = ['pdf'];
            const maxSizeMB = 2;

            $('input[type="file"]').each(function () {
                const file = this.files[0];
                if (file) {
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    const fileSizeMB = file.size / (1024 * 1024);

                    if (!allowedExtensions.includes(fileExt)) {
                        alert(`Invalid file type: ${file.name}. Allowed types are ${allowedExtensions.join(', ')}`);
                        isValid = false;
                        return false;
                    }

                    if (fileSizeMB > maxSizeMB) {
                        alert(`File too large: ${file.name}. Max size is ${maxSizeMB} MB.`);
                        isValid = false;
                        return false;
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
            } else {
                localStorage.removeItem('savedUniversities'); // Clear on successful submission
            }
        });
    });

    // University input section
    $(document).ready(function () {
        let maxFields = 5;
        let wrapper = $("#university-group");
        let addButton = $("#add-university");

        $(addButton).click(function () {
            let fieldCount = $(".university-field").length;
            if (fieldCount < maxFields) {
                $(wrapper).append(`
                    <div class="university-input mt-2">
                        <div class="input-group">
                            <input type="text" name="universities[]" class="form-control university-field" placeholder="Enter University Name" required>
                            <button type="button" class="btn btn-danger remove-university">Remove</button>
                        </div>
                    </div>
                `);
            } else {
                alert("You can add a maximum of 5 universities.");
            }
        });

        $(wrapper).on("click", ".remove-university", function () {
            let fieldCount = $(".university-field").length;
            if (fieldCount > 1) {
                $(this).closest('.university-input').remove();
            } else {
                alert("You must enter at least one university.");
            }
        });
    });

    // Save universities to localStorage
    document.querySelector('button[name="genlor"]').addEventListener('click', function () {
        let universityFields = document.querySelectorAll('.university-field');
        let universities = [];

        universityFields.forEach(field => {
            universities.push(field.value);
        });

        localStorage.setItem('savedUniversities', JSON.stringify(universities));
    });

    // Restore from localStorage
    document.addEventListener('DOMContentLoaded', function () {
        const saved = localStorage.getItem('savedUniversities');
        const wrapper = document.getElementById('university-group');

        if (saved) {
            const universities = JSON.parse(saved);
            wrapper.innerHTML = '';

            universities.forEach((name, index) => {
                const universityInput = document.createElement('div');
                universityInput.className = 'university-input mt-2';
                universityInput.innerHTML = `
                    <div class="input-group">
                        <input type="text" name="universities[]" value="${name}" class="form-control university-field" placeholder="Enter University Name" required>
                        ${index !== 0 ? '<button type="button" class="btn btn-danger remove-university">Remove</button>' : ''}
                    </div>
                `;
                wrapper.appendChild(universityInput);
            });
        }
    });

    // Handle manual remove
    function removeDocument(docId) {
        if (confirm('Are you sure you want to remove this document?')) {
            $('#deleteDocId').val(docId);
            $('#deleteForm').submit();
        }
    }
</script>

</html>