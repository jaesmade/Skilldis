<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = trim($_POST['role']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($role) || empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: register.php?error=empty_fields");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: register.php?error=password_mismatch");
        exit();
    }

    $allowed_roles = ['job_seeker', 'pwd', 'employer'];
    if (!in_array($role, $allowed_roles)) {
        header("Location: register.php?error=invalid_role");
        exit();
    }

    try {
        $checkEmailStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $checkEmailStmt->execute(['email' => $email]);
        if ($checkEmailStmt->fetch()) {
            header("Location: register.php?error=email_taken");
            exit();
        }

        $pdo->beginTransaction();

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $insertUserSql = "INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, 'active')";
        $userStmt = $pdo->prepare($insertUserSql);
        $userStmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => $hashedPassword,
            'role'     => $role
        ]);
		
        $userId = $pdo->lastInsertId();

        if ($role === 'job_seeker') {
            $seekerSql = "INSERT INTO seeker_profiles (user_id, is_pwd) VALUES (:user_id, 0)";
            $seekerStmt = $pdo->prepare($seekerSql);
            $seekerStmt->execute(['user_id' => $userId]);

        } elseif ($role === 'pwd') {
            $disability_type = trim($_POST['disability_type']);
            $pwd_id_number   = trim($_POST['pwd_id_number']);

            $pwdSql = "INSERT INTO seeker_profiles (user_id, is_pwd, disability_type, pwd_id_number) 
                       VALUES (:user_id, 1, :disability_type, :pwd_id_number)";
            $pwdStmt = $pdo->prepare($pwdSql);
            $pwdStmt->execute([
                'user_id'         => $userId,
                'disability_type' => $disability_type,
                'pwd_id_number'   => $pwd_id_number
            ]);

        } elseif ($role === 'employer') {
            $company_name    = trim($_POST['company_name']);
            $company_address = trim($_POST['company_address']);

            $employerSql = "INSERT INTO employer_profiles (user_id, company_name, company_address) 
                            VALUES (:user_id, :company_name, :company_address)";
            $employerStmt = $pdo->prepare($employerSql);
            $employerStmt->execute([
                'user_id'         => $userId,
                'company_name'    => $company_name,
                'company_address' => $company_address
            ]);
        }

        $pdo->commit();
        header("Location: login.php?registration=success");
        exit();

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        header("Location: register.php?error=system_failure");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>