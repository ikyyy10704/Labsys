<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - LabSy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            width: 100%;
        }

        /* Ensure full width */
        .fullwidth-container {
            width: 100%;
            max-width: 100%;
        }

        /* Loading animation */
        .loading {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Fade in animation */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
<!-- Header Section - SAMA TINGGI DENGAN INDEX -->
<div class="w-full bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="inbox" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Permintaan Pemeriksaan Masuk</h1>
                    <p class="text-blue-100">Kelola permintaan pemeriksaan laboratorium yang masuk</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Stats Card -->
                <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 px-4 py-3 shadow-lg">
                    <p class="text-blue-100 text-xs font-medium mb-0.5">Total Menunggu</p>
                    <p class="text-xl font-bold text-white"><?= count($requests) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Content - FULLWIDTH (NO MAX-WIDTH) -->
<div class="w-full px-6 py-6 space-y-6">
    
    <!-- Filters Section -->
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                <span>Filter & Pencarian</span>
            </h2>
        </div>
        
        <div class="p-6">
            <form method="GET" action="<?= base_url('laboratorium/incoming_requests') ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                        <input type="date" id="date_from" name="date_from" value="<?= $filters['date_from'] ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                        <input type="date" id="date_to" name="date_to" value="<?= $filters['date_to'] ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="jenis_pemeriksaan" class="block text-sm font-medium text-gray-700 mb-2">Jenis Pemeriksaan</label>
                        <select id="jenis_pemeriksaan" name="jenis_pemeriksaan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Jenis</option>
                            <?php foreach ($examination_types as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $filters['jenis_pemeriksaan'] == $key ? 'selected' : '' ?>><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
                        <select id="priority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Prioritas</option>
                            <option value="urgent" <?= $filters['priority'] == 'urgent' ? 'selected' : '' ?>>Mendesak</option>
                            <option value="high" <?= $filters['priority'] == 'high' ? 'selected' : '' ?>>Tinggi</option>
                            <option value="normal" <?= $filters['priority'] == 'normal' ? 'selected' : '' ?>>Normal</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                        <input type="text" id="search" name="search" value="<?= $filters['search'] ?>" 
                               placeholder="Nama, NIK, nomor..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-100">
                    <div class="flex items-center space-x-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                            <span>Terapkan</span>
                        </button>
                        <a href="<?= base_url('laboratorium/incoming_requests') ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            <span>Reset</span>
                        </a>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-semibold text-gray-900"><?= $total_requests ?></span> permintaan
                        <span class="mx-2">•</span>
                        Hal <span class="font-semibold text-gray-900"><?= $current_page ?></span>/<span class="font-semibold text-gray-900"><?= $total_pages ?></span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Pilih Semua</span>
                </label>
                
                <button type="button" id="acceptSelectedBtn" disabled 
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors duration-200">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1.5"></i>
                    Terima Terpilih
                </button>
            </div>
            <span class="text-sm text-gray-600">
                <span id="selectedCount">0</span> dipilih
            </span>
        </div>
    </div>

    <!-- Requests List -->
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                <i data-lucide="list" class="w-5 h-5 text-blue-600"></i>
                <span>Daftar Permintaan</span>
                <span class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                    <?= count($requests) ?> item
                </span>
            </h2>
        </div>
        
        <?php if (empty($requests)): ?>
        <!-- Empty State -->
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="inbox" class="w-10 h-10 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Tidak Ada Permintaan</h3>
            <p class="text-sm text-gray-500">Permintaan baru akan muncul di sini</p>
        </div>
        <?php else: ?>
        
        <div class="divide-y divide-gray-200">
            <?php foreach ($requests as $request): ?>
            <!-- Request Card -->
            <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                <div class="flex items-start gap-3">
                    <!-- Checkbox -->
                    <input type="checkbox" value="<?= $request['pemeriksaan_id'] ?>" 
                           class="request-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1">
                    
                    <!-- Priority Badge -->
                    <div class="flex-shrink-0">
                        <?php
                        $priorityConfig = [
                            'urgent' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'alert-triangle'],
                            'high' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => 'clock'],
                            'normal' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'file-text']
                        ];
                        $config = $priorityConfig[$request['priority_info']['level']] ?? $priorityConfig['normal'];
                        ?>
                        <div class="w-10 h-10 <?= $config['bg'] ?> rounded-lg flex items-center justify-center">
                            <i data-lucide="<?= $config['icon'] ?>" class="w-5 h-5 <?= $config['text'] ?>"></i>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <!-- Header Row with Nomor Pemeriksaan -->
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="flex-1">
                                <!-- Nomor Pemeriksaan Badge - PROMINENT -->
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 border border-blue-200 rounded-md">
                                        <i data-lucide="hash" class="w-3.5 h-3.5 text-blue-600 mr-1"></i>
                                        <span class="text-xs font-mono font-semibold text-blue-900"><?= $request['nomor_pemeriksaan'] ?></span>
                                    </span>
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full <?= $config['bg'] ?> <?= $config['text'] ?>">
                                        <?= $request['priority_info']['label'] ?>
                                    </span>
                                </div>
                                
                                <!-- Jenis Pemeriksaan -->
                                <h3 class="text-base font-bold text-gray-900 mb-1"><?= $request['jenis_pemeriksaan'] ?></h3>
                                
                                <!-- Patient Info -->
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium"><?= $request['nama_pasien'] ?></span>
                                    <span class="mx-1">•</span>
                                    <?= $request['jenis_kelamin'] == 'L' ? 'L' : 'P' ?>, <?= $request['umur'] ?>th
                                    <span class="mx-1">•</span>
                                    NIK: <?= $request['nik'] ?>
                                </p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <button onclick="acceptRequest(<?= $request['pemeriksaan_id'] ?>)" 
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                                    <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                    Terima
                                </button>
                                <button onclick="viewDetails(<?= $request['pemeriksaan_id'] ?>)" 
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                    Detail
                                </button>
                            </div>
                        </div>
                        
                        <!-- Info Grid -->
                       <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-x-8 gap-y-2 text-xs text-gray-600 mt-3">
    <div class="flex items-center gap-1">
        <i data-lucide="calendar" class="w-3 h-3 text-gray-500"></i>
        <span class="font-medium text-gray-700">Tanggal:</span> 
        <span><?= date('d/m/Y H:i', strtotime($request['tanggal_pemeriksaan'])) ?></span>
    </div>
    <div class="flex items-center gap-1">
        <i data-lucide="clock" class="w-3 h-3 text-orange-500"></i>
        <span class="font-medium text-orange-600">Menunggu:</span> 
        <span class="font-semibold"><?= $request['hours_waiting'] ?> jam</span>
    </div>
    <?php if ($request['dokter_perujuk']): ?>
    <div class="flex items-center gap-1">
        <i data-lucide="user-round" class="w-3 h-3 text-gray-500"></i>
        <span class="font-medium text-gray-700">Dokter:</span> 
        <span class="truncate"><?= $request['dokter_perujuk'] ?></span>
    </div>
    <?php endif; ?>
    <?php if ($request['asal_rujukan']): ?>
    <div class="flex items-center gap-1">
        <i data-lucide="building-2" class="w-3 h-3 text-gray-500"></i>
        <span class="font-medium text-gray-700">Rujukan:</span> 
        <span class="truncate"><?= $request['asal_rujukan'] ?></span>
    </div>
    <?php endif; ?>
    <?php if ($request['telepon']): ?>
    <div class="flex items-center gap-1">
        <i data-lucide="phone" class="w-3 h-3 text-gray-500"></i>
        <span class="font-medium text-gray-700">Telepon:</span> 
        <span><?= $request['telepon'] ?></span>
    </div>
    <?php endif; ?>
</div>
                        
                        <!-- Notes -->
                        <?php if ($request['keterangan']): ?>
                        <div class="mt-2 p-2 bg-amber-50 border border-amber-200 rounded text-xs text-gray-700">
                            <strong>Keterangan:</strong> <?= $request['keterangan'] ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="p-4 border-t border-gray-100 flex items-center justify-between">
            <div class="text-xs text-gray-600">
                Hal <?= $current_page ?> dari <?= $total_pages ?>
            </div>
            <div class="flex gap-1">
                <?php if ($has_prev): ?>
                <a href="<?= base_url('laboratorium/incoming_requests') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page - 1])) ?>" 
                   class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-50 text-xs">
                    <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                <a href="<?= base_url('laboratorium/incoming_requests') ?>?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>" 
                   class="px-2.5 py-1 border text-xs <?= $i == $current_page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?> rounded">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($has_next): ?>
                <a href="<?= base_url('laboratorium/incoming_requests') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page + 1])) ?>" 
                   class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-50 text-xs">
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Detail Modal (tetap sama) -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden fade-in">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-6 border-b border-blue-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                            <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Detail Pemeriksaan</h2>
                            <p class="text-sm text-blue-100" id="modalSubtitle">Loading...</p>
                        </div>
                    </div>
                    <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(90vh-120px)] p-6" id="modalContent">
                <!-- Loading State -->
                <div class="flex items-center justify-center py-12">
                    <i data-lucide="loader" class="w-8 h-8 text-blue-600 loading"></i>
                    <span class="ml-3 text-gray-600">Memuat data...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.request-checkbox').forEach(cb => cb.checked = this.checked);
    updateBulkActionButton();
});

// Update bulk action button state
function updateBulkActionButton() {
    const checkedBoxes = document.querySelectorAll('.request-checkbox:checked');
    document.getElementById('acceptSelectedBtn').disabled = checkedBoxes.length === 0;
    document.getElementById('selectedCount').textContent = checkedBoxes.length;
}

// Add event listeners to individual checkboxes
document.querySelectorAll('.request-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActionButton);
});

// Accept single request with confirmation
function acceptRequest(examId) {
    if (confirm('Terima permintaan pemeriksaan ini?')) {
        const button = event.target.closest('button');
        button.innerHTML = '<i data-lucide="loader" class="w-3 h-3 mr-1 loading"></i>Loading...';
        button.disabled = true;
        lucide.createIcons();
        window.location.href = '<?= base_url('laboratorium/accept_request') ?>/' + examId;
    }
}

// Accept multiple requests
document.getElementById('acceptSelectedBtn').addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.request-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Terima ${checkedBoxes.length} permintaan terpilih?`)) {
        const requestIds = Array.from(checkedBoxes).map(cb => cb.value);
        this.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-1.5 loading"></i>Proses...';
        this.disabled = true;
        lucide.createIcons();
        
        fetch('<?= base_url('laboratorium/accept_multiple_requests') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'request_ids=' + JSON.stringify(requestIds)
        })
        .then(r => r.json())
        .then(data => {
            alert(data.success ? data.message : 'Error: ' + data.message);
            if (data.success) location.reload();
            else {
                this.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-1.5"></i>Terima Terpilih';
                this.disabled = false;
                lucide.createIcons();
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan');
            this.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-1.5"></i>Terima Terpilih';
            this.disabled = false;
            lucide.createIcons();
        });
    }
});

// VIEW DETAILS - OPEN MODAL
function viewDetails(examId) {
    document.getElementById('detailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Fetch detail data
    fetch('<?= base_url('laboratorium/get_examination_detail') ?>/' + examId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderDetailContent(data.examination);
            } else {
                document.getElementById('modalContent').innerHTML = `
                    <div class="text-center py-12">
                        <i data-lucide="alert-circle" class="w-16 h-16 text-red-500 mx-auto mb-4"></i>
                        <p class="text-gray-600">${data.message || 'Gagal memuat data'}</p>
                    </div>
                `;
            }
            lucide.createIcons();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center py-12">
                    <i data-lucide="wifi-off" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-600">Terjadi kesalahan koneksi</p>
                </div>
            `;
            lucide.createIcons();
        });
}

// Close modal
function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Render detail content
function renderDetailContent(exam) {
    document.getElementById('modalSubtitle').textContent = `${exam.nomor_pemeriksaan} - ${exam.jenis_pemeriksaan}`;
    
    const priorityConfig = {
        'urgent': {badge: 'bg-red-100 text-red-800', label: 'Mendesak'},
        'high': {badge: 'bg-orange-100 text-orange-800', label: 'Tinggi'},
        'normal': {badge: 'bg-blue-100 text-blue-800', label: 'Normal'}
    };
    
    const statusConfig = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'progress': 'bg-orange-100 text-orange-800',
        'selesai': 'bg-green-100 text-green-800'
    };
    
    const priority = exam.priority_level || 'normal';
    const pConfig = priorityConfig[priority];
    
    const content = `
        <div class="space-y-4">
            <!-- Status & Priority Bar -->
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-gray-600">Status:</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusConfig[exam.status_pemeriksaan] || 'bg-gray-100 text-gray-800'}">
                        ${exam.status_pemeriksaan.toUpperCase()}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-gray-600">Prioritas:</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${pConfig.badge}">
                        ${pConfig.label}
                    </span>
                </div>
            </div>
            
            <!-- Main Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Patient Info Card -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                        <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                        <h3 class="font-semibold text-gray-900">Informasi Pasien</h3>
                    </div>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama:</span>
                            <span class="font-medium text-gray-900">${exam.nama_pasien}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">NIK:</span>
                            <span class="font-medium text-gray-900">${exam.nik}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jenis Kelamin:</span>
                            <span class="font-medium text-gray-900">${exam.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Umur:</span>
                            <span class="font-medium text-gray-900">${exam.umur} tahun</span>
                        </div>
                        ${exam.telepon ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Telepon:</span>
                            <span class="font-medium text-gray-900">${exam.telepon}</span>
                        </div>
                        ` : ''}
                        ${exam.pekerjaan ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pekerjaan:</span>
                            <span class="font-medium text-gray-900">${exam.pekerjaan}</span>
                        </div>
                        ` : ''}
                    </div>
                    ${exam.alamat_domisili ? `
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-600 block mb-1">Alamat:</span>
                        <p class="text-xs text-gray-900">${exam.alamat_domisili}</p>
                    </div>
                    ` : ''}
                </div>
                
                <!-- Examination Info Card -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-600"></i>
                        <h3 class="font-semibold text-gray-900">Informasi Pemeriksaan</h3>
                    </div>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. Pemeriksaan:</span>
                            <span class="font-medium text-gray-900 font-mono">${exam.nomor_pemeriksaan}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jenis:</span>
                            <span class="font-medium text-gray-900">${exam.jenis_pemeriksaan}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal:</span>
                            <span class="font-medium text-gray-900">${new Date(exam.tanggal_pemeriksaan).toLocaleDateString('id-ID')}</span>
                        </div>
                        ${exam.biaya ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Biaya:</span>
                            <span class="font-medium text-gray-900">Rp ${parseInt(exam.biaya).toLocaleString('id-ID')}</span>
                        </div>
                        ` : ''}
                        ${exam.dokter_perujuk ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dokter:</span>
                            <span class="font-medium text-gray-900">${exam.dokter_perujuk}</span>
                        </div>
                        ` : ''}
                        ${exam.asal_rujukan ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rujukan:</span>
                            <span class="font-medium text-gray-900">${exam.asal_rujukan}</span>
                        </div>
                        ` : ''}
                    </div>
                    ${exam.nama_petugas ? `
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-600 block mb-1">Petugas Lab:</span>
                        <p class="text-xs font-medium text-gray-900">${exam.nama_petugas}</p>
                    </div>
                    ` : ''}
                </div>
            </div>
            
            <!-- Medical Info -->
            ${exam.diagnosis_awal || exam.rekomendasi_pemeriksaan ? `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                ${exam.diagnosis_awal ? `
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <h4 class="text-xs font-semibold text-orange-900 mb-2">Diagnosis Awal</h4>
                    <p class="text-xs text-orange-800">${exam.diagnosis_awal}</p>
                </div>
                ` : ''}
                ${exam.rekomendasi_pemeriksaan ? `
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <h4 class="text-xs font-semibold text-green-900 mb-2">Rekomendasi</h4>
                    <p class="text-xs text-green-800">${exam.rekomendasi_pemeriksaan}</p>
                </div>
                ` : ''}
            </div>
            ` : ''}
            
            <!-- Additional Notes -->
            ${exam.riwayat_pasien ? `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <h4 class="text-xs font-semibold text-blue-900 mb-2 flex items-center gap-1">
                    <i data-lucide="heart" class="w-3.5 h-3.5"></i>
                    Riwayat Medis
                </h4>
                <p class="text-xs text-blue-800">${exam.riwayat_pasien}</p>
            </div>
            ` : ''}
            
            ${exam.keterangan ? `
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                <h4 class="text-xs font-semibold text-gray-900 mb-2 flex items-center gap-1">
                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                    Keterangan
                </h4>
                <p class="text-xs text-gray-700">${exam.keterangan}</p>
            </div>
            ` : ''}
            
            <!-- Action Buttons -->
            ${exam.status_pemeriksaan == 'pending' ? `
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button onclick="acceptFromModal(${exam.pemeriksaan_id})" 
                        class="flex-1 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Terima Pemeriksaan
                </button>
                <button onclick="closeDetailModal()" 
                        class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
            ` : `
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button onclick="closeDetailModal()" 
                        class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Tutup
                </button>
            </div>
            `}
        </div>
    `;
    
    document.getElementById('modalContent').innerHTML = content;
    lucide.createIcons();
}

// Accept from modal
function acceptFromModal(examId) {
    if (confirm('Terima pemeriksaan ini?')) {
        closeDetailModal();
        acceptRequest(examId);
    }
}

// Close modal when clicking outside
document.getElementById('detailModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('detailModal').classList.contains('hidden')) {
        closeDetailModal();
    }
});
</script>

</body>
</html>