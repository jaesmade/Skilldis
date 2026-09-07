<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillDis - Inclusive Job Matching Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3 tracking-wide">
                <div class="w-10 h-10 rounded-xl bg-[#01449b] text-white flex items-center justify-center text-xl shadow-md shadow-blue-700/20">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">Skill<span class="text-[#01449b]">Dis</span></span>
            </a>

            <div class="flex items-center gap-6">
                <a href="#features" class="text-sm font-medium text-gray-500 hover:text-[#01449b] transition-colors">Features</a>
                <a href="#sectors" class="text-sm font-medium text-gray-500 hover:text-[#01449b] transition-colors">Portals</a>
                <a href="login.php" class="text-sm font-semibold text-gray-700 hover:text-[#01449b] transition-colors">Sign In</a>
                <a href="login.php" class="bg-[#01449b] hover:bg-blue-800 text-white font-medium px-5 py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-blue-700/10">
                    Get Started
                </a>
            </div>
        </div>
    </header>

    <section class="max-w-7xl mx-auto px-6 pt-16 pb-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6 max-w-xl">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-[#01449b] text-xs font-semibold uppercase tracking-wider rounded-md">
                <i class="fa-solid fa-sparkles"></i> AI-Powered Opportunity
            </span>
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 tracking-tight leading-tight">
                Bridging abilities with the right <span class="text-[#01449b]">opportunities</span>.
            </h1>
            <p class="text-gray-500 text-base sm:text-lg leading-relaxed">
                SkillDis is an intelligent, inclusive job matching platform designed to connect regular workers and Persons with Disabilities (PWDs) to equal employment opportunities through context-aware text matching.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <a href="login.php" class="bg-[#01449b] hover:bg-blue-800 text-white font-medium px-6 py-3.5 rounded-xl text-center text-sm transition-all shadow-xl shadow-blue-700/10">
                    Find a Job Today
                </a>
                <a href="login.php" class="border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium px-6 py-3.5 rounded-xl text-center text-sm transition-all">
                    Post a Job Opening
                </a>
            </div>
        </div>
        
        <div class="relative flex justify-center lg:justify-end">
            <div class="w-full max-w-md bg-white p-6 rounded-2xl border border-gray-100 shadow-2xl space-y-6 relative z-10">
                <div class="flex items-center justify-between border-b border-gray-50 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                    </div>
                    <span class="text-xs text-gray-400 font-mono">matching_engine.sys</span>
                </div>
                <div class="space-y-3">
                    <div class="h-4 bg-gray-100 rounded-md w-3/4 animate-pulse"></div>
                    <div class="h-4 bg-gray-100 rounded-md w-1/2 animate-pulse"></div>
                </div>
                <div class="p-4 bg-blue-50/60 border border-blue-100/50 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-nodes text-[#01449b] text-lg"></i>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Semantic Text Score</p>
                            <p class="text-sm font-bold text-gray-800">Resume & Job Alignment</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-[#01449b] bg-white px-2.5 py-1 rounded-lg border border-blue-100">94.8%</span>
                </div>
            </div>
            <div class="absolute -bottom-6 -left-6 w-48 h-48 bg-blue-100/40 rounded-full blur-2xl z-0"></div>
        </section>

    <section id="sectors" class="bg-white border-t border-b border-gray-100 py-20">
        <div class="max-w-7xl mx-auto px-6 space-y-12">
            <div class="text-center max-w-xl mx-auto space-y-3">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">Tailored Portals for Every User</h2>
                <p class="text-sm text-gray-500">Access specialized workflows depending on your objective and profile status.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="border border-gray-100 rounded-2xl p-6 space-y-4 hover:shadow-xl hover:border-transparent transition-all group bg-gray-50/30">
                    <div class="w-12 h-12 rounded-xl bg-gray-100 group-hover:bg-[#01449b] group-hover:text-white text-gray-700 flex items-center justify-center text-xl transition-all">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Regular Employee Access</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Standard access framework for general applicants. Build your digital industry profile, upload structured resumes, and search targeted open positions.
                    </p>
                </div>

                <div class="border border-gray-100 rounded-2xl p-6 space-y-4 hover:shadow-xl hover:border-transparent transition-all group bg-gray-50/30">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 group-hover:bg-[#01449b] group-hover:text-white text-[#01449b] flex items-center justify-center text-xl transition-all">
                        <i class="fa-solid fa-wheelchair"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">PWD Special Access</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Exclusive portal featuring intelligent AI similarity matching, system assistive layout adjustments, and customized workplace accessibility filtering options.
                    </p>
                </div>

                <div class="border border-gray-100 rounded-2xl p-6 space-y-4 hover:shadow-xl hover:border-transparent transition-all group bg-gray-50/30">
                    <div class="w-12 h-12 rounded-xl bg-gray-100 group-hover:bg-[#01449b] group-hover:text-white text-gray-700 flex items-center justify-center text-xl transition-all">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Enterprise & Employer</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Post job criteria metrics along with specific accessibility accommodations to instantly match profiles using contextual skill matrices.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="max-w-7xl mx-auto px-6 py-20 space-y-12">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="space-y-3 max-w-xl">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">System Integration</span>
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">Engineered for Equal Recruitment</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-5 bg-white border border-gray-100 rounded-2xl shadow-sm space-y-3">
                <i class="fa-solid fa-file-invoice text-xl text-[#01449b]"></i>
                <h4 class="font-bold text-gray-800 text-sm">Resume Data Parsing</h4>
                <p class="text-xs text-gray-500 leading-relaxed">Transforms raw uploaded files into operational semantic datasets dynamically.</p>
            </div>
            <div class="p-5 bg-white border border-gray-100 rounded-2xl shadow-sm space-y-3">
                <i class="fa-solid fa-chart-simple text-xl text-[#01449b]"></i>
                <h4 class="font-bold text-gray-800 text-sm">Similarity Score Model</h4>
                <p class="text-xs text-gray-500 leading-relaxed">Evaluates semantic proximity values to rank alignment metrics objectively.</p>
            </div>
            <div class="p-5 bg-white border border-gray-100 rounded-2xl shadow-sm space-y-3">
                <i class="fa-solid fa-universal-access text-xl text-[#01449b]"></i>
                <h4 class="font-bold text-gray-800 text-sm">WCAG UI Standards</h4>
                <p class="text-xs text-gray-500 leading-relaxed">High contrast typography layout explicitly balanced for robust compatibility interfaces.</p>
            </div>
            <div class="p-5 bg-white border border-gray-100 rounded-2xl shadow-sm space-y-3">
                <i class="fa-solid fa-shield-halved text-xl text-[#01449b]"></i>
                <h4 class="font-bold text-gray-800 text-sm">Verified Credentials</h4>
                <p class="text-xs text-gray-500 leading-relaxed">Protects matching ecosystems through administrative institutional account approval validations.</p>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 border-t border-gray-800 py-12">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-6 text-sm">
            <p>&copy; 2026 SkillDis. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

</body>
</html>