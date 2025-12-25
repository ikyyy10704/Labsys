<!-- Header - KONSISTEN DENGAN DASHBOARD LAB -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="clipboard-check" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Quality Control Alat Laboratorium</h1>
                <p class="text-blue-100">Lakukan QC untuk memastikan alat berfungsi dengan baik</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= base_url('quality_control/riwayat') ?>" 
               class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                <i data-lucide="history" class="w-4 h-4"></i>
                <span>Riwayat QC</span>
            </a>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>

<!-- Main Content -->
<div class="p-6 space-y-6">

    <!-- QC Form -->
    <form id="qc-form" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        
        <!-- Equipment Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Alat Laboratorium <span class="text-red-500">*</span>
            </label>
            <select id="alat_id" name="alat_id" required 
                    class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    onchange="loadEquipmentInfo(this.value)">
                <option value="">-- Pilih Alat --</option>
                <?php foreach ($alat_list as $alat): ?>
                    <option value="<?= $alat['alat_id'] ?>">
                        <?= $alat['nama_alat'] ?> - <?= $alat['kode_unik'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Equipment Info Card (Hidden initially) -->
        <div id="equipment-info" class="hidden space-y-6">
            
            <!-- Calibration Status Alert -->
            <div id="calibration-alert"></div>

            <!-- Equipment Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <p class="text-xs font-medium text-blue-600 uppercase mb-1">Merek/Model</p>
                    <p id="equipment-model" class="font-semibold text-gray-900">-</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                    <p class="text-xs font-medium text-purple-600 uppercase mb-1">Lokasi</p>
                    <p id="equipment-location" class="font-semibold text-gray-900">-</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <p class="text-xs font-medium text-green-600 uppercase mb-1">Last Calibration</p>
                    <p id="equipment-last-cal" class="font-semibold text-gray-900">-</p>
                </div>
            </div>

            <!-- QC Date & Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal QC <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_qc" name="tanggal_qc" required
                           value="<?= date('Y-m-d') ?>"
                           class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu QC <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="waktu_qc" name="waktu_qc" required
                           value="<?= date('H:i') ?>"
                           class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- QC Parameters -->
            <div id="qc-parameters-container" class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="sliders" class="w-5 h-5 text-blue-600"></i>
                        <span>Parameter QC</span>
                    </h3>
                    <span id="param-count" class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-700 rounded-full">0 parameter</span>
                </div>
                <div id="qc-parameters" class="space-y-3">
                    <!-- Parameters will be loaded here -->
                </div>
            </div>

            <!-- Teknisi & Supervisor -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Teknisi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="teknisi" name="teknisi" required
                               class="w-full pl-10 rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Nama teknisi">
                        <i data-lucide="user" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Supervisor
                    </label>
                    <div class="relative">
                        <input type="text" id="supervisor" name="supervisor"
                               class="w-full pl-10 rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Nama supervisor (optional)">
                        <i data-lucide="user-check" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                    </div>
                </div>
            </div>

            <!-- QC Type & Batch -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe QC</label>
                    <select id="qc_type" name="qc_type"
                            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="routine">Routine</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="unscheduled">Unscheduled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                    <input type="text" id="batch_number" name="batch_number"
                           class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Batch number (optional)">
                </div>
            </div>

            <!-- Catatan -->
            <div id="catatan-container">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan <span id="catatan-required" class="text-red-500 hidden">*</span>
                </label>
                <textarea id="catatan" name="catatan" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Catatan tambahan (wajib diisi jika QC Failed)"></textarea>
                <p class="text-xs text-gray-500 mt-1">
                    <i data-lucide="info" class="w-3 h-3 inline"></i>
                    Catatan wajib diisi jika ada parameter yang failed
                </p>
            </div>

            <!-- Tindakan Korektif (if failed) -->
            <div id="tindakan-container" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tindakan Korektif <span class="text-red-500">*</span>
                </label>
                <textarea id="tindakan_korektif" name="tindakan_korektif" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Tindakan korektif yang dilakukan"></textarea>
            </div>

            <!-- QC Status Summary -->
            <div id="qc-status-summary" class="hidden rounded-lg p-4">
                <div class="flex items-center space-x-3">
                    <i data-lucide="activity" class="w-6 h-6"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Status QC:</p>
                        <p id="qc-status-text" class="text-xl font-bold mt-1">-</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="resetForm()"
                        class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-700">
                    Reset
                </button>
                <button type="submit" id="submit-btn"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium flex items-center space-x-2" disabled>
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Quality Control</span>
                </button>
            </div>
        </div>

    </form>

</div>

<script>
let equipmentData = null;
let parametersData = [];
let calibrationStatus = null;

// Load equipment info when selected
async function loadEquipmentInfo(alatId) {
    if (!alatId) {
        document.getElementById('equipment-info').classList.add('hidden');
        return;
    }

    try {
        const response = await fetch('<?= base_url("quality_control/get_equipment_info/") ?>' + alatId);
        const data = await response.json();

        if (data.success) {
            equipmentData = data.equipment;
            calibrationStatus = data.calibration_status;
            parametersData = data.parameters;

            // Show equipment info with animation
            const infoDiv = document.getElementById('equipment-info');
            infoDiv.classList.remove('hidden');
            infoDiv.style.animation = 'fadeIn 0.3s ease-in';

            // Fill equipment details
            document.getElementById('equipment-model').textContent = equipmentData.merek_model || '-';
            document.getElementById('equipment-location').textContent = equipmentData.lokasi || '-';
            document.getElementById('equipment-last-cal').textContent = equipmentData.tanggal_kalibrasi_terakhir || 'Belum pernah';

            // Show calibration status
            displayCalibrationStatus();

            // Load parameters
            loadQCParameters();

            // Enable/disable form based on calibration
            toggleFormAvailability();
            
            // Reinitialize icons
            lucide.createIcons();
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat memuat info alat');
    }
}

function displayCalibrationStatus() {
    const alertDiv = document.getElementById('calibration-alert');
    
    if (calibrationStatus.status === 'EXPIRED') {
        alertDiv.innerHTML = `
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-800">Kalibrasi Expired!</p>
                        <p class="text-sm text-red-700 mt-1">${calibrationStatus.message}</p>
                        <p class="text-xs text-red-600 mt-2 flex items-center">
                            <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                            QC tidak dapat dilakukan. Harap lakukan kalibrasi terlebih dahulu.
                        </p>
                    </div>
                </div>
            </div>
        `;
    } else if (calibrationStatus.status === 'WARNING') {
        alertDiv.innerHTML = `
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm">
                <div class="flex">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-500 mr-3 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-yellow-800">Peringatan Kalibrasi</p>
                        <p class="text-sm text-yellow-700 mt-1">${calibrationStatus.message}</p>
                    </div>
                </div>
            </div>
        `;
    } else {
        alertDiv.innerHTML = `
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                <div class="flex">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-green-800">Kalibrasi Valid</p>
                        <p class="text-sm text-green-700 mt-1">${calibrationStatus.message}</p>
                    </div>
                </div>
            </div>
        `;
    }

    lucide.createIcons();
}

function loadQCParameters() {
    const container = document.getElementById('qc-parameters');
    const paramCount = document.getElementById('param-count');

    if (!parametersData || parametersData.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                <p>Tidak ada parameter QC untuk alat ini</p>
            </div>
        `;
        paramCount.textContent = '0 parameter';
        lucide.createIcons();
        return;
    }

    paramCount.textContent = `${parametersData.length} parameter`;

    container.innerHTML = parametersData.map((param, index) => `
        <div class="bg-gradient-to-r from-gray-50 to-white rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-colors">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="text-xs font-medium text-gray-500 uppercase">Parameter</label>
                    <input type="hidden" name="parameter_name[]" value="${param.parameter_name}">
                    <input type="hidden" name="parameter_unit[]" value="${param.unit || ''}">
                    <input type="hidden" name="min_value[]" value="${param.min_value || 0}">
                    <input type="hidden" name="max_value[]" value="${param.max_value || 0}">
                    <p class="font-semibold text-gray-900 mt-1">${param.parameter_name}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-xs text-gray-500">Range:</span>
                        <span class="text-xs font-medium text-blue-600">${param.min_value} - ${param.max_value} ${param.unit || ''}</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase">Hasil Pengukuran *</label>
                    <input type="number" step="0.01" name="result_value[]" required
                           class="w-full mt-1 rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           onchange="validateParameter(this, ${param.min_value}, ${param.max_value}, ${index})"
                           placeholder="Nilai">
                </div>
                <div class="flex items-center justify-center">
                    <span id="param-status-${index}" class="text-xs font-medium"></span>
                </div>
            </div>
        </div>
    `).join('');
    
    lucide.createIcons();
}

function validateParameter(input, min, max, index) {
    const value = parseFloat(input.value);
    const statusSpan = document.getElementById(`param-status-${index}`);

    if (isNaN(value)) {
        statusSpan.textContent = '';
        statusSpan.className = 'text-xs font-medium';
        return;
    }

    if (value < min || value > max) {
        statusSpan.innerHTML = '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full">✗ Failed</span>';
        input.classList.add('border-red-500', 'bg-red-50');
        input.classList.remove('border-gray-300');
        
        // Show corrective action field
        document.getElementById('tindakan-container').classList.remove('hidden');
        document.getElementById('catatan-required').classList.remove('hidden');
        document.getElementById('catatan').setAttribute('required', 'required');
    } else {
        statusSpan.innerHTML = '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full">✓ Passed</span>';
        input.classList.remove('border-red-500', 'bg-red-50');
        input.classList.add('border-gray-300');
    }

    checkOverallStatus();
}

function checkOverallStatus() {
    const results = document.querySelectorAll('input[name="result_value[]"]');
    const parameters = parametersData;
    let hasFailed = false;

    results.forEach((input, index) => {
        const value = parseFloat(input.value);
        if (!isNaN(value)) {
            const min = parameters[index].min_value;
            const max = parameters[index].max_value;
            if (value < min || value > max) {
                hasFailed = true;
            }
        }
    });

    const statusDiv = document.getElementById('qc-status-summary');
    const statusText = document.getElementById('qc-status-text');
    
    statusDiv.classList.remove('hidden');
    
    if (hasFailed) {
        statusText.innerHTML = '<span class="text-red-600">QC FAILED ✗</span>';
        statusDiv.className = 'bg-red-50 rounded-lg p-4 border-l-4 border-red-500';
    } else {
        statusText.innerHTML = '<span class="text-green-600">QC PASSED ✓</span>';
        statusDiv.className = 'bg-green-50 rounded-lg p-4 border-l-4 border-green-500';
    }
    
    lucide.createIcons();
}

function toggleFormAvailability() {
    const submitBtn = document.getElementById('submit-btn');
    const formInputs = document.querySelectorAll('#qc-form input, #qc-form select, #qc-form textarea');

    if (calibrationStatus.status === 'EXPIRED') {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        formInputs.forEach(input => {
            if (input.id !== 'alat_id') {
                input.disabled = true;
            }
        });
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        formInputs.forEach(input => input.disabled = false);
    }
}

// Form submission
document.getElementById('qc-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    if (calibrationStatus.status === 'EXPIRED') {
        showToast('error', 'QC tidak dapat dilakukan. Kalibrasi alat sudah expired!');
        return;
    }

    const formData = new FormData(this);

    try {
        const response = await fetch('<?= base_url("quality_control/submit_qc") ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('success', data.message);
            resetForm();
            setTimeout(() => {
                window.location.href = '<?= base_url("quality_control/riwayat") ?>';
            }, 1000);
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat menyimpan QC');
    }
});

function resetForm() {
    document.getElementById('qc-form').reset();
    document.getElementById('equipment-info').classList.add('hidden');
    document.getElementById('tindakan-container').classList.add('hidden');
    document.getElementById('qc-status-summary').classList.add('hidden');
    document.getElementById('catatan-required').classList.add('hidden');
    document.getElementById('catatan').removeAttribute('required');
}

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Add fadeIn animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);

// Toast Notification System
function showToast(type, message) {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2 pointer-events-none';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const iconName = type === 'success' ? 'check-circle' : type === 'info' ? 'info' : type === 'warning' ? 'alert-triangle' : 'alert-circle';
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : type === 'info' ? 'bg-blue-50 border-blue-200' : type === 'warning' ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200';
    const iconColor = type === 'success' ? 'text-green-600' : type === 'info' ? 'text-blue-600' : type === 'warning' ? 'text-yellow-600' : 'text-red-600';
    const textColor = type === 'success' ? 'text-green-800' : type === 'info' ? 'text-blue-800' : type === 'warning' ? 'text-yellow-800' : 'text-red-800';
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${bgColor} ${textColor} border rounded-lg p-4 max-w-sm shadow-lg transform transition-all duration-500 ease-out translate-x-full opacity-0 pointer-events-auto`;
    toast.innerHTML = `
        <div class="flex items-start space-x-3">
            <i data-lucide="${iconName}" class="w-5 h-5 ${iconColor} flex-shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="removeToast('${toastId}')" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    lucide.createIcons();
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    setTimeout(() => {
        removeToast(toastId);
    }, 5000);
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 500);
    }
}
</script>