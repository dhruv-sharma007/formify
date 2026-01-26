<?php
session_start();
$pageTitle = "Build Forms Effortlessly";
$isLoggedIn = false;
if (isset($_SESSION['logged_in'])) {
    $isLoggedIn = true;
}
require_once __DIR__ . '/layouts/header.php';
?>


<!-- Hero -->
<section class="relative pt-20 pb-32 overflow-hidden text-center min-h-[90vh] flex flex-col justify-center">
    <div class="absolute inset-0 overflow-hidden z-0 pointer-events-none">
        <div
            class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-600/20 blur-[100px] animate-float">
        </div>
        <div class="absolute top-[40%] -right-[10%] w-[40%] h-[60%] rounded-full bg-purple-600/20 blur-[120px] animate-float"
            style="animation-delay: -2s;"></div>
        <div class="absolute -bottom-[20%] left-[20%] w-[30%] h-[50%] rounded-full bg-pink-600/20 blur-[100px] animate-float"
            style="animation-delay: -4s;"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto px-6">
        <div
            class="inline-block mb-6 px-4 py-1.5 rounded-full border border-white/10 bg-white/5 backdrop-blur-md text-sm font-medium text-blue-300 animate-slide-up">
            ✨ Redefining Data Collection
        </div>

        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 animate-slide-up-delay-1 leading-tight">
            Build Forms. <br class="hidden md:block" />
            <span class="text-gradient">Collect & Analyze.</span>
        </h1>

        <p
            class="mt-6 text-lg md:text-xl text-gray-400 max-w-2xl mx-auto mb-10 animate-slide-up-delay-2 leading-relaxed">
            A modern, self-hosted alternative to Google Forms.
            Create beautiful forms, collect responses securely, and gain actionable insights in minutes.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4 animate-slide-up-delay-2">
            <a href="register.php"
                class="btn btn-primary btn-lg border-none bg-gradient-to-r from-blue-600 to-purple-600 hover:shadow-lg hover:shadow-blue-500/40 text-white transition-all transform hover:scale-105">

                <?php if ($isLoggedIn): ?>
                    Dashboard
                <?php else: ?>
                    Create Free Account
                <?php endif; ?>
            </a>
            <a href="login.php"
                class="btn btn-outline btn-lg text-white border-white/20 hover:bg-white/10 hover:border-white/40 transition-all">
                View Demo
            </a>
        </div>

        <!-- Dashboard Preview/Abstract Graphic -->
        <div class="mt-20 relative mx-auto max-w-4xl animate-slide-up-delay-2">
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-30">
            </div>
            <div class="relative rounded-2xl border border-white/10 bg-[#1e293b]/80 backdrop-blur-xl p-4 shadow-2xl">
                <div class="flex items-center gap-2 mb-4 px-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/50"></div>
                </div>
                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 h-[200px] md:h-[300px] items-center justify-center text-center opacity-50">
                    <div class="h-32 rounded-lg bg-white/5 flex items-center justify-center">
                        <span class="text-4xl">📊</span>
                    </div>
                    <div class="h-32 rounded-lg bg-white/5 flex items-center justify-center">
                        <span class="text-4xl">📝</span>
                    </div>
                    <div class="h-32 rounded-lg bg-white/5 flex items-center justify-center">
                        <span class="text-4xl">📈</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-24 relative z-10">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4">Everything you need</h2>4
            <p class="text-gray-400 max-w-2xl mx-auto">Powerful features to help you build better forms and make
                data-driven decisions.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="card glass-effect hover:bg-white/5 transition-all duration-300 card-hover-effect">
                <div class="card-body">
                    <div
                        class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center mb-4 text-blue-400 text-2xl">
                        <i class="ri-layout-masonry-line"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-2 text-white">Drag & Drop Builder</h3>
                    <p class="text-gray-400">
                        Intuitively design forms with our drag-and-drop interface. Support for multiple question types
                        and conditional logic.
                    </p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="card glass-effect hover:bg-white/5 transition-all duration-300 card-hover-effect">
                <div class="card-body">
                    <div
                        class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center mb-4 text-purple-400 text-2xl">
                        <i class="ri-bar-chart-groupped-line"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-2 text-white">Real-time Analytics</h3>
                    <p class="text-gray-400">
                        Visualize responses instantly. Track completion rates, analyze trends, and export data with a
                        single click.
                    </p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="card glass-effect hover:bg-white/5 transition-all duration-300 card-hover-effect">
                <div class="card-body">
                    <div
                        class="w-12 h-12 rounded-xl bg-pink-500/20 flex items-center justify-center mb-4 text-pink-400 text-2xl">
                        <i class="ri-moon-line"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-2 text-white">Dark Mode First</h3>
                    <p class="text-gray-400">
                        Built with a modern aesthetic in mind. Easy on the eyes and perfect for professional
                        environments.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-20 relative">
    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/20 to-transparent pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl font-bold mb-6">Ready to get started?</h2>
        <p class="text-xl text-gray-400 mb-10">Join thousands of users building better forms today.</p>
        <a href="register.php"
            class="btn btn-primary btn-lg bg-white text-blue-900 hover:bg-gray-100 border-none font-bold px-10">
            Start Building Free
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>