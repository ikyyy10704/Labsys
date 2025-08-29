<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'med-blue': '#1E40AF',
                        'med-light-blue': '#3B82F6',
                        'med-gray': '#6B7280'
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        .login-bg {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 50%, #60A5FA 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .slide-in {
            animation: slideIn 0.8s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">

    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white opacity-10 rounded-full floating-animation"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white opacity-5 rounded-full floating-animation" style="animation-delay: -2s;"></div>
        <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white opacity-10 rounded-full floating-animation" style="animation-delay: -4s;"></div>
    </div>

    <!-- Login Container -->
    <div class="relative z-10 w-full max-w-md mx-auto slide-in">
        
        <!-- Header -->
       <div class="text-center mb-8">
    <div class="flex justify-center">
        <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
            <img src="<?= base_url('assets/logo/logo.png') ?>" 
                 alt="Hospital Logo" 
                 class="w-12 h-12 object-contain">
        </div>
    </div>
    <h1 class="text-4xl font-bold text-white mb-2">Labsys System</h1>
    <p class="text-blue-100">Hospital Information System</p>
</div>

        <!-- Login Form -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Welcome Back</h2>
                <p class="text-gray-600">Please sign in to your account</p>
            </div>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center space-x-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                <span class="text-red-700"><?= $error ?></span>
            </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (isset($success)): ?>
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                <span class="text-green-700"><?= $success ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('auth/login') ?>" class="space-y-6" id="loginForm">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="user" class="w-4 h-4 inline mr-1"></i>
                        Username
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           required 
                           value="<?= set_value('username') ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-med-blue focus:border-transparent transition-all duration-200 bg-white"
                           placeholder="Enter your username">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="lock" class="w-4 h-4 inline mr-1"></i>
                        Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-med-blue focus:border-transparent transition-all duration-200 bg-white pr-12"
                               placeholder="Enter your password">
                        <button type="button" 
                                onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                            <i data-lucide="eye" id="eyeIcon" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" 
                        id="loginBtn"
                        class="w-full bg-med-blue text-white py-3 px-4 rounded-lg hover:bg-med-light-blue focus:ring-4 focus:ring-blue-200 transition-all duration-200 font-semibold flex items-center justify-center space-x-2">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    <span>Sign In</span>
                </button>