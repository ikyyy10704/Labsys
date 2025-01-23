<!-- register.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prohire - Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-blue-500 flex items-center justify-center min-h-screen py-6">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Registrasi Akun Baru</h2>
        
        <?php if ($this->session->flashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline"><?php echo $this->session->flashdata('error'); ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('user/register_process') ?>" method="post">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" id="username" required
                       class="w-full p-2 border border-gray-300 rounded mt-1">
            </div>
            
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
            
            <div class="mb-4">
                <label for="confirm_password" class="block text-gray-700">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" name="confirm_password" id="confirm_password" required
                           class="w-full p-2 border border-gray-300 rounded mt-1">
                    <button type="button" onclick="togglePassword('confirm_password')"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">
                Daftar
            </button>

            <div class="mt-4 text-center">
                <p class="text-gray-600">Sudah punya akun? 
                    <a href="<?= site_url('user/login') ?>" class="text-blue-600 hover:text-blue-800">Masuk</a>
                </p>
            </div>
        </form>
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