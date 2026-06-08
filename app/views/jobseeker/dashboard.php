<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - S.I.K.A.P. Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#7C3AED',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .glass-search { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .job-card { transition: all 0.3s ease; }
        .job-card:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.05); border-color: #C7D2FE; }
        .gradient-text { background: linear-gradient(135deg, #4F46E5, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col">

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center shadow-md">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight">S.I.K.A.P. <span class="text-primary">Hub</span></span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="#" class="text-sm font-semibold text-slate-500 hover:text-primary transition-colors">Find Jobs</a>
                    <a href="#" class="text-sm font-semibold text-slate-500 hover:text-primary transition-colors">My Applications</a>
                    <div class="w-9 h-9 rounded-full bg-slate-200 border-2 border-primary overflow-hidden cursor-pointer">
                        <img src="https://ui-avatars.com/api/?name=User&background=C7D2FE&color=4F46E5" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow pb-16">
        
        <div class="bg-gradient-to-br from-indigo-900 via-primary to-secondary pt-16 pb-24 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
            
            <div class="max-w-5xl mx-auto relative z-10 text-center">
                <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">Find your next great opportunity</h1>
                <p class="text-indigo-100 text-lg mb-8 max-w-2xl mx-auto">Our AI analyzes your profile to surface the most relevant jobs in Nueva Ecija and beyond.</p>
                
                <form class="glass-search p-2 sm:p-3 rounded-2xl shadow-2xl flex flex-col sm:flex-row gap-2 max-w-4xl mx-auto border border-white/20">
                    <div class="flex-1 flex items-center bg-white rounded-xl px-4 py-3 border border-slate-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
                        <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input type="text" placeholder="Job title, skills, or company" class="w-full bg-transparent border-none outline-none text-slate-800 placeholder-slate-400 text-sm font-medium">
                    </div>
                    <div class="hidden sm:block w-px bg-slate-200 mx-1"></div>
                    <div class="flex-1 flex items-center bg-white rounded-xl px-4 py-3 border border-slate-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
                        <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        <input type="text" placeholder="City, province, or region" class="w-full bg-transparent border-none outline-none text-slate-800 placeholder-slate-400 text-sm font-medium">
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-500 hover:to-violet-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 whitespace-nowrap">
                        Search Jobs
                    </button>
                </form>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-xl font-bold text-slate-800">Recommended for You</h2>
                        <span class="text-sm font-medium text-slate-500 bg-white px-3 py-1 rounded-full shadow-sm border border-slate-200">Based on your AI Profile</span>
                    </div>

                    <div class="job-card bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-6 relative">
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-primary transition-colors cursor-pointer">Senior Web Developer</h3>
                                    <p class="text-sm font-semibold text-primary">TechNova Solutions</p>
                                </div>
                                <div class="flex flex-col items-center justify-center bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2 min-w-[80px]">
                                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-1">Match</span>
                                    <span class="text-xl font-extrabold text-indigo-700">85%</span>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mb-4 mt-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    Cabanatuan City
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255v2.257a2.25 2.25 0 01-2.25 2.25h-13.5a2.25 2.25 0 01-2.25-2.25v-2.257M6.75 7.5a3 3 0 116 0m-6 0a3 3 0 106 0m-6 0H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25h16.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25h-3"/></svg>
                                    Hybrid
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    ₱ 45,000 - 60,000
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-100">
                                <span class="text-xs font-medium text-slate-400">Posted 2 days ago</span>
                                <button class="text-sm font-bold text-white bg-slate-900 hover:bg-primary px-6 py-2 rounded-lg transition-colors">View Job</button>
                            </div>
                        </div>
                    </div>

                    <div class="job-card bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-6 relative">
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-primary transition-colors cursor-pointer">Junior UI/UX Designer</h3>
                                    <p class="text-sm font-semibold text-primary">Creative Labs</p>
                                </div>
                                <div class="flex flex-col items-center justify-center bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2 min-w-[80px]">
                                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-1">Match</span>
                                    <span class="text-xl font-extrabold text-indigo-700">78%</span>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mb-4 mt-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    Makati City
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255v2.257a2.25 2.25 0 01-2.25 2.25h-13.5a2.25 2.25 0 01-2.25-2.25v-2.257M6.75 7.5a3 3 0 116 0m-6 0a3 3 0 106 0m-6 0H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25h16.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25h-3"/></svg>
                                    Remote
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    ₱ 35,000 - 45,000
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-100">
                                <span class="text-xs font-medium text-slate-400">Posted 5 hours ago</span>
                                <button class="text-sm font-bold text-white bg-slate-900 hover:bg-primary px-6 py-2 rounded-lg transition-colors">View Job</button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        
                        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 rounded-full bg-slate-100 border-4 border-white shadow-md overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name=User&background=C7D2FE&color=4F46E5" alt="Avatar" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">Your Profile</h3>
                                    <p class="text-sm text-slate-500">Job Seeker</p>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm font-bold text-slate-700">Profile Strength</span>
                                    <span class="text-sm font-extrabold text-emerald-600">Excellent</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 mb-1 overflow-hidden">
                                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-500 h-2.5 rounded-full" style="width: 90%"></div>
                                </div>
                                <p class="text-xs text-slate-400 mt-2">Your profile is highly visible to employers.</p>
                            </div>

                            <div class="bg-slate-50 rounded-xl p-4 mb-6 border border-slate-100">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm font-medium text-slate-600 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Visibility
                                    </span>
                                    <span class="text-xs font-bold text-slate-800 bg-white px-2 py-1 rounded shadow-sm border border-slate-200">Public</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-slate-600 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                        Saved Jobs
                                    </span>
                                    <span class="text-sm font-bold text-slate-800">0</span>
                                </div>
                            </div>

                            <a href="/sikaphub_v2/build-profile" class="block w-full text-center py-3 rounded-xl border-2 border-slate-200 text-slate-600 font-bold hover:border-primary hover:text-primary hover:bg-indigo-50 transition-all">
                                Edit Profile
                            </a>
                        </div>

                    </div>
                </div>
                
            </div>
        </div>
    </main>
</body>
</html>