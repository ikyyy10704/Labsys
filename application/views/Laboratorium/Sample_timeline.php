<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-med-blue to-med-light-blue">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="clock" class="w-8 h-8 text-med-blue"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Timeline Sampel</h1>
                <p class="text-blue-100">Pelacakan progres pemeriksaan: <?= isset($examination['nomor_pemeriksaan']) ? $examination['nomor_pemeriksaan'] : 'N/A' ?></p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= base_url('laboratorium/sample_data') ?>" 
               class="px-4 py-2 bg-white bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 transition-all duration-200">
                <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<!-- Examination Details -->
<div class="p-6 bg-white border-b border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Patient Info -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Pasien</h3>
            <div class="space-y-2">
                <p class="text-sm"><span class="font-medium">Nama:</span> <?= isset($examination['nama_pasien']) ? $examination['nama_pasien'] : 'N/A' ?></p>
                <p class="text-sm"><span class="font-medium">NIK:</span> <?= isset($examination['nik']) ? $examination['nik'] : 'N/A' ?></p>
                <p class="text-sm"><span class="font-medium">Jenis Kelamin:</span> <?= isset($examination['jenis_kelamin']) ? ($examination['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan') : 'N/A' ?></p>
                <p class="text-sm"><span class="font-medium">Tempat, Tgl Lahir:</span> 
                    <?= isset($examination['tempat_lahir']) ? $examination['tempat_lahir'] : 'N/A' ?>, 
                    <?= isset($examination['tanggal_lahir']) ? date('d/m/Y', strtotime($examination['tanggal_lahir'])) : 'N/A' ?>
                </p>
                <p class="text-sm"><span class="font-medium">Umur:</span> <?= isset($examination['umur']) ? $examination['umur'] : 'N/A' ?> tahun</p>
                <?php if (isset($examination['telepon']) && !empty($examination['telepon'])): ?>
                <p class="text-sm"><span class="font-medium">Telepon:</span> <?= $examination['telepon'] ?></p>
                <?php endif; ?>
                <?php if (isset($examination['pekerjaan']) && !empty($examination['pekerjaan'])): ?>
                <p class="text-sm"><span class="font-medium">Pekerjaan:</span> <?= $examination['pekerjaan'] ?></p>
                <?php endif; ?>
                <?php if (isset($examination['alamat_domisili']) && !empty($examination['alamat_domisili'])): ?>
                <div class="pt-3 border-t border-gray-200">
                    <span class="text-sm font-medium text-gray-600">Alamat:</span>
                    <p class="text-sm text-gray-900 mt-1"><?= $examination['alamat_domisili'] ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Examination Info -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Pemeriksaan</h3>
            <div class="space-y-2">
                <p class="text-sm"><span class="font-medium">No. Pemeriksaan:</span> 
                    <span class="font-mono"><?= isset($examination['nomor_pemeriksaan']) ? $examination['nomor_pemeriksaan'] : 'N/A' ?></span>
                </p>
                <p class="text-sm"><span class="font-medium">Jenis Pemeriksaan:</span> <?= isset($examination['jenis_pemeriksaan']) ? $examination['jenis_pemeriksaan'] : 'N/A' ?></p>
                <p class="text-sm"><span class="font-medium">Tanggal:</span> <?= isset($examination['tanggal_pemeriksaan']) ? date('d/m/Y', strtotime($examination['tanggal_pemeriksaan'])) : 'N/A' ?></p>
                <p class="text-sm"><span class="font-medium">Status:</span> 
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        <?php 
                        $status = isset($examination['status_pemeriksaan']) ? $examination['status_pemeriksaan'] : 'pending';
                        if ($status == 'pending'): ?>
                            bg-yellow-100 text-yellow-800
                        <?php elseif ($status == 'progress'): ?>
                            bg-orange-100 text-orange-800
                        <?php elseif ($status == 'selesai'): ?>
                            bg-green-100 text-green-800
                        <?php else: ?>
                            bg-gray-100 text-gray-800
                        <?php endif; ?>">
                        <?= strtoupper($status) ?>
                    </span>
                </p>
                <?php if (isset($examination['biaya']) && $examination['biaya'] > 0): ?>
                <p class="text-sm"><span class="font-medium">Biaya:</span> Rp <?= number_format($examination['biaya'], 0, ',', '.') ?></p>
                <?php endif; ?>
                <p class="text-sm"><span class="font-medium">Dibuat:</span> <?= isset($examination['created_at']) ? date('d/m/Y H:i', strtotime($examination['created_at'])) : 'N/A' ?></p>
                <?php if (isset($examination['started_at']) && !empty($examination['started_at'])): ?>
                <p class="text-sm"><span class="font-medium">Mulai Diproses:</span> <?= date('d/m/Y H:i', strtotime($examination['started_at'])) ?></p>
                <?php endif; ?>
                <?php if (isset($examination['completed_at']) && !empty($examination['completed_at'])): ?>
                <p class="text-sm"><span class="font-medium">Selesai:</span> <?= date('d/m/Y H:i', strtotime($examination['completed_at'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Staff & Reference Info -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Rujukan & Petugas</h3>
            <div class="space-y-2">
                <?php if (isset($examination['dokter_perujuk']) && !empty($examination['dokter_perujuk'])): ?>
                <p class="text-sm"><span class="font-medium">Dokter Perujuk:</span> <?= $examination['dokter_perujuk'] ?></p>
                <?php endif; ?>
                <?php if (isset($examination['asal_rujukan']) && !empty($examination['asal_rujukan'])): ?>
                <p class="text-sm"><span class="font-medium">Asal Rujukan:</span> <?= $examination['asal_rujukan'] ?></p>
                <?php endif; ?>
                <?php if (isset($examination['diagnosis_awal']) && !empty($examination['diagnosis_awal'])): ?>
                <p class="text-sm"><span class="font-medium">Diagnosis Awal:</span> <?= $examination['diagnosis_awal'] ?></p>
                <?php endif; ?>
                <?php if (isset($examination['nama_petugas']) && !empty($examination['nama_petugas'])): ?>
                <p class="text-sm"><span class="font-medium">Petugas Lab:</span> <?= $examination['nama_petugas'] ?></p>
                <?php else: ?>
                <p class="text-sm"><span class="font-medium">Petugas Lab:</span> <span class="text-gray-500 italic">Belum ditugaskan</span></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Timeline Section -->
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Timeline Progres</h2>
        <div class="text-sm text-gray-600">
            Total update: <?= count($timeline) ?> kejadian
        </div>
    </div>
    
    <?php if (empty($timeline)): ?>
    <div class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="clock" class="w-12 h-12 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Update</h3>
        <p class="text-gray-500">Timeline progres akan muncul setelah ada update status.</p>
    </div>
    <?php else: ?>
    <div class="relative">
        <!-- Timeline Line -->
        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
        
        <div class="space-y-6">
            <?php foreach ($timeline as $index => $item): ?>
            <div class="relative flex items-start space-x-4">
                <!-- Timeline Dot -->
                <div class="relative flex items-center justify-center w-12 h-12 rounded-full shadow-sm z-10
                    <?php 
                    $status_lower = strtolower($item['status']);
                    if (strpos($status_lower, 'diterima') !== false || strpos($status_lower, 'mulai') !== false): ?>
                        bg-blue-500
                    <?php elseif (strpos($status_lower, 'selesai') !== false || strpos($status_lower, 'divalidasi') !== false): ?>
                        bg-green-500
                    <?php elseif (strpos($status_lower, 'dibatalkan') !== false || strpos($status_lower, 'gagal') !== false): ?>
                        bg-red-500
                    <?php else: ?>
                        bg-orange-500
                    <?php endif; ?>">
                    
                    <?php if (strpos($status_lower, 'diterima') !== false): ?>
                    <i data-lucide="package" class="w-5 h-5 text-white"></i>
                    <?php elseif (strpos($status_lower, 'mulai') !== false): ?>
                    <i data-lucide="play" class="w-5 h-5 text-white"></i>
                    <?php elseif (strpos($status_lower, 'selesai') !== false): ?>
                    <i data-lucide="check" class="w-5 h-5 text-white"></i>
                    <?php elseif (strpos($status_lower, 'divalidasi') !== false): ?>
                    <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                    <?php elseif (strpos($status_lower, 'dibatalkan') !== false): ?>
                    <i data-lucide="x" class="w-5 h-5 text-white"></i>
                    <?php else: ?>
                    <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Timeline Content -->
                <div class="flex-1 min-w-0 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900"><?= isset($item['status']) ? $item['status'] : 'Status Unknown' ?></h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">
                                    <?= isset($item['tanggal_update']) ? date('d/m/Y', strtotime($item['tanggal_update'])) : 'N/A' ?>
                                </span>
                                <span class="text-sm font-medium text-gray-700">
                                    <?= isset($item['tanggal_update']) ? date('H:i', strtotime($item['tanggal_update'])) : 'N/A' ?>
                                </span>
                                
                                <!-- Action buttons for timeline entry -->
                                <?php if (isset($examination['status_pemeriksaan']) && $examination['status_pemeriksaan'] == 'progress' && $index < 3): ?>
                                <div class="flex space-x-1">
                                    <button type="button" onclick="editTimelineEntry(<?= isset($item['timeline_id']) ? $item['timeline_id'] : '0' ?>, '<?= isset($item['status']) ? addslashes($item['status']) : '' ?>', '<?= isset($item['keterangan']) ? addslashes($item['keterangan']) : '' ?>')" 
                                            class="p-1 text-blue-600 hover:bg-blue-100 rounded transition-colors duration-200" title="Edit">
                                        <i data-lucide="edit-2" class="w-3 h-3"></i>
                                    </button>
                                    <button type="button" onclick="deleteTimelineEntry(<?= isset($item['timeline_id']) ? $item['timeline_id'] : '0' ?>)" 
                                            class="p-1 text-red-600 hover:bg-red-100 rounded transition-colors duration-200" title="Hapus">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (isset($item['keterangan']) && !empty($item['keterangan'])): ?>
                        <p class="text-gray-700 mb-3"><?= $item['keterangan'] ?></p>
                        <?php endif; ?>
                        
                        <div class="flex items-center justify-between text-sm">
                            <div class="text-gray-500">
                                <?php if ($index == 0): ?>
                                    <span class="font-medium text-green-600">Status terbaru</span>
                                <?php else: ?>
                                    <?php
                                    if (isset($timeline[$index-1]['tanggal_update']) && isset($item['tanggal_update'])) {
                                        $time_diff = strtotime($timeline[$index-1]['tanggal_update']) - strtotime($item['tanggal_update']);
                                        $hours_diff = round($time_diff / 3600, 1);
                                        echo $hours_diff . ' jam sebelumnya';
                                    }
                                    ?>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (isset($item['nama_petugas']) && !empty($item['nama_petugas'])): ?>
                            <div class="flex items-center space-x-1 text-gray-600">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                <span><?= $item['nama_petugas'] ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Medical Information -->
<?php if ((isset($examination['diagnosis_awal']) && !empty($examination['diagnosis_awal'])) || 
          (isset($examination['rekomendasi_pemeriksaan']) && !empty($examination['rekomendasi_pemeriksaan']))): ?>
<div class="p-6 bg-gray-50 border-t border-gray-200">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <i data-lucide="stethoscope" class="w-5 h-5 mr-2 text-blue-600"></i>
        Informasi Medis
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if (isset($examination['diagnosis_awal']) && !empty($examination['diagnosis_awal'])): ?>
        <div class="bg-white rounded-lg p-4 border border-orange-200">
            <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                <i data-lucide="activity" class="w-4 h-4 mr-2 text-orange-600"></i>
                Diagnosis Awal
            </h4>
            <p class="text-sm text-gray-700"><?= $examination['diagnosis_awal'] ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (isset($examination['rekomendasi_pemeriksaan']) && !empty($examination['rekomendasi_pemeriksaan'])): ?>
        <div class="bg-white rounded-lg p-4 border border-green-200">
            <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                <i data-lucide="clipboard-list" class="w-4 h-4 mr-2 text-green-600"></i>
                Rekomendasi Pemeriksaan
            </h4>
            <p class="text-sm text-gray-700"><?= $examination['rekomendasi_pemeriksaan'] ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Medical History -->
<?php if (isset($examination['riwayat_pasien']) && !empty($examination['riwayat_pasien'])): ?>
<div class="p-6 bg-white border-t border-gray-200">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <i data-lucide="heart" class="w-5 h-5 mr-2 text-blue-600"></i>
        Riwayat Medis Pasien
    </h3>
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
        <p class="text-sm text-gray-700"><?= $examination['riwayat_pasien'] ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Add Timeline Entry Section -->
<?php if (isset($examination['status_pemeriksaan']) && $examination['status_pemeriksaan'] == 'progress'): ?>
<div class="p-6 bg-gray-50 border-t border-gray-200">
    <div class="max-w-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Update Timeline</h3>
            <button type="button" onclick="showTemplateModal()" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i data-lucide="list" class="w-4 h-4 inline mr-1"></i>Template
            </button>
        </div>
        
        <form id="addTimelineForm" onsubmit="addTimelineEntry(event)">
            <input type="hidden" value="<?= isset($examination['pemeriksaan_id']) ? $examination['pemeriksaan_id'] : '' ?>" id="examId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status/Kejadian *</label>
                    <input type="text" id="timelineStatus" name="status" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: Analisis Kimia Dimulai" required maxlength="100">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Waktu (Opsional)</label>
                    <input type="datetime-local" id="timelineDate" name="tanggal_update" 
                           value="<?= date('Y-m-d\TH:i') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan Detail *</label>
                <textarea id="timelineKeterangan" name="keterangan" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Masukkan detail keterangan untuk update ini..." required maxlength="500"></textarea>
                <div class="text-xs text-gray-500 mt-1">
                    <span id="charCount">0</span>/500 karakter
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>Tambah Update
                </button>
                <button type="button" onclick="clearForm()" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                    <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>Clear
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
// Simple notification function
function showNotification(type, message) {
    if (type === 'success') {
        alert('✅ ' + message);
    } else {
        alert('❌ ' + message);
    }
}

// Character counter
document.addEventListener('DOMContentLoaded', function() {
    const keteranganTextarea = document.getElementById('timelineKeterangan');
    const charCountSpan = document.getElementById('charCount');
    
    if (keteranganTextarea && charCountSpan) {
        keteranganTextarea.addEventListener('input', function() {
            const charCount = this.value.length;
            charCountSpan.textContent = charCount;
            
            if (charCount > 450) {
                charCountSpan.className = 'text-red-600 font-medium';
            } else if (charCount > 400) {
                charCountSpan.className = 'text-orange-600';
            } else {
                charCountSpan.className = '';
            }
        });
    }
});

// Add timeline entry
function addTimelineEntry(event) {
    event.preventDefault();
    
    const examId = document.getElementById('examId').value;
    if (!examId) {
        showNotification('error', 'ID pemeriksaan tidak valid');
        return;
    }
    
    const formData = new FormData(event.target);
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 inline mr-2 animate-spin"></i>Menambah...';
    submitBtn.disabled = true;
    
    fetch('<?= base_url('laboratorium/add_timeline_entry') ?>/' + examId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            location.reload();
        } else {
            showNotification('error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat menambah timeline');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Clear form
function clearForm() {
    const form = document.getElementById('addTimelineForm');
    if (form) {
        form.reset();
        document.getElementById('timelineDate').value = new Date().toISOString().slice(0, 16);
        const charCount = document.getElementById('charCount');
        if (charCount) charCount.textContent = '0';
    }
}

// Edit timeline entry (placeholder)
function editTimelineEntry(timelineId, status, keterangan) {
    const newStatus = prompt('Edit Status:', status);
    const newKeterangan = prompt('Edit Keterangan:', keterangan);
    
    if (newStatus && newKeterangan) {
        // Implementation would go here
        showNotification('success', 'Fitur edit akan tersedia segera');
    }
}

// Delete timeline entry (placeholder)
function deleteTimelineEntry(timelineId) {
    if (confirm('Apakah Anda yakin ingin menghapus entry ini?')) {
        // Implementation would go here
        showNotification('success', 'Fitur hapus akan tersedia segera');
    }
}

// Show template modal (placeholder)
function showTemplateModal() {
    showNotification('success', 'Fitur template akan tersedia segera');
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