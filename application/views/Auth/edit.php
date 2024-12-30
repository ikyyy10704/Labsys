<div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md mx-4">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Reset Password</h1>

    <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <?= $this->session->flashdata('error') ?>
        </div>
    <?php endif; ?>

    <?= form_open('auth/reset_password/'.$token, ['class' => 'space-y-6']) ?>
        <div>
            <label class="block text-sm font-medium text-gray-700">Password Baru</label>
            <input type="password" name="password" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
            <input type="password" name="confirm_password" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                   required>
        </div>

        <button type="submit" 
                class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700">
            Simpan Password Baru
        </button>
    <?= form_close() ?>
</div>