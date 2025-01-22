
<div class="flex h-screen bg-gray-100">
    <div class="w-64 bg-white shadow-lg">
        <div class="flex flex-col h-full">
            <div class="p-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Sistem Penggajian</h2>
            </div>
    
            <nav class="flex-1 overflow-y-auto py-4">
                <div class="px-4 space-y-2">
                    <a href="<?= base_url('dashboard') ?>" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-md">
                        Dashboard
                    </a>
                    <a href="<?= base_url('karyawan') ?>" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-md">
                        Data Karyawan
                    </a>
                    <a href="<?= base_url('manajer') ?>" class="block px-4 py-2 text-blue-600 bg-blue-50 rounded-md">
                        Data Manajer
                    </a>
                    <a href="<?= base_url('laporan') ?>" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-md">
                        Laporan
                    </a>
                </div>
            </nav>
        </div>
    </div>
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm">
            <div class="px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Edit Data Manajer</h1>
            </div>
        </header>
        <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
            <div class="max-w-4xl mx-auto">
                <?php if(validation_errors()): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        <?= validation_errors() ?>
                    </div>
                <?php endif; ?>

                <?= form_open('manajer/edit/'.$manajer->id_manajer, ['class' => 'bg-white rounded-lg shadow-sm']) ?>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-sm font-medium text-gray-700">Nama Manajer</label>
                            <div class="col-span-2">
                                <input type="text" name="nama_manajer" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                       value="<?= $manajer->nama_manajer ?>">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-sm font-medium text-gray-700">Departemen</label>
                            <div class="col-span-2">
                                <input type="text" name="departemen" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                       value="<?= $manajer->departemen ?>">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-sm font-medium text-gray-700">email</label>
                            <div class="col-span-2">
                                <input type="text" name="departemen" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                       value="<?= $manajer->email ?>">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg flex justify-end space-x-4">
                        <a href="<?= base_url('index.php/manajer') ?>" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update
                        </button>
                    </div>
                <?= form_close() ?>
            </div>
        </main>
    </div>
</div