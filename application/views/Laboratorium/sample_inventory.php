<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - LabSy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .loading { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        
        .status-badge {
            @apply px-3 py-1 rounded-full text-xs font-medium;
        }
        
        .sample-card {
            transition: all 0.3s ease;
        }
        
        .sample-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
<!-- Toast Container -->
<div id="toast-container" class="fixed top-24 right-6 z-50 flex flex-col gap-3 pointer-events-none"></div>

<!-- Header -->
<div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="archive" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Inventory Sampel</h1>
                <p class="text-blue-100">Monitoring Penyimpanan & Masa Berlaku Sampel</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <button onclick="openTempLogModal()" 
                    class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                <i data-lucide="thermometer" class="w-4 h-4"></i>
                <span>Log Suhu</span>
            </button>
            <a href="<?= base_url('sample_inventory/export_excel') ?>" 
               class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="p-6 space-y-6">
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Sampel</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $summary['total_samples'] ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="test-tubes" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Lokasi Storage</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $summary['total_locations'] ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="map-pin" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Valid</p>
                    <p class="text-2xl font-bold text-green-600"><?= $summary['valid_count'] ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Akan Expired</p>
                    <p class="text-2xl font-bold text-orange-600"><?= $summary['expiring_soon_count'] ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Expired</p>
                    <p class="text-2xl font-bold text-red-600"><?= $summary['expired_count'] ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Soon Alert -->
    <?php if (!empty($expiring_samples)): ?>
    <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="alert-circle" class="w-5 h-5 text-orange-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-orange-800">Perhatian: <?= count($expiring_samples) ?> Sampel Akan Segera Expired</h3>
                <div class="mt-2 text-sm text-orange-700">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach (array_slice($expiring_samples, 0, 3) as $sample): ?>
                        <li><?= $sample['nomor_pemeriksaan'] ?> - <?= $sample['jenis_sampel'] ?> 
                            (<?= $sample['days_remaining'] ?> hari lagi)</li>
                        <?php endforeach; ?>
                        <?php if (count($expiring_samples) > 3): ?>
                        <li class="text-orange-600 font-medium">Dan <?= count($expiring_samples) - 3 ?> sampel lainnya...</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filter & Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" id="searchInput" 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Cari nomor pemeriksaan, pasien...">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Storage</label>
                <select id="filterLocation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Lokasi</option>
                    <?php foreach ($storage_locations as $loc): ?>
                    <option value="<?= $loc['lokasi_penyimpanan'] ?>"><?= $loc['lokasi_penyimpanan'] ?> (<?= $loc['jumlah_sampel'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Berlaku</label>
                <select id="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="valid">Valid</option>
                    <option value="expiring_soon">Akan Expired</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button onclick="applyFilters()" 
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="filter" class="w-4 h-4 inline mr-2"></i>
                    Terapkan Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Samples Grid -->
    <div id="samplesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($samples)): ?>
        <div class="col-span-3 text-center py-12">
            <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Sampel di Inventory</h3>
            <p class="text-gray-500">Sampel akan otomatis masuk ke inventory setelah diambil</p>
        </div>
        <?php else: ?>
            <?php foreach ($samples as $sample): ?>
            <div class="sample-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                 data-location="<?= $sample['lokasi_penyimpanan'] ?>"
                 data-status="<?= $sample['status_berlaku'] ?>"
                 data-search="<?= strtolower($sample['nomor_pemeriksaan'] . ' ' . $sample['nama_pasien'] . ' ' . $sample['jenis_sampel']) ?>">
                
                <!-- Status Bar -->
                <div class="h-2 <?php 
                    switch($sample['status_berlaku']) {
                        case 'expired': echo 'bg-red-500'; break;
                        case 'expiring_soon': echo 'bg-orange-500'; break;
                        default: echo 'bg-green-500';
                    }
                ?>"></div>
                
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 mb-1"><?= $sample['nomor_pemeriksaan'] ?></h3>
                            <p class="text-sm text-gray-600"><?= $sample['nama_pasien'] ?></p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs font-medium
                            <?php 
                            switch($sample['status_berlaku']) {
                                case 'expired': echo 'bg-red-100 text-red-700'; break;
                                case 'expiring_soon': echo 'bg-orange-100 text-orange-700'; break;
                                default: echo 'bg-green-100 text-green-700';
                            }
                            ?>">
                            <?php 
                            switch($sample['status_berlaku']) {
                                case 'expired': echo 'Expired'; break;
                                case 'expiring_soon': echo 'Akan Expired'; break;
                                default: echo 'Valid';
                            }
                            ?>
                        </span>
                    </div>
                    
                    <!-- Sample Info -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm">
                            <i data-lucide="test-tube" class="w-4 h-4 text-gray-400 mr-2"></i>
                            <span class="text-gray-600">Jenis:</span>
                            <span class="ml-auto font-medium text-gray-900"><?= ucfirst(str_replace('_', ' ', $sample['jenis_sampel'])) ?></span>
                        </div>
                        
                        <div class="flex items-center text-sm">
                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-400 mr-2"></i>
                            <span class="text-gray-600">Lokasi:</span>
                            <span class="ml-auto font-medium text-gray-900"><?= $sample['lokasi_penyimpanan'] ?></span>
                        </div>
                        
                        <div class="flex items-center text-sm">
                            <i data-lucide="thermometer" class="w-4 h-4 text-gray-400 mr-2"></i>
                            <span class="text-gray-600">Suhu:</span>
                            <span class="ml-auto font-medium text-gray-900"><?= $sample['suhu_penyimpanan'] ?>°C</span>
                        </div>
                        
                        <div class="flex items-center text-sm">
                            <i data-lucide="droplet" class="w-4 h-4 text-gray-400 mr-2"></i>
                            <span class="text-gray-600">Volume:</span>
                            <span class="ml-auto font-medium text-gray-900"><?= $sample['volume_sampel'] ?> <?= $sample['satuan_volume'] ?></span>
                        </div>
                    </div>
                    
                    <!-- Timeline -->
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex items-center text-xs text-gray-500">
                            <i data-lucide="calendar" class="w-3 h-3 mr-2"></i>
                            <span>Masuk: <?= !empty($sample['tanggal_masuk']) ? date('d/m/Y H:i', strtotime($sample['tanggal_masuk'])) : 'N/A' ?></span>
                        </div>
                        
                        <div class="flex items-center text-xs 
                            <?php echo $sample['status_berlaku'] == 'expired' ? 'text-red-600' : 
                                     ($sample['status_berlaku'] == 'expiring_soon' ? 'text-orange-600' : 'text-green-600'); ?>">
                            <i data-lucide="clock" class="w-3 h-3 mr-2"></i>
                            <span>Expired: <?= !empty($sample['tanggal_kadaluarsa']) ? date('d/m/Y H:i', strtotime($sample['tanggal_kadaluarsa'])) : 'N/A' ?></span>
                            <?php if ($sample['days_remaining'] > 0): ?>
                            <span class="ml-auto font-medium"><?= $sample['days_remaining'] ?> hari lagi</span>
                            <?php else: ?>
                            <span class="ml-auto font-medium">Sudah Expired</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="border-t border-gray-200 pt-4 mt-4 flex space-x-2">
                        <button onclick="viewSampleDetail(<?= $sample['storage_id'] ?>)"
                                class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors">
                            <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                            Detail
                        </button>
                        
                        <button onclick="updateStorageStatus(<?= $sample['storage_id'] ?>)"
                                class="flex-1 px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm transition-colors">
                            <i data-lucide="edit" class="w-4 h-4 inline mr-1"></i>
                            Update
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Temperature Log -->
<div id="tempLogModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Log Monitoring Suhu</h3>
        </div>
        <form id="tempLogForm" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Storage <span class="text-red-500">*</span></label>
                <select name="lokasi_storage" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Lokasi --</option>
                    <?php foreach ($storage_locations as $loc): ?>
                    <option value="<?= $loc['lokasi_penyimpanan'] ?>"><?= $loc['lokasi_penyimpanan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Suhu (°C) <span class="text-red-500">*</span></label>
                <input type="number" step="0.1" name="suhu_tercatat" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: 4.5">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelembaban (%)</label>
                <input type="number" step="0.1" name="kelembaban" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: 60">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" rows="2" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Catatan tambahan..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="closeTempLogModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan Log
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Sample Detail -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-900">Detail Sampel</h3>
        </div>
        <div id="detailContent" class="p-6">
            <!-- Will be filled by JavaScript -->
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="modal" class="fixed inset-0 bg-black/50 hidden z-[60] flex items-center justify-center p-4 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-xl max-w-sm w-full shadow-2xl transform transition-all scale-100 opacity-100">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-lg font-bold text-gray-900">Konfirmasi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <p id="modal-message" class="text-gray-600 mb-6 font-medium">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
            
            <div id="modal-content-extra" class="mb-4 hidden">
                <!-- For additional inputs like select or textarea -->
            </div>
            
            <div class="flex items-center justify-end space-x-3">
                <button onclick="closeModal()" class="px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 font-medium transition-colors">
                    Batal
                </button>
                <button id="modal-confirm-btn" class="px-4 py-2 rounded-lg font-medium transition-all duration-300 bg-red-500/10 text-red-600 border border-red-500/20 hover:bg-red-500/20 hover:shadow-[0_0_15px_rgba(239,68,68,0.3)]">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Lucide
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Temperature Log Modal
function openTempLogModal() {
    document.getElementById('tempLogModal').classList.remove('hidden');
}

function closeTempLogModal() {
    document.getElementById('tempLogModal').classList.add('hidden');
    document.getElementById('tempLogForm').reset();
}

document.getElementById('tempLogForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('sample_inventory/log_temperature') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            closeTempLogModal();
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan sistem');
    });
});

// Filter Functions
function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const filterLocation = document.getElementById('filterLocation').value;
    const filterStatus = document.getElementById('filterStatus').value;
    
    const cards = document.querySelectorAll('.sample-card');
    
    cards.forEach(card => {
        const searchData = card.getAttribute('data-search');
        const location = card.getAttribute('data-location');
        const status = card.getAttribute('data-status');
        
        const matchSearch = !searchTerm || searchData.includes(searchTerm);
        const matchLocation = !filterLocation || location === filterLocation;
        const matchStatus = !filterStatus || status === filterStatus;
        
        if (matchSearch && matchLocation && matchStatus) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Real-time search
document.getElementById('searchInput').addEventListener('input', applyFilters);

// Toast Notification System
// Toast Notification System
function showToast(type, message) {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const iconName = type === 'success' ? 'check-circle' : type === 'info' ? 'info' : type === 'warning' ? 'alert-triangle' : 'alert-circle';
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : type === 'info' ? 'bg-blue-50 border-blue-200' : type === 'warning' ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200';
    const iconColor = type === 'success' ? 'text-green-600' : type === 'info' ? 'text-blue-600' : type === 'warning' ? 'text-yellow-600' : 'text-red-600';
    const textColor = type === 'success' ? 'text-green-800' : type === 'info' ? 'text-blue-800' : type === 'warning' ? 'text-yellow-800' : 'text-red-800';
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${bgColor} ${textColor} border rounded-lg p-4 max-w-sm shadow-lg transform transition-all duration-500 ease-out translate-x-full opacity-0`;
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
        setTimeout(() => toast.remove(), 500);
    }
}

// Check for PHP Flashdata
document.addEventListener('DOMContentLoaded', () => {
    <?php if($this->session->flashdata('success')): ?>
    setTimeout(() => showToast('success', '<?= $this->session->flashdata('success') ?>'), 500);
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
    setTimeout(() => showToast('error', '<?= $this->session->flashdata('error') ?>'), 500);
    <?php endif; ?>
});

// View Sample Detail
function viewSampleDetail(storageId) {
    fetch('<?= base_url('sample_inventory/get_sample_detail') ?>/' + storageId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const sample = data.sample;
            const content = document.getElementById('detailContent');
            
            content.innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nomor Pemeriksaan</p>
                            <p class="font-semibold text-gray-900">${sample.nomor_pemeriksaan}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nama Pasien</p>
                            <p class="font-semibold text-gray-900">${sample.nama_pasien}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">NIK</p>
                            <p class="font-semibold text-gray-900">${sample.nik}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jenis Sampel</p>
                            <p class="font-semibold text-gray-900">${sample.jenis_sampel}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lokasi Penyimpanan</p>
                            <p class="font-semibold text-gray-900">${sample.lokasi_penyimpanan}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Suhu Penyimpanan</p>
                            <p class="font-semibold text-gray-900">${sample.suhu_penyimpanan}°C</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Suhu Optimal</p>
                            <p class="font-semibold text-gray-900">${sample.suhu_optimal_min}°C - ${sample.suhu_optimal_max}°C</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Volume</p>
                            <p class="font-semibold text-gray-900">${sample.volume_sampel} ${sample.satuan_volume}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Masuk</p>
                            <p class="font-semibold text-gray-900">${sample.tanggal_masuk}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Kadaluarsa</p>
                            <p class="font-semibold text-gray-900">${sample.tanggal_kadaluarsa}</p>
                        </div>
                    </div>
                    
                    ${sample.keterangan ? `
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Keterangan</p>
                        <p class="text-gray-900">${sample.keterangan}</p>
                    </div>
                    ` : ''}
                    
                    <div class="border-t pt-4">
                        <button onclick="closeDetailModal()" 
                                class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">
                            Tutup
                        </button>
                    </div>
                </div>
            `;
            
            document.getElementById('detailModal').classList.remove('hidden');
        } else {
            showToast('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan sistem');
    });
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

// Custom Modal Logic
function showModal(title, message, options = {}) {
    document.getElementById('modal-title').innerText = title;
    document.getElementById('modal-message').innerText = message;
    
    // Reset extra content
    const extraContent = document.getElementById('modal-content-extra');
    extraContent.innerHTML = '';
    extraContent.classList.add('hidden');
    
    // Handle extra inputs if any
    if (options.input) {
        extraContent.innerHTML = options.input;
        extraContent.classList.remove('hidden');
    }

    const confirmBtn = document.getElementById('modal-confirm-btn');
    
    // Reset classes
    confirmBtn.className = 'px-4 py-2 rounded-lg font-medium transition-all duration-300 ' + 
        (options.confirmClass || 'bg-red-500/10 text-red-600 border border-red-500/20 hover:bg-red-500/20 hover:shadow-[0_0_15px_rgba(239,68,68,0.3)]');
    
    confirmBtn.onclick = function() {
        if (options.onConfirm) options.onConfirm();
    };
    
    const modal = document.getElementById('modal');
    modal.classList.remove('hidden');
    // Animate in
    setTimeout(() => {
        modal.firstElementChild.classList.remove('scale-95', 'opacity-0');
        modal.firstElementChild.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('modal');
    // Animate out
    modal.firstElementChild.classList.remove('scale-100', 'opacity-100');
    modal.firstElementChild.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Update Storage Status
function updateStorageStatus(storageId) {
    const inputHtml = `
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Baru</label>
                <select id="modal-status-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="tersimpan">Tersimpan</option>
                    <option value="diproses">Diproses</option>
                    <option value="dibuang">Dibuang</option>
                    <option value="dikembalikan">Dikembalikan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea id="modal-keterangan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Opsional..."></textarea>
            </div>
        </div>
    `;

    showModal(
        'Update Status Penyimpanan',
        'Silakan pilih status baru untuk sampel ini:',
        {
            input: inputHtml,
            confirmClass: 'bg-blue-500/10 text-blue-600 border border-blue-500/20 hover:bg-blue-500/20 hover:shadow-[0_0_15px_rgba(37,99,235,0.3)]',
            onConfirm: function() {
                const newStatus = document.getElementById('modal-status-select').value;
                const keterangan = document.getElementById('modal-keterangan').value;
                
                // Show loading state on button
                const btn = document.getElementById('modal-confirm-btn');
                btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 loading inline"></i> Proses...';
                btn.disabled = true;
                lucide.createIcons();
                
                const formData = new FormData();
                formData.append('status_penyimpanan', newStatus);
                formData.append('keterangan', keterangan);
                
                fetch('<?= base_url('sample_inventory/update_storage_status') ?>/' + storageId, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('error', 'Error: ' + data.message);
                        btn.innerHTML = 'Konfirmasi';
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Terjadi kesalahan sistem');
                    btn.innerHTML = 'Konfirmasi';
                    btn.disabled = false;
                });
                
                closeModal();
            }
        }
    );
}
</script>

</body>
</html>