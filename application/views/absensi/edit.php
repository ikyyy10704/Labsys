<!-- application/views/absensi/edit.php -->
<div class="ml-64 p-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Edit Data Absensi</h1>
        </div>

        <?php if(validation_errors()): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <?= validation_errors() ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="space-y-6">
            <!-- Display selected employee name (read-only) -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Nama Karyawan</label>
                <div class="col-span-2">
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" 
                           value="<?= $absensi->nama_krywn ?>" readonly>
                    <input type="hidden" name="id_krywn" value="<?= $absensi->id_krywn ?>">
                </div>
            </div>

            <!-- Tanggal -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Tanggal</label>
                <div class="col-span-2">
                    <input type="date" name="tanggal" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                           value="<?= $absensi->tanggal ?>">
                </div>
            </div>

            <!-- Shift -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Shift</label>
                <div class="col-span-2">
                    <select name="shift" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="Pagi" <?= $absensi->shift == 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                        <option value="Siang" <?= $absensi->shift == 'Siang' ? 'selected' : '' ?>>Siang</option>
                        <option value="Malam" <?= $absensi->shift == 'Malam' ? 'selected' : '' ?>>Malam</option>
                    </select>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Keterangan</label>
                <div class="col-span-2">
                    <select name="keterangan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="Hadir" <?= $absensi->keterangan == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                        <option value="Sakit" <?= $absensi->keterangan == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                        <option value="Tidak Hadir" <?= $absensi->keterangan == 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="<?= base_url('index.php/absensi') ?>" 
                   class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>