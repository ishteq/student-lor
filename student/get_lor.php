<?php
session_start();
error_reporting(0);
if (strlen($_SESSION['studlogin']) == 0) {
    header('location:../index.php');
}
if (isset($_SESSION['last_lor_time']) && time() - $_SESSION['last_lor_time'] < 60) {
    http_response_code(429);
    echo "Too many requests. Please wait.";
    exit;
}
$_SESSION['last_lor_time'] = time();


if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo "Invalid CSRF token.";
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST["student_name"];
    $recommender_name = $_POST["recommender_name"];
    $recommender_designation = $_POST["recommender_designation"];
    $institution = $_POST["institution"];
    $academic_subjects = explode(",", $_POST["academic_subjects"]);
    $extra_curricular_activities = explode(",", $_POST["extra_curricular_activities"]);

    // Process achievements
    $achievements = [];
    if (isset($_POST["project_name"])) {
        for ($i = 0; $i < count($_POST["project_name"]); $i++) {
            $achievements[] = [
                "project_name" => $_POST["project_name"][$i],
                "description" => $_POST["description"][$i],
                "technologies_used" => explode(",", $_POST["technologies_used"][$i]),
                "outcome" => $_POST["outcome"][$i]
            ];
        }
    }

    // Create prompt
    $prompt = "Generate a Letter of Recommendation (LOR) based on the following details:\n\n";
    $prompt .= "Student Name: $student_name\n";
    $prompt .= "Recommender Name: $recommender_name\n";
    $prompt .= "Recommender Designation: $recommender_designation\n";
    $prompt .= "Institution: $institution\n";
    $prompt .= "Academic Subjects: " . implode(", ", $academic_subjects) . "\n\n";
    $prompt .= "Achievements:\n";

    foreach ($achievements as $ach) {
        $prompt .= "- Project Name: {$ach['project_name']}\n";
        $prompt .= "  Description: {$ach['description']}\n";
        $prompt .= "  Technologies Used: " . implode(", ", $ach['technologies_used']) . "\n";
        $prompt .= "  Outcome: {$ach['outcome']}\n\n";
    }

    $prompt .= "Extra-Curricular Activities:\n- " . implode("\n- ", $extra_curricular_activities) . "\n\n";
    $prompt .= "Write a professional, formal, and well-structured letter.";

    // Cohere API Key
    $config = include '../config.php';
    $api_key = $config['COHERE_API_KEY'] ?? '';

    // API URL
    $api_url = "https://api.cohere.ai/v1/generate";

    // Prepare request data
    $data = json_encode([
        "model" => "command-r-plus",
        "prompt" => $prompt,
        "max_tokens" => 80,
        "temperature" => 0.7,
        "k" => 0,
        "p" => 0.9,
        "frequency_penalty" => 0,
        "presence_penalty" => 0,
        "stop_sequences" => []
    ]);

    // Send API request
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    // Decode response
    $response_data = json_decode($response, true);
    $generated_lor = $response_data["generations"][0]["text"] ?? "Error generating LOR.";

    echo nl2br(htmlspecialchars($generated_lor)); // Return response to AJAX
}