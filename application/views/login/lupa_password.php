<!-- forgot_password.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prohire - Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-500 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Reset Password</h2>
        
        <?php if ($this->session->flashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline"><?php echo $this->session->flashdata('error'); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline"><?php echo $this->session->flashdata('success'); ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('user/send_reset_link') ?>" method="post">
            <div class="mb-6">
                <label for="email" class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" id="email" required
                       class="w-full p-3 border border-gray-300 rounded"
                       placeholder="Masukkan email yang terdaftar">
            </div>

            <button type="submit" 
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded">
                Kirim Link Reset Password
            </button>

            <div class="mt-4 text-center">
                <a href="<?= site_url('user/login') ?>" 
                   class="text-blue-600 hover:text-blue-800">Kembali ke halaman login</a>
            </div>
        </form>
    </div>
</body>
</html>