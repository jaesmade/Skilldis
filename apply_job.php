<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pwd') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $job_id = (int)$_GET['id'];
    $seeker_id = $_SESSION['user_id'];

    try {
        $checkStmt = $pdo->prepare("SELECT id FROM job_applications WHERE job_id = :job_id AND seeker_id = :seeker_id");
        $checkStmt->execute(['job_id' => $job_id, 'seeker_id' => $seeker_id]);
        
        if ($checkStmt->fetch()) {
            header("Location: my_applications.php?error=already_applied");
            exit();
        }

        $insertStmt = $pdo->prepare("
            INSERT INTO job_applications (job_id, seeker_id, status, applied_at) 
            VALUES (:job_id, :seeker_id, 'applied', NOW())
        ");
        $insertStmt->execute([
            'job_id' => $job_id,
            'seeker_id' => $seeker_id
        ]);

        header("Location: my_applications.php?msg=applied_success");
        exit();

    } catch (PDOException $e) {
        header("Location: pwd_dashboard.php?error=system_failure");
        exit();
    }
} else {
    header("Location: pwd_dashboard.php");
    exit();
}
?>