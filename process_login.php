<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            switch ($user['role']) {
                case 'superadmin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'employer':
                    header("Location: employer_dashboard.php");
                    break;
                case 'pwd':
                    header("Location: pwd_dashboard.php");
                    break;
                case 'job_seeker':
                    header("Location: seeker_dashboard.php");
                    break;
                default:
                    header("Location: login.php?error=invalid_role");
                    break;
            }
            exit();
        } else {
            header("Location: login.php?error=wrong_credentials");
            exit();
        }
    }
}
?>