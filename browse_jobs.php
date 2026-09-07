<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'pwd' && $_SESSION['role'] !== 'job_seeker')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$resumeCheck = $pdo->prepare("SELECT resume_path, skills, extracted_text, education_text, experience_text FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
$resumeCheck->execute(['uid' => $user_id]);
$profile_record = $resumeCheck->fetch();

$has_uploaded_resume = ($profile_record && !empty($profile_record['resume_path']));

$resume_text_raw = ($profile_record && !empty($profile_record['extracted_text'])) ? $profile_record['extracted_text'] : '';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$accommodation = isset($_GET['accommodation']) ? trim($_GET['accommodation']) : '';

$queryStr = "SELECT jl.*, ep.company_name 
             FROM job_listings jl
             JOIN employer_profiles ep ON jl.employer_id = ep.user_id
             WHERE jl.job_status = 'open'";
$params = [];

if (!empty($search)) {
    $queryStr .= " AND (jl.job_title LIKE :search OR jl.required_skills LIKE :search_skills)";
    $params['search'] = "%$search%";
    $params['search_skills'] = "%$search%";
}

if (!empty($accommodation)) {
    $queryStr .= " AND jl.accessibility_tags LIKE :accommodation";
    $params['accommodation'] = "%$accommodation%";
}

$stmt = $pdo->prepare($queryStr);
$stmt->execute($params);
$raw_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Accessible Jobs - SkillDis</title>
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
            <h1 class="text-lg font-semibold text-gray-700">Explore Open Positions</h1>
            <span id="ai-status" class="text-xs text-blue-600 font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-brain animate-pulse"></i> Preparing Match Core...
            </span>
        </header>

        <main class="p-6 max-w-7xl w-full mx-auto space-y-6">
            
            <?php if (!$has_uploaded_resume): ?>
                <div class="p-4 bg-red-50 border border-red-100 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 text-red-700 rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-file-circle-exclamation"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900">Application Core Locked</h4>
                            <p class="text-[11px] text-gray-500">You must upload your baseline resume structure before submitting applications to employers.</p>
                        </div>
                    </div>
                    <a href="upload_resume.php" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-sm whitespace-nowrap">
                        <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Upload Now
                    </a>
                </div>
            <?php endif; ?>
            
            <form method="GET" action="browse_jobs.php" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Search Keywords</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-2.5 outline-none focus:border-[#01449b]" placeholder="e.g., Data Clerk, Excel">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Accessibility Accommodation</label>
                    <select name="accommodation" class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-2.5 outline-none focus:border-[#01449b]">
                        <option value="">Any Work Setting</option>
                        <option value="Wheelchair" <?php if($accommodation === 'Wheelchair') echo 'selected'; ?>>Wheelchair Accessible</option>
                        <option value="Remote" <?php if($accommodation === 'Remote') echo 'selected'; ?>>Remote / Work From Home</option>
                        <option value="Screen Reader" <?php if($accommodation === 'Screen Reader') echo 'selected'; ?>>Screen Reader Friendly</option>
                    </select>
                </div>
                <button type="submit" class="bg-[#01449b] hover:bg-blue-800 text-white font-medium p-2.5 rounded-xl text-sm transition-all flex items-center justify-center gap-2 shadow-md shadow-blue-700/10">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Apply Filters
                </button>
            </form>

            <div id="debug-box" class="hidden bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-xs"></div>

            <div id="jobs-container" class="space-y-3">
                <?php if (empty($raw_jobs)): ?>
                    <div class="bg-white p-8 text-center text-gray-400 rounded-2xl border border-gray-100 shadow-sm">
                        No current employment options fit the specified search parameters.
                    </div>
                <?php else: ?>
                    <?php foreach ($raw_jobs as $job): ?>
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="space-y-3 w-full flex-1">
                                <div class="h-5 w-48 shimmer rounded-md"></div>
                                <div class="h-3 w-24 shimmer rounded-md"></div>
                                <div class="space-y-1.5">
                                    <div class="h-3 w-full shimmer rounded-md"></div>
                                    <div class="h-3 w-4/5 shimmer rounded-md"></div>
                                </div>
                            </div>
                            <div class="w-full md:w-auto flex justify-end pt-3 md:pt-0">
                                <div class="h-8 w-24 shimmer rounded-md"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    const resumeText = <?php echo json_encode($resume_text_raw); ?>;
    const rawJobs = <?php echo json_encode($raw_jobs); ?>;
    const hasResume = <?php echo json_encode($has_uploaded_resume); ?>;
    
    const HF_TOKEN = "***********************************"; 

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
            console.error("Hugging Face API Error:", error);
            showErrorUI(error.message);
            return 0;
        }
    }

    function showErrorUI(msg) {
        const dBox = document.getElementById('debug-box');
        dBox.classList.remove('hidden');
        dBox.innerHTML = `<strong><i class="fa-solid fa-triangle-exclamation"></i> AI Matching Connection Error:</strong> ${msg}. Gagamit muna ang system ng backup scoring.`;
    }

    async function processJobsMatching() {
        const jobsContainer = document.getElementById('jobs-container');
        const statusDisplay = document.getElementById('ai-status');

        if (!resumeText || rawJobs.length === 0) {
            statusDisplay.innerHTML = `<span class="text-gray-400"><i class="fa-solid fa-circle-info"></i> Upload CV to enable AI matching.</span>`;
            if (rawJobs.length > 0) {
                renderJobsList(rawJobs.map(j => ({ ...j, match_percentage: 0 })));
            }
            return;
        }

        statusDisplay.innerHTML = `<i class="fa-solid fa-brain animate-pulse text-purple-600"></i> AI Calculating Matrix...`;

        let scoredJobs = [];

        for (let job of rawJobs) {
            const jobContext = `Job Title: ${job.job_title}. Required Skills: ${job.required_skills}. Job Description: ${job.job_description}`;
            
            let score = await calculateBertSimilarity(resumeText, jobContext);

            scoredJobs.push({
                ...job,
                match_percentage: score
            });
        }

        scoredJobs.sort((a, b) => b.match_percentage - a.match_percentage);

        renderJobsList(scoredJobs);

        statusDisplay.innerHTML = `<span class="text-emerald-600"><i class="fa-solid fa-circle-check"></i> Semantic Ranking Loaded</span>`;
    }

    function renderJobsList(jobs) {
        const container = document.getElementById('jobs-container');
        container.innerHTML = '';

        if (jobs.length === 0) {
            container.innerHTML = `
                <div class="bg-white p-8 text-center text-gray-400 rounded-2xl border border-gray-100 shadow-sm">
                    No current employment options fit the specified search parameters.
                </div>
            `;
            return;
        }

        jobs.forEach(job => {
            const pct = job.match_percentage;
            let badgeColor = "bg-green-50 text-green-700 border-green-200";
            if (pct < 40) {
                badgeColor = "bg-red-50 text-red-600 border-red-200";
            } else if (pct < 70) {
                badgeColor = "bg-blue-50 text-blue-700 border-blue-200";
            }

            const card = document.createElement('div');
            card.className = "bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-blue-200 transition-all";
            card.innerHTML = `
                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="font-bold text-gray-900 text-base">${escapeHTML(job.job_title)}</h4>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold border ${badgeColor}">
                            <i class="fa-solid fa-chart-simple"></i> ${pct}.00% Match
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">${escapeHTML(job.company_name)}</p>
                    <p class="text-sm text-gray-500 line-clamp-2">${escapeHTML(job.job_description)}</p>
                    
                    <div class="flex items-center gap-2 pt-1 flex-wrap">
                        <span class="text-[11px] bg-blue-50 text-[#01449b] font-semibold px-2.5 py-0.5 rounded-md">
                            Skills: ${escapeHTML(job.required_skills)}
                        </span>
                        ${job.accessibility_tags ? `
                            <span class="text-[11px] bg-amber-50 text-amber-800 font-semibold px-2.5 py-0.5 rounded-md">
                                <i class="fa-solid fa-universal-access"></i> ${escapeHTML(job.accessibility_tags)}
                            </span>
                        ` : ''}
                    </div>
                </div>

                <div class="whitespace-nowrap pt-3 md:pt-0 w-full md:w-auto border-t md:border-0 border-gray-100 flex items-center justify-end">
                    ${hasResume ? `
                        <a href="apply_job.php?id=${job.id}" class="bg-[#01449b] hover:bg-blue-800 text-white font-medium px-5 py-2.5 rounded-xl text-xs transition-all w-full md:w-auto text-center shadow-sm">
                            Apply Now
                        </a>
                    ` : `
                        <div class="flex flex-col items-end gap-1 w-full md:w-auto">
                            <button disabled class="bg-gray-200 text-gray-400 font-medium px-5 py-2.5 rounded-xl text-xs cursor-not-allowed flex items-center justify-center gap-1.5 w-full md:w-auto">
                                <i class="fa-solid fa-lock text-[10px]"></i> Apply Locked
                            </button>
                            <span class="text-[10px] text-red-500 font-medium hidden md:inline">Upload CV to open</span>
                        </div>
                    `}
                </div>
            `;
            container.appendChild(card);
        });
    }

    function escapeHTML(str) {
        if (!str) return '';
        return str.replace(/[&<>'"]/g, 
            tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
        );
    }

    window.addEventListener('DOMContentLoaded', () => {
        processJobsMatching();
    });
    </script>
</body>
</html>