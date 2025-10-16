<!-- Header Section -->
<div class="w-full bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="badge-check" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Validasi Hasil</h1>
                    <p class="text-blue-100">Validasi dan kontrol kualitas hasil pemeriksaan laboratorium</p>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 px-4 py-3 shadow-lg">
                <p class="text-blue-100 text-xs font-medium mb-0.5">Status QC</p>
                <p class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                    Active
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="w-full px-6 py-6 bg-gray-50 border-b border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-orange-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Menunggu Validasi</p>
                    <p class="text-lg font-bold text-gray-900" id="pending-count">
                        <?= isset($pending_validation) ? count($pending_validation) : 0 ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Divalidasi Hari Ini</p>
                    <p class="text-lg font-bold text-gray-900" id="validated-today">
                        <?= isset($qc_stats['validated_today']) ? $qc_stats['validated_today'] : 0 ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bulan Ini</p>
                    <p class="text-lg font-bold text-gray-900" id="total-month">
                        <?= isset($qc_stats['validated_this_month']) ? $qc_stats['validated_this_month'] : 0 ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="timer" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata Waktu</p>
                    <p class="text-lg font-bold text-gray-900" id="avg-time">
                        <?= isset($qc_stats['avg_validation_time']) ? $qc_stats['avg_validation_time'] : 0 ?>h
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="w-full px-6 pb-6">
    <!-- Pending Validation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-orange-600"></i>
                    <span>Hasil Menunggu Validasi</span>
                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium" id="pending-badge">
                        <?= isset($pending_validation) ? count($pending_validation) : 0 ?> Item
                    </span>
                </h2>
                <div class="flex items-center space-x-2">
                    <button id="select-all-btn" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <i data-lucide="check-square" class="w-4 h-4 inline mr-1"></i>Pilih Semua
                    </button>
                    <button id="validate-selected" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i>
                        <span>Validasi Terpilih</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Pending Validation List -->
        <div class="space-y-4 p-6" id="pending-validation-list">
            <?php if (isset($pending_validation) && !empty($pending_validation)): ?>
                <?php foreach ($pending_validation as $exam): ?>
                    <div class="bg-white rounded-xl border border-gray-200 hover:shadow-md transition-all duration-200">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <!-- Checkbox -->
                                    <div class="flex items-center pt-1">
                                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" data-exam-id="<?= $exam['pemeriksaan_id'] ?>">
                                    </div>
                                    
                                    <!-- Status Icon -->
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center shadow-sm
                                        <?php
                                        $hours = isset($exam['hours_waiting']) ? $exam['hours_waiting'] : 0;
                                        if ($hours > 24) {
                                            echo 'bg-gradient-to-br from-red-500 to-red-600';
                                        } else {
                                            echo 'bg-gradient-to-br from-orange-500 to-orange-600';
                                        }
                                        ?>">
                                        <?php if ($hours > 24): ?>
                                        <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                                        <?php else: ?>
                                        <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Examination Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($exam['jenis_pemeriksaan']) ?></h3>
                                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                                <?php if ($hours > 24): ?>
                                                    bg-red-100 text-red-800
                                                <?php else: ?>
                                                    bg-orange-100 text-orange-800
                                                <?php endif; ?>">
                                                <?= $hours > 24 ? 'URGENT' : 'MENUNGGU' ?>
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-x-8 gap-y-2 text-xs text-gray-600 mt-3">
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="user" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">Pasien:</span> 
                                                <span><?= htmlspecialchars($exam['nama_pasien']) ?></span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="credit-card" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">NIK:</span> 
                                                <span><?= htmlspecialchars($exam['nik']) ?></span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="hash" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">No. Pemeriksaan:</span> 
                                                <span class="font-mono"><?= htmlspecialchars($exam['nomor_pemeriksaan']) ?></span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="calendar" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">Tanggal:</span> 
                                                <span><?= date('d/m/Y', strtotime($exam['tanggal_pemeriksaan'])) ?></span>
                                            </div>
                                            <?php if ($exam['nama_petugas']): ?>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="user-round" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">Petugas:</span> 
                                                <span class="truncate"><?= htmlspecialchars($exam['nama_petugas']) ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="clock" class="w-3 h-3 text-orange-500"></i>
                                                <span class="font-medium text-orange-600">Menunggu:</span> 
                                                <span class="font-semibold"><?= $hours ?> jam</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="activity" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">Update:</span> 
                                                <span><?= date('H:i', strtotime($exam['updated_at'] ?: $exam['created_at'])) ?> WIB</span>
                                            </div>
                                            <?php if ($hours > 24): ?>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="alert-circle" class="w-3 h-3 text-red-500"></i>
                                                <span class="font-medium text-red-600">Prioritas Tinggi</span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex flex-col space-y-2 ml-4">
                                    <button type="button" onclick="viewResult(<?= $exam['pemeriksaan_id'] ?>)" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                                        <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                        <span>Lihat</span>
                                    </button>
                                    <button type="button" onclick="validateResult(<?= $exam['pemeriksaan_id'] ?>)" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                                        <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                        <span>Validasi</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="check-circle" class="w-12 h-12 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Hasil Menunggu</h3>
                    <p class="text-gray-500">Semua hasil pemeriksaan sudah divalidasi.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Validations -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                <span>Validasi Terbaru Hari Ini</span>
            </h2>
        </div>
        
        <!-- Recent Validations List -->
        <div class="space-y-4 p-6">
            <?php if (isset($recent_validations) && !empty($recent_validations)): ?>
                <?php foreach ($recent_validations as $exam): ?>
                    <div class="bg-white rounded-xl border border-gray-200 hover:shadow-md transition-all duration-200">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <!-- Status Icon -->
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-sm">
                                        <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                                    </div>
                                    
                                    <!-- Examination Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($exam['jenis_pemeriksaan']) ?></h3>
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                VALIDATED
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-x-8 gap-y-2 text-xs text-gray-600 mt-3">
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="user" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">Pasien:</span> 
                                                <span><?= htmlspecialchars($exam['nama_pasien']) ?></span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="credit-card" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">NIK:</span> 
                                                <span><?= htmlspecialchars($exam['nik']) ?></span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="hash" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">No. Pemeriksaan:</span> 
                                                <span class="font-mono"><?= htmlspecialchars($exam['nomor_pemeriksaan']) ?></span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="calendar" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">Tanggal:</span> 
                                                <span><?= date('d/m/Y', strtotime($exam['tanggal_pemeriksaan'])) ?></span>
                                            </div>
                                            <?php if ($exam['nama_petugas']): ?>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="user-round" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">Validator:</span> 
                                                <span class="truncate"><?= htmlspecialchars($exam['nama_petugas']) ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="clock" class="w-3 h-3 text-green-500"></i>
                                                <span class="font-medium text-green-600">Validasi:</span> 
                                                <span><?= date('H:i', strtotime($exam['completed_at'])) ?> WIB</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="timer" class="w-3 h-3 text-gray-500"></i>
                                                <span class="font-medium text-gray-700">Waktu lalu:</span>
                                                <span class="text-green-600">
                                                    <?php
                                                    $minutes = round((time() - strtotime($exam['completed_at'])) / 60);
                                                    if ($minutes < 60) {
                                                        echo $minutes . ' menit';
                                                    } else {
                                                        echo round($minutes / 60) . ' jam';
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Button -->
                                <div class="ml-4">
                                    <button type="button" onclick="viewResult(<?= $exam['pemeriksaan_id'] ?>)" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 transition-colors duration-200">
                                        <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                                        <span>Lihat Hasil</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="calendar" class="w-12 h-12 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Validasi Hari Ini</h3>
                    <p class="text-gray-500">Validasi hari ini akan muncul di sini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Result Detail Modal -->
<div id="result-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden fade-in">
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-6 border-b border-blue-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Detail Hasil Pemeriksaan</h3>
                        <p class="text-sm text-blue-100">Review dan validasi hasil laboratorium</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="text-white hover:text-gray-200 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]" id="modal-content">
            <!-- Content will be loaded here -->
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                Tutup
            </button>
            <button id="modal-validate-btn" onclick="validateFromModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium flex items-center space-x-2 transition-colors duration-200">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Validasi Hasil</span>
            </button>
        </div>
    </div>
</div>

<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    // Select all button
    const selectAllBtn = document.getElementById('select-all-btn');
    if (selectAllBtn) {
        selectAllBtn.onclick = function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
            
            this.innerHTML = allChecked ? 
                '<i data-lucide="check-square" class="w-4 h-4 inline mr-1"></i>Pilih Semua' : 
                '<i data-lucide="x-square" class="w-4 h-4 inline mr-1"></i>Batal Pilih';
            
            updateValidateButton();
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        };
    }
    
    // Individual checkbox listeners
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateValidateButton);
    });
});

function updateValidateButton() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const validateBtn = document.getElementById('validate-selected');
    
    if (selectedCheckboxes.length > 0) {
        validateBtn.disabled = false;
        validateBtn.querySelector('span').textContent = `Validasi Terpilih (${selectedCheckboxes.length})`;
    } else {
        validateBtn.disabled = true;
        validateBtn.querySelector('span').textContent = 'Validasi Terpilih';
    }
}

// Validate selected results
document.getElementById('validate-selected').addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const examIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.examId);
    
    if (examIds.length === 0) return;
    
    if (confirm(`Yakin ingin memvalidasi ${examIds.length} hasil pemeriksaan?`)) {
        // Show loading
        this.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i><span>Memvalidasi...</span>';
        this.disabled = true;
        
        // Send AJAX request
        fetch('<?= base_url("laboratorium/batch_validate_results") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                examination_ids: examIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${data.success_count} hasil berhasil divalidasi!`);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Gagal memvalidasi hasil'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memvalidasi hasil');
        })
        .finally(() => {
            // Reset button
            this.innerHTML = '<i data-lucide="shield-check" class="w-4 h-4 mr-2"></i><span>Validasi Terpilih</span>';
            this.disabled = true;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    }
});

function viewResult(examId) {
    // Load result details
    fetch('<?= base_url("laboratorium/get_result_details") ?>/' + examId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayResultModal(data.examination, data.results);
        } else {
            alert('Error: ' + (data.message || 'Gagal memuat detail hasil'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail hasil');
    });
}

function displayResultModal(examination, results) {
    const content = `
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
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-blue-600"></i>
                    <span>Hasil Pemeriksaan</span>
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
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center space-x-2 mb-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                    <h4 class="font-semibold text-yellow-800">Catatan Validasi</h4>
                </div>
                <p class="text-sm text-yellow-700">
                    Pastikan semua nilai berada dalam rentang normal dan sesuai dengan kondisi klinis pasien. 
                    Periksa kembali kalibrasi instrumen dan kualitas sampel sebelum memvalidasi.
                </p>
            </div>
        </div>
    `;
    
    document.getElementById('modal-content').innerHTML = content;
    document.getElementById('result-modal').classList.remove('hidden');
    document.getElementById('modal-validate-btn').dataset.examId = examination.pemeriksaan_id;
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function validateResult(examId) {
    if (confirm('Yakin ingin memvalidasi hasil pemeriksaan ini?')) {
        // Send AJAX request
        fetch('<?= base_url("laboratorium/validate_result") ?>/' + examId, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Hasil berhasil divalidasi!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Gagal memvalidasi hasil'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memvalidasi hasil');
        });
    }
}

function validateFromModal() {
    const examId = document.getElementById('modal-validate-btn').dataset.examId;
    validateResult(examId);
}

function closeModal() {
    document.getElementById('result-modal').classList.add('hidden');
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Close modal on click outside
document.getElementById('result-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Load dashboard stats
function loadDashboardStats() {
    fetch('<?= base_url("laboratorium/get_qc_dashboard_data") ?>')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('pending-count').textContent = data.data.pending_validation;
            document.getElementById('validated-today').textContent = data.data.validated_today;
            document.getElementById('total-month').textContent = data.data.validated_this_month;
            document.getElementById('avg-time').textContent = data.data.avg_validation_time + 'h';
        }
    })
    .catch(error => {
        console.error('Error loading dashboard stats:', error);
    });
}

// Load stats on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
});
</script>

<style>
/* Fade in animation */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Loading animation */
@keyframes spin {
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>