<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tambah Data Pasien</h2>
                <p class="text-gray-600">Form pendaftaran pasien baru</p>
            </div>
            <a href="<?= base_url('administrasi/patient_history') ?>" 
               class="flex items-center space-x-2 px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>

        <?php if(validation_errors()): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center space-x-2 text-red-700">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span class="font-medium">Terjadi kesalahan:</span>
            </div>
            <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                <?= validation_errors('<li>', '</li>') ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="<?= base_url('administrasi/add_patient_data') ?>" method="post" class="space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                    <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                    Data Pribadi
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="nama" value="<?= set_value('nama') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK (16 digit) *</label>
                        <input type="text" name="nik" value="<?= set_value('nik') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               maxlength="16" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" <?= set_select('jenis_kelamin', 'L') ?>>Laki-laki</option>
                            <option value="P" <?= set_select('jenis_kelamin', 'P') ?>>Perempuan</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="<?= set_value('tempat_lahir') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" value="<?= set_value('tanggal_lahir') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="<?= set_value('pekerjaan') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Kontak & Alamat -->
            <div class="bg-green-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                    <i data-lucide="phone" class="w-5 h-5 mr-2"></i>
                    Kontak & Alamat
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon/HP *</label>
                        <input type="tel" name="telepon" value="<?= set_value('telepon') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Darurat</label>
                        <input type="text" name="kontak_darurat" value="<?= set_value('kontak_darurat') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                        <textarea name="alamat_domisili" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= set_value('alamat_domisili') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Data Medis -->
            <div class="bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
                    <i data-lucide="heart" class="w-5 h-5 mr-2"></i>
                    Data Medis
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Riwayat Pasien</label>
                        <textarea name="riwayat_pasien" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= set_value('riwayat_pasien') ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Permintaan Pemeriksaan</label>
                        <textarea name="permintaan_pemeriksaan" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= set_value('permintaan_pemeriksaan') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Data Rujukan -->
            <div class="bg-orange-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                    Data Rujukan (Opsional)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dokter Perujuk</label>
                        <input type="text" name="dokter_perujuk" value="<?= set_value('dokter_perujuk') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asal Rujukan</label>
                        <input type="text" name="asal_rujukan" value="<?= set_value('asal_rujukan') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rujukan</label>
                        <input type="text" name="nomor_rujukan" value="<?= set_value('nomor_rujukan') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Rujukan</label>
                        <input type="date" name="tanggal_rujukan" value="<?= set_value('tanggal_rujukan') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Awal</label>
                        <textarea name="diagnosis_awal" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= set_value('diagnosis_awal') ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi Pemeriksaan</label>
                        <textarea name="rekomendasi_pemeriksaan" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= set_value('rekomendasi_pemeriksaan') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="reset" 
                        class="px-6 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Reset
                </button>
                <button type="submit" 
                        class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Auto-calculate age when date of birth changes
    const dobInput = document.querySelector('input[name="tanggal_lahir"]');
    if (dobInput) {
        dobInput.addEventListener('change', function() {
            const birthDate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            // You can display the age somewhere if needed
            console.log('Calculated age:', age);
        });
    }
});
</script>