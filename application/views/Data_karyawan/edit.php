<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Edit Karyawan' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <div class="container mx-auto mt-8 p-4">
                <h3 class="text-2xl font-semibold mb-4">Edit Karyawan</h3>
                
                <form action="process_edit_karyawan.php" method="POST">
                    <input type="hidden" name="id_krywn" value="<?= htmlspecialchars($karyawan['id_krywn'] ?? '') ?>">

                    <div class="form-group mb-4">
                        <label class="block">Nama Karyawan</label>
                        <input type="text" name="nama_krywn" class="form-control w-full px-4 py-2 border rounded" 
                               value="<?= htmlspecialchars($karyawan['nama_krywn'] ?? '') ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="block">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control w-full px-4 py-2 border rounded" required>
                            <option value="L" <?= (isset($karyawan['jenis_kelamin']) && $karyawan['jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-Laki</option>
                            <option value="P" <?= (isset($karyawan['jenis_kelamin']) && $karyawan['jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="block">Alamat</label>
                        <textarea name="alamat" class="form-control w-full px-4 py-2 border rounded" required><?= htmlspecialchars($karyawan['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="block">Email</label>
                        <input type="email" name="email" class="form-control w-full px-4 py-2 border rounded" 
                               value="<?= htmlspecialchars($karyawan['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="block">Status</label>
                        <input type="text" name="status" class="form-control w-full px-4 py-2 border rounded" 
                               value="<?= htmlspecialchars($karyawan['status'] ?? '') ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="block">Posisi</label>
                        <input type="text" name="posisi" class="form-control w-full px-4 py-2 border rounded" 
                               value="<?= htmlspecialchars($karyawan['posisi'] ?? '') ?>" required>
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">Update</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
