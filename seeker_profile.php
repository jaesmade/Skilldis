<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pwd') {
    header("Location: login.php");
    exit();
}

$seeker_id = $_SESSION['user_id'];
$status_flag = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $disability = trim($_POST['disability_type']);
    $bio = trim($_POST['bio']);

    $check = $pdo->prepare("SELECT id FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
    $check->execute(['uid' => $seeker_id]);
    $exists = $check->fetch();

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE seeker_profiles SET disability_type = :dis, bio = :bio WHERE user_id = :uid");
        $stmt->execute(['dis' => $disability, 'bio' => $bio, 'uid' => $seeker_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO seeker_profiles (user_id, disability_type, bio, is_pwd) VALUES (:uid, :dis, :bio, 1)");
        $stmt->execute(['uid' => $seeker_id, 'dis' => $disability, 'bio' => $bio]);
    }
    $status_flag = 'success';
}

$profileStmt = $pdo->prepare("SELECT * FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
$profileStmt->execute(['uid' => $seeker_id]);
$profile = $profileStmt->fetch();

$current_disability = ($profile && isset($profile['disability_type'])) ? $profile['disability_type'] : '';
$current_bio = ($profile && isset($profile['bio'])) ? $profile['bio'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - SkillDis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white border-b border-gray-100 h-16 flex items-center px-6 sticky top-0 z-40">
            <h1 class="text-lg font-semibold text-gray-700">Account Configuration Center</h1>
        </header>

        <main class="p-6 max-w-2xl w-full mx-auto space-y-6">
            
            <?php if ($status_flag === 'success'): ?>
                <div class="p-3 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-xl border border-emerald-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> Demographic tracking definitions modified successfully.
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Demographic and Verification Specifics</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Keep this data updated to help our automated keyword analysis optimize your matching metrics.</p>
                </div>

                <form method="POST" action="seeker_profile.php" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Disability Classification Type</label>
                        <select name="disability_type" required class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-3 outline-none focus:border-[#01449b]">
                            <option value="">Select Classification Type</option>
                            <option value="Visual Impairment" <?php if($current_disability === 'Visual Impairment') echo 'selected'; ?>>Visual Impairment / Blindness</option>
                            <option value="Hearing Impairment" <?php if($current_disability === 'Hearing Impairment') echo 'selected'; ?>>Hearing Impairment / Deafness</option>
                            <option value="Orthopedic / Mobility" <?php if($current_disability === 'Orthopedic / Mobility') echo 'selected'; ?>>Orthopedic / Physical Mobility Limitation</option>
                            <option value="Speech Impairment" <?php if($current_disability === 'Speech Impairment') echo 'selected'; ?>>Speech and Language Impairment</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Professional Bio & Executive Summary</label>
                        <textarea name="bio" rows="4" class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-3 outline-none focus:border-[#01449b]" placeholder="Write a brief professional summary about your career goals..."><?php echo htmlspecialchars($current_bio); ?></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#01449b] hover:bg-blue-800 text-white font-medium p-3 rounded-xl text-sm transition-all shadow-sm">
                        Commit Profile Modifications
                    </button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>