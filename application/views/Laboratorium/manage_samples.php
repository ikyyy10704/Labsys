<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - LabSy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        /* Loading animation */
        .loading { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        /* Fade in animation */
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-50">

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-[100] space-y-2 pointer-events-none"></div>

<!-- Custom Modal (Neon Style) -->
<div id="custom-modal" class="fixed inset-0 z-[110] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md transform scale-95 transition-all duration-300 relative z-10 border border-gray-100">
        <div class="text-center">
            <div id="modal-icon-container" class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-6 bg-blue-50">
                <i id="modal-icon" data-lucide="alert-circle" class="h-8 w-8 text-blue-600"></i>
            </div>
            <h3 id="modal-title" class="text-xl font-bold text-gray-900 mb-2">Konfirmasi</h3>
            <p id="modal-message" class="text-gray-600 mb-8">Apakah Anda yakin ingin melakukan tindakan ini?</p>
            <div class="flex gap-3 justify-center">
                <button onclick="closeModal()" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-medium transition-colors duration-200">
                    Batal
                </button>
                <button id="modal-confirm-btn" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium shadow-lg shadow-blue-200 transition-all duration-200">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<div class="bg-gradient-to-r from-blue-600 to-blue-700 border-b border-blue-500 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="test-tubes" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Manajemen Sampel</h1>
                <p class="text-blue-100">Kelola sampel untuk pemeriksaan: <?= $examination['nomor_pemeriksaan'] ?></p>
            </div>
        </div>
        <a href="<?= base_url('sample_data') ?>" 
           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
            Kembali
        </a>
    </div>
</div>

<!-- Patient Info -->
<div class="p-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pasien</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Nama Pasien</p>
                <p class="font-semibold text-gray-900"><?= $examination['nama_pasien'] ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600">NIK</p>
                <p class="font-semibold text-gray-900"><?= $examination['nik'] ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Jenis Pemeriksaan</p>
                <p class="font-semibold text-gray-900"><?= $examination['jenis_pemeriksaan'] ?></p>
            </div>
        </div>
    </div>

    <!-- Samples Summary -->
    <?php if ($samples_summary): ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Sampel</p>
            <p class="text-2xl font-bold text-gray-900"><?= $samples_summary['total_samples'] ?></p>
        </div>
        <div class="bg-orange-50 rounded-lg shadow-sm border border-orange-200 p-4">
            <p class="text-sm text-orange-600">Belum Diambil</p>
            <p class="text-2xl font-bold text-orange-900"><?= $samples_summary['belum_diambil'] ?></p>
        </div>
        <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-4">
            <p class="text-sm text-blue-600">Sudah Diambil</p>
            <p class="text-2xl font-bold text-blue-900"><?= $samples_summary['sudah_diambil'] ?></p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4">
            <p class="text-sm text-green-600">Diterima</p>
            <p class="text-2xl font-bold text-green-900"><?= $samples_summary['diterima'] ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Samples List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Sampel</h2>
                <button onclick="openAddSampleModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                    Tambah Sampel
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <?php if (empty($samples)): ?>
            <div class="text-center py-12">
                <i data-lucide="test-tube" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Sampel</h3>
                <p class="text-gray-500">Klik tombol "Tambah Sampel" untuk menambahkan sampel baru</p>
            </div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($samples as $sample): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">
                                <?= ($sample['jenis_sampel'] == 'lain') 
                                    ? ($sample['keterangan_sampel'] ? $sample['keterangan_sampel'] : 'Lain - Lain')
                                    : ($jenis_sampel_options[$sample['jenis_sampel']] ?? 'Unknown') ?>
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Status: 
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    <?php 
                                    switch($sample['status_sampel']) {
                                        case 'belum_diambil': echo 'bg-gray-100 text-gray-700'; break;
                                        case 'sudah_diambil': echo 'bg-blue-100 text-blue-700'; break;
                                        case 'diterima': echo 'bg-green-100 text-green-700'; break;
                                        case 'ditolak': echo 'bg-red-100 text-red-700'; break;
                                    }
                                    ?>">
                                    <?php
                                    switch($sample['status_sampel']) {
                                        case 'belum_diambil': echo 'Belum Diambil'; break;
                                        case 'sudah_diambil': echo 'Sudah Diambil'; break;
                                        case 'diterima': echo 'Diterima'; break;
                                        case 'ditolak': echo 'Ditolak'; break;
                                    }
                                    ?>
                                </span>
                            </p>
                            
                            <!-- Kondisi Sampel (jika ada) -->
                            <div class="mt-2">
                                <?php if (!empty($sample['kondisi_details'])): ?>
                                <p class="text-xs text-gray-500 mb-1">Kondisi Sampel:</p>
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($sample['kondisi_details'] as $kondisi): ?>
                                    <span class="px-2 py-1 rounded text-xs flex items-center
                                        <?php 
                                        switch($kondisi['kategori']) {
                                            case 'normal': echo 'bg-green-50 text-green-700 border border-green-200'; break;
                                            case 'acceptable': echo 'bg-yellow-50 text-yellow-700 border border-yellow-200'; break;
                                            case 'critical': echo 'bg-red-50 text-red-700 border border-red-200'; break;
                                            default: echo 'bg-gray-50 text-gray-700 border border-gray-200';
                                        }
                                        ?>">
                                        <?= $kondisi['nama_kondisi'] ?>
                                        <?php if (in_array($sample['status_sampel'], ['diterima', 'ditolak'])): ?>
                                        <button onclick="removeCondition(<?= $sample['sampel_id'] ?>, <?= $kondisi['kondisi_id'] ?>)" 
                                                class="ml-1 text-gray-400 hover:text-red-500">
                                            <i data-lucide="x" class="w-3 h-3"></i>
                                        </button>
                                        <?php endif; ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p class="text-xs text-gray-500 italic">Belum ada kondisi yang dicatat</p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Tanggal Info -->
                            <?php if ($sample['tanggal_pengambilan']): ?>
                            <p class="text-xs text-gray-500 mt-2">
                                Diambil: <?= date('d/m/Y H:i', strtotime($sample['tanggal_pengambilan'])) ?>
                                <?php if ($sample['petugas_pengambil_nama']): ?>
                                oleh <?= $sample['petugas_pengambil_nama'] ?>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if ($sample['tanggal_evaluasi']): ?>
                            <p class="text-xs text-gray-500">
                                Dievaluasi: <?= date('d/m/Y H:i', strtotime($sample['tanggal_evaluasi'])) ?>
                                <?php if ($sample['petugas_evaluasi_nama']): ?>
                                oleh <?= $sample['petugas_evaluasi_nama'] ?>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <?php if ($sample['status_sampel'] == 'belum_diambil'): ?>
                            <button onclick="updateSamplePengambilan(<?= $sample['sampel_id'] ?>)" 
                                    class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm transition-colors">
                                <i data-lucide="check" class="w-3 h-3 inline mr-1"></i>
                                Tandai Diambil
                            </button>
                            <?php elseif ($sample['status_sampel'] == 'sudah_diambil'): ?>
                            <button onclick="openEvaluasiModal(<?= $sample['sampel_id'] ?>)" 
                                    class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm transition-colors">
                                <i data-lucide="clipboard-check" class="w-3 h-3 inline mr-1"></i>
                                Evaluasi
                            </button>
                            <?php elseif (in_array($sample['status_sampel'], ['diterima', 'ditolak'])): ?>
                            <div class="flex space-x-2">
                                <button onclick="openAddConditionModal(<?= $sample['sampel_id'] ?>)" 
                                        class="px-3 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg text-sm transition-colors">
                                    <i data-lucide="plus-circle" class="w-3 h-3 inline mr-1"></i>
                                    Tambah Kondisi
                                </button>
                                <?php if ($sample['status_sampel'] == 'diterima'): ?>
                                <button onclick="viewSampleDetail(<?= $sample['sampel_id'] ?>)" 
                                        class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors">
                                    <i data-lucide="eye" class="w-3 h-3 inline mr-1"></i>
                                    Detail
                                </button>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Tambah Sampel -->
<div id="addSampleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Sampel Baru</h3>
        </div>
        <form id="addSampleForm" class="p-6">
            <input type="hidden" name="pemeriksaan_id" value="<?= $examination['pemeriksaan_id'] ?>">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Sampel</label>
                <select name="jenis_sampel" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Pilih Jenis Sampel --</option>
                    <?php foreach ($jenis_sampel_options as $key => $label): ?>
                    <option value="<?= $key ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="custom_sample_div" class="mb-4 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sampel Lainnya <span class="text-red-500">*</span></label>
                <input type="text" name="jenis_sampel_custom" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Contoh: Swab Tenggorokan">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
                <textarea name="keterangan_sampel" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="closeAddSampleModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Evaluasi Sampel -->
<div id="evaluasiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-900">Evaluasi Sampel</h3>
            <p class="text-sm text-gray-500 mt-1" id="evaluasiSampleName"></p>
        </div>
        
        <form id="evaluasiForm" class="p-6">
            <input type="hidden" name="sampel_id" id="evaluasiSampelId">
            
            <!-- Kondisi Sampel -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Kondisi Sampel</label>
                <div id="kondisiSampelList" class="space-y-2">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Catatan Kondisi -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                <textarea name="catatan_kondisi" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            
            <!-- Keputusan -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Keputusan Evaluasi</label>
                <div class="space-y-2">
                    <label class="flex items-center p-3 border-2 border-green-200 rounded-lg cursor-pointer hover:bg-green-50 transition-colors">
                        <input type="radio" name="keputusan" value="terima" required class="mr-3">
                        <div>
                            <p class="font-medium text-green-700">Terima Sampel</p>
                            <p class="text-xs text-green-600">Sampel dalam kondisi baik dan dapat diproses</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-3 border-2 border-red-200 rounded-lg cursor-pointer hover:bg-red-50 transition-colors">
                        <input type="radio" name="keputusan" value="tolak" required class="mr-3">
                        <div>
                            <p class="font-medium text-red-700">Tolak Sampel</p>
                            <p class="text-xs text-red-600">Sampel tidak memenuhi syarat, perlu pengambilan ulang</p>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Alasan Penolakan (akan muncul jika memilih tolak) -->
            <div id="alasanPenolakanSection" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="catatan_penolakan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Jelaskan alasan penolakan sampel..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="closeEvaluasiModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan Evaluasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Tambah Kondisi Sampel -->
<div id="addConditionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Kondisi Sampel</h3>
            <p class="text-sm text-gray-500 mt-1" id="conditionSampleName"></p>
        </div>
        
        <form id="addConditionForm" class="p-6">
            <input type="hidden" name="sampel_id" id="conditionSampelId">
            
            <!-- Pilih Jenis Kondisi -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Kondisi</label>
                <div id="conditionOptionsList" class="space-y-2 max-h-60 overflow-y-auto p-2 border border-gray-200 rounded-lg">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Catatan Kondisi -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                <textarea name="catatan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tambahkan catatan mengenai kondisi sampel..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="closeAddConditionModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Tambah Kondisi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Pass PHP data to JavaScript
const jenisSampelOptions = <?= json_encode($jenis_sampel_options) ?>;

// Toast Notification System
function showToast(type, message) {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toastId = 'toast-' + Date.now();
    const iconName = type === 'success' ? 'check-circle' : type === 'info' ? 'info' : type === 'warning' ? 'alert-triangle' : 'alert-circle';
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : type === 'info' ? 'bg-blue-50 border-blue-200' : type === 'warning' ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200';
    const iconColor = type === 'success' ? 'text-green-600' : type === 'info' ? 'text-blue-600' : type === 'warning' ? 'text-yellow-600' : 'text-red-600';
    const textColor = type === 'success' ? 'text-green-800' : type === 'info' ? 'text-blue-800' : type === 'warning' ? 'text-yellow-800' : 'text-red-800';
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${bgColor} ${textColor} border rounded-lg p-4 max-w-sm shadow-lg transform transition-all duration-500 ease-out translate-x-full opacity-0 pointer-events-auto flex items-start space-x-3`;
    toast.innerHTML = `
        <i data-lucide="${iconName}" class="w-5 h-5 ${iconColor} flex-shrink-0 mt-0.5"></i>
        <div class="flex-1">
            <p class="text-sm font-medium">${message}</p>
        </div>
        <button onclick="removeToast('${toastId}')" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    lucide.createIcons();
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    setTimeout(() => removeToast(toastId), 5000);
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (toast.parentElement) toast.parentElement.removeChild(toast);
        }, 500);
    }
}

// Custom Modal System
let modalConfirmCallback = null;

function showModal(config) {
    const modal = document.getElementById('custom-modal');
    const modalContent = modal.querySelector('div.bg-white');
    const title = document.getElementById('modal-title');
    const message = document.getElementById('modal-message');
    const iconContainer = document.getElementById('modal-icon-container');
    const icon = document.getElementById('modal-icon');
    const confirmBtn = document.getElementById('modal-confirm-btn');

    const defaultConfig = {
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin?',
        type: 'info',
        confirmText: 'Ya, Lanjutkan',
        onConfirm: () => {}
    };
    
    const finalConfig = { ...defaultConfig, ...config };

    title.textContent = finalConfig.title;
    message.textContent = finalConfig.message;
    confirmBtn.textContent = finalConfig.confirmText;
    modalConfirmCallback = finalConfig.onConfirm;

    // Reset classes
    iconContainer.className = 'mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-6 transition-colors duration-300';
    confirmBtn.className = 'px-6 py-2.5 text-white rounded-xl font-medium shadow-lg transition-all duration-200 transform hover:scale-105 focus:ring-4';

    // Style based on type
    if (finalConfig.type === 'danger') {
        iconContainer.classList.add('bg-red-50');
        icon.className = 'h-8 w-8 text-red-600';
        confirmBtn.classList.add('bg-gradient-to-r', 'from-red-600', 'to-red-700', 'hover:from-red-700', 'hover:to-red-800', 'shadow-red-200', 'focus:ring-red-200');
        icon.setAttribute('data-lucide', 'alert-triangle');
    } else if (finalConfig.type === 'success') {
        iconContainer.classList.add('bg-green-50');
        icon.className = 'h-8 w-8 text-green-600';
        confirmBtn.classList.add('bg-gradient-to-r', 'from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800', 'shadow-green-200', 'focus:ring-green-200');
        icon.setAttribute('data-lucide', 'check-circle');
    } else {
        iconContainer.classList.add('bg-blue-50');
        icon.className = 'h-8 w-8 text-blue-600';
        confirmBtn.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'hover:from-blue-700', 'hover:to-blue-800', 'shadow-blue-200', 'focus:ring-blue-200');
        icon.setAttribute('data-lucide', 'info');
    }

    modal.classList.remove('pointer-events-none', 'opacity-0');
    modalContent.classList.remove('scale-95');
    modalContent.classList.add('scale-100');
    lucide.createIcons();
}

function closeModal() {
    const modal = document.getElementById('custom-modal');
    const modalContent = modal.querySelector('div.bg-white');
    
    modal.classList.add('opacity-0');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('pointer-events-none');
        modalConfirmCallback = null;
    }, 300);
}

document.getElementById('modal-confirm-btn').addEventListener('click', () => {
    if (modalConfirmCallback) modalConfirmCallback();
    closeModal();
});

// Initialize on Load
document.addEventListener('DOMContentLoaded', () => {
    // Check sessionStorage
    const successMsg = sessionStorage.getItem('toast_success');
    if (successMsg) {
        setTimeout(() => showToast('success', successMsg), 500);
        sessionStorage.removeItem('toast_success');
    }
    
    // Check PHP Flashdata
    <?php if($this->session->flashdata('success')): ?>
    setTimeout(() => showToast('success', '<?= $this->session->flashdata('success') ?>'), 500);
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
    setTimeout(() => showToast('error', '<?= $this->session->flashdata('error') ?>'), 500);
    <?php endif; ?>
});

// Add Sample Modal Functions
function openAddSampleModal() {
    document.getElementById('addSampleModal').classList.remove('hidden');
}

function closeAddSampleModal() {
    document.getElementById('addSampleModal').classList.add('hidden');
    document.getElementById('addSampleForm').reset();
}

// Custom Sample Toggle
document.querySelector('select[name="jenis_sampel"]').addEventListener('change', function() {
    const customDiv = document.getElementById('custom_sample_div');
    const customInput = document.querySelector('input[name="jenis_sampel_custom"]');
    
    if (this.value === 'lain') {
        customDiv.classList.remove('hidden');
        customInput.required = true;
        customInput.focus();
    } else {
        customDiv.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
    }
});

// Handle Add Sample Form
// Handle Add Sample Form
document.getElementById('addSampleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('sample_data/create_sample') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            sessionStorage.setItem('toast_success', data.message);
            location.reload();
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat menyimpan data');
    });
});

// Update Sample Pengambilan
// Update Sample Pengambilan
function updateSamplePengambilan(sampelId) {
    showModal({
        title: 'Konfirmasi Pengambilan',
        message: 'Apakah Anda yakin ingin menandai sampel ini sebagai sudah diambil?',
        type: 'info',
        confirmText: 'Ya, Tandai Diambil',
        onConfirm: () => {
            fetch('<?= base_url('sample_data/update_sample_pengambilan') ?>/' + sampelId, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    sessionStorage.setItem('toast_success', data.message);
                    location.reload();
                } else {
                    showToast('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Terjadi kesalahan');
            });
        }
    });
}

// Open Evaluasi Modal
function openEvaluasiModal(sampelId) {
    document.getElementById('evaluasiSampelId').value = sampelId;
    
    // Fetch sample data
    fetch('<?= base_url('sample_data/get_sample_data') ?>/' + sampelId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const sample = data.sample;
            const conditions = data.available_conditions;
            
            // Set sample name using JavaScript variable
            document.getElementById('evaluasiSampleName').textContent = 
                jenisSampelOptions[sample.jenis_sampel] || sample.jenis_sampel;
            
            // Populate kondisi list
            const kondisiList = document.getElementById('kondisiSampelList');
            kondisiList.innerHTML = '';
            
            conditions.forEach(kondisi => {
                const div = document.createElement('div');
                const borderColor = kondisi.kategori === 'critical' ? 'border-red-200 bg-red-50' : 
                                   kondisi.kategori === 'acceptable' ? 'border-yellow-200 bg-yellow-50' : 
                                   'border-green-200 bg-green-50';
                const textColor = kondisi.kategori === 'critical' ? 'text-red-700' : 
                                 kondisi.kategori === 'acceptable' ? 'text-yellow-700' : 
                                 'text-green-700';
                const descColor = kondisi.kategori === 'critical' ? 'text-red-600' : 
                                 kondisi.kategori === 'acceptable' ? 'text-yellow-600' : 
                                 'text-green-600';
                
                div.className = 'flex items-start p-3 border rounded-lg ' + borderColor;
                div.innerHTML = `
                    <input type="checkbox" name="kondisi_ids[]" value="${kondisi.kondisi_id}" 
                           class="mt-1 mr-3" id="kondisi_${kondisi.kondisi_id}">
                    <label for="kondisi_${kondisi.kondisi_id}" class="flex-1 cursor-pointer">
                        <p class="font-medium ${textColor}">${kondisi.nama_kondisi}</p>
                        <p class="text-xs ${descColor}">${kondisi.deskripsi}</p>
                    </label>
                `;
                
                kondisiList.appendChild(div);
            });
            
            document.getElementById('evaluasiModal').classList.remove('hidden');
            document.getElementById('evaluasiModal').classList.remove('hidden');
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat memuat data');
    });
}

function closeEvaluasiModal() {
    document.getElementById('evaluasiModal').classList.add('hidden');
    document.getElementById('evaluasiForm').reset();
    document.getElementById('alasanPenolakanSection').classList.add('hidden');
}

// Toggle alasan penolakan
document.querySelectorAll('input[name="keputusan"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const alasanSection = document.getElementById('alasanPenolakanSection');
        const catatanPenolakan = document.querySelector('textarea[name="catatan_penolakan"]');
        
        if (this.value === 'tolak') {
            alasanSection.classList.remove('hidden');
            catatanPenolakan.required = true;
        } else {
            alasanSection.classList.add('hidden');
            catatanPenolakan.required = false;
        }
    });
});

// Handle Evaluasi Form
document.getElementById('evaluasiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const keputusan = formData.get('keputusan');
    const sampelId = formData.get('sampel_id');
    
    const url = keputusan === 'terima' 
        ? '<?= base_url('sample_data/update_sample_diterima_with_conditions') ?>/' + sampelId
        : '<?= base_url('sample_data/update_sample_ditolak_with_conditions') ?>/' + sampelId;
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            sessionStorage.setItem('toast_success', data.message);
            location.reload();
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat menyimpan evaluasi');
    });
});

// Open Add Condition Modal
function openAddConditionModal(sampelId) {
    document.getElementById('conditionSampelId').value = sampelId;
    
    // Fetch sample data
    fetch('<?= base_url('sample_data/get_sample_data') ?>/' + sampelId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const sample = data.sample;
            const conditions = data.available_conditions;
            
            // Set sample name
            document.getElementById('conditionSampleName').textContent = 
                jenisSampelOptions[sample.jenis_sampel] || sample.jenis_sampel;
            
            // Populate condition options
            const optionsList = document.getElementById('conditionOptionsList');
            optionsList.innerHTML = '';
            
            if (conditions.length === 0) {
                optionsList.innerHTML = `
                    <div class="text-center py-4 text-gray-500">
                        <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                        <p>Tidak ada kondisi tersedia untuk jenis sampel ini</p>
                    </div>
                `;
                return;
            }
            
            // Group conditions by kategori
            const groupedConditions = {};
            conditions.forEach(kondisi => {
                if (!groupedConditions[kondisi.kategori]) {
                    groupedConditions[kondisi.kategori] = [];
                }
                groupedConditions[kondisi.kategori].push(kondisi);
            });
            
            // Display by kategori
            Object.keys(groupedConditions).forEach(kategori => {
                const categoryDiv = document.createElement('div');
                categoryDiv.className = 'mb-4';
                
                // Category label
                let categoryLabel = '';
                let categoryColor = '';
                
                switch(kategori) {
                    case 'critical':
                        categoryLabel = 'Kritis';
                        categoryColor = 'text-red-700 bg-red-50';
                        break;
                    case 'acceptable':
                        categoryLabel = 'Dapat Diterima';
                        categoryColor = 'text-yellow-700 bg-yellow-50';
                        break;
                    case 'normal':
                        categoryLabel = 'Normal';
                        categoryColor = 'text-green-700 bg-green-50';
                        break;
                    default:
                        categoryLabel = 'Lainnya';
                        categoryColor = 'text-gray-700 bg-gray-50';
                }
                
                categoryDiv.innerHTML = `
                    <h4 class="text-sm font-medium mb-2 px-2 py-1 rounded ${categoryColor}">
                        ${categoryLabel} (${groupedConditions[kategori].length})
                    </h4>
                `;
                
                // Condition items
                const conditionsDiv = document.createElement('div');
                conditionsDiv.className = 'space-y-2';
                
                groupedConditions[kategori].forEach(kondisi => {
                    const conditionDiv = document.createElement('div');
                    conditionDiv.className = 'flex items-start p-3 border rounded-lg hover:bg-gray-50 transition-colors';
                    conditionDiv.innerHTML = `
                        <input type="checkbox" name="kondisi_ids[]" value="${kondisi.kondisi_id}" 
                               class="mt-1 mr-3" id="condition_${kondisi.kondisi_id}">
                        <label for="condition_${kondisi.kondisi_id}" class="flex-1 cursor-pointer">
                            <p class="font-medium text-gray-900">${kondisi.nama_kondisi}</p>
                            <p class="text-xs text-gray-600 mt-1">${kondisi.deskripsi}</p>
                        </label>
                    `;
                    conditionsDiv.appendChild(conditionDiv);
                });
                
                categoryDiv.appendChild(conditionsDiv);
                optionsList.appendChild(categoryDiv);
            });
            
            document.getElementById('addConditionModal').classList.remove('hidden');
            
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat memuat data');
    });
}

// Close Add Condition Modal
function closeAddConditionModal() {
    document.getElementById('addConditionModal').classList.add('hidden');
    document.getElementById('addConditionForm').reset();
}

// Handle Add Condition Form
document.getElementById('addConditionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const sampelId = formData.get('sampel_id');
    
    // Get selected kondisi_ids
    const selectedConditions = [];
    document.querySelectorAll('input[name="kondisi_ids[]"]:checked').forEach(checkbox => {
        selectedConditions.push(checkbox.value);
    });
    
    if (selectedConditions.length === 0) {
        showToast('warning', 'Pilih minimal satu kondisi');
        return;
    }
    
    // Add kondisi_ids to formData
    selectedConditions.forEach(id => {
        formData.append('kondisi_ids[]', id);
    });
    
    fetch('<?= base_url('sample_data/bulk_add_conditions') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            sessionStorage.setItem('toast_success', data.message);
            location.reload();
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat menyimpan kondisi');
    });
});

// Remove individual condition
// Remove individual condition
function removeCondition(sampelId, kondisiId) {
    showModal({
        title: 'Hapus Kondisi',
        message: 'Apakah Anda yakin ingin menghapus kondisi ini dari sampel?',
        type: 'danger',
        confirmText: 'Ya, Hapus',
        onConfirm: () => {
            fetch('<?= base_url('sample_data/remove_sample_condition') ?>/' + kondisiId, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    sessionStorage.setItem('toast_success', data.message);
                    location.reload();
                } else {
                    showToast('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Terjadi kesalahan');
            });
        }
    });
}

function viewSampleDetail(sampelId) {
    // Fetch sample detail data
    fetch('<?= base_url('sample_data/get_sample_data') ?>/' + sampelId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const sample = data.sample;
            
            // Create modal content
            let detailHtml = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Jenis Sampel</p>
                            <p class="font-medium">${jenisSampelOptions[sample.jenis_sampel] || sample.jenis_sampel}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-medium">${sample.status_sampel}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Pengambilan</p>
                            <p class="font-medium">${sample.tanggal_pengambilan || '-'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Evaluasi</p>
                            <p class="font-medium">${sample.tanggal_evaluasi || '-'}</p>
                        </div>
                    </div>
                    ${sample.kondisi_details && sample.kondisi_details.length > 0 ? `
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Kondisi Sampel</p>
                            <div class="flex flex-wrap gap-2">
                                ${sample.kondisi_details.map(k => `
                                    <span class="px-2 py-1 text-xs rounded ${
                                        k.kategori === 'critical' ? 'bg-red-100 text-red-700' :
                                        k.kategori === 'warning' ? 'bg-yellow-100 text-yellow-700' :
                                        'bg-green-100 text-green-700'
                                    }">${k.nama_kondisi}</span>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    ${sample.catatan_kondisi ? `
                        <div>
                            <p class="text-sm text-gray-500">Catatan</p>
                            <p class="font-medium">${sample.catatan_kondisi}</p>
                        </div>
                    ` : ''}
                </div>
            `;
            
            // Show using custom modal or alert
            showModal({
                title: 'Detail Sampel',
                message: '',
                type: 'info',
                confirmText: 'Tutup',
                onConfirm: () => {}
            });
            
            // Update modal message with detail HTML
            document.getElementById('modal-message').innerHTML = detailHtml;
            
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat memuat detail sampel');
    });
}
</script>


</body>
</html>