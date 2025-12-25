<!-- Header -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="history" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Histori Validasi QC Alat</h1>
                <p class="text-blue-100">Riwayat pemeriksaan quality control alat yang telah divalidasi</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
             <a href="<?= base_url('supervisor/qc_alat_validation') ?>" 
               class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Validasi</span>
            </a>
        </div>
    </div>
</div>

<div class="p-6 space-y-6">

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Alat / Teknisi</label>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" name="search" value="<?= html_escape($filters['search']) ?>" 
                           class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nama alat, kode, atau teknisi...">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="<?= html_escape($filters['start_date']) ?>"
                       class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="<?= html_escape($filters['end_date']) ?>"
                       class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center space-x-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span>Terapkan</span>
                </button>
                <?php if(!empty($filters['search']) || !empty($filters['start_date']) || !empty($filters['end_date'])): ?>
                <a href="<?= base_url('supervisor/qc_alat_history') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal QC</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Personel</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hasil</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Divalidasi Oleh</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if(empty($history_qc)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada data histori ditemukan</p>
                            <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($history_qc as $qc): ?>
                        <tr class="hover:bg-blue-50/50 transition-colors">
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
                                <div class="text-xs text-gray-500 mt-1 font-mono bg-gray-100 inline-block px-1 rounded">
                                    <?= $qc['kode_unik'] ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-blue-600"><?= substr($qc['teknisi'], 0, 1) ?></span>
                                    </div>
                                    <span class="text-sm text-gray-600"><?= $qc['teknisi'] ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($qc['hasil_qc'] === 'Passed'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 border border-green-200">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        Passed
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 border border-red-200">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                        Failed
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium"><?= $qc['supervisor'] ?></div>
                                <?php if($qc['catatan'] && strpos($qc['catatan'], 'Catatan Supervisor') !== false): ?>
                                    <div class="text-xs text-blue-600 mt-1 flex items-center">
                                        <i data-lucide="message-square" class="w-3 h-3 mr-1"></i>
                                        Ada catatan
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="viewDetail(<?= $qc['qc_id'] ?>)" 
                                        class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors inline-flex items-center">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                    Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Halaman <span class="font-semibold text-gray-900"><?= $current_page ?></span> dari <span class="font-semibold text-gray-900"><?= $total_pages ?></span>
            </div>
            <div class="flex space-x-2">
                <?php if($has_prev): ?>
                <a href="?page=<?= $current_page - 1 ?>&search=<?= urlencode($filters['search']) ?>&start_date=<?= $filters['start_date'] ?>&end_date=<?= $filters['end_date'] ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600 text-sm">
                    Sebelumnya
                </a>
                <?php endif; ?>
                
                <?php if($has_next): ?>
                <a href="?page=<?= $current_page + 1 ?>&search=<?= urlencode($filters['search']) ?>&start_date=<?= $filters['start_date'] ?>&end_date=<?= $filters['end_date'] ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600 text-sm">
                    Selanjutnya
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- View Detail Modal -->
<div id="detail-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900 flex items-center space-x-2">
                <i data-lucide="file-check" class="w-6 h-6 text-blue-600"></i>
                <span>Detail QC Alat</span>
            </h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div id="detail-content" class="p-6">
            <!-- Will be filled by JS -->
        </div>
    </div>
</div>

<script>
async function viewDetail(qcId) {
    try {
        const response = await fetch('<?= base_url("supervisor/get_qc_alat_detail/") ?>' + qcId);
        const data = await response.json();

        if (data.success) {
            const qc = data.qc;
            const content = document.getElementById('detail-content');
            const params = qc.parameter_qc || [];
            const hasil = qc.nilai_hasil || [];
            const standar = qc.nilai_standar || [];

            content.innerHTML = `
                <div class="space-y-6">
                    <!-- Status Header -->
                    <div class="flex items-center justify-between p-4 rounded-lg ${qc.hasil_qc === 'Passed' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'}">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full ${qc.hasil_qc === 'Passed' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'} flex items-center justify-center">
                                <i data-lucide="${qc.hasil_qc === 'Passed' ? 'check-circle' : 'alert-circle'}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="font-bold ${qc.hasil_qc === 'Passed' ? 'text-green-800' : 'text-red-800'} text-lg">QC ${qc.hasil_qc.toUpperCase()}</p>
                                <p class="text-sm ${qc.hasil_qc === 'Passed' ? 'text-green-600' : 'text-red-600'}">Divalidasi oleh ${qc.supervisor || '-'}</p>
                            </div>
                        </div>
                        <div class="text-right text-sm text-gray-500">
                            <p>${formatDate(qc.tanggal_qc)}</p>
                            <p>${qc.waktu_qc}</p>
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                <i data-lucide="activity" class="w-4 h-4 mr-2 text-blue-600"></i>
                                Informasi Alat
                            </h4>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-xs text-gray-500 uppercase block">Nama Alat</span>
                                    <span class="font-medium text-gray-900">${qc.nama_alat}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase block">Kode Unik</span>
                                    <span class="text-gray-900 font-mono text-sm bg-white px-1.5 py-0.5 rounded border border-gray-200 inline-block">${qc.kode_unik}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase block">Lokasi</span>
                                    <span class="text-gray-900">${qc.lokasi || '-'}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                             <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                <i data-lucide="users" class="w-4 h-4 mr-2 text-blue-600"></i>
                                Personel
                            </h4>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-xs text-gray-500 uppercase block">Teknisi Pelaksana</span>
                                    <span class="font-medium text-gray-900">${qc.teknisi}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 uppercase block">Supervisor Validator</span>
                                    <span class="font-medium text-blue-700">${qc.supervisor || 'Belum divalidasi'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parameters -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                            <i data-lucide="sliders" class="w-4 h-4 mr-2 text-blue-600"></i>
                            Hasil Pengujian Parameter
                        </h4>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-600">Parameter</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-600">Range Standar</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-600">Hasil Ukur</th>
                                        <th class="px-4 py-3 text-center font-medium text-gray-600">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    ${params.map((param, index) => {
                                        const value = hasil[index];
                                        const std = standar[index];
                                        const isPassed = value >= std.min && value <= std.max;
                                        
                                        return `
                                            <tr class="bg-white">
                                                <td class="px-4 py-3 font-medium text-gray-900">${param.name}</td>
                                                <td class="px-4 py-3 text-gray-500">${std.min} - ${std.max} ${param.unit || ''}</td>
                                                <td class="px-4 py-3 text-right font-bold ${isPassed ? 'text-green-600' : 'text-red-600'}">
                                                    ${value} ${param.unit || ''}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${isPassed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                                                        ${isPassed ? 'OK' : 'FAIL'}
                                                    </span>
                                                </td>
                                            </tr>
                                        `;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    ${qc.catatan ? `
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-2 flex items-center">
                            <i data-lucide="message-square" class="w-4 h-4 mr-2"></i>
                            Catatan & Riwayat
                        </h4>
                        <p class="text-sm text-gray-800 whitespace-pre-wrap font-mono text-xs">${qc.catatan}</p>
                    </div>` : ''}
                    
                    ${qc.tindakan_korektif ? `
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-orange-800 mb-2 flex items-center">
                            <i data-lucide="tool" class="w-4 h-4 mr-2"></i>
                            Tindakan Korektif
                        </h4>
                        <p class="text-sm text-gray-800 whitespace-pre-wrap">${qc.tindakan_korektif}</p>
                    </div>` : ''}

                </div>
            `;

            document.getElementById('detail-modal').classList.remove('hidden');
            lucide.createIcons();
        } else {
            alert('Gagal memuat detail data');
        }
    } catch (e) {
        console.error(e);
        alert('Terjadi kesalahan');
    }
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

function formatDate(dateString) {
    if (!dateString) return '-';
    // Simple formatter, can be improved
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

// Init icons
document.addEventListener('DOMContentLoaded', () => {
    if(typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
