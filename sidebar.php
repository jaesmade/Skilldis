<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$current_role = $_SESSION['role'];
$user_display_name = htmlspecialchars($_SESSION['name']);

function isPageActive($keywords) {
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    foreach ((array)$keywords as $keyword) {
        if (strpos($current_script, $keyword) !== false) {
            return 'bg-white/10 text-white';
        }
    }
    return 'text-blue-100 hover:bg-white/5 hover:text-white';
}
?>
<aside class="w-64 bg-[#01449b] text-white flex flex-col justify-between hidden md:flex z-10 shadow-xl flex-shrink-0">
    <div>
        <div class="p-6 flex items-center gap-3 tracking-wide border-b border-blue-800">
            <i class="fa-solid fa-graduation-cap text-blue-200 text-xl"></i>
            <span class="text-xl font-bold">Skill<span class="text-blue-200">Dis</span></span>
        </div>
        
        <nav class="p-4 space-y-1">
            
            <?php if ($current_role === 'superadmin' || $current_role === 'admin'): ?>
                <a href="admin_dashboard.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('admin'); ?>">
                    <i class="fa-solid fa-chart-pie w-5"></i> System Overview
                </a>
                <a href="manage_branches.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('branch'); ?>">
                    <i class="fa-solid fa-network-wired w-5"></i> Manage Branches
                </a>

            <?php elseif ($current_role === 'employer'): ?>
                <a href="employer_dashboard.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('employer_dashboard'); ?>">
                    <i class="fa-solid fa-table-columns w-5"></i> Workspace Home
                </a>
                <a href="jobs.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('jobs'); ?>">
                    <i class="fa-solid fa-briefcase w-5"></i> Manage Jobs
                </a>
                <a href="candidates.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('candidates'); ?>">
                    <i class="fa-solid fa-users w-5"></i> Active Candidates
                </a>

            <?php elseif ($current_role === 'pwd'): ?>
                <a href="pwd_dashboard.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('pwd_dashboard'); ?>">
                    <i class="fa-solid fa-table-columns w-5"></i> Seeker Center
                </a>
                <a href="upload_resume.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('upload_resume'); ?>">
                    <i class="fa-solid fa-file-arrow-up w-5"></i> Upload Resume
                </a>
                <a href="browse_jobs.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('browse_jobs'); ?>">
                    <i class="fa-solid fa-magnifying-glass w-5"></i> Browse Accessible Jobs
                </a>
                <a href="my_applications.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('my_applications'); ?>">
                    <i class="fa-solid fa-file-invoice w-5"></i> Track Applications
                </a>
                <a href="skill_assessment.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('assessment'); ?>">
                    <i class="fa-solid fa-star-shooting w-5"></i> Skill Assessment
                </a>
                <a href="seeker_profile.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('profile'); ?>">
                    <i class="fa-solid fa-user-gear w-5"></i> Profile Settings
                </a>

            <?php elseif ($current_role === 'job_seeker'): ?>
                <a href="seeker_dashboard.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('seeker'); ?>">
                    <i class="fa-solid fa-user-tie w-5"></i> My Profile
                </a>
                <a href="upload_resume.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('upload_resume'); ?>">
                    <i class="fa-solid fa-file-arrow-up w-5"></i> Upload Resume
                </a>
                <a href="browse_jobs.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('browse_jobs'); ?>">
                    <i class="fa-solid fa-magnifying-glass w-5"></i> Browse Jobs
                </a>
                <a href="my_applications.php" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?php echo isPageActive('my_applications'); ?>">
                    <i class="fa-solid fa-file-invoice w-5"></i> Track Applications
                </a>
            <?php endif; ?>

        </nav>
    </div>

    <div class="p-4 border-t border-blue-800 bg-black/10 flex items-center justify-between">
        <div class="flex items-center gap-3 truncate mr-2">
            <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#01449b] flex items-center justify-center font-bold text-sm uppercase flex-shrink-0">
                <?php echo substr($user_display_name, 0, 2); ?>
            </div>
            <div class="text-sm truncate">
                <p class="font-semibold leading-none truncate"><?php echo $user_display_name; ?></p>
                <span class="text-[10px] text-blue-200 uppercase tracking-wider font-semibold"><?php echo str_replace('_', ' ', $current_role); ?></span>
            </div>
        </div>
        <a href="login.php" class="text-blue-200 hover:text-white transition-colors flex-shrink-0">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</aside>