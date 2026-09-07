<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - SkillDis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4 md:p-8">

    <div class="bg-white w-full max-w-lg rounded-2xl border border-gray-100 shadow-xl p-8 space-y-6">
        
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Join SkillDis</h1>
            <p class="text-sm text-gray-500">Select your profile category to begin your registration</p>
        </div>

        <div class="grid grid-cols-3 gap-2 bg-gray-100 p-1 rounded-xl text-xs font-semibold">
            <button id="tab-seeker" onclick="switchRole('job_seeker')" class="py-2.5 rounded-lg text-center transition-all bg-white text-gray-900 shadow-sm">
                Regular Seeker
            </button>
            <button id="tab-pwd" onclick="switchRole('pwd')" class="py-2.5 rounded-lg text-center transition-all text-gray-500 hover:text-gray-900">
                PWD Seeker
            </button>
            <button id="tab-employer" onclick="switchRole('employer')" class="py-2.5 rounded-lg text-center transition-all text-gray-500 hover:text-gray-900">
                Employer
            </button>
        </div>

        <form action="process_register.php" method="POST" class="space-y-4">
            
            <input type="hidden" name="role" id="user-role" value="job_seeker">

            <div class="grid grid-cols-1 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider" id="label-name">Full Name / Primary Contact</label>
                    <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block p-3 outline-none transition-all" placeholder="John Doe">
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block p-3 outline-none transition-all" placeholder="john@example.com">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Password</label>
                        <input type="password" name="password" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block p-3 outline-none transition-all" placeholder="••••••••">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Confirm Password</label>
                        <input type="password" name="confirm_password" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block p-3 outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div id="pwd-fields" class="hidden border-t border-gray-100 pt-4 space-y-4 animate-fade-in">
                <span class="text-xs font-bold text-[#01449b] uppercase tracking-widest block"><i class="fa-solid fa-wheelchair mr-1"></i> PWD Identification Data</span>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Disability Type</label>
                        <select name="disability_type" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block p-3 outline-none transition-all">
                            <option value="">Select Type</option>
                            <option value="Visual">Visual Impairment</option>
                            <option value="Hearing">Hearing Impairment</option>
                            <option value="Mobility">Mobility Impairment</option>
                            <option value="Speech">Speech Impairment</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">PWD ID Card Number</label>
                        <input type="text" name="pwd_id_number" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block p-3 outline-none transition-all" placeholder="XX-XXXX-XXX-XXXX">
                    </div>
                </div>
            </div>

            <div id="employer-fields" class="hidden border-t border-gray-100 pt-4 space-y-4">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest block"><i class="fa-solid fa-building mr-1"></i> Corporate Operational Data</span>
                
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Registered Company Name</label>
                    <input type="text" name="company_name" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block p-3 outline-none transition-all" placeholder="Acme Corporation Ltd.">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Company Address</label>
                    <textarea name="company_address" rows="2" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-[#01449b]/20 focus:border-[#01449b] block p-3 outline-none transition-all" placeholder="Building, Street Name, Siniloan, Laguna"></textarea>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#01449b] hover:bg-blue-800 text-white font-medium p-3 rounded-xl text-sm transition-all shadow-lg shadow-blue-700/10 mt-2">
                Complete Registration
            </button>
        </form>

        <div class="text-center pt-2 border-t border-gray-100 text-sm">
            <span class="text-gray-500">Already verified?</span>
            <a href="login.php" class="font-semibold text-[#01449b] hover:underline ml-1">Log In</a>
        </div>

    </div>

    <script>
        function switchRole(role) {
            document.getElementById('user-role').value = role;

            const tabSeeker = document.getElementById('tab-seeker');
            const tabPwd = document.getElementById('tab-pwd');
            const tabEmployer = document.getElementById('tab-employer');
            const pwdFields = document.getElementById('pwd-fields');
            const employerFields = document.getElementById('employer-fields');
            const labelName = document.getElementById('label-name');

            [tabSeeker, tabPwd, tabEmployer].forEach(tab => {
                tab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                tab.classList.add('text-gray-500');
            });

            pwdFields.classList.add('hidden');
            employerFields.classList.add('hidden');

            if (role === 'job_seeker') {
                tabSeeker.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                labelName.innerText = "Full Name";
            } else if (role === 'pwd') {
                tabPwd.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                pwdFields.classList.remove('hidden');
                labelName.innerText = "Full Name (As written on PWD ID)";
            } else if (role === 'employer') {
                tabEmployer.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                employerFields.classList.remove('hidden');
                labelName.innerText = "HR / Authorized Liaison Person";
            }
        }
    </script>
</body>
</html>