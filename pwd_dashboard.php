<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pwd') {
    header("Location: login.php");
    exit();
}

$seeker_id = $_SESSION['user_id'];

$profileStmt = $pdo->prepare("SELECT * FROM seeker_profiles WHERE user_id = :user_id LIMIT 1");
$profileStmt->execute(['user_id' => $seeker_id]);
$profile = $profileStmt->fetch();

$disability_type = $profile ? $profile['disability_type'] : 'Not Specified';

$resume_text = $profile ? $profile['extracted_text'] : '';

$appCountStmt = $pdo->prepare("SELECT COUNT(id) as total FROM job_applications WHERE seeker_id = :seeker_id");
$appCountStmt->execute(['seeker_id' => $seeker_id]);
$total_applications = $appCountStmt->fetch()['total'];

$jobStmt = $pdo->prepare("
    SELECT jl.*, ep.company_name 
    FROM job_listings jl
    JOIN employer_profiles ep ON jl.employer_id = ep.user_id
    WHERE jl.job_status = 'open' 
");
$jobStmt->execute();
$raw_jobs = $jobStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWD Job Seeker Portal - SkillDis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght=300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .shimmer {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">
        
        <header class="bg-white border-b border-gray-100 h-16 flex items-center justify-between px-6 sticky top-0 z-40">
            <h1 class="text-sm font-bold tracking-wider text-gray-400 uppercase">Seeker Dashboard</h1>
            <div class="text-sm text-gray-500 font-medium">
                Welcome, <span class="text-gray-900 font-bold"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
            </div>
        </header>

        <main class="p-6 space-y-6 max-w-7xl w-full mx-auto">
            
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Your Inclusive Career Workspace</h2>
                    <p class="text-gray-500 text-sm mt-1">Our intelligent verification algorithm is matching your skill sets against live enterprise demands.</p>
                </div>
                <div class="px-4 py-2 bg-amber-50 border border-amber-100 rounded-xl text-amber-800 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-wheelchair"></i>
                    <span>Classification: PWD (<?php echo htmlspecialchars($disability_type); ?>)</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Submitted Applications</span>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1"><?php echo $total_applications; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-[#01449b] rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Highly Compatible Matches</span>
                        <h3 id="compat-count-display" class="text-3xl font-bold text-gray-900 mt-1">
                            <i class="fa-solid fa-circle-notch animate-spin text-gray-300 text-2xl"></i>
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-sparkles"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-purple-600"></i> AI Smart Recommendation Matches
                    </h3>
                    <span id="ai-status" class="text-xs text-blue-600 font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-brain animate-pulse"></i> Analyzing Match Matrix...
                    </span>
                </div>

                <div id="debug-box" class="hidden bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-xs"></div>

                <div id="jobs-container" class="space-y-3">
                    <?php if (empty($raw_jobs)): ?>
                        <div class="bg-white p-8 text-center text-gray-400 rounded-2xl border border-gray-100 shadow-sm">
                            No active vacancies found in the database system at this moment.
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($raw_jobs, 0, 5) as $idx => $job): ?>
                            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="space-y-3 w-full max-w-xl">
                                    <div class="h-5 w-48 shimmer rounded-md"></div>
                                    <div class="h-3 w-24 shimmer rounded-md"></div>
                                    <div class="space-y-1.5">
                                        <div class="h-3 w-full shimmer rounded-md"></div>
                                        <div class="h-3 w-5/6 shimmer rounded-md"></div>
                                    </div>
                                </div>
                                <div class="w-full sm:w-auto flex sm:flex-col items-end gap-3 pt-3 sm:pt-0">
                                    <div class="h-8 w-20 shimmer rounded-md"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
    const resumeText = <?php echo json_encode($resume_text); ?>;
    const rawJobs = <?php echo json_encode($raw_jobs); ?>;
    
    const HF_TOKEN = "**********************************"; 

    async function calculateBertSimilarity(resume, jobDesc) {
        const url = "https://router.huggingface.co/hf-inference/models/sentence-transformers/all-MiniLM-L6-v2";
        
        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Authorization": `Bearer ${HF_TOKEN}`,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    inputs: {
                        source_sentence: jobDesc,
                        sentences: [resume]
                    },
                    options: { wait_for_model: true }
                })
            });

            if (!response.ok) {
                const errText = await response.text();
                throw new Error(`HTTP ${response.status}: ${errText}`);
            }

            const result = await response.json();
            if (result && result[0] !== undefined) {
                const rawScore = result[0];

                if (rawScore < 0.35) {
                    return Math.round(rawScore * 15);
                } else if (rawScore >= 0.35 && rawScore < 0.60) {
                    return Math.round(rawScore * 100 * 0.8);
                } else {
                    return Math.min(100, Math.round(rawScore * 100 * 1.15));
                }
            }
            return 0;
        } catch (error) {
            console.error("Hugging Face API Call Failed:", error);
            showErrorUI(error.message);
            return 0;
        }
    }

    function showErrorUI(msg) {
        const dBox = document.getElementById('debug-box');
        dBox.classList.remove('hidden');
        dBox.innerHTML = `<strong><i class="fa-solid fa-triangle-exclamation"></i> AI Matching Connection Error:</strong> ${msg}. Gagamit muna ang system ng fallback simulation.`;
    }

    async function processDashboardMatching() {
        const jobsContainer = document.getElementById('jobs-container');
        const compatCountDisplay = document.getElementById('compat-count-display');
        const statusDisplay = document.getElementById('ai-status');

        if (!resumeText || rawJobs.length === 0) {
            statusDisplay.innerHTML = `<span class="text-gray-400"><i class="fa-solid fa-circle-info"></i> No resume text uploaded to compare.</span>`;
            if (rawJobs.length > 0) {
                renderJobsList(rawJobs.slice(0, 5).map(j => ({ ...j, synergy_score: 0 })));
            }
            compatCountDisplay.textContent = "0";
            return;
        }

        let scoredJobs = [];
        let highlyCompatibleCount = 0;

        for (let job of rawJobs) {
            const jobContext = `Job Title: ${job.job_title}. Required Skills: ${job.required_skills}. Job Description: ${job.job_description}`;
            
            let score = await calculateBertSimilarity(resumeText, jobContext);
            
            if (score >= 50) {
                highlyCompatibleCount++;
            }

            scoredJobs.push({
                ...job,
                synergy_score: score
            });
        }

        scoredJobs.sort((a, b) => b.synergy_score - a.synergy_score);

        const top5Jobs = scoredJobs.slice(0, 5);

        renderJobsList(top5Jobs);

        compatCountDisplay.textContent = highlyCompatibleCount;
        statusDisplay.innerHTML = `<span class="text-emerald-600"><i class="fa-solid fa-circle-check"></i> Semantic Scoring Synced</span>`;
    }

    function renderJobsList(jobs) {
        const container = document.getElementById('jobs-container');
        container.innerHTML = '';

        if (jobs.length === 0) {
            container.innerHTML = `
                <div class="bg-white p-8 text-center text-gray-400 rounded-2xl border border-gray-100 shadow-sm">
                    No active vacancies found in the database system at this moment.
                </div>
            `;
            return;
        }

        jobs.forEach(job => {
            const score = job.synergy_score;
            let scoreColor = "text-red-500";
            if (score >= 70) scoreColor = "text-[#01449b]";
            else if (score >= 40) scoreColor = "text-blue-500";

            const jobCard = document.createElement('div');
            jobCard.className = "bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:border-blue-200 transition-all";
            jobCard.innerHTML = `
                <div class="space-y-1.5 max-w-xl">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="font-bold text-gray-900 text-base">${escapeHTML(job.job_title)}</h4>
                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 rounded-full font-bold text-[10px] tracking-wide uppercase">Live Opportunity</span>
                    </div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">${escapeHTML(job.company_name)}</p>
                    <p class="text-sm text-gray-500 line-clamp-2">${escapeHTML(job.job_description)}</p>
                    
                    <div class="flex items-center gap-2 pt-1 flex-wrap">
                        <span class="text-[11px] bg-blue-50 text-[#01449b] font-medium px-2 py-0.5 rounded-md">
                            <i class="fa-solid fa-bolt text-[10px]"></i> Skills: ${escapeHTML(job.required_skills)}
                        </span>
                        ${job.accessibility_tags ? `
                            <span class="text-[11px] bg-amber-50 text-amber-800 font-medium px-2 py-0.5 rounded-md">
                                <i class="fa-solid fa-universal-access text-[10px]"></i> Accommodation: ${escapeHTML(job.accessibility_tags)}
                            </span>
                        ` : ''}
                    </div>
                </div>

                <div class="sm:text-right flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 border-t sm:border-0 pt-3 sm:pt-0 border-gray-100">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider block">Synergy Score</span>
                        <div class="text-xl font-black ${scoreColor}">${score}.00%</div>
                    </div>
                    <a href="apply_job.php?id=${job.id}" class="bg-[#01449b] hover:bg-blue-800 text-white font-medium px-4 py-2.5 rounded-xl text-xs shadow-md shadow-blue-700/10 transition-all whitespace-nowrap">
                        Submit Profile Application
                    </a>
                </div>
            `;
            container.appendChild(jobCard);
        });
    }

    function escapeHTML(str) {
        if (!str) return '';
        return str.replace(/[&<>'"]/g, 
            tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
        );
    }

    window.addEventListener('DOMContentLoaded', () => {
        processDashboardMatching();
    });
    </script>
</body>
</html>