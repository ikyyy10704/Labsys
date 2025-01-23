<!-- application/views/data_karyawan/create.php -->
<div class="ml-64 p-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Tambah Data Karyawan</h1>
        </div>

        <?php if(validation_errors()): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <?= validation_errors() ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('data_karyawan/create') ?>" method="post" class="space-y-6">
            <!-- ID Karyawan (Auto Generate) -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">ID Karyawan</label>
                <div class="col-span-2">
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" 
                           value="<?= $next_id ?>" readonly>
                    <p class="mt-1 text-sm text-gray-500">ID generated automatically</p>
                </div>
            </div>

            <!-- Nama -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Nama Karyawan</label>
                <div class="col-span-2">
                    <input type="text" name="nama_krywn" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <!-- Jenis Kelamin -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Jenis Kelamin</label>
                <div class="col-span-2">
                    <select name="jenis_kelamin" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Alamat -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Alamat</label>
                <div class="col-span-2">
                    <textarea name="alamat" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <!-- Email -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Email</label>
                <div class="col-span-2">
                    <input type="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <!-- Status -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Status</label>
                <div class="col-span-2">
                    <select name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Pilih Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Posisi -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Posisi</label>
                <div class="col-span-2">
                    <select name="posisi" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Pilih Posisi</option>
                        <option value="Manager">Manager</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Staff">Staff</option>
                        <option value="Admin">Admin</option>
                        <option value="Teknisi">Teknisi</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Operator">Operator</option>
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