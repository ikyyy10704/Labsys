<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
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
        <a href="<?= site_url('data_karyawan/create') ?>" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mb-4 inline-block">Tambah Karyawan</a>
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2">ID Karyawan</th>
                        <th class="border border-gray-300 px-4 py-2">Nama Karyawan</th>
                        <th class="border border-gray-300 px-4 py-2">Jenis Kelamin</th>
                        <th class="border border-gray-300 px-4 py-2">Alamat</th>
                        <th class="border border-gray-300 px-4 py-2">Email</th>
                        <th class="border border-gray-300 px-4 py-2">Status</th>
                        <th class="border border-gray-300 px-4 py-2">Posisi</th>
                        <th class="border border-gray-300 px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($karyawan as $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2 text-center"><?= $row['id_krywn'] ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= $row['nama_krywn'] ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-center"><?= $row['jenis_kelamin'] == 'L' ? 'Laki-Laki' : 'Perempuan' ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= $row['alamat'] ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= $row['email'] ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= $row['status'] ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= $row['posisi'] ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <a href="<?= site_url('Data_karyawan/edit/' . $row['id_krywn']) ?>" class='text-blue-500 hover:text-blue-700'><i class='fas fa-edit'></i></a>
                                <a href="<?= site_url('Data_karyawan/delete/' . $row['id_krywn']) ?>" class='text-red-500 hover:text-red-700 ml-3'><i class='fas fa-trash' onclick="return confirm('Yakin ingin menghapus?')"></i></a>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
