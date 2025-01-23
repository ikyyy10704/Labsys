<!-- application/views/data_karyawan/edit.php -->
<div class="ml-64 p-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Edit Data Karyawan</h1>
        </div>

        <?php if(validation_errors()): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <?= validation_errors() ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="space-y-6">
            <!-- ID Karyawan (Read Only) -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">ID Karyawan</label>
                <div class="col-span-2">
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" 
                           value="<?= $karyawan['id_krywn'] ?>" readonly>
                </div>
            </div>

            <!-- Nama -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Nama Karyawan</label>
                <div class="col-span-2">
                    <input type="text" name="nama_krywn" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                           value="<?= htmlspecialchars($karyawan['nama_krywn']) ?>">
                </div>
            </div>

            <!-- Jenis Kelamin -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Jenis Kelamin</label>
                <div class="col-span-2">
                    <select name="jenis_kelamin" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="L" <?= $karyawan['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-Laki</option>
                        <option value="P" <?= $karyawan['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Alamat -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Alamat</label>
                <div class="col-span-2">
                    <textarea name="alamat" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"><?= htmlspecialchars($karyawan['alamat']) ?></textarea>
                </div>
            </div>

            <!-- Email -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Email</label>
                <div class="col-span-2">
                    <input type="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                           value="<?= htmlspecialchars($karyawan['email']) ?>">
                </div>
            </div>

            <!-- Status -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Status</label>
                <div class="col-span-2">
                    <select name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="Aktif" <?= $karyawan['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Tidak Aktif" <?= $karyawan['status'] == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Posisi -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Posisi</label>
                <div class="col-span-2">
                    <select name="posisi" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="Manager" <?= $karyawan['posisi'] == 'Manager' ? 'selected' : '' ?>>Manager</option>
                        <option value="Supervisor" <?= $karyawan['posisi'] == 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
                        <option value="Staff" <?= $karyawan['posisi'] == 'Staff' ? 'selected' : '' ?>>Staff</option>
                        <option value="Admin" <?= $karyawan['posisi'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="Teknisi" <?= $karyawan['posisi'] == 'Teknisi' ? 'selected' : '' ?>>Teknisi</option>
                        <option value="Marketing" <?= $karyawan['posisi'] == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                        <option value="Operator" <?= $karyawan['posisi'] == 'Operator' ? 'selected' : '' ?>>Operator</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="<?= base_url('index.php/data_karyawan') ?>" 
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