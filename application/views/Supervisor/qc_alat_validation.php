<!-- Header - KONSISTEN DENGAN TEMA SUPERVISOR (PURPLE) -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="shield-check" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Validasi Quality Control Alat</h1>
                <p class="text-blue-100">Review dan validasi hasil QC alat laboratorium</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="bg-white/10 rounded-lg px-4 py-2">
                <p class="text-xs text-blue-200">Last Update</p>
                <p class="text-sm font-semibold text-white" id="last-update"><?= date('H:i') ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>

<!-- Main Content -->
<div class="p-6 space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Validation</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1"><?= $stats['total_pending'] ?></p>
                    <p class="text-xs text-gray-500 mt-1">Menunggu review</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-7 h-7 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Passed (Pending)</p>
                    <p class="text-3xl font-bold text-green-600 mt-1"><?= $stats['passed_pending'] ?></p>
                    <p class="text-xs text-green-600 mt-1 font-medium">
                        <?= $stats['total_pending'] > 0 ? round(($stats['passed_pending']/$stats['total_pending'])*100) : 0 ?>% dari total
                    </p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-7 h-7 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Failed (Pending)</p>
                    <p class="text-3xl font-bold text-red-600 mt-1"><?= $stats['failed_pending'] ?></p>
                    <p class="text-xs text-red-600 mt-1 font-medium">Perlu perhatian</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-7 h-7 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending QC Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="list-checks" class="w-5 h-5 text-blue-600"></i>
                    <span>QC Menunggu Validasi</span>
                </h2>
                <span class="text-sm text-gray-500">
                    Total: <span class="font-semibold text-gray-900"><?= count($pending_qc) ?></span> QC
                </span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal QC</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teknisi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Parameter</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hasil QC</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($pending_qc)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <i data-lucide="check-circle-2" class="w-16 h-16 text-green-300 mx-auto mb-3"></i>
                                <p class="text-gray-500 font-medium">Tidak ada QC yang menunggu validasi</p>
                                <p class="text-sm text-gray-400 mt-1">Semua QC sudah divalidasi</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pending_qc as $qc): ?>
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= date('d M Y', strtotime($qc['tanggal_qc'])) ?>
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center mt-1">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        <?= $qc['waktu_qc'] ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900"><?= $qc['nama_alat'] ?></div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i data-lucide="hash" class="w-3 h-3 inline mr-1"></i>
                                        <?= $qc['kode_unik'] ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                                        </div>
                                        <span class="text-sm text-gray-900"><?= $qc['teknisi'] ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="sliders" class="w-4 h-4 text-gray-400"></i>
                                        <span class="text-sm font-medium text-gray-900">
                                            <?php 
                                                $params = is_array($qc['parameter_qc']) ? $qc['parameter_qc'] : json_decode($qc['parameter_qc'], true);
                                                echo count($params ?? []) . ' parameter';
                                            ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($qc['hasil_qc'] === 'Passed'): ?>
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-700 border border-green-200">
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                            Passed
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full bg-red-100 text-red-700 border border-red-200">
                                            <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                            Failed
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="reviewQC(<?= $qc['qc_id'] ?>)" 
                                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                            Review
                                        </button>
                                        <button onclick="validateQC(<?= $qc['qc_id'] ?>)" 
                                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors">
                                            <i data-lucide="check" class="w-4 h-4 mr-1"></i>
                                            Validate
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Review Modal -->
<div id="review-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                    <span>Review Quality Control</span>
                </h3>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <div id="review-content" class="p-6">
            <!-- Will be filled by JavaScript -->
        </div>
    </div>
</div>

<!-- Validate Modal -->
<div id="validate-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full shadow-2xl">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                <i data-lucide="shield-check" class="w-5 h-5 text-blue-600"></i>
                <span>Validasi Quality Control</span>
            </h3>
        </div>
        
        <form id="validate-form" class="p-6 space-y-4">
            <input type="hidden" id="validate-qc-id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Supervisor (Optional)
                </label>
                <textarea id="validation-note" rows="4"
                          class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Tambahkan catatan validasi jika diperlukan..."></textarea>
                <p class="text-xs text-gray-500 mt-1">
                    <i data-lucide="info" class="w-3 h-3 inline"></i>
                    Catatan ini akan tersimpan dalam history QC
                </p>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeValidateModal()"
                        class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-700">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium flex items-center space-x-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Validasi QC</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentQCId = null;

// Review QC Detail
async function reviewQC(qcId) {
    try {
        const response = await fetch('<?= base_url("supervisor/get_qc_alat_detail/") ?>' + qcId);
        const data = await response.json();

        if (data.success) {
            const qc = data.qc;
            const content = document.getElementById('review-content');

            const params = qc.parameter_qc || [];
            const hasil = qc.nilai_hasil || [];
            const standar = qc.nilai_standar || [];

            content.innerHTML = `
                <div class="space-y-6">
                    <!-- QC Status Badge -->
                    <div class="flex items-center justify-center p-6 rounded-xl ${qc.hasil_qc === 'Passed' ? 'bg-green-50 border-2 border-green-200' : 'bg-red-50 border-2 border-red-200'}">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto rounded-full ${qc.hasil_qc === 'Passed' ? 'bg-green-100' : 'bg-red-100'} flex items-center justify-center mb-3">
                                <i data-lucide="${qc.hasil_qc === 'Passed' ? 'check-circle' : 'alert-circle'}" class="w-8 h-8 ${qc.hasil_qc === 'Passed' ? 'text-green-600' : 'text-red-600'}"></i>
                            </div>
                            <p class="text-2xl font-bold ${qc.hasil_qc === 'Passed' ? 'text-green-700' : 'text-red-700'}">
                                QC ${qc.hasil_qc === 'Passed' ? 'PASSED' : 'FAILED'}
                            </p>
                        </div>
                    </div>

                    <!-- Equipment Info -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-4 flex items-center">
                            <i data-lucide="activity" class="w-4 h-4 mr-2 text-blue-600"></i>
                            Informasi Alat
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Alat Laboratorium</p>
                                <p class="font-semibold text-gray-900">${qc.nama_alat}</p>
                                <p class="text-sm text-gray-600 mt-0.5">${qc.kode_unik}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Lokasi</p>
                                <p class="font-semibold text-gray-900">${qc.lokasi || '-'}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Tanggal & Waktu QC</p>
                                <p class="font-semibold text-gray-900">${formatDate(qc.tanggal_qc)} ${qc.waktu_qc}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Tipe QC</p>
                                <p class="font-semibold text-gray-900 capitalize">${qc.qc_type || 'routine'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Parameters -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                            <i data-lucide="sliders" class="w-4 h-4 mr-2 text-blue-600"></i>
                            Parameter QC (${params.length})
                        </h4>
                        <div class="space-y-3">
                            ${params.map((param, index) => {
                                const value = hasil[index];
                                const std = standar[index];
                                const isPassed = value >= std.min && value <= std.max;
                                
                                return `
                                    <div class="bg-white border-2 ${isPassed ? 'border-green-200' : 'border-red-200'} rounded-lg p-4 hover:shadow-sm transition-shadow">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900">${param.name}</p>
                                                <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                    <i data-lucide="arrow-right" class="w-3 h-3 mr-1"></i>
                                                    Range standar: ${std.min} - ${std.max} ${param.unit || ''}
                                                </p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-2xl font-bold ${isPassed ? 'text-green-600' : 'text-red-600'}">
                                                    ${value} <span class="text-sm">${param.unit || ''}</span>
                                                </p>
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full mt-1 ${isPassed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                                                    <i data-lucide="${isPassed ? 'check' : 'x'}" class="w-3 h-3 mr-1"></i>
                                                    ${isPassed ? 'Passed' : 'Failed'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>

                    <!-- Personnel -->
                    <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-4 flex items-center">
                            <i data-lucide="users" class="w-4 h-4 mr-2 text-blue-600"></i>
                            Personel
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Teknisi</p>
                                <p class="font-semibold text-gray-900">${qc.teknisi || '-'}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Supervisor (Akan Diisi)</p>
                                <p class="font-semibold text-blue-600">Pending Validation</p>
                            </div>
                        </div>
                    </div>

                    <!-- QC Details -->
                    ${qc.batch_number ? `
                        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                            <p class="text-sm font-semibold text-blue-800 mb-2 flex items-center">
                                <i data-lucide="package" class="w-4 h-4 mr-2"></i>
                                Batch Number
                            </p>
                            <p class="font-semibold text-gray-900">${qc.batch_number}</p>
                        </div>
                    ` : ''}

                    <!-- Notes -->
                    ${qc.catatan ? `
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
                            <p class="text-sm font-semibold text-yellow-800 mb-2 flex items-center">
                                <i data-lucide="message-square" class="w-4 h-4 mr-2"></i>
                                Catatan Teknisi
                            </p>
                            <p class="text-gray-900 whitespace-pre-wrap">${qc.catatan}</p>
                        </div>
                    ` : ''}
                    
                    <!-- Tindakan Korektif -->
                    ${qc.tindakan_korektif ? `
                        <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-4">
                            <p class="text-sm font-semibold text-orange-800 mb-2 flex items-center">
                                <i data-lucide="tool" class="w-4 h-4 mr-2"></i>
                                Tindakan Korektif
                            </p>
                            <p class="text-gray-900 whitespace-pre-wrap">${qc.tindakan_korektif}</p>
                        </div>
                    ` : ''}

                    <div class="pt-4 border-t flex justify-end space-x-3">
                        <button onclick="closeReviewModal()" 
                                class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">
                            Tutup
                        </button>
                        <button onclick="closeReviewModal(); validateQC(${qc.qc_id})" 
                                class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium flex items-center space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Validasi QC</span>
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('review-modal').classList.remove('hidden');
            lucide.createIcons();
            document.getElementById('review-modal').classList.remove('hidden');
            lucide.createIcons();
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat memuat detail');
    }
}

function closeReviewModal() {
    document.getElementById('review-modal').classList.add('hidden');
}

// Validate QC
function validateQC(qcId) {
    currentQCId = qcId;
    document.getElementById('validate-qc-id').value = qcId;
    document.getElementById('validation-note').value = '';
    document.getElementById('validate-modal').classList.remove('hidden');
}

function closeValidateModal() {
    document.getElementById('validate-modal').classList.add('hidden');
}

// Submit Validation
document.getElementById('validate-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const qcId = document.getElementById('validate-qc-id').value;
    const validationNote = document.getElementById('validation-note').value;

    const formData = new FormData();
    formData.append('qc_id', qcId);
    formData.append('validation_note', validationNote);

    try {
        const response = await fetch('<?= base_url("supervisor/validate_qc_alat") ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('success', data.message);
            closeValidateModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat memvalidasi QC');
    }
});

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
}

// Update last update time
function updateLastUpdateTime() {
    document.getElementById('last-update').textContent = new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    updateLastUpdateTime();
    setInterval(updateLastUpdateTime, 60000);
});


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

<style>
/* Smooth animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.3s ease-in;
}

/* Modal backdrop animation */
#review-modal:not(.hidden),
#validate-modal:not(.hidden) {
    animation: fadeIn 0.2s ease-in;
}
</style>