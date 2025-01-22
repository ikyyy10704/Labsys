<!-- application/views/manajer/create.php -->
<div class="ml-64 p-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Tambah Data Manajer</h1>
        </div>

        <?php if(validation_errors()): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <?= validation_errors() ?>
            </div>
        <?php endif; ?>

        <?= form_open('manajer/create', ['class' => 'space-y-6']) ?>
            <!-- ID Manajer -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">ID Manajer</label>
                <div class="col-span-2">
                    <input type="text" name="id_manajer" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                           value="<?= set_value('id_manajer') ?>"
                           placeholder="Auto Generated">
                    <p class="mt-1 text-sm text-gray-500">ID will be generated automatically</p>
                </div>
            </div>

            <!-- Nama Manajer -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Nama Manajer</label>
                <div class="col-span-2">
                    <input type="text" name="nama_manajer" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                           value="<?= set_value('nama_manajer') ?>">
                </div>
            </div>

            <!-- Username -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Username</label>
                <div class="col-span-2">
                    <input type="text" name="username" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                           value="<?= set_value('username') ?>">
                </div>
            </div>

            <!-- Email -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Email</label>
                <div class="col-span-2">
                    <input type="email" name="email" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                           value="<?= set_value('email') ?>">
                </div>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Password</label>
                <div class="col-span-2">
                    <input type="password" name="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <div class="col-span-2">
                    <input type="password" name="confirm_password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <!-- Departemen -->
            <div class="grid grid-cols-3 gap-4 items-center">
                <label class="text-sm font-medium text-gray-700">Departemen</label>
                <div class="col-span-2">
                    <input type="text" name="departemen" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                           value="<?= set_value('departemen') ?>">
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="<?= base_url('index.php/manajer') ?>" 
                   class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    Simpan
                </button>
            </div>
        <?= form_close() ?>
    </div>
</div>