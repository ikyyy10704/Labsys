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

        <form action="<?= base_url('administrasi/add_patient_data') ?>" method="post" id="add-patient-form" class="space-y-6">
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
                        <div class="relative">
                            <input type="text" 
                                   id="nik-create" 
                                   name="nik" 
                                   value="<?= set_value('nik') ?>"
                                   class="w-full px-3 py-2 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   maxlength="16" 
                                   required
                                   oninput="validateNIK(this); updateNIKCounter(this, 'nik-counter-create')"
                                   onblur="checkNIKExists(this.value, 'nik-message-create')">
                            <span id="nik-counter-create" class="absolute right-3 top-2.5 text-xs font-medium text-gray-500">0/16</span>
                        </div>
                        <div id="nik-message-create" class="mt-1 text-xs"></div>
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
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="<?= set_value('tanggal_lahir') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               required onchange="calculateAge()">
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
                        class="px-6 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 flex items-center">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i>
                    Reset
                </button>
                <button type="submit" 
                        id="submit-btn"
                        class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url() ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Auto-calculate age when date of birth changes
    const dobInput = document.getElementById('tanggal_lahir');
    if (dobInput) {
        dobInput.addEventListener('change', function() {
            calculateAge();
        });
    }
    
    // Initialize NIK counter with existing value
    const nikInput = document.getElementById('nik-create');
    if (nikInput && nikInput.value) {
        updateNIKCounter(nikInput, 'nik-counter-create');
    }
});

// Calculate age from birth date
function calculateAge() {
    const birthDateInput = document.getElementById('tanggal_lahir');
    if (!birthDateInput || !birthDateInput.value) return;
    
    const birthDate = new Date(birthDateInput.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    console.log('Calculated age:', age);
}

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
    const submitBtn = document.getElementById('submit-btn');
    
    // Clear message if NIK is not 16 digits
    if (!nik || nik.length !== 16) {
        messageElement.innerHTML = '';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
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
            // Build URL with exclude_id parameter if provided
            let url = BASE_URL + `administrasi/check_nik_exists?nik=${encodeURIComponent(nik)}`;
            if (excludePatientId) {
                url += `&exclude_id=${excludePatientId}`;
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.exists) {
                // NIK already exists
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
                    submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
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
                    submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
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
                submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }
        }
    }, 500); // 500ms delay
}

// Form validation before submit
document.getElementById('add-patient-form').addEventListener('submit', function(e) {
    const nikInput = document.getElementById('nik-create');
    const submitBtn = document.getElementById('submit-btn');
    
    // Check if NIK is 16 digits
    if (nikInput.value.length !== 16) {
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
        alert('NIK sudah terdaftar! Silakan gunakan NIK yang berbeda.');
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
document.getElementById('nik-create').addEventListener('input', function() {
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