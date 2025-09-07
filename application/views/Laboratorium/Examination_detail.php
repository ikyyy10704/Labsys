<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-med-blue to-med-light-blue">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="file-text" class="w-8 h-8 text-med-blue"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Detail Pemeriksaan</h1>
                <p class="text-blue-100"><?= $examination['nomor_pemeriksaan'] ?> - <?= $examination['jenis_pemeriksaan'] ?></p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= base_url('laboratorium/incoming_requests') ?>" 
               class="px-4 py-2 bg-white bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 transition-all duration-200">
                <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Patient Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="user" class="w-5 h-5 mr-2 text-blue-600"></i>
                Informasi Pasien
            </h3>
            
            <div class="space-y-3">
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Nama:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= $examination['nama_pasien'] ?></span>
                </div>
                
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">NIK:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= $examination['nik'] ?></span>
                </div>
                
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Jenis Kelamin:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= $examination['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
                </div>
                
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Tempat, Tgl Lahir:</span>
                    <span class="text-sm text-gray-900 col-span-2">
                        <?= $examination['tempat_lahir'] ?>, <?= date('d/m/Y', strtotime($examination['tanggal_lahir'])) ?>
                    </span>
                </div>
                
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Umur:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= $examination['umur'] ?> tahun</span>
                </div>
                
                <?php if ($examination['telepon']): ?>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Telepon:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= $examination['telepon'] ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($examination['pekerjaan']): ?>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Pekerjaan:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= $examination['pekerjaan'] ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($examination['alamat_domisili']): ?>
                <div class="pt-3 border-t border-gray-200">
                    <span class="text-sm font-medium text-gray-600">Alamat:</span>
                    <p class="text-sm text-gray-900 mt-1"><?= $examination['alamat_domisili'] ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Examination Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="clipboard-list" class="w-5 h-5 mr-2 text-blue-600"></i>
                Informasi Pemeriksaan
            </h3>
            
            <div class="space-y-3">
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">No. Pemeriksaan:</span>
                    <span class="text-sm text-gray-900 col-span-2 font-mono"><?= $examination['nomor_pemeriksaan'] ?></span>
                </div>
                
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Jenis Pemeriksaan:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= $examination['jenis_pemeriksaan'] ?></span>
                </div>
                
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Tanggal:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= date('d/m/Y', strtotime($examination['tanggal_pemeriksaan'])) ?></span>
                </div>
                
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Status:</span>
                    <span class="col-span-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            <?php if ($examination['status_pemeriksaan'] == 'pending'): ?>
                                bg-yellow-100 text-yellow-800
                            <?php elseif ($examination['status_pemeriksaan'] == 'progress'): ?>
                                bg-orange-100 text-orange-800
                            <?php elseif ($examination['status_pemeriksaan'] == 'selesai'): ?>
                                bg-green-100 text-green-800
                            <?php else: ?>
                                bg-gray-100 text-gray-800
                            <?php endif; ?>">
                            <?= strtoupper($examination['status_pemeriksaan']) ?>
                        </span>
                    </span>
                </div>
                
                <?php if ($examination['biaya']): ?>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Biaya:</span>
                    <span class="text-sm text-gray-900 col-span-2">Rp <?= number_format($examination['biaya'], 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Dibuat:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= date('d/m/Y H:i', strtotime($examination['created_at'])) ?></span>
                </div>
                
                <?php if ($examination['updated_at'] != $examination['created_at']): ?>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-sm font-medium text-gray-600">Diperbarui:</span>
                    <span class="text-sm text-gray-900 col-span-2"><?= date('d/m/Y H:i', strtotime($examination['updated_at'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Staff Information -->
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i data-lucide="users" class="w-5 h-5 mr-2 text-blue-600"></i>
            Informasi Petugas
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-2">Dokter Perujuk</h4>
                <?php if ($examination['dokter_perujuk']): ?>
                <p class="text-sm text-gray-900"><?= $examination['dokter_perujuk'] ?></p>
                <?php if ($examination['asal_rujukan']): ?>
                <p class="text-xs text-gray-500"><?= $examination['asal_rujukan'] ?></p>
                <?php endif; ?>
                <?php if ($examination['diagnosis_awal']): ?>
                <p class="text-xs text-blue-600 mt-1">Diagnosis: <?= $examination['diagnosis_awal'] ?></p>
                <?php endif; ?>
                <?php else: ?>
                <p class="text-sm text-gray-500 italic">Tidak ada dokter perujuk</p>
                <?php endif; ?>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-2">Petugas Laboratorium</h4>
                <?php if ($examination['nama_petugas']): ?>
                <p class="text-sm text-gray-900"><?= $examination['nama_petugas'] ?></p>
                <p class="text-xs text-gray-500">Analis Laboratorium</p>
                <?php else: ?>
                <p class="text-sm text-gray-500 italic">Belum ditugaskan</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Medical Information Section -->
    <?php if ($examination['diagnosis_awal'] || $examination['rekomendasi_pemeriksaan']): ?>
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i data-lucide="stethoscope" class="w-5 h-5 mr-2 text-blue-600"></i>
            Informasi Medis
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if ($examination['diagnosis_awal']): ?>
            <div class="bg-orange-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">Diagnosis Awal</h4>
                <p class="text-sm text-gray-700"><?= $examination['diagnosis_awal'] ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($examination['rekomendasi_pemeriksaan']): ?>
            <div class="bg-green-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">Rekomendasi Pemeriksaan</h4>
                <p class="text-sm text-gray-700"><?= $examination['rekomendasi_pemeriksaan'] ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Medical History -->
    <?php if ($examination['riwayat_pasien']): ?>
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i data-lucide="heart" class="w-5 h-5 mr-2 text-blue-600"></i>
            Riwayat Medis Pasien
        </h3>
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-700"><?= $examination['riwayat_pasien'] ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Notes/Keterangan -->
    <?php if ($examination['keterangan']): ?>
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i data-lucide="message-square" class="w-5 h-5 mr-2 text-blue-600"></i>
            Keterangan Pemeriksaan
        </h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-700"><?= $examination['keterangan'] ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <?php if ($examination['status_pemeriksaan'] == 'pending'): ?>
    <div class="mt-6 flex justify-center space-x-4">
        <button type="button" onclick="acceptExamination(<?= $examination['pemeriksaan_id'] ?>)" 
                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
            <i data-lucide="check" class="w-5 h-5 inline mr-2"></i>Terima Pemeriksaan
        </button>
        
        <button type="button" onclick="rejectExamination(<?= $examination['pemeriksaan_id'] ?>)" 
                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
            <i data-lucide="x" class="w-5 h-5 inline mr-2"></i>Tolak Pemeriksaan
        </button>
    </div>
    <?php endif; ?>
</div>

<script>
// Accept examination
function acceptExamination(examId) {
    if (confirm('Apakah Anda yakin ingin menerima pemeriksaan ini?')) {
        window.location.href = '<?= base_url('laboratorium/accept_request') ?>/' + examId;
    }
}

// Reject examination (placeholder - implement as needed)
function rejectExamination(examId) {
    if (confirm('Apakah Anda yakin ingin menolak pemeriksaan ini?')) {
        // Implement reject functionality
        alert('Fitur penolakan akan segera tersedia');
    }
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