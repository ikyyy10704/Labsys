<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah Data Absensi</h1>
        <form action="" method="post" class="space-y-4">
            <div>
                <label for="id_krywn" class="block text-gray-700 font-medium">ID Karyawan:</label>
                <select id="id_krywn" name="id_krywn" required 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Karyawan</option>
                    <?php foreach ($karyawan as $krywn): ?>
                        <option value="<?= $krywn->id_krywn ?>"><?= $krywn->nama_krywn ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="tanggal" class="block text-gray-700 font-medium">Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" required 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="shift" class="block text-gray-700 font-medium">Shift:</label>
                <input type="text" id="shift" name="shift" required 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="keterangan" class="block text-gray-700 font-medium">Keterangan:</label>
                <input type="text" id="keterangan" name="keterangan" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="text-center">
                <button type="submit" 
                    class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</body>
</html>
