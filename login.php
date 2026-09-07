<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - SkillDis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white w-full max-w-md rounded-2xl border border-gray-100 shadow-xl overflow-hidden p-8 space-y-6">
        
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-[#01449b] text-white text-2xl shadow-md shadow-blue-700/20">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">SkillDis</h1>
            <p class="text-sm text-gray-500">Inclusive Job Matching Platform</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="p-3 bg-red-50 border border-red-100 text-red-600 text-xs font-medium rounded-xl flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-sm"></i>
                <span>
                    <?php 
                        if ($_GET['error'] == 'wrong_credentials') echo "Invalid email or password.";
                        elseif ($_GET['error'] == 'invalid_role') echo "Account configuration error. Contact admin.";
                        else echo "An error occurred. Please try again.";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <form action="process_login.php" method="POST" class="space-y-4">
            
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 pointer-events-none">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block pl-10 p-3 outline-none transition-all" placeholder="name@company.com">
                </div>
            </div>

            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Password</label>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 pointer-events-none">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block pl-10 p-3 outline-none transition-all" placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="w-full bg-[#01449b] hover:bg-blue-800 text-white font-medium p-3 rounded-xl text-sm transition-all shadow-lg shadow-blue-700/10 active:scale-[0.98]">
                Sign In
            </button>
        </form>

        <div class="text-center pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Don't have an account yet?</p>
            <a href="register.php" class="mt-2 text-sm font-semibold text-[#01449b] hover:underline inline-block">
                Create an Account
            </a>
        </div>

    </div>

</body>
</html>