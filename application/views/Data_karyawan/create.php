<!-- data_karyawan/create.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
  
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md h-screen">
            <div class="p-4">
                <img src="logo.png" alt="Logo" class="w-16 h-16 mx-auto">
            </div>
            <nav>
                <ul class="space-y-4">
                    <li><a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-200"><i class="fas fa-home mr-3"></i>Beranda</a></li>
                    <li><a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-200"><i class="fas fa-tasks mr-3"></i>Programs</a></li>
                    <li><a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-200"><i class="fas fa-user mr-3"></i>Profile</a></li>
                    <li><a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-200"><i class="fas fa-info-circle mr-3"></i>About Us</a></li>
                </ul>
            </nav>
        </aside>
        <main class="flex-1 p-6">
            <div class="container mx-auto mt-8">
                <h2 class="text-2xl font-bold"><?= $title ?></h2>
                <?= form_open('data_karyawan/create') ?>
                <div class="mt-4">
                        <label for="id_krywn" class="block text-sm font-medium text-gray-700">Id Karyawan</label>
                        <input type="text" name="id_krywn" class="form-input mt-1 block w-full" required>
                    </div>
                    <div class="mt-4">
                        <label for="nama_krywn" class="block text-sm font-medium text-gray-700">Nama Karyawan</label>
                        <input type="text" name="nama_krywn" class="form-input mt-1 block w-full" required>
                    </div>
                    <div class="mt-4">
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select mt-1 block w-full" required>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="alamat" class="form-textarea mt-1 block w-full" required></textarea>
                    </div>
                    <div class="mt-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" class="form-input mt-1 block w-full" required>
                    </div>
                    <div class="mt-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <input type="text" name="status" class="form-input mt-1 block w-full" required>
                    </div>
                    <div class="mt-4">
                        <label for="posisi" class="block text-sm font-medium text-gray-700">Posisi</label>
                        <input type="text" name="posisi" class="form-input mt-1 block w-full" required>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg flex justify-end space-x-4">
                        <a href="<?= base_url('index.php/Data_karyawan') ?>" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Simpan
                        </button>
                    </div>
                <?= form_close() ?>
            </div>
        </main>
    </div>
</div>
</body>
</html>
