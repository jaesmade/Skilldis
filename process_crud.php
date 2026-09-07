<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: login.php");
    exit();
}

$employer_id = $_SESSION['user_id'];

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    try {
        if ($action === 'toggle_job' && isset($_GET['id'])) {
            $job_id = (int)$_GET['id'];
            
            $stmt = $pdo->prepare("SELECT job_status FROM job_listings WHERE id = :id AND employer_id = :employer_id");
            $stmt->execute(['id' => $job_id, 'employer_id' => $employer_id]);
            $job = $stmt->fetch();
            
            if ($job) {
                $new_status = ($job['job_status'] === 'open') ? 'closed' : 'open';
                $updateStmt = $pdo->prepare("UPDATE job_listings SET job_status = :status WHERE id = :id");
                $updateStmt->execute(['status' => $new_status, 'id' => $job_id]);
                
                header("Location: jobs.php?msg=job_updated");
                exit();
            }
        }

        if ($action === 'delete_job' && isset($_GET['id'])) {
            $job_id = (int)$_GET['id'];
            
            $deleteStmt = $pdo->prepare("DELETE FROM job_listings WHERE id = :id AND employer_id = :employer_id");
            $deleteStmt->execute(['id' => $job_id, 'employer_id' => $employer_id]);
            
            header("Location: jobs.php?msg=job_deleted");
            exit();
        }

        if ($action === 'update_candidate' && isset($_POST['application_id']) && isset($_POST['status'])) {
            $app_id = (int)$_POST['application_id'];
            $status = $_POST['status'];
            
            $allowed_statuses = ['applied', 'reviewing', 'shortlisted', 'hired', 'rejected'];
            if (in_array($status, $allowed_statuses)) {
                $updateAppStmt = $pdo->prepare("UPDATE job_applications SET status = :status WHERE id = :id");
                $updateAppStmt->execute(['status' => $status, 'id' => $app_id]);
                
                header("Location: candidates.php?msg=candidate_updated");
                exit();
            }
        }

    } catch (PDOException $e) {
        header("Location: employer_dashboard.php?error=crud_failure");
        exit();
    }
}

header("Location: employer_dashboard.php");
exit();
?>