<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-med-blue to-med-light-blue">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="inbox" class="w-8 h-8 text-med-blue"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Permintaan Pemeriksaan Masuk</h1>
                <p class="text-blue-100">Kelola permintaan pemeriksaan laboratorium yang masuk</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                <p class="text-sm text-white opacity-90">Total Menunggu</p>
                <p class="text-lg font-semibold text-white"><?= count($requests) ?> Permintaan</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="p-6 bg-white border-b border-gray-200">
    <form method="GET" action="<?= base_url('laboratorium/incoming_requests') ?>" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
                <input type="date" name="date_from" value="<?= $filters['date_from'] ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                <input type="date" name="date_to" value="<?= $filters['date_to'] ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <!-- Examination Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pemeriksaan</label>
                <select name="jenis_pemeriksaan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Jenis</option>
                    <?php foreach ($examination_types as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $filters['jenis_pemeriksaan'] == $key ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Priority -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Prioritas</option>
                    <option value="urgent" <?= $filters['priority'] == 'urgent' ? 'selected' : '' ?>>Mendesak</option>
                    <option value="high" <?= $filters['priority'] == 'high' ? 'selected' : '' ?>>Tinggi</option>
                    <option value="normal" <?= $filters['priority'] == 'normal' ? 'selected' : '' ?>>Normal</option>
                </select>
            </div>
            
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="<?= $filters['search'] ?>" placeholder="Nama pasien, NIK, atau nomor pemeriksaan" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="flex space-x-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>Filter
            </button>
            <a href="<?= base_url('laboratorium/incoming_requests') ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Bulk Actions -->
<div class="p-6 bg-gray-50 border-b border-gray-200">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <label class="flex items-center">
                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
            </label>
            <button type="button" id="acceptSelectedBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 disabled:bg-gray-400" disabled>
                <i data-lucide="check" class="w-4 h-4 inline mr-2"></i>Terima yang Dipilih
            </button>
        </div>
        <div class="text-sm text-gray-600">
            Total: <?= $total_requests ?> permintaan | Halaman <?= $current_page ?> dari <?= $total_pages ?>
        </div>
    </div>
</div>

<!-- Requests List -->
<div class="p-6">
    <?php if (empty($requests)): ?>
    <div class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Permintaan</h3>
        <p class="text-gray-500">Tidak ada permintaan pemeriksaan yang menunggu saat ini.</p>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($requests as $request): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Checkbox -->
                        <label class="flex items-center mt-1">
                            <input type="checkbox" name="request_ids[]" value="<?= $request['pemeriksaan_id'] ?>" 
                                   class="request-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </label>
                        
                        <!-- Priority Icon -->
                        <div class="w-12 h-12 bg-gradient-to-br from-<?= $request['priority_info']['color'] ?>-500 to-<?= $request['priority_info']['color'] ?>-600 rounded-full flex items-center justify-center shadow-sm">
                            <?php if ($request['priority_info']['level'] == 'urgent'): ?>
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                            <?php elseif ($request['priority_info']['level'] == 'high'): ?>
                            <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                            <?php else: ?>
                            <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Request Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900"><?= $request['jenis_pemeriksaan'] ?></h3>
                                <span class="px-3 py-1 text-xs font-medium bg-<?= $request['priority_info']['color'] ?>-100 text-<?= $request['priority_info']['color'] ?>-800 rounded-full">
                                    <?= $request['priority_info']['label'] ?>
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Pasien: <?= $request['nama_pasien'] ?></p>
                                    <p class="text-sm text-gray-500">NIK: <?= $request['nik'] ?></p>
                                    <p class="text-sm text-gray-500"><?= $request['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>, <?= $request['umur'] ?> tahun</p>
                                    <?php if ($request['telepon']): ?>
                                    <p class="text-sm text-gray-500">Tel: <?= $request['telepon'] ?></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">No. Pemeriksaan: <?= $request['nomor_pemeriksaan'] ?></p>
                                    <p class="text-sm text-gray-500">Tanggal: <?= date('d/m/Y', strtotime($request['tanggal_pemeriksaan'])) ?></p>
                                    <?php if ($request['dokter_perujuk']): ?>
                                    <p class="text-sm text-gray-500">Dokter: <?= $request['dokter_perujuk'] ?></p>
                                    <?php endif; ?>
                                    <?php if ($request['asal_rujukan']): ?>
                                    <p class="text-sm text-gray-500">Rujukan: <?= $request['asal_rujukan'] ?></p>
                                    <?php endif; ?>
                                    <p class="text-sm text-gray-500">Menunggu: <?= $request['hours_waiting'] ?> jam</p>
                                </div>
                            </div>
                            
                            <?php if ($request['keterangan']): ?>
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700"><strong>Keterangan:</strong> <?= $request['keterangan'] ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2 ml-4">
                        <button type="button" onclick="acceptRequest(<?= $request['pemeriksaan_id'] ?>)" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <i data-lucide="check" class="w-4 h-4 inline mr-2"></i>Terima
                        </button>
                        <button type="button" onclick="viewDetails(<?= $request['pemeriksaan_id'] ?>)" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="mt-8 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Menampilkan halaman <?= $current_page ?> dari <?= $total_pages ?> (<?= $total_requests ?> total)
        </div>
        <div class="flex space-x-2">
            <?php if ($has_prev): ?>
            <a href="<?= base_url('laboratorium/incoming_requests') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page - 1])) ?>" 
               class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
            <a href="<?= base_url('laboratorium/incoming_requests') ?>?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>" 
               class="px-3 py-2 border <?= $i == $current_page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?> rounded-lg transition-colors duration-200">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($has_next): ?>
            <a href="<?= base_url('laboratorium/incoming_requests') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page + 1])) ?>" 
               class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.request-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    updateBulkActionButton();
});

// Update bulk action button state
function updateBulkActionButton() {
    const checkedBoxes = document.querySelectorAll('.request-checkbox:checked');
    const acceptButton = document.getElementById('acceptSelectedBtn');
    acceptButton.disabled = checkedBoxes.length === 0;
}

// Add event listeners to individual checkboxes
document.querySelectorAll('.request-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActionButton);
});

// Accept single request
function acceptRequest(examId) {
    if (confirm('Apakah Anda yakin ingin menerima permintaan pemeriksaan ini?')) {
        window.location.href = '<?= base_url('laboratorium/accept_request') ?>/' + examId;
    }
}

// Accept multiple requests
document.getElementById('acceptSelectedBtn').addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.request-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Apakah Anda yakin ingin menerima ${checkedBoxes.length} permintaan yang dipilih?`)) {
        const requestIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        fetch('<?= base_url('laboratorium/accept_multiple_requests') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'request_ids=' + JSON.stringify(requestIds)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan saat memproses permintaan');
        });
    }
});

// View details
function viewDetails(examId) {
    // Implement modal or redirect to detail page
    window.location.href = '<?= base_url('laboratorium/view_examination_detail') ?>/' + examId;
}

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>

<style>
/* Custom variables untuk konsistensi warna */
:root {
    --med-blue: #2563eb;
    --med-light-blue: #3b82f6;
    --med-orange: #f59e0b;
    --med-green: #10b981;
}

.text-med-blue { color: var(--med-blue); }
.text-med-orange { color: var(--med-orange); }
.text-med-green { color: var(--med-green); }
.bg-med-blue { background-color: var(--med-blue); }
.border-med-blue { border-color: var(--med-blue); }
</style>