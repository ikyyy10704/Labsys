<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex">

        <!-- Kontainer Utama -->
        <div class="flex-1 ml-64 p-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Data Absensi</h1>
                    <a href="<?php echo site_url('absensi/tambah'); ?>" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-plus mr-2"></i>Tambah Data
                    </a>
                </div>

                <!-- Tabel Data Absensi -->
                <table class="w-full bg-white shadow-md rounded">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Absensi</th>
                            <th class="py-3 px-6 text-left">Nama Karyawan</th>
                            <th class="py-3 px-6 text-left">Tanggal</th>
                            <th class="py-3 px-6 text-left">Shift</th>
                            <th class="py-3 px-6 text-left">Keterangan</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php foreach ($absensi as $row): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left"><?php echo $row->id_absensi; ?></td>
                                <td class="py-3 px-6 text-left"><?php echo $row->nama_krywn; ?></td>
                                <td class="py-3 px-6 text-left"><?php echo $row->tanggal; ?></td>
                                <td class="py-3 px-6 text-left"><?php echo $row->shift; ?></td>
                                <td class="py-3 px-6 text-left"><?php echo $row->keterangan; ?></td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <a href="<?php echo site_url('absensi/edit/' . $row->id_absensi); ?>" class="text-blue-500 hover:text-blue-700">
                                            Edit
                                        </a>
                                        <a href="<?php echo site_url('absensi/hapus/' . $row->id_absensi); ?>" onclick="return confirm('Apakah Anda yakin?')" class="text-red-500 hover:text-red-700">
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>



