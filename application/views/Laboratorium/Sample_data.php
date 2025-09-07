<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-med-blue to-med-light-blue">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="test-tube" class="w-8 h-8 text-med-blue"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Data Sampel / Pelacakan Spesimen</h1>
                <p class="text-blue-100">Monitor progress dan status pemeriksaan sampel laboratorium</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                <p class="text-sm text-white opacity-90">Total Sampel</p>
                <p class="text-lg font-semibold text-white"><?= $total_samples ?> Sampel</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="p-6 bg-white border-b border-gray-200">
    <form method="GET" action="<?= base_url('laboratorium/sample_data') ?>" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <?php foreach ($status_options as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $filters['status'] == $key ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
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
            
            <!-- Petugas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Petugas Lab</label>
                <select name="petugas_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Petugas</option>
                    <?php foreach ($petugas_list as $petugas): ?>
                    <option value="<?= $petugas['petugas_id'] ?>" <?= $filters['petugas_id'] == $petugas['petugas_id'] ? 'selected' : '' ?>><?= $petugas['nama_petugas'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="<?= $filters['search'] ?>" placeholder="Nama pasien, NIK, atau nomor" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="flex space-x-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>Filter
            </button>
            <a href="<?= base_url('laboratorium/sample_data') ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="p-6 bg-gray-50 border-b border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-orange-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Sedang Diproses</p>
                    <p class="text-lg font-bold text-gray-900"><?= array_sum(array_column(array_filter($samples, function($s) { return $s['status_pemeriksaan'] == 'progress'; }), 'timeline_count')) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-lg font-bold text-gray-900"><?= count(array_filter($samples, function($s) { return $s['status_pemeriksaan'] == 'selesai'; })) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Petugas Aktif</p>
                    <p class="text-lg font-bold text-gray-900"><?= count(array_unique(array_column($samples, 'petugas_id'))) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata Waktu</p>
                    <p class="text-lg font-bold text-gray-900"><?= !empty($samples) ? round(array_sum(array_column($samples, 'processing_hours')) / count($samples)) : 0 ?> jam</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Samples List -->
<div class="p-6">
    <?php if (empty($samples)): ?>
    <div class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="test-tube" class="w-12 h-12 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Sampel</h3>
        <p class="text-gray-500">Tidak ada sampel yang sesuai dengan filter yang dipilih.</p>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($samples as $sample): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Status Icon -->
                        <div class="w-12 h-12 rounded-full flex items-center justify-center shadow-sm
                            <?php if ($sample['status_pemeriksaan'] == 'progress'): ?>
                                bg-gradient-to-br from-orange-500 to-orange-600
                            <?php elseif ($sample['status_pemeriksaan'] == 'selesai'): ?>
                                bg-gradient-to-br from-green-500 to-green-600
                            <?php else: ?>
                                bg-gradient-to-br from-gray-500 to-gray-600
                            <?php endif; ?>">
                            <?php if ($sample['status_pemeriksaan'] == 'progress'): ?>
                            <i data-lucide="loader" class="w-6 h-6 text-white"></i>
                            <?php elseif ($sample['status_pemeriksaan'] == 'selesai'): ?>
                            <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                            <?php else: ?>
                            <i data-lucide="x-circle" class="w-6 h-6 text-white"></i>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Sample Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900"><?= $sample['jenis_pemeriksaan'] ?></h3>
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    <?php if ($sample['status_pemeriksaan'] == 'progress'): ?>
                                        bg-orange-100 text-orange-800
                                    <?php elseif ($sample['status_pemeriksaan'] == 'selesai'): ?>
                                        bg-green-100 text-green-800
                                    <?php else: ?>
                                        bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?= strtoupper($sample['status_pemeriksaan']) ?>
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                                <!-- Patient Info -->
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Pasien: <?= $sample['nama_pasien'] ?></p>
                                    <p class="text-sm text-gray-500">NIK: <?= $sample['nik'] ?></p>
                                    <p class="text-sm text-gray-500"><?= $sample['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>, <?= $sample['umur'] ?> tahun</p>
                                    <?php if ($sample['telepon']): ?>
                                    <p class="text-sm text-gray-500">Tel: <?= $sample['telepon'] ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Sample Info -->
                                <div>
                                    <p class="text-sm font-medium text-gray-900">No. Pemeriksaan: <?= $sample['nomor_pemeriksaan'] ?></p>
                                    <p class="text-sm text-gray-500">Tanggal: <?= date('d/m/Y', strtotime($sample['tanggal_pemeriksaan'])) ?></p>
                                    <?php if ($sample['nama_petugas']): ?>
                                    <p class="text-sm text-gray-500">Petugas: <?= $sample['nama_petugas'] ?></p>
                                    <?php endif; ?>
                                    <p class="text-sm text-gray-500">Waktu proses: <?= $sample['processing_hours'] ?> jam</p>
                                </div>
                                
                                <!-- Progress Info -->
                                <div>
                                    <?php if (!empty($sample['latest_status'])): ?>
                                    <p class="text-sm font-medium text-gray-900">Status Terakhir:</p>
                                    <p class="text-sm text-gray-700"><?= $sample['latest_status']['status'] ?></p>
                                    <p class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($sample['latest_status']['tanggal_update'])) ?></p>
                                    <?php endif; ?>
                                    <p class="text-sm text-gray-500">Total Update: <?= $sample['timeline_count'] ?></p>
                                </div>
                            </div>
                            
                            <?php if ($sample['keterangan']): ?>
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700"><strong>Keterangan:</strong> <?= $sample['keterangan'] ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Latest Status Description -->
                            <?php if (!empty($sample['latest_status']['keterangan'])): ?>
                            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-700"><strong>Update Terakhir:</strong> <?= $sample['latest_status']['keterangan'] ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col space-y-2 ml-4">
                        <button type="button" onclick="viewTimeline(<?= $sample['pemeriksaan_id'] ?>)" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i data-lucide="clock" class="w-4 h-4 inline mr-2"></i>Timeline
                        </button>
                        
                        <?php if ($sample['status_pemeriksaan'] == 'progress'): ?>
                        <button type="button" onclick="updateStatus(<?= $sample['pemeriksaan_id'] ?>)" 
                                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200">
                            <i data-lucide="edit" class="w-4 h-4 inline mr-2"></i>Update
                        </button>
                        
                        <button type="button" onclick="inputResults(<?= $sample['pemeriksaan_id'] ?>)" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <i data-lucide="plus-circle" class="w-4 h-4 inline mr-2"></i>Input Hasil
                        </button>
                        <?php elseif ($sample['status_pemeriksaan'] == 'selesai'): ?>
                        <button type="button" onclick="viewResults(<?= $sample['pemeriksaan_id'] ?>)" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                            <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>Lihat Hasil
                        </button>
                        <?php endif; ?>
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
            Menampilkan halaman <?= $current_page ?> dari <?= $total_pages ?> (<?= $total_samples ?> total)
        </div>
        <div class="flex space-x-2">
            <?php if ($has_prev): ?>
            <a href="<?= base_url('laboratorium/sample_data') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page - 1])) ?>" 
               class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
            <a href="<?= base_url('laboratorium/sample_data') ?>?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>" 
               class="px-3 py-2 border <?= $i == $current_page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?> rounded-lg transition-colors duration-200">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($has_next): ?>
            <a href="<?= base_url('laboratorium/sample_data') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page + 1])) ?>" 
               class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Update Status Sampel</h3>
                <button type="button" onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <form id="updateStatusForm" onsubmit="submitStatusUpdate(event)">
                <input type="hidden" id="updateExamId" value="">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                    <select id="newStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="progress">Sedang Diproses</option>
                        <option value="selesai">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea id="statusKeterangan" name="keterangan" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                              placeholder="Masukkan keterangan update status..." required></textarea>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Update Status
                    </button>
                    <button type="button" onclick="closeUpdateModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// View timeline
function viewTimeline(examId) {
    window.location.href = '<?= base_url('laboratorium/view_sample_timeline') ?>/' + examId;
}

// Update status modal
function updateStatus(examId) {
    document.getElementById('updateExamId').value = examId;
    document.getElementById('updateStatusModal').classList.remove('hidden');
}

function closeUpdateModal() {
    document.getElementById('updateStatusModal').classList.add('hidden');
    document.getElementById('updateStatusForm').reset();
}

// Submit status update
function submitStatusUpdate(event) {
    event.preventDefault();
    
    const examId = document.getElementById('updateExamId').value;
    const formData = new FormData(event.target);
    
    fetch(`<?= base_url('laboratorium/update_sample_status') ?>/${examId}`, {
        method: 'POST',
        body: formData
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
        alert('Terjadi kesalahan saat memperbarui status');
    });
}

// Input results
function inputResults(examId) {
    window.location.href = '<?= base_url('laboratorium/input_results_form') ?>/' + examId;
}

// View results
function viewResults(examId) {
    window.location.href = '<?= base_url('laboratorium/view_results') ?>/' + examId;
}

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Close modal when clicking outside
document.getElementById('updateStatusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUpdateModal();
    }
});
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