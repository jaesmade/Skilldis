<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pwd') {
    header("Location: login.php");
    exit();
}

$seeker_id = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_add'])) {
    $skill = trim($_POST['skill_name']);
    $level = $_POST['proficiency_level'];

    if (!empty($skill)) {
        $pStmt = $pdo->prepare("SELECT id, skills_vector FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
        $pStmt->execute(['uid' => $seeker_id]);
        $profile = $pStmt->fetch();

        if ($profile) {
            $current_skills = $profile['skills_vector'] ? explode(',', $profile['skills_vector']) : [];
            if (!in_array($skill, $current_skills)) {
                $current_skills[] = $skill;
                $updated_string = implode(',', $current_skills);

                $upStmt = $pdo->prepare("UPDATE seeker_profiles SET skills_vector = :sk WHERE id = :id");
                $upStmt->execute(['sk' => $updated_string, 'id' => $profile['id']]);
                $msg = 'added_success';
            } else {
                $msg = 'duplicate';
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT skills_vector FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
$stmt->execute(['uid' => $seeker_id]);
$res = $stmt->fetch();
$logged_skills = ($res && $res['skills_vector']) ? explode(',', $res['skills_vector']) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Assessment - SkillDis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white border-b border-gray-100 h-16 flex items-center px-6 sticky top-0 z-40">
            <h1 class="text-lg font-semibold text-gray-700">Verified Competency Metrics</h1>
        </header>

        <main class="p-6 max-w-3xl w-full mx-auto space-y-6">
            
            <?php if ($msg === 'added_success'): ?>
                <div class="p-3 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-xl border border-emerald-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> Competency parameter logged successfully.
                </div>
            <?php elseif ($msg === 'duplicate'): ?>
                <div class="p-3 bg-amber-50 text-amber-700 text-xs font-medium rounded-xl border border-amber-100 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> That particular competency parameter is already tracked on your ledger.
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-900 text-base">Register Core Ability Parameter</h3>
                <form method="POST" action="skill_assessment.php" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Skill / Technology Name</label>
                            <input type="text" name="skill_name" required class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-3 outline-none focus:border-[#01449b]" placeholder="e.g., Technical Writing, JavaScript">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Self-Assessment Level</label>
                            <select name="proficiency_level" class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-3 outline-none focus:border-[#01449b]">
                                <option value="Beginner">Beginner Level</option>
                                <option value="Intermediate">Intermediate / Competent</option>
                                <option value="Advanced">Advanced / Subject Specialist</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="action_add" class="w-full bg-[#01449b] hover:bg-blue-800 text-white font-medium p-3 rounded-xl text-sm transition-all shadow-sm">
                        Save Competency Data
                    </button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-3">
                <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider text-gray-400">Your Current Core Skills</h3>
                <div class="flex flex-wrap gap-2">
                    <?php if (empty($logged_skills)): ?>
                        <p class="text-sm text-gray-400 py-2">No active verified competency points registered yet.</p>
                    <?php else: ?>
                        <?php foreach ($logged_skills as $sk): ?>
                            <span class="px-3 py-1.5 bg-gray-100 text-gray-800 text-xs font-semibold rounded-xl flex items-center gap-2">
                                <i class="fa-solid fa-award text-[#01449b]"></i> <?php echo htmlspecialchars(trim($sk)); ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>