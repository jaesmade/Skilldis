<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: login.php");
    exit();
}

$employer_id = $_SESSION['user_id'];

$jobStmt = $pdo->prepare("SELECT * FROM job_listings WHERE employer_id = :employer_id ORDER BY created_at DESC");
$jobStmt->execute(['employer_id' => $employer_id]);
$job_listings = $jobStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs - SkillDis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white border-b border-gray-100 h-16 flex items-center justify-between px-6 sticky top-0 z-40">
            <h1 class="text-lg font-semibold text-gray-700">Job Board Management</h1>
            <button onclick="toggleModal(true)" class="bg-[#01449b] hover:bg-blue-800 text-white font-medium px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Publish New Job
            </button>
        </header>

        <main class="p-6 max-w-7xl w-full mx-auto space-y-6">
            
            <?php if (isset($_GET['msg']) || isset($_GET['post'])): ?>
                <div class="p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>
                        <?php
                            if (($_GET['msg'] ?? '') === 'job_updated') echo "Job visibility state changed successfully.";
                            elseif (($_GET['msg'] ?? '') === 'job_deleted') echo "Job entry permanently removed from system records.";
                            elseif (($_GET['post'] ?? '') === 'success') echo "New job opportunity published successfully!";
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold uppercase text-gray-400 border-b border-gray-100">
                                <th class="p-4">Position Title</th>
                                <th class="p-4">Required Knowledge Base</th>
                                <th class="p-4">Accessibility Tags</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php if (empty($job_listings)): ?>
                                <tr><td colspan="5" class="p-8 text-center text-gray-400">No career vacancies posted yet. Click "Publish New Job" to begin.</td></tr>
                            <?php else: ?>
                                <?php foreach ($job_listings as $job): ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="p-4 font-semibold text-gray-900"><?php echo htmlspecialchars($job['job_title']); ?></td>
                                        <td class="p-4 text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($job['required_skills']); ?></td>
                                        <td class="p-4"><span class="text-xs px-2.5 py-1 bg-blue-50 text-[#01449b] rounded-lg font-medium"><?php echo htmlspecialchars($job['accessibility_tags'] ?? 'Standard Baseline'); ?></span></td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase <?php echo $job['job_status'] === 'open' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'; ?>">
                                                <?php echo $job['job_status']; ?>
                                            </span>
                                        </td>
                                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                            <a href="process_crud.php?action=toggle_job&id=<?php echo $job['id']; ?>" class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-semibold transition-all inline-block">
                                                <i class="fa-solid fa-toggle-on"></i> Toggle
                                            </a>
                                            <a href="process_crud.php?action=delete_job&id=<?php echo $job['id']; ?>" onclick="return confirm('Erase this career post completely?')" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg font-semibold transition-all inline-block">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </a>
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

    <div id="jobModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Publish New Opportunity</h3>
                <button onclick="toggleModal(false)" class="w-8 h-8 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="process_job.php" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Job Title</label>
                    <input type="text" name="job_title" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 outline-none focus:border-[#01449b]" placeholder="e.g., Office Operations Clerk">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Job Description</label>
                    <textarea name="job_description" rows="3" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 outline-none focus:border-[#01449b]" placeholder="Outline required project scope context details..."></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Required Skills (Comma-separated)</label>
                    <input type="text" name="required_skills" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 outline-none focus:border-[#01449b]" placeholder="Data Entry, Filing, Documentation">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Accessibility Accommodations</label>
                    <input type="text" name="accessibility_tags" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 outline-none focus:border-[#01449b]" placeholder="Wheelchair Ramps, Flexible Shift Rest Breaks">
                </div>
                <button type="submit" class="w-full bg-[#01449b] hover:bg-blue-800 text-white font-medium p-3 rounded-xl text-sm transition-all shadow-lg shadow-blue-700/10">Submit Vacancy</button>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(state) {
            const modal = document.getElementById('jobModal');
            if(state) modal.classList.remove('hidden');
            else modal.classList.add('hidden');
        }
    </script>
</body>
</html>