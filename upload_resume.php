<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$seeker_id = $_SESSION['user_id'];
$msg = '';

function extractRawResumeText($filename, $fileExt) {
    if ($fileExt === 'txt') {
        return file_get_contents($filename);
    }
    
    if ($fileExt === 'docx') {
        if (!file_exists($filename) || !is_readable($filename)) return false;
        if (!class_exists('ZipArchive')) return false;
        
        $zip = new ZipArchive();
        $full_text = '';
        
        if ($zip->open($filename) === true) {
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $data = $zip->getFromIndex($index);
                $dom = new DOMDocument();
                @$dom->loadXML($data);
                
                $paragraphs = $dom->getElementsByTagName('p');
                foreach ($paragraphs as $paragraph) {
                    $node_text = '';
                    $texts = $paragraph->getElementsByTagName('t');
                    foreach ($texts as $t) {
                        $node_text .= $t->nodeValue;
                    }
                    $full_text .= $node_text . " \n";
                }
            }
            $zip->close();
        }
        return $full_text;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume_file'])) {
    $file = $_FILES['resume_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExt, ['txt', 'docx'])) {
        if ($file['error'] === 0) {
            $upload_dir = 'uploads/resumes/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $stmt = $pdo->prepare("SELECT resume_path FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
            $stmt->execute(['uid' => $seeker_id]);
            $old_res = $stmt->fetch();
            if ($old_res && !empty($old_res['resume_path']) && file_exists($old_res['resume_path'])) {
                unlink($old_res['resume_path']);
            }

            $newFileName = "resume_" . $seeker_id . "_" . uniqid('', true) . "." . $fileExt;
            $fileDestination = $upload_dir . $newFileName;

            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $raw_text = extractRawResumeText($fileDestination, $fileExt);
                $raw_text_clean = preg_replace('/\s+/', ' ', $raw_text); 

                $check = $pdo->prepare("SELECT id FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
                $check->execute(['uid' => $seeker_id]);
                
                if ($check->fetch()) {
                    $stmt = $pdo->prepare("UPDATE seeker_profiles SET resume_path = :path, extracted_text = :full_text, education_text = NULL, experience_text = NULL WHERE user_id = :uid");
                    $stmt->execute([
                        'path' => $fileDestination, 
                        'full_text' => $raw_text_clean,
                        'uid' => $seeker_id
                    ]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO seeker_profiles (user_id, resume_path, extracted_text, is_pwd) VALUES (:uid, :path, :full_text, 1)");
                    $stmt->execute([
                        'uid' => $seeker_id, 
                        'path' => $fileDestination, 
                        'full_text' => $raw_text_clean
                    ]);
                }
                $msg = 'upload_success';
            }
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $stmt = $pdo->prepare("SELECT resume_path FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
    $stmt->execute(['uid' => $seeker_id]);
    $res = $stmt->fetch();
    if ($res && !empty($res['resume_path'])) {
        if (file_exists($res['resume_path'])) unlink($res['resume_path']); 
        $up = $pdo->prepare("UPDATE seeker_profiles SET resume_path = NULL, extracted_text = NULL, education_text = NULL, experience_text = NULL WHERE user_id = :uid");
        $up->execute(['uid' => $seeker_id]);
        $msg = 'delete_success';
    }
}

$stmt = $pdo->prepare("SELECT resume_path, extracted_text FROM seeker_profiles WHERE user_id = :uid LIMIT 1");
$stmt->execute(['uid' => $seeker_id]);
$current_profile = $stmt->fetch();
$has_resume = ($current_profile && !empty($current_profile['resume_path']) && file_exists($current_profile['resume_path']));

$open_jobs = [];
if ($has_resume && !empty($current_profile['extracted_text'])) {
    $jobStmt = $pdo->prepare("SELECT id, job_title, job_description FROM job_listings WHERE job_status = 'open'");
    $jobStmt->execute();
    $open_jobs = $jobStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BERT-Powered Skill Matrix Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 flex flex-col overflow-y-auto">
        <main class="p-6 max-w-2xl w-full mx-auto space-y-6">

            <div id="debug-box" class="hidden"></div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                <?php if ($has_resume): ?>
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-900 text-base">BERT Match Profile</h3>
                                <p class="text-xs text-blue-600 font-medium mt-0.5"><i class="fa-solid fa-brain animate-pulse"></i> Hugging Face JS Inference: Connected</p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">Overall Match</span>
                                <span id="overall-score" class="text-2xl font-black text-blue-700">Calculating...</span>
                            </div>
                        </div>

                        <div class="border border-gray-100 p-4 rounded-xl space-y-3 bg-gradient-to-br from-gray-50 to-white">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500"><i class="fa-solid fa-network-wired text-blue-700"></i> BERT Semantic Matches</h4>
                            <div id="matches-container" class="divide-y divide-gray-100 max-h-60 overflow-y-auto space-y-1">
                                <div class="text-xs text-gray-400 py-4 text-center"><i class="fa-solid fa-spinner animate-spin"></i> Processing semantic comparison via Hugging Face...</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <a href="upload_resume.php?mode=edit" class="flex items-center justify-center gap-2 border border-gray-200 text-gray-600 font-medium p-3 rounded-xl text-xs hover:bg-gray-50"><i class="fa-solid fa-rotate"></i> Re-upload Document</a>
                            <a href="upload_resume.php?action=delete" class="flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium p-3 rounded-xl text-xs"><i class="fa-solid fa-trash-can"></i> Clear Index</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="POST" action="upload_resume.php" enctype="multipart/form-data" class="space-y-4">
                        <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-blue-700 bg-gray-50/50">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300 block mb-2"></i>
                            <input type="file" name="resume_file" required class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-50 file:text-blue-700 font-semibold cursor-pointer">
                        </div>
                        <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-medium p-3 rounded-xl text-sm shadow-sm">Upload & Map to Matrix</button>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    const resumeText = <?php echo json_encode($current_profile['extracted_text'] ?? ''); ?>;
    const jobs = <?php echo json_encode($open_jobs); ?>;
    
    const HF_TOKEN = "*************************************"; 

    async function calculateBertSimilarity(resume, jobDesc) {
        const url = "https://router.huggingface.co/hf-inference/models/sentence-transformers/all-MiniLM-L6-v2"
        
        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Authorization": `Bearer ${HF_TOKEN.trim()}`,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    inputs: {
                        source_sentence: jobDesc,
                        sentences: [resume]
                    },
                    options: {
                        wait_for_model: true
                    }
                })
            });

            if (!response.ok) {
                const errData = await response.text();
                throw new Error(`HTTP ${response.status}: ${errData}`);
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
            console.error("HF API Error:", error);
            showDebugError(error.message);
            return 0;
        }
    }

    function showDebugError(msg) {
        const debugBox = document.getElementById('debug-box');
        debugBox.className = "bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-xs space-y-1 mx-auto max-w-2xl my-3 block";
        debugBox.innerHTML = `<strong><i class="fa-solid fa-triangle-exclamation"></i> Hugging Face JS Connection Error:</strong><br>${msg}`;
    }

    async function processMatching() {
        if (!resumeText || jobs.length === 0) return;

        const container = document.getElementById('matches-container');
        const overallScoreEl = document.getElementById('overall-score');
        
        let totalScore = 0;
        let jobMatches = [];

        for (let job of jobs) {
            const jobContext = `Job Title: ${job.job_title}. Job Description: ${job.job_description}`;
            const score = await calculateBertSimilarity(resumeText, jobContext);
            
            totalScore += score;
            jobMatches.push({
                title: job.job_title,
                score: score
            });
        }

        jobMatches.sort((a, b) => b.score - a.score);

        container.innerHTML = '';
        jobMatches.forEach(match => {
            const row = document.createElement('div');
            row.className = "flex justify-between items-center py-2.5 text-xs animate-fade-in";
            row.innerHTML = `
                <span class="text-gray-700 font-semibold">${match.title}</span>
                <span class="font-bold p-1 px-2.5 rounded-lg bg-blue-50 text-blue-700">${match.score}% Match</span>
            `;
            container.appendChild(row);
        });

        const average = Math.round(totalScore / jobs.length);
        overallScoreEl.textContent = `${average}%`;
    }

    window.addEventListener('DOMContentLoaded', () => {
        if (resumeText) {
            processMatching();
        }
    });
    </script>
</body>
</html>