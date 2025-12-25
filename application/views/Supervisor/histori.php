<!-- Header Section -->
<script src="https://unpkg.com/lucide@latest"></script>

<div class="w-full bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="history" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Histori Validasi</h1>
                    <p class="text-blue-100">Riwayat pemeriksaan laboratorium yang telah divalidasi</p>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 px-4 py-3 shadow-lg">
                <p class="text-blue-100 text-xs font-medium mb-0.5">Total Validasi</p>
                <p class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                    <?= number_format($total_results) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="w-full px-6 py-6 pb-20">
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 p-4">
        <form action="<?= base_url('supervisor/histori') ?>" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pasien / No. Periksa</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="<?= isset($filters['search']) ? htmlspecialchars($filters['search']) : '' ?>" 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" 
                           placeholder="Nama Pasien, NIK, atau No. Pemeriksaan">
                </div>
            </div>
            
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="<?= isset($filters['date_from']) ? $filters['date_from'] : '' ?>" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="<?= isset($filters['date_to']) ? $filters['date_to'] : '' ?>" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            
            <!-- Filter Button -->
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center">
                    <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                    Filter
                </button>
                <a href="<?= base_url('supervisor/histori') ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors" title="Reset Filter">
                    <i data-lucide="refresh-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pemeriksaan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pemeriksaan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Validasi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validator</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($results) && !empty($results)): ?>
                        <?php foreach ($results as $item): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium font-mono text-gray-900"><?= htmlspecialchars($item['nomor_pemeriksaan']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['nama_pasien']) ?></span>
                                        <span class="text-xs text-gray-500">NIK: <?= htmlspecialchars($item['nik']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= htmlspecialchars($item['jenis_pemeriksaan']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center text-sm text-gray-900">
                                        <i data-lucide="calendar" class="w-4 h-4 mr-2 text-gray-400"></i>
                                        <?= date('d/m/Y H:i', strtotime($item['completed_at'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center text-sm text-gray-900">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-2 text-indigo-600 font-bold text-xs">
                                            <?= substr(strtoupper($item['nama_petugas']), 0, 1) ?>
                                        </div>
                                        <?= htmlspecialchars($item['nama_petugas']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="viewResultDetails(<?= $item['pemeriksaan_id'] ?>)" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors inline-flex items-center">
                                        <i data-lucide="file-text" class="w-4 h-4 mr-1"></i>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i data-lucide="search" class="w-8 h-8 text-gray-400"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-900">Tidak ada data ditemukan</p>
                                    <p class="text-sm text-gray-500 mt-1">Coba sesuaikan filter pencarian Anda</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Menampilkan halaman <span class="font-medium"><?= $current_page ?></span> dari <span class="font-medium"><?= $total_pages ?></span>
            </div>
            <div class="flex space-x-2">
                <?php if ($has_prev): ?>
                    <a href="<?= base_url('supervisor/histori') ?>?page=<?= $current_page - 1 ?>&<?= http_build_query(array_merge($filters, ['page' => null])) ?>" 
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                <?php endif; ?>
                
                <?php if ($has_next): ?>
                    <a href="<?= base_url('supervisor/histori') ?>?page=<?= $current_page + 1 ?>&<?= http_build_query(array_merge($filters, ['page' => null])) ?>" 
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail Result (Reuse from Quality Control logic if needed, or implement new) -->
<div id="result-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden fade-in">
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-6 border-b border-blue-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <i data-lucide="file-check" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Detail Hasil Pemeriksaan</h3>
                        <p class="text-sm text-blue-100">Informasi lengkap hasil laboratorium</p>
                    </div>
                </div>
                <button onclick="closeModal('result-modal')" class="text-white hover:text-gray-200 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]" id="modal-content">
            <!-- Content loaded via AJAX -->
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end bg-gray-50">
            <button onclick="closeModal('result-modal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-200 font-medium">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    function viewResultDetails(examId) {
        document.getElementById('result-modal').classList.remove('hidden');
        document.getElementById('modal-content').innerHTML = `
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 animate-spin"></i>
                </div>
                <p class="text-gray-500">Memuat detail hasil pemeriksaan...</p>
            </div>
        `;
        
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Use the existing get_result_details endpoint
        fetch('<?= base_url("supervisor/get_result_details") ?>/' + examId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Determine whether it's single or multiple
                if (data.is_multiple) {
                    renderMultipleResults(data.examination, data.results);
                } else {
                    renderSingleResult(data.examination, data.results);
                }
            } else {
                document.getElementById('modal-content').innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        <p>${data.message || 'Gagal memuat data'}</p>
                    </div>
                `;
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('modal-content').innerHTML = '<p class="text-center text-red-500">Terjadi kesalahan koneksi.</p>';
        });
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function renderSingleResult(examination, results) {
        // Reuse logic from Quality Control but readonly
        let content = `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Pemeriksaan</label>
                        <p class="text-lg font-semibold text-gray-900">${examination.nomor_pemeriksaan}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Pasien</label>
                        <p class="text-lg font-semibold text-gray-900">${examination.nama_pasien}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Pemeriksaan</label>
                        <p class="text-lg font-semibold text-gray-900">${examination.jenis_pemeriksaan}</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center space-x-2">
                        <i data-lucide="clipboard-list" class="w-5 h-5 text-green-600"></i>
                        <span>Hasil Pemeriksaan (Divalidasi)</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${Object.entries(results || {}).map(([key, value]) => `
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600 font-medium text-sm">${key}:</span>
                                <span class="font-semibold text-gray-900 text-sm">${value || '-'}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center space-x-3 text-green-800">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <div>
                        <p class="text-sm font-medium">Status: Selesai & Divalidasi</p>
                        <p class="text-xs mt-0.5">Pada: ${examination.completed_at || '-'}</p>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('modal-content').innerHTML = content;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function renderMultipleResults(examination, results) {
        let content = `
             <div class="space-y-6">
                <!-- Patient Info Header -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Pemeriksaan</label>
                        <p class="text-lg font-semibold text-gray-900">${examination.nomor_pemeriksaan}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Pasien</label>
                        <p class="text-lg font-semibold text-gray-900">${examination.nama_pasien}</p>
                    </div>
                     <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Divalidasi
                        </span>
                    </div>
                </div>
                
                <h4 class="font-bold text-lg text-gray-800 mb-4">Rincian Hasil:</h4>
                <div class="space-y-4">
        `;

        // Iterate over results (keyed by jenis_pemeriksaan)
        for (const [jenis, data] of Object.entries(results)) {
            content += `
                <div class="border rounded-xl overflow-hidden bg-white shadow-sm">
                    <div class="bg-gray-100 px-4 py-3 border-b flex justify-between items-center">
                        <h5 class="font-bold text-gray-700">${jenis}</h5>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2">
                         ${Object.entries(data || {}).map(([key, value]) => `
                            <div class="flex justify-between py-2 border-b border-gray-50 last:border-0">
                                <span class="text-gray-600 text-sm">${key}:</span>
                                <span class="font-semibold text-gray-900 text-sm">${value || '-'}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        content += `</div></div>`;
        document.getElementById('modal-content').innerHTML = content;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
    
    // Auto-close modals on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('result-modal');
        if (event.target == modal) {
            closeModal('result-modal');
        }
    }
</script>

<style>
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
