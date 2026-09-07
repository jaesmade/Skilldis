<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$seeker_id = $_SESSION['user_id'];
$msg = '';

if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['app_id'])) {
    $app_id = intval($_GET['app_id']);

    $check_sql = "SELECT status FROM job_applications WHERE id = :app_id AND seeker_id = :seeker_id LIMIT 1";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute(['app_id' => $app_id, 'seeker_id' => $seeker_id]);
    $application = $check_stmt->fetch();

    if ($application) {
        if ($application['status'] !== 'hired') {
            $delete_sql = "DELETE FROM job_applications WHERE id = :app_id AND seeker_id = :seeker_id";
            $delete_stmt = $pdo->prepare($delete_sql);
            $delete_stmt->execute(['app_id' => $app_id, 'seeker_id' => $seeker_id]);
            $msg = 'cancel_success';
        } else {
            $msg = 'cancel_denied';
        }
    }
}

$sql = "SELECT ja.id AS app_id, ja.status AS app_status, ja.applied_at,
               jl.job_title, jl.accessibility_tags, ep.company_name
        FROM job_applications ja
        JOIN job_listings jl ON ja.job_id = jl.id
        JOIN employer_profiles ep ON jl.employer_id = ep.user_id
        WHERE ja.seeker_id = :seeker_id
        ORDER BY ja.applied_at DESC";
        
$stmt = $pdo->prepare($sql);
$stmt->execute(['seeker_id' => $seeker_id]);
$my_applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track My Applications - SkillDis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">
        
        <header class="bg-white border-b border-gray-100 h-16 flex items-center px-6 sticky top-0 z-40">
            <h1 class="text-lg font-semibold text-gray-700">Application History Tracking</h1>
        </header>

        <main class="p-6 max-w-7xl w-full mx-auto space-y-6">
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'applied_success'): ?>
                <div class="p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>Your career profile summary has been securely transmitted to the target employer.</span>
                </div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'already_applied'): ?>
                <div class="p-3 bg-amber-50 border border-amber-100 text-amber-700 text-xs font-medium rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-sm"></i>
                    <span>Duplicate Application Warning: You have already submitted a profile entry for this exact post.</span>
                </div>
            <?php  endif; ?>

            <?php if ($msg === 'cancel_success'): ?>
                <div class="p-3 bg-blue-50 border border-blue-100 text-[#01449b] text-xs font-medium rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                    <span>Application has been successfully withdrawn and cancelled.</span>
                </div>
            <?php elseif ($msg === 'cancel_denied'): ?>
                <div class="p-3 bg-red-50 border border-red-100 text-red-700 text-xs font-medium rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark text-sm"></i>
                    <span>Action Denied: You cannot withdraw an application that has already been approved or hired.</span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900 text-base">Submitted Profile Tracking Feed</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold uppercase text-gray-400 border-b border-gray-100">
                                <th class="p-4">Enterprise / Corporate Identity</th>
                                <th class="p-4">Target Career Assignment</th>
                                <th class="p-4">Submission Date Stamp</th>
                                <th class="p-4">Evaluation Phase Status</th>
                                <th class="p-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php if (empty($my_applications)): ?>
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400">
                                        You have not submitted any job applications yet. Go to <a href="pwd_dashboard.php" class="text-[#01449b] font-semibold underline">Dashboard</a> to look for matches.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($my_applications as $app): ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="p-4 font-bold text-gray-900">
                                            <?php echo htmlspecialchars($app['company_name']); ?>
                                        </td>
                                        <td class="p-4 font-medium text-gray-600">
                                            <p><?php echo htmlspecialchars($app['job_title']); ?></p>
                                            <span class="text-[10px] text-gray-400 font-normal">Accommodation: <?php echo htmlspecialchars($app['accessibility_tags'] ?? 'Standard Baseline'); ?></span>
                                        </td>
                                        <td class="p-4 text-gray-500">
                                            <?php echo date('M d, Y', strtotime($app['applied_at'])); ?>
                                        </td>
                                        <td class="p-4">
                                            <?php 
                                                $status = $app['app_status'];
                                                $badge_classes = "bg-gray-100 text-gray-700";
                                                $display_text = "Applied";

                                                if ($status === 'reviewing') {
                                                    $badge_classes = "bg-blue-50 text-blue-700 border border-blue-100";
                                                    $display_text = "Under Review";
                                                } elseif ($status === 'shortlisted') {
                                                    $badge_classes = "bg-amber-50 text-amber-800 border border-amber-100";
                                                    $display_text = "Shortlisted";
                                                } elseif ($status === 'hired') {
                                                    $badge_classes = "bg-emerald-50 text-emerald-700 border border-emerald-100";
                                                    $display_text = "Hired / Approved";
                                                } elseif ($status === 'rejected') {
                                                    $badge_classes = "bg-red-50 text-red-700 border border-red-100";
                                                    $display_text = "Closed / Refused";
                                                }
                                            ?>
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider <?php echo $badge_classes; ?>">
                                                <?php echo $display_text; ?>
                                            </span>
                                        </td>
                                        <td class="p-4 text-center">
                                            <?php if ($status !== 'hired'): ?>
                                                <a href="my_applications.php?action=cancel&app_id=<?php echo $app['app_id']; ?>" 
                                                   onclick="return confirm('Are you sure you want to withdraw your application for this job post?');" 
                                                   class="text-xs text-red-600 hover:text-red-800 font-semibold bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-xl transition-all">
                                                    <i class="fa-solid fa-xmark"></i> Cancel
                                                </a>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400 italic">Locked</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>