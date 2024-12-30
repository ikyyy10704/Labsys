<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center h-screen">
        <div class="w-full max-w-sm p-6 bg-white rounded-lg shadow-md">
            <h2 class="mb-6 text-2xl font-bold text-center text-gray-700">Login</h2>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-4 text-sm text-red-500"><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>
            <form action="<?= site_url('auth/login'); ?>" method="post">
                <div class="mb-4">
                    <label for="username" class="block mb-1 text-sm font-medium text-gray-600">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter your username" 
                        class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="mb-4">
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-600">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" 
                        class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <button type="submit" 
                    class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
