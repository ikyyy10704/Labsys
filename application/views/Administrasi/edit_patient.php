<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Edit Data Pasien</h2>
                <p class="text-gray-600">Perbarui informasi pasien: <?= $patient['nama'] ?></p>
            </div>
            <a href="<?= base_url('administrasi/patient_detail/' . $patient['pasien_id']) ?>" 
               class="flex items-center space-x-2 px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Validation Errors -->
        <?php if (validation_errors()): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center space-x-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <div class="flex-1">
                    <span class="font-medium">Terjadi kesalahan:</span>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        <?= validation_errors('<li>', '</li>') ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('administrasi/edit_patient/' . $patient['pasien_id']) ?>" method="post" id="edit-patient-form" class="space-y-6">
            
            <!-- Data Pribadi -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                    <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                    Data Pribadi
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" 
                               name="nama" 
                               value="<?= set_value('nama', $patient['nama']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK (16 digit) *</label>
                        <div class="relative">
                            <input type="text" 
                                   id="nik-edit" 
                                   name="nik" 
                                   value="<?= set_value('nik', $patient['nik']) ?>"
                                   class="w-full px-3 py-2 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   maxlength="16" 
                                   required
                                   oninput="validateNIK(this); updateNIKCounter(this, 'nik-counter-edit')"
                                   onblur="checkNIKExists(this.value, 'nik-message-edit', <?= $patient['pasien_id'] ?>)">
                            <span id="nik-counter-edit" class="absolute right-3 top-2.5 text-xs font-medium text-gray-500">0/16</span>
                        </div>
                        <div id="nik-message-edit" class="mt-1 text-xs"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                                required>
                            <option value="L" <?= set_select('jenis_kelamin', 'L', $patient['jenis_kelamin'] == 'L') ?>>Laki-laki</option>
                            <option value="P" <?= set_select('jenis_kelamin', 'P', $patient['jenis_kelamin'] == 'P') ?>>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" 
                               name="tempat_lahir" 
                               value="<?= set_value('tempat_lahir', $patient['tempat_lahir']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                        <input type="date" 
                               name="tanggal_lahir" 
                               value="<?= set_value('tanggal_lahir', $patient['tanggal_lahir']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                        <input type="text" 
                               name="pekerjaan" 
                               value="<?= set_value('pekerjaan', $patient['pekerjaan']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
                        <input type="tel" 
                               name="telepon" 
                               value="<?= set_value('telepon', $patient['telepon']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Darurat</label>
                        <input type="text" 
                               name="kontak_darurat" 
                               value="<?= set_value('kontak_darurat', $patient['kontak_darurat']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                        <textarea name="alamat_domisili" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= set_value('alamat_domisili', $patient['alamat_domisili']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Informasi Medis -->
            <div class="bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
                    <i data-lucide="heart" class="w-5 h-5 mr-2"></i>
                    Informasi Medis
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Riwayat Pasien</label>
                        <textarea name="riwayat_pasien" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= set_value('riwayat_pasien', $patient['riwayat_pasien']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Permintaan Pemeriksaan</label>
                        <textarea name="permintaan_pemeriksaan" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= set_value('permintaan_pemeriksaan', $patient['permintaan_pemeriksaan']) ?></textarea>
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
                        <input type="text" 
                               name="dokter_perujuk" 
                               value="<?= set_value('dokter_perujuk', $patient['dokter_perujuk']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asal Rujukan</label>
                        <input type="text" 
                               name="asal_rujukan" 
                               value="<?= set_value('asal_rujukan', $patient['asal_rujukan']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rujukan</label>
                        <input type="text" 
                               name="nomor_rujukan" 
                               value="<?= set_value('nomor_rujukan', $patient['nomor_rujukan']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Rujukan</label>
                        <input type="date" 
                               name="tanggal_rujukan" 
                               value="<?= set_value('tanggal_rujukan', $patient['tanggal_rujukan']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Awal</label>
                        <textarea name="diagnosis_awal" 
                                  rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= set_value('diagnosis_awal', $patient['diagnosis_awal']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi Pemeriksaan</label>
                        <textarea name="rekomendasi_pemeriksaan" 
                                  rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= set_value('rekomendasi_pemeriksaan', $patient['rekomendasi_pemeriksaan']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="<?= base_url('administrasi/patient_detail/' . $patient['pasien_id']) ?>" 
                   class="px-6 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 flex items-center">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        id="submit-btn-edit" 
                        class="px-6 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Section -->
<script>
const BASE_URL = '<?= base_url() ?>';
const CURRENT_PATIENT_ID = <?= $patient['pasien_id'] ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Initialize NIK counter with existing value
    const nikInput = document.getElementById('nik-edit');
    if (nikInput && nikInput.value) {
        updateNIKCounter(nikInput, 'nik-counter-edit');
    }
});

// Validate NIK (only numbers)
function validateNIK(input) {
    // Remove all non-numeric characters
    input.value = input.value.replace(/[^0-9]/g, '');
}

// Update NIK counter
function updateNIKCounter(input, counterId) {
    const counter = document.getElementById(counterId);
    if (!counter) return;
    
    const length = input.value.length;
    counter.textContent = `${length}/16`;
    
    // Change color based on length
    counter.classList.remove('text-gray-500', 'text-red-600', 'text-yellow-600', 'text-green-600', 'font-bold');
    
    if (length === 0) {
        counter.classList.add('text-gray-500');
    } else if (length === 16) {
        counter.classList.add('text-green-600', 'font-bold');
    } else if (length > 10) {
        counter.classList.add('text-yellow-600');
    } else {
        counter.classList.add('text-red-600');
    }
}

// Check if NIK already exists
let nikCheckTimeout;
async function checkNIKExists(nik, messageElementId, excludePatientId = null) {
    clearTimeout(nikCheckTimeout);
    
    const messageElement = document.getElementById(messageElementId);
    const submitBtn = document.getElementById('submit-btn-edit');
    
    // Clear message if NIK is not 16 digits
    if (!nik || nik.length !== 16) {
        messageElement.innerHTML = '';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
            submitBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
        }
        return;
    }
    
    // Show checking status
    messageElement.innerHTML = `
        <span class="flex items-center text-blue-600 animate-pulse">
            <svg class="animate-spin h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memeriksa NIK...
        </span>
    `;
    
    // Delay to avoid too many requests
    nikCheckTimeout = setTimeout(async () => {
        try {
            // Build URL with exclude_id parameter
            let url = BASE_URL + `administrasi/check_nik_exists?nik=${encodeURIComponent(nik)}`;
            if (excludePatientId) {
                url += `&exclude_id=${excludePatientId}`;
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.exists) {
                // NIK already exists (belonging to different patient)
                messageElement.innerHTML = `
                    <div class="flex items-start space-x-1 text-red-600 bg-red-50 p-2 rounded border border-red-200 mt-1">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <span class="font-semibold block">NIK sudah terdaftar!</span>
                            <div class="mt-1 text-sm">
                                <span class="font-medium text-gray-900">${data.patient.nama}</span>
                                <span class="text-gray-600"> - ${data.patient.nomor_registrasi}</span>
                            </div>
                            <span class="text-xs text-gray-600">Telp: ${data.patient.telepon}</span>
                        </div>
                    </div>
                `;
                
                // Disable submit button
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                    submitBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                }
            } else {
                // NIK available
                messageElement.innerHTML = `
                    <span class="flex items-center text-green-600 bg-green-50 px-2 py-1 rounded mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-sm">NIK tersedia</span>
                    </span>
                `;
                
                // Enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                    submitBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
                }
            }
            
        } catch (error) {
            console.error('Error checking NIK:', error);
            messageElement.innerHTML = `
                <span class="flex items-center text-yellow-600 bg-yellow-50 px-2 py-1 rounded mt-1">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs font-medium">Gagal memeriksa NIK, coba lagi</span>
                </span>
            `;
            
            // Enable submit button on error (allow user to proceed)
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                submitBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
            }
        }
    }, 500); // 500ms delay
}

// Form validation before submit
document.getElementById('edit-patient-form').addEventListener('submit', function(e) {
    const nikInput = document.getElementById('nik-edit');
    const submitBtn = document.getElementById('submit-btn-edit');
    
    // Check if NIK is 16 digits
    if (nikInput.value && nikInput.value.length !== 16) {
        e.preventDefault();
        
        // Show alert
        alert('NIK harus 16 digit! Saat ini: ' + nikInput.value.length + ' digit');
        
        // Focus on NIK input
        nikInput.focus();
        nikInput.select();
        
        return false;
    }
    
    // Check if submit button is disabled (NIK already exists)
    if (submitBtn.disabled) {
        e.preventDefault();
        alert('NIK sudah terdaftar pada pasien lain! Silakan gunakan NIK yang berbeda atau periksa kembali.');
        nikInput.focus();
        nikInput.select();
        return false;
    }
    
    // Show loading state on submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Menyimpan...</span>
    `;
});

// Add visual feedback when typing in NIK field
document.getElementById('nik-edit').addEventListener('input', function() {
    // Add border color feedback
    if (this.value.length === 16) {
        this.classList.remove('border-gray-300', 'border-red-300', 'border-yellow-300');
        this.classList.add('border-green-500');
    } else if (this.value.length > 10) {
        this.classList.remove('border-gray-300', 'border-red-300', 'border-green-500');
        this.classList.add('border-yellow-300');
    } else if (this.value.length > 0) {
        this.classList.remove('border-gray-300', 'border-yellow-300', 'border-green-500');
        this.classList.add('border-red-300');
    } else {
        this.classList.remove('border-red-300', 'border-yellow-300', 'border-green-500');
        this.classList.add('border-gray-300');
    }
});
</script>
