<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prohire - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-blue-500 flex items-center justify-center min-h-screen py-6">
    <div class="bg-white rounded-lg shadow-lg p-8 flex w-3/4 max-w-screen-md">
        <div class="w-1/2 p-8">
            <h2 class="text-3xl font-bold text-blue-700">Welcome to ProHire!</h2>
            <p class="text-gray-600 mt-2">Lengkapi data diri Anda untuk meningkatkan pengalaman manajemen karyawan yang lebih baik.</p>
        </div>
        <div class="w-1/2 p-8">
            <h2 class="text-2xl font-bold text-gray-700">Masuk</h2>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4">
                    <span class="block sm:inline"><?php echo $this->session->flashdata('error'); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($this->session->flashdata('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4">
                    <span class="block sm:inline"><?php echo $this->session->flashdata('success'); ?></span>
                </div>
            <?php endif; ?>
            
            <form action="<?= site_url('user/login_process') ?>" method="post" class="mt-4">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required 
                           class="w-full p-2 border border-gray-300 rounded mt-1">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               class="w-full p-2 border border-gray-300 rounded mt-1">
                        <button type="button" onclick="togglePassword('password')"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="remember">
                        <span class="ml-2">Ingat saya</span>
                    </label>
                    <a href="<?= site_url('user/forgot_password') ?>" 
                       class="text-sm text-blue-600 hover:text-blue-800">Lupa Password?</a>
                </div>
                <button type="submit" class="mt-4 w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">
                    Masuk
                </button>
                <div class="mt-4 text-center">
                    <p class="text-gray-600">Belum punya akun?
                        <a href="<?= site_url('user/register') ?>" class="text-blue-600 hover:text-blue-800">Daftar</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
            
            const icon = event.currentTarget.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
</body>
</html>