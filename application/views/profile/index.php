<div class="flex h-screen bg-gray-100">
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm">
            <div class="px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Profile</h1>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
            <div class="max-w-4xl mx-auto">
                <?php if ($this->session->flashdata('success')) : ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                        <?= $this->session->flashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')) : ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        <?= $this->session->flashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?= form_open_multipart('profile/update', ['class' => 'bg-white rounded-lg shadow-sm']) ?>
                    <div class="p-6 space-y-6">
                        <div class="flex flex-col items-center justify-center mb-6">
                            <div class="relative">
                                <img src="<?= base_url('uploads/manajer/profile/' . ($profile->foto ?? 'default.jpg')) ?>" 
                                    alt="Profile" 
                                    id="preview-foto"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                                <label for="foto" 
                                    class="absolute bottom-0 right-0 bg-blue-500 text-white rounded-full p-2 cursor-pointer hover:bg-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </label>
                                <input type="file" id="foto" name="foto" class="hidden" accept="image/*">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Klik icon kamera untuk mengubah foto</p>
                        </div>
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-sm font-medium text-gray-700">ID Manajer</label>
                            <div class="col-span-2">
                                <input type="text" value="<?= $profile->id_manajer ?>" 
                                       class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" 
                                       readonly>
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-sm font-medium text-gray-700">Username</label>
                            <div class="col-span-2">
                                <input type="text" value="<?= $profile->username ?>" 
                                       class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" 
                                       readonly>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-sm font-medium text-gray-700">Email</label>
                            <div class="col-span-2">
                                <input type="email" name="email" value="<?= $profile->email ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                       required>
                            </div>
                        </div>

                        <?php if ($profile->id_manajer): ?>
                        <!-- Nama Manajer -->
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-sm font-medium text-gray-700">Nama Manajer</label>
                            <div class="col-span-2">
                                <input type="text" name="nama_manajer" value="<?= $profile->nama_manajer ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                       required>
                                <input type="hidden" name="id_manajer" value="<?= $profile->id_manajer ?>">
                            </div>
                        </div>

                        <!-- Departemen -->
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label class="text-sm font-medium text-gray-700">Departemen</label>
                            <div class="col-span-2">
                                <input type="text" name="departemen" value="<?= $profile->departemen ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                       required>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Password Section -->
                        

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg flex justify-end space-x-4">
                        <a href="<?= base_url('dashboard') ?>" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Profile
                        </button>
                    </div>
                <?= form_close() ?>
            </div>
        </main>
    </div>
</div>

<script>
document.getElementById('foto').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-foto').src = e.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>