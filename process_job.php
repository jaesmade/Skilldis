<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employer_id        = $_SESSION['user_id'];
    $job_title          = trim($_POST['job_title']);
    $job_description    = trim($_POST['job_description']);
    $required_skills    = trim($_POST['required_skills']);
    $accessibility_tags = trim($_POST['accessibility_tags']);

    if (empty($job_title) || empty($job_description) || empty($required_skills)) {
        header("Location: employer_dashboard.php?error=empty_fields");
        exit();
    }

    try {
        $sql = "INSERT INTO job_listings (employer_id, job_title, job_description, required_skills, accessibility_tags, job_status) 
                VALUES (:employer_id, :job_title, :job_description, :required_skills, :accessibility_tags, 'open')";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            'employer_id'        => $employer_id,
            'job_title'          => $job_title,
            'job_description'    => $job_description,
            'required_skills'    => $required_skills,
            'accessibility_tags' => !empty($accessibility_tags) ? $accessibility_tags : null
        ]);

        header("Location: employer_dashboard.php?post=success");
        exit();

    } catch (PDOException $e) {
        header("Location: employer_dashboard.php?error=system_failure");
        exit();
    }
} else {
    header("Location: employer_dashboard.php");
    exit();
}
?>