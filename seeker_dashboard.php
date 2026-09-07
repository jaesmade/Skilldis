<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_seeker') {
    header("Location: login.php");
    exit();
}

$seeker_id = $_SESSION['user_id'];
$user_display_name = htmlspecialchars($_SESSION['name']);

$profileStmt = $pdo->prepare("SELECT * FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
$profileStmt->execute(['uid' => $seeker_id]);
$profile = $profileStmt->fetch();

$education = $profile ? htmlspecialchars($profile['education_level'] ?? 'Not Specified') : 'Not Specified';
$skills_list = ($profile && !empty($profile['skills'])) ? explode(',', $profile['skills']) : [];

$countApp = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE seeker_id = :uid");
$countApp->execute(['uid' => $seeker_id]);
$total_apps = $countApp->fetchColumn();

$countShort = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE seeker_id = :uid AND status = 'shortlisted'");
$countShort->execute(['uid' => $seeker_id]);
$total_shortlisted = $countShort->fetchColumn();

$jobsStmt = $pdo->prepare("SELECT jl.*, ep.company_name FROM job_listings jl 
                           JOIN employer_profiles ep ON jl.employer_id = ep.user_id 
                           ORDER BY jl.created_at DESC LIMIT 3");
$jobsStmt->execute();
$recommended_jobs = $jobsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile Dashboard - SkillDis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">
        
        <header class="bg-white border-b border-gray-100 h-16 flex items-center justify-between px-6 sticky top-0 z-40">
            <h1 class="text-lg font-semibold text-gray-700">Job Seeker Workspace</h1>
            <span class="text-xs text-gray-400 font-medium">Welcome back, <?php echo $user_display_name; ?>!</span>
        </header>

        <main class="p-6 max-w-5xl w-full mx-auto space-y-6">
            
            <div class="bg-gradient-to-r from-[#01449b] to-blue-800 p-6 rounded-2xl text-white shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold tracking-tight">Professional Profile Hub</h2>
                    <p class="text-xs text-blue-100 max-w-md">Manage your regular employment credentials, track live corporate evaluations, and update your baseline skill assets.</p>
                </div>
                <div class="bg-white/10 px-4 py-2 rounded-xl text-xs font-semibold backdrop-blur-sm">
                    Account Status: <span class="text-emerald-300 font-bold uppercase tracking-wider">Active</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Applications Sent</span>
                        <div class="text-2xl font-black text-gray-900"><?php echo $total_apps; ?></div>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-[#01449b] rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Shortlisted Status</span>
                        <div class="text-2xl font-black text-amber-600"><?php echo $total_shortlisted; ?></div>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm">Credential Overview</h3>
                            <p class="text-[11px] text-gray-400">Core parameters saved in system logs.</p>
                        </div>

                        <div class="space-y-3 pt-2">
                            <div class="text-xs">
                                <span class="text-gray-400 block font-medium">Educational Attainment:</span>
                                <span class="font-semibold text-gray-700"><?php echo $education; ?></span>
                            </div>

                            <div class="text-xs">
                                <span class="text-gray-400 block font-medium">Registered Competencies:</span>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    <?php if (empty($skills_list)): ?>
                                        <span class="text-gray-400 italic text-[11px]">No manual skills declared.</span>
                                    <?php else: ?>
                                        <?php foreach ($skills_list as $skill): ?>
                                            <span class="bg-gray-100 text-gray-600 text-[10px] font-semibold px-2 py-1 rounded-md">
                                                <?php echo htmlspecialchars(trim($skill)); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-4">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Recent Corporate Openings</h3>
                                <p class="text-[11px] text-gray-400">Latest structural job postings published on the portal.</p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <?php if (empty($recommended_jobs)): ?>
                                <div class="text-center py-6 text-xs text-gray-400 italic">
                                    No dynamic job records found in the database directory.
                                </div>
                            <?php else: ?>
                                <?php foreach ($recommended_jobs as $job): ?>
                                    <div class="p-4 border border-gray-100 rounded-xl hover:border-blue-100 transition-all flex justify-between items-center bg-gray-50/30">
                                        <div class="space-y-1 max-w-md">
                                            <h4 class="font-bold text-xs text-gray-900"><?php echo htmlspecialchars($job['job_title']); ?></h4>
                                            <p class="text-[11px] font-medium text-gray-500"><i class="fa-solid fa-building text-[10px] mr-1"></i> <?php echo htmlspecialchars($job['company_name']); ?></p>
                                            <div class="text-[10px] text-gray-400 truncate mt-1">
                                                Skills Required: <?php echo htmlspecialchars($job['required_skills'] ?? 'General Competencies'); ?>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] text-gray-400 block mb-2"><?php echo date('M d, Y', strtotime($job['created_at'])); ?></span>
                                            <span class="bg-blue-50 text-[#01449b] text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wide">Available</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

</body>
</html>