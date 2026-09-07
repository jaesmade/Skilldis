<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: login.php");
    exit();
}

$employer_id = $_SESSION['user_id'];

$companyStmt = $pdo->prepare("SELECT * FROM employer_profiles WHERE user_id = :user_id LIMIT 1");
$companyStmt->execute(['user_id' => $employer_id]);
$company = $companyStmt->fetch();
$company_name = $company ? $company['company_name'] : 'Enterprise Client';

$jobStmt = $pdo->prepare("SELECT * FROM job_listings WHERE employer_id = :employer_id ORDER BY created_at DESC");
$jobStmt->execute(['employer_id' => $employer_id]);
$job_listings = $jobStmt->fetchAll();

$candidateSql = "SELECT ja.id AS app_id, ja.status AS app_status, ja.applied_at, 
                        u.name AS candidate_name, jl.job_title, sp.is_pwd, sp.disability_type
                 FROM job_applications ja
                 JOIN users u ON ja.seeker_id = u.id
                 JOIN job_listings jl ON ja.job_id = jl.id
                 JOIN seeker_profiles sp ON u.id = sp.user_id
                 WHERE jl.employer_id = :employer_id 
                 ORDER BY ja.applied_at DESC";
$candidateStmt = $pdo->prepare($candidateSql);
$candidateStmt->execute(['employer_id' => $employer_id]);
$candidates = $candidateStmt->fetchAll();

$total_posts = count($job_listings);
$active_applicants = count($candidates);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Workspace - SkillDis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">
        
        <header class="bg-white border-b border-gray-100 h-16 flex items-center justify-between px-6 sticky top-0 z-40">
            <h1 class="text-sm font-bold tracking-wider text-gray-400 uppercase">Dashboard Hub</h1>
            <div class="text-sm text-gray-500 font-medium">
                Enterprise Entity: <span class="text-gray-900 font-bold"><?php echo htmlspecialchars($company_name); ?></span>
            </div>
        </header>

        <main class="p-6 space-y-8 max-w-7xl w-full mx-auto">
            
            <?php if (isset($_GET['msg'])): ?>
                <div class="p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>
                        <?php
                            if ($_GET['msg'] === 'job_updated') echo "Job structural tracking status successfully updated.";
                            elseif ($_GET['msg'] === 'job_deleted') echo "Job configuration profile securely expunged.";
                            elseif ($_GET['msg'] === 'candidate_updated') echo "Candidate process assessment lifecycle metric adjusted.";
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Enterprise Workspace Terminal</h2>
                    <p class="text-gray-500 text-sm mt-1">Publish open career roles and monitor contextual application score evaluations.</p>
                </div>
                <button onclick="toggleModal(true)" class="bg-[#01449b] hover:bg-blue-800 text-white font-medium px-5 py-3 rounded-xl text-sm shadow-md shadow-blue-600/10 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Create New Job Post
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Active Job Positions</span>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_posts; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-[#01449b] rounded-xl flex items-center justify-center text-lg"><i class="fa-solid fa-briefcase"></i></div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Profile Submissions</span>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1"><?php echo $active_applicants; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-lg"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>

            <section id="jobs-section" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden scroll-mt-6">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900 text-base">Corporate Vacancy Listings</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold uppercase text-gray-400 border-b border-gray-100">
                                <th class="p-4">Position Title</th>
                                <th class="p-4">Required Knowledge Base</th>
                                <th class="p-4">Accessibility Criteria</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php if (empty($job_listings)): ?>
                                <tr><td colspan="5" class="p-8 text-center text-gray-400">No operational openings posted yet.</td></tr>
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
                                        <td class="p-4 text-right space-x-2">
                                            <a href="process_crud.php?action=toggle_job&id=<?php echo $job['id']; ?>" class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-semibold transition-all inline-block">
                                                <i class="fa-solid fa-toggle-on"></i> Toggle
                                            </a>
                                            <a href="process_crud.php?action=delete_job&id=<?php echo $job['id']; ?>" onclick="return confirm('Erase this career post completely from operational database models?')" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg font-semibold transition-all inline-block">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="candidates-section" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden scroll-mt-6">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900 text-base">Active Applicant Profile Track</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold uppercase text-gray-400 border-b border-gray-100">
                                <th class="p-4">Full Applicant Name</th>
                                <th class="p-4">Target Career Assignment</th>
                                <th class="p-4">Profile Designation Group</th>
                                <th class="p-4">Pipeline Action Tracking</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php if (empty($candidates)): ?>
                                <tr><td colspan="4" class="p-8 text-center text-gray-400">No applicant profiles submitted yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($candidates as $cand): ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="p-4 font-semibold text-gray-900"><?php echo htmlspecialchars($cand['candidate_name']); ?></td>
                                        <td class="p-4 text-gray-600"><?php echo htmlspecialchars($cand['job_title']); ?></td>
                                        <td class="p-4">
                                            <?php if ($cand['is_pwd']): ?>
                                                <span class="px-2.5 py-1 bg-amber-50 text-amber-800 rounded-lg text-xs font-semibold flex items-center gap-1 w-max">
                                                    <i class="fa-solid fa-wheelchair text-xs"></i> PWD (<?php echo htmlspecialchars($cand['disability_type']); ?>)
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium w-max flex items-center gap-1">
                                                    <i class="fa-solid fa-user-tie text-xs"></i> Regular Employee
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-4">
                                            <form action="process_crud.php?action=update_candidate" method="POST" class="flex items-center">
                                                <input type="hidden" name="application_id" value="<?php echo $cand['app_id']; ?>">
                                                <select name="status" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-900 text-xs rounded-xl p-2 outline-none focus:border-[#01449b]">
                                                    <option value="applied" <?php if($cand['app_status'] === 'applied') echo 'selected'; ?>>Applied</option>
                                                    <option value="reviewing" <?php if($cand['app_status'] === 'reviewing') echo 'selected'; ?>>Reviewing</option>
                                                    <option value="shortlisted" <?php if($cand['app_status'] === 'shortlisted') echo 'selected'; ?>>Shortlisted</option>
                                                    <option value="hired" <?php if($cand['app_status'] === 'hired') echo 'selected'; ?>>Hired</option>
                                                    <option value="rejected" <?php if($cand['app_status'] === 'rejected') echo 'selected'; ?>>Rejected</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
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