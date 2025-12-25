<!-- Header Section -->
<script src="https://unpkg.com/lucide@latest"></script>

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
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
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
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
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
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
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
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
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
                    <button id="select-all-btn" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
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
                                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4" data-exam-id="<?= $exam['pemeriksaan_id'] ?>">
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
                                    <button type="button" onclick="showValidationHistory(<?= $exam['pemeriksaan_id'] ?>, '<?= $exam['nik'] ?>', '<?= htmlspecialchars($exam['nama_pasien'], ENT_QUOTES) ?>')" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 transition-colors duration-200">
                                        <i data-lucide="history" class="w-3 h-3 mr-1"></i>
                                        <span>Histori</span>
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
                                
                                <!-- Action Buttons -->
                                <div class="flex flex-col space-y-2 ml-4">
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
                <button onclick="closeModal('result-modal')" class="text-white hover:text-gray-200 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]" id="modal-content">
            <!-- Content will be loaded here -->
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
            <button onclick="closeModal('result-modal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                Tutup
            </button>
            <button id="modal-validate-btn" onclick="validateFromModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium flex items-center space-x-2 transition-colors duration-200">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Validasi Hasil</span>
            </button>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div id="invoice-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden fade-in">
        <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-purple-800 p-6 border-b border-purple-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <i data-lucide="receipt" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Detail Invoice</h3>
                        <p class="text-sm text-purple-100">Informasi pembayaran dan tagihan</p>
                    </div>
                </div>
                <button onclick="closeModal('invoice-modal')" class="text-white hover:text-gray-200 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]" id="invoice-modal-content">
            <!-- Content will be loaded here -->
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-between bg-gray-50">
            <button onclick="printInvoice()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 flex items-center space-x-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak</span>
            </button>
            <button onclick="closeModal('invoice-modal')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Validation History Modal -->
<div id="history-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden fade-in">
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-800 p-6 border-b border-purple-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <i data-lucide="history" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white" id="history-modal-title">Histori Validasi Pasien</h3>
                        <p class="text-sm text-purple-100">Riwayat hasil pemeriksaan sebelumnya</p>
                    </div>
                </div>
                <button onclick="closeModal('history-modal')" class="text-white hover:text-gray-200 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]" id="history-modal-content">
            <!-- Content will be loaded here -->
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-between items-center bg-gray-50">
            <div class="text-sm text-gray-600" id="history-stats">
                <!-- Statistics will be shown here -->
            </div>
            <div class="flex space-x-3">
                <button onclick="printHistory()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 flex items-center space-x-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak</span>
                </button>
                <button onclick="closeModal('history-modal')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Custom Validation Confirmation Modal -->
<div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg fade-in transform scale-100 transition-all">
        <div class="p-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi Validasi</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Apakah Anda sudah memastikan bahwa seluruh data hasil pemeriksaan sudah benar dan sesuai sebelum divalidasi?
                    </p>
                    <div class="mt-4 bg-orange-50 border border-orange-200 rounded-lg p-3 text-sm text-orange-800 flex items-start">
                        <i data-lucide="info" class="w-4 h-4 mt-0.5 mr-2 flex-shrink-0"></i>
                         Tindakan ini tidak dapat dibatalkan setelah dikonfirmasi.
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-gray-100">
            <button onclick="closeModal('confirmation-modal')" 
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                Batal
            </button>
            <button id="confirm-validate-btn" onclick="confirmValidation()" 
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center">
                <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                Ya, Validasi
            </button>
        </div>
    </div>
</div>

<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Global variables
let currentExamId = null;
let currentInvoiceId = null;
let currentHistoryData = null;
let currentPatientNik = null;

// Checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeCheckboxes();
    loadDashboardStats();
     console.log(' Quality Control page loaded');
    
    const validateSelectedBtn = document.getElementById('validate-selected');
    if (validateSelectedBtn) {
        validateSelectedBtn.addEventListener('click', handleBatchValidation);
    }
    
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', updateValidateButton);
    });
});

function initializeCheckboxes() {
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
}

function updateValidateButton() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const validateBtn = document.getElementById('validate-selected');
    
    if (validateBtn) {
        if (selectedCheckboxes.length > 0) {
            validateBtn.disabled = false;
            validateBtn.querySelector('span').textContent = `Validasi Terpilih (${selectedCheckboxes.length})`;
        } else {
            validateBtn.disabled = true;
            validateBtn.querySelector('span').textContent = 'Validasi Terpilih';
        }
    }
}

// Batch validation handler
function handleBatchValidation() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const examIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.examId);
    
    if (examIds.length === 0) return;
    
    if (confirm(`Yakin ingin memvalidasi ${examIds.length} hasil pemeriksaan?`)) {
        const btn = document.getElementById('validate-selected');
        const originalHTML = btn.innerHTML;
        
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i><span>Memvalidasi...</span>';
        btn.disabled = true;
        
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        fetch('<?= base_url("supervisor/batch_validate_results") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ examination_ids: examIds })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
                
                examIds.forEach(examId => {
                    const item = document.querySelector(`[data-exam-id="${examId}"]`)?.closest('.bg-white');
                    if (item) {
                        item.style.opacity = '0.5';
                        item.style.pointerEvents = 'none';
                        setTimeout(() => item.remove(), 500);
                    }
                });
                
                setTimeout(() => {
                    updatePendingCount();
                    loadDashboardStats();
                }, 600);
            } else {
                showNotification('error', data.message || 'Gagal memvalidasi hasil');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat memvalidasi');
        })
        .finally(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = true;
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
            updateValidateButton();
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    }
}

function viewResult(examId) {
    console.log('viewResult called with examId:', examId);
    
    currentExamId = examId;
    
    // Set examId di button validasi modal SEBELUM modal dibuka
    const validateBtn = document.getElementById('modal-validate-btn');
    if (validateBtn) {
        validateBtn.dataset.examId = examId;
        console.log('Set modal-validate-btn dataset.examId to:', examId);
    }
    
    document.getElementById('result-modal').classList.remove('hidden');
    document.getElementById('modal-content').innerHTML = `
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 animate-spin"></i>
            </div>
            <p class="text-gray-500">Memuat detail hasil pemeriksaan...</p>
        </div>
    `;
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    
    fetch('<?= base_url("supervisor/get_result_details") ?>/' + examId)
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (!response.ok || !contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response from server');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (data.is_multiple) {
                displayMultipleResultsModal(data.examination, data.results);
            } else {
                displaySingleResultModal(data.examination, data.results);
            }
        } else {
            showNotification('error', data.message || 'Gagal memuat detail hasil');
            closeModal('result-modal');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat memuat detail hasil');
        closeModal('result-modal');
    });
}

function displaySingleResultModal(examination, results) {
    const content = `
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Pemeriksaan</label>
                    <p class="text-lg font-semibold text-gray-900">${escapeHtml(examination.nomor_pemeriksaan)}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Pasien</label>
                    <p class="text-lg font-semibold text-gray-900">${escapeHtml(examination.nama_pasien)}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Pemeriksaan</label>
                    <p class="text-lg font-semibold text-gray-900">${escapeHtml(examination.jenis_pemeriksaan)}</p>
                </div>
            </div>
            
            ${examination.status_pasien ? renderStatusPasienInfo(examination) : ''}
            
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center space-x-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-blue-600"></i>
                    <span>Hasil Pemeriksaan</span>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${Object.entries(results || {}).map(([key, value]) => `
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600 font-medium text-sm">${escapeHtml(key)}:</span>
                            <span class="font-semibold text-gray-900 text-sm">${escapeHtml(value || '-')}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
            
            ${renderValidationWarning()}
        </div>
    `;
    
    document.getElementById('modal-content').innerHTML = content;
    document.getElementById('modal-validate-btn').dataset.examId = examination.pemeriksaan_id;
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function displayMultipleResultsModal(examination, results) {
    const details = examination.examination_details || [];
    
    let content = `
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Pemeriksaan</label>
                    <p class="text-lg font-semibold text-gray-900">${escapeHtml(examination.nomor_pemeriksaan)}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Pasien</label>
                    <p class="text-lg font-semibold text-gray-900">${escapeHtml(examination.nama_pasien)}</p>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border-2 border-blue-300">
                    <label class="block text-sm font-medium text-blue-700 mb-1 flex items-center">
                        <i data-lucide="layers" class="w-4 h-4 mr-1"></i>
                        Multiple Pemeriksaan
                    </label>
                    <p class="text-lg font-bold text-blue-900">${details.length} Jenis Pemeriksaan</p>
                </div>
            </div>`;
    
    if (examination.status_pasien) {
        content += renderStatusPasienInfo(examination);
    }
    
    content += `
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                <div class="flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-1">Pemeriksaan Multi-Jenis</h4>
                        <p class="text-sm text-blue-700">
                            Pasien ini memiliki <strong>${details.length} jenis pemeriksaan berbeda</strong>. 
                            Pastikan semua hasil telah diperiksa sebelum melakukan validasi.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">`;
    
    details.forEach((detail, index) => {
        const jenisType = detail.jenis_pemeriksaan || 'Unknown';
        const subDisplay = detail.sub_pemeriksaan_display || '';
        const jenisResults = results[jenisType] || {};
        
        content += `
                <div class="border-2 border-blue-300 rounded-xl overflow-hidden bg-gradient-to-br from-white to-blue-50">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                    <i data-lucide="${getExaminationIcon(jenisType)}" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-white">${escapeHtml(jenisType)}</h4>
                                    ${subDisplay ? `<p class="text-xs text-blue-100">${escapeHtml(subDisplay)}</p>` : ''}
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium text-white">
                                #${index + 1}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4">`;
        
        if (Object.keys(jenisResults).length > 0) {
            content += '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">';
            for (const [key, value] of Object.entries(jenisResults)) {
                content += `
                    <div class="flex justify-between py-2 px-3 bg-white rounded-lg border border-blue-100">
                        <span class="text-gray-600 font-medium text-sm">${escapeHtml(key)}:</span>
                        <span class="font-semibold text-gray-900 text-sm">${escapeHtml(value || '-')}</span>
                    </div>`;
            }
            content += '</div>';
        } else {
            content += `
                <div class="text-center py-6 bg-yellow-50 rounded-lg border border-yellow-200">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-yellow-600 mx-auto mb-2"></i>
                    <p class="text-sm text-yellow-700 font-medium">Hasil untuk ${escapeHtml(jenisType)} belum tersedia</p>
                </div>`;
        }
        
        content += `
                    </div>
                </div>`;
    });
    
    content += `
            </div>
            ${renderValidationWarning()}
        </div>`;
    
    document.getElementById('modal-content').innerHTML = content;
    document.getElementById('modal-validate-btn').dataset.examId = examination.pemeriksaan_id;
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function renderStatusPasienInfo(examination) {
    const status = examination.status_pasien;
    const keterangan = examination.keterangan_obat || '';
    
    let bgColor, borderColor, iconColor, icon, label, message;
    
    if (status === 'puasa') {
        bgColor = 'from-green-50 to-emerald-50';
        borderColor = 'border-green-500';
        iconColor = 'text-green-600';
        icon = 'coffee';
        label = 'Pasien Puasa';
        message = 'Pasien telah berpuasa sesuai persyaratan pemeriksaan';
    } else if (status === 'minum_obat') {
        bgColor = 'from-red-50 to-rose-50';
        borderColor = 'border-red-500';
        iconColor = 'text-red-600';
        icon = 'pill';
        label = 'Pasien Minum Obat';
        message = `Obat: ${escapeHtml(keterangan)}<br><span class="text-red-600 italic">⚠️ Perhatian: Konsumsi obat dapat mempengaruhi hasil</span>`;
    } else {
        bgColor = 'from-yellow-50 to-amber-50';
        borderColor = 'border-yellow-500';
        iconColor = 'text-yellow-600';
        icon = 'utensils';
        label = 'Pasien Belum Puasa';
        message = 'Hasil mungkin terpengaruh untuk pemeriksaan tertentu';
    }
    
    return `
        <div class="bg-gradient-to-r ${bgColor} border-l-4 ${borderColor} rounded-r-lg p-4 mb-4">
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm">
                        <i data-lucide="${icon}" class="w-5 h-5 ${iconColor}"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold ${iconColor} mb-1">${label}</h4>
                    <p class="text-sm ${iconColor.replace('-600', '-700')}">${message}</p>
                </div>
            </div>
        </div>
    `;
}

function renderValidationWarning() {
    return `
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
    `;
}

function getExaminationIcon(jenisType) {
    const map = {
        'Kimia Darah': 'droplet',
        'Hematologi': 'activity',
        'Urinologi': 'beaker',
        'Serologi': 'shield-check',
        'Serologi Imunologi': 'shield-check',
        'TBC': 'wind',
        'IMS': 'alert-triangle'
    };
    return map[jenisType] || 'clipboard';
}
async function validateResult(examId) {
    console.log(' Starting validation for exam:', examId);
    
    // Get button element
    const btn = event?.target?.closest('button') || 
                document.querySelector(`button[onclick*="validateResult(${examId})"]`);
    
    if (!btn) {
        console.error(' Button not found');
        showNotification('error', 'Tombol tidak ditemukan');
        return;
    }
    
    const originalHTML = btn.innerHTML;
    
    // Set loading state
    btn.innerHTML = '<i data-lucide="loader-2" class="w-3 h-3 mr-1 animate-spin"></i>Memvalidasi...';
    btn.disabled = true;
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    try {
        console.log('📤 Sending validation request...');
        
        const response = await fetch(`<?= base_url("supervisor/validate_result_simple") ?>/${examId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        console.log('📥 Response status:', response.status);
        console.log('📥 Response headers:', [...response.headers.entries()]);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // Check content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            console.error(' Invalid content type:', contentType);
            const text = await response.text();
            console.error('Response text:', text);
            throw new Error('Server tidak mengembalikan JSON response');
        }
        
        const data = await response.json();
        console.log('Parsed response:', data);
        
        if (data.success) {
            console.log('Validation successful!');
            
            showNotification('success', data.message || 'Hasil berhasil divalidasi!');
            
            // Remove exam from list with animation
            removeExamFromList(examId);
            
            // Update counters
            setTimeout(() => {
                updatePendingCount();
                loadDashboardStats();
            }, 500);
            
        } else {
            console.error(' Validation failed:', data.message);
            showNotification('error', data.message || 'Validasi gagal');
            
            // Restore button
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
        
    } catch (error) {
        console.error('Validation error:', error);
        showNotification('error', 'Terjadi kesalahan: ' + error.message);
        
        // Restore button
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

function removeExamFromList(examId) {
    const item = document.querySelector(`[data-exam-id="${examId}"]`)?.closest('.bg-white');
    if (!item) {
        console.log('Item not found in DOM, reloading page...');
        setTimeout(() => location.reload(), 1000);
        return;
    }
    
    item.style.transition = 'all 0.5s ease';
    item.style.opacity = '0';
    item.style.transform = 'translateX(20px)';
    
    setTimeout(() => {
        item.remove();
        
        const pendingList = document.getElementById('pending-validation-list');
        if (pendingList && pendingList.querySelectorAll('.bg-white').length === 0) {
            showEmptyState();
        }
    }, 500);
}
function showEmptyState() {
    const emptyState = `
        <div class="text-center py-12 animate-fadeIn">
            <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-green-200">
                <i data-lucide="check-circle" class="w-12 h-12 text-green-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2"> Semua Hasil Telah Divalidasi!</h3>
            <p class="text-gray-500 mb-4">Tidak ada hasil pemeriksaan yang menunggu validasi.</p>
            <button onclick="location.reload()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                Refresh Halaman
            </button>
        </div>
    `;
    
    const pendingList = document.getElementById('pending-validation-list');
    if (pendingList) {
        pendingList.innerHTML = emptyState;
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

function validateFromModal() {
    // PERBAIKAN: Gunakan currentExamId global yang sudah di-set di viewResult()
    // Jangan ambil dari dataset karena bisa tidak sinkron
    console.log('validateFromModal called');
    console.log('currentExamId (global):', currentExamId);
    
    // Fallback ke dataset jika global tidak ada
    let examId = currentExamId;
    if (!examId) {
        const validateBtn = document.getElementById('modal-validate-btn');
        examId = validateBtn ? validateBtn.dataset.examId : null;
        console.log('Fallback to dataset.examId:', examId);
    }
    
    if (!examId || examId === 'undefined' || examId === 'null' || examId === null) {
        console.error('examId is invalid:', examId);
        showNotification('error', 'ID pemeriksaan tidak ditemukan. Silakan tutup modal dan coba lagi.');
        return;
    }
    
    // Set currentExamId untuk digunakan di confirmValidation
    currentExamId = examId;
    console.log('Final examId to validate:', currentExamId);
    
    // Show confirmation modal instead of validating directly
    document.getElementById('confirmation-modal').classList.remove('hidden');
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}


function confirmValidation() {
    console.log('confirmValidation called, currentExamId:', currentExamId);
    
    if (!currentExamId || currentExamId === 'undefined' || currentExamId === 'null') {
        console.error('currentExamId is invalid:', currentExamId);
        showNotification('error', 'ID pemeriksaan tidak valid. Silakan refresh halaman.');
        closeModal('confirmation-modal');
        return;
    }
    
    // PERBAIKAN: Simpan examId ke variabel lokal SEBELUM closeModal
    // karena closeModal('result-modal') akan reset currentExamId = null
    const examIdToValidate = currentExamId;
    console.log('Saved examIdToValidate:', examIdToValidate);
    
    closeModal('confirmation-modal');
    closeModal('result-modal'); // This resets currentExamId to null!
    
    // Gunakan variabel lokal, bukan currentExamId global
    validateResult(examIdToValidate);
}



function loadDashboardStats() {
    fetch('<?= base_url("supervisor/get_qc_dashboard_data") ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.data;
                
                const pendingEl = document.getElementById('pending-count');
                if (pendingEl) pendingEl.textContent = stats.pending_validation || 0;
                
                const todayEl = document.getElementById('validated-today');
                if (todayEl) todayEl.textContent = stats.validated_today || 0;
                
                const monthEl = document.getElementById('total-month');
                if (monthEl) monthEl.textContent = stats.validated_this_month || 0;
                
                const avgEl = document.getElementById('avg-time');
                if (avgEl) avgEl.textContent = (stats.avg_validation_time || 0) + 'h';
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
        });
}


function updatePendingCount() {
    const pendingItems = document.querySelectorAll('#pending-validation-list > .bg-white:not([style*="opacity: 0"])');
    const pendingCount = pendingItems.length;
    
    const countEl = document.getElementById('pending-count');
    if (countEl) countEl.textContent = pendingCount;
    
    const badgeEl = document.getElementById('pending-badge');
    if (badgeEl) badgeEl.textContent = pendingCount + ' Item';
    
    if (pendingCount === 0) {
        showEmptyState();
    }
}
function showValidationHistory(examId, nik, patientName) {
    console.log('Loading validation history for exam:', examId, 'NIK:', nik);
    
    currentPatientNik = nik;
    
    document.getElementById('history-modal').classList.remove('hidden');
    document.getElementById('history-modal-title').textContent = `Histori Pasien: ${patientName}`;
    
    document.getElementById('history-modal-content').innerHTML = `
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                <i data-lucide="loader-2" class="w-8 h-8 text-purple-600 animate-spin"></i>
            </div>
            <p class="text-gray-500">Memuat histori validasi...</p>
        </div>
    `;
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    fetch(`<?= base_url("supervisor/get_validation_history") ?>/${examId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response bukan JSON');
            }
            return response.json();
        })
        .then(data => {
            console.log('History data:', data);
            
            if (data.success) {
                currentHistoryData = data;
                displayValidationHistory(data);
            } else {
                showHistoryError(data.message || 'Gagal memuat histori');
            }
        })
        .catch(error => {
            console.error('Error loading history:', error);
            showHistoryError('Terjadi kesalahan: ' + error.message);
        });
}

function displayValidationHistory(data) {
    const { patient, current_examination, history, stats, has_history } = data;
    
    let html = `
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <h4 class="font-semibold text-indigo-900 mb-1">Informasi Pasien</h4>
                    <div class="space-y-1 text-sm">
                        <p><span class="text-gray-600">Nama:</span> <span class="font-medium">${escapeHtml(patient.nama)}</span></p>
                        <p><span class="text-gray-600">NIK:</span> <span class="font-medium font-mono">${escapeHtml(patient.nik)}</span></p>
                        <p><span class="text-gray-600">Umur/Jenis Kelamin:</span> <span class="font-medium">${escapeHtml(patient.umur || '-')} / ${escapeHtml(patient.jenis_kelamin || '-')}</span></p>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-indigo-900 mb-1">Pemeriksaan Saat Ini</h4>
                    <div class="space-y-1 text-sm">
                        <p><span class="text-gray-600">No. Pemeriksaan:</span> <span class="font-medium font-mono">${escapeHtml(current_examination.nomor_pemeriksaan)}</span></p>
                        <p><span class="text-gray-600">Jenis:</span> <span class="font-medium">${escapeHtml(current_examination.jenis_pemeriksaan)}</span></p>
                        <p><span class="text-gray-600">Tanggal:</span> <span class="font-medium">${formatDate(current_examination.tanggal_pemeriksaan)}</span></p>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-indigo-900 mb-1">Statistik Histori</h4>
                    <div class="space-y-1 text-sm">
                        <p><span class="text-gray-600">Total Validasi:</span> <span class="font-bold text-purple-600">${stats.total_validations}</span></p>
                        ${stats.most_common_test ? `<p><span class="text-gray-600">Pemeriksaan Terbanyak:</span> <span class="font-medium">${escapeHtml(stats.most_common_test)}</span></p>` : ''}
                        ${stats.last_validation ? `<p><span class="text-gray-600">Terakhir:</span> <span class="font-medium">${formatDate(stats.last_validation)}</span></p>` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    if (!has_history) {
        html += `
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="inbox" class="w-12 h-12 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Riwayat Validasi</h3>
                <p class="text-gray-500">Pasien ini belum memiliki riwayat validasi sebelumnya.</p>
                <p class="text-gray-400 text-sm mt-2">Ini mungkin pemeriksaan pertama pasien.</p>
            </div>
        `;
    } else {
        html += `
            <div class="mb-4">
                <h4 class="font-semibold text-gray-900 text-lg mb-2">Riwayat Validasi Sebelumnya</h4>
                <p class="text-sm text-gray-600">Menampilkan ${history.length} hasil pemeriksaan yang telah divalidasi</p>
            </div>
            
            <div class="space-y-4">
        `;
        
        history.forEach((item, index) => {
            const daysAgo = item.days_ago || 0;
            let timeBadge = '';
            
            if (daysAgo === 0) {
                timeBadge = '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Hari Ini</span>';
            } else if (daysAgo <= 7) {
                timeBadge = `<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">${daysAgo} hari lalu</span>`;
            } else if (daysAgo <= 30) {
                timeBadge = `<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">${Math.floor(daysAgo/7)} minggu lalu</span>`;
            } else {
                timeBadge = `<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">${Math.floor(daysAgo/30)} bulan lalu</span>`;
            }
            
            html += `
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow ${index === 0 ? 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-300' : ''}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h5 class="font-semibold text-gray-900">${escapeHtml(item.jenis_pemeriksaan)}</h5>
                                ${timeBadge}
                                ${index === 0 ? '<span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Terbaru</span>' : ''}
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-sm text-gray-600 mb-3">
                                <div class="flex items-center space-x-1">
                                    <i data-lucide="hash" class="w-3 h-3 text-gray-500"></i>
                                    <span class="font-medium">No:</span>
                                    <span class="font-mono">${escapeHtml(item.nomor_pemeriksaan)}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <i data-lucide="calendar" class="w-3 h-3 text-gray-500"></i>
                                    <span class="font-medium">Pemeriksaan:</span>
                                    <span>${formatDate(item.tanggal_pemeriksaan)}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <i data-lucide="check-circle" class="w-3 h-3 text-green-500"></i>
                                    <span class="font-medium">Validasi:</span>
                                    <span>${formatDate(item.completed_at)}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <i data-lucide="user-check" class="w-3 h-3 text-blue-500"></i>
                                    <span class="font-medium">Petugas:</span>
                                    <span>${escapeHtml(item.nama_petugas || 'Sistem')}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col space-y-2 ml-4">
                            <button onclick="viewHistoricalResult(${item.pemeriksaan_id})" 
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                                <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                                Lihat Hasil
                            </button>
                        </div>
                    </div>
                    
                    ${item.status_pemeriksaan === 'selesai' ? `
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center text-xs text-green-600">
                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                            <span>Status: <span class="font-medium">Telah divalidasi</span></span>
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
        });
        
        html += `</div>`;
    }
    
    document.getElementById('history-modal-content').innerHTML = html;
    
    const statsHtml = `
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">
                <i data-lucide="file-text" class="w-3 h-3 inline mr-1"></i>
                Total: <span class="font-semibold">${stats.total_validations}</span> validasi
            </span>
            ${stats.first_validation ? `
            <span class="text-sm text-gray-600">
                <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                Rentang: ${formatDate(stats.first_validation)} - ${formatDate(stats.last_validation)}
            </span>
            ` : ''}
        </div>
    `;
    
    document.getElementById('history-stats').innerHTML = statsHtml;
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function viewHistoricalResult(examId) {
    closeModal('history-modal');
    viewResult(examId);
}

function showHistoryError(message) {
    document.getElementById('history-modal-content').innerHTML = `
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-circle" class="w-8 h-8 text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Gagal Memuat Histori</h3>
            <p class="text-gray-500">${escapeHtml(message)}</p>
        </div>
    `;
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function printHistory() {
    const content = document.getElementById('history-modal-content').innerHTML;
    const printWindow = window.open('', '_blank');
    
    const title = document.getElementById('history-modal-title').textContent;
    const stats = document.getElementById('history-stats').innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Histori Validasi - ${currentPatientNik || 'Pasien'}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                .patient-info { background: #f8fafc; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
                .history-item { border: 1px solid #e2e8f0; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
                .print-date { text-align: right; font-size: 12px; color: #666; margin-bottom: 20px; }
                @media print {
                    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>${escapeHtml(title)}</h1>
                <div class="print-date">Dicetak pada: ${new Date().toLocaleDateString('id-ID')} ${new Date().toLocaleTimeString('id-ID')}</div>
                <div class="patient-info">${stats}</div>
            </div>
            ${content}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    if (modalId === 'result-modal') {
        currentExamId = null;
    } else if (modalId === 'invoice-modal') {
        currentInvoiceId = null;
    } else if (modalId === 'history-modal') {
        currentHistoryData = null;
        currentPatientNik = null;
    }
}
function showNotification(type, message) {
    // Remove existing notifications
    document.querySelectorAll('.custom-notification').forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `custom-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 
        type === 'info' ? 'bg-blue-50 border-blue-200 text-blue-800' :
        'bg-red-50 border-red-200 text-red-800'
    }`;
    
    const icon = type === 'success' ? 'check-circle' : type === 'info' ? 'info' : 'alert-circle';
    const title = type === 'success' ? 'Berhasil' : type === 'info' ? 'Informasi' : 'Error';
    
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <i data-lucide="${icon}" class="w-5 h-5 ${type === 'success' ? 'text-green-600' : type === 'info' ? 'text-blue-600' : 'text-red-600'}"></i>
            <div>
                <p class="font-medium">${title}</p>
                <p class="text-sm">${escapeHtml(message)}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric' 
    });
}

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal('result-modal');
        closeModal('invoice-modal');
        closeModal('history-modal');
        closeModal('confirmation-modal');
    }
});

// Close modals on click outside
['result-modal', 'invoice-modal', 'history-modal', 'confirmation-modal'].forEach(modalId => {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(modalId);
            }
        });
    }
});
</script>

<style>
/* Animations */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Print styles */
@media print {
    body * {
        visibility: hidden;
    }
    #invoice-modal-content, #invoice-modal-content * {
        visibility: visible;
    }
    #invoice-modal-content {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>