<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prohire - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-500 flex items-center justify-center h-screen">
    <div class="bg-white rounded-lg shadow-lg p-8 flex w-3/4 max-w-screen-md">
        <div class="w-1/2 p-8">
        <h2 class="text-3xl font-bold text-blue-700">Welcome to ProHire!</h2> <p class="text-gray-600 mt-2">Empowering your workforce management. ProHire is your ultimate solution for efficient and streamlined employee management. With our user-friendly platform, you can easily oversee employee data, track performance, and ensure seamless operations. Join us and take the first step towards a more organized and productive workplace.</p>
        </div>
        <div class="w-1/2 p-8">
            <h2 class="text-2xl font-bold text-gray-700">Sign In</h2>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo $this->session->flashdata('error'); ?></span>
                </div>
            <?php endif; ?>
            <form action="<?= site_url('user/login_process') ?>" method="post" class="mt-4">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded mt-1">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Password</label>
                    <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded mt-1">
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox">
                        <span class="ml-2">Keep me logged in</span>
                    </label>
                    <a href="#" class="text-sm text-purple-600 hover:text-blue-800">Forgot Password?</a>
                </div>
                <button type="submit" class="mt-4 w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">Sign In</button>
                <div class="mt-4 text-center">
                    <p>Or, Use social media to sign in</p>
                    <div class="flex justify-center mt-2">
                        <a href="#" class="text-blue-600 mx-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-blue-400 mx-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-blue-700 mx-2"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
