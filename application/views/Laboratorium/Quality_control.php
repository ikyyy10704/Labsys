<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Kualitas - Labsys</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Main Content - Full Width -->
    <main class="min-h-screen bg-gray-50">
        <!-- Page Header -->
        <div class=" bg-gradient-to-r from-med-blue border-b border-gray-200 shadow-sm">
            <div class="px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                                <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                            </div>
                            <span>Kontrol Kualitas</span>
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">Validasi dan kontrol kualitas hasil pemeriksaan laboratorium</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                            <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                            QC Active
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Menunggu Validasi</p>
                            <p class="text-2xl font-bold text-orange-600" id="pending-count">
                                <?= isset($pending_validation) ? count($pending_validation) : 0 ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6 text-orange-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Divalidasi Hari Ini</p>
                            <p class="text-2xl font-bold text-green-600" id="validated-today">
                                <?= isset($recent_validations) ? count($recent_validations) : 0 ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Bulan Ini</p>
                            <p class="text-2xl font-bold text-blue-600" id="total-month">156</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="bar-chart-3" class="w-6 h-6 text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Rata-rata Waktu</p>
                            <p class="text-2xl font-bold text-purple-600" id="avg-time">2.4h</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="timer" class="w-6 h-6 text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>

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
                            <button id="validate-selected" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                <span>Validasi Terpilih</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Input</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="pending-validation-table">
                            <?php if (isset($pending_validation) && !empty($pending_validation)): ?>
                                <?php foreach ($pending_validation as $exam): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" data-exam-id="<?= $exam['pemeriksaan_id'] ?>">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($exam['nomor_pemeriksaan']) ?></div>
                                            <div class="text-sm text-gray-500"><?= date('d M Y', strtotime($exam['tanggal_pemeriksaan'])) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($exam['nama_pasien']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($exam['nik']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i data-lucide="flask" class="w-3 h-3 mr-1"></i>
                                                <?= htmlspecialchars($exam['jenis_pemeriksaan']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($exam['nama_petugas'] ?: 'N/A') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div><?= date('H:i', strtotime($exam['updated_at'] ?: $exam['created_at'])) ?> WIB</div>
                                            <div class="text-xs text-orange-600">
                                                <?php
                                                $hours = round((time() - strtotime($exam['updated_at'] ?: $exam['created_at'])) / 3600);
                                                echo $hours . ' jam lalu';
                                                ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $hours = round((time() - strtotime($exam['updated_at'] ?: $exam['created_at'])) / 3600);
                                            if ($hours > 24) {
                                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">';
                                                echo '<i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>Urgent</span>';
                                            } else {
                                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">';
                                                echo '<i data-lucide="clock" class="w-3 h-3 mr-1"></i>Menunggu</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <button onclick="viewResult(<?= $exam['pemeriksaan_id'] ?>)" class="text-blue-600 hover:text-blue-800 font-medium">
                                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                                Lihat
                                            </button>
                                            <button onclick="validateResult(<?= $exam['pemeriksaan_id'] ?>)" class="text-green-600 hover:text-green-800 font-medium">
                                                <i data-lucide="check" class="w-4 h-4 inline mr-1"></i>
                                                Validasi
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada hasil yang menunggu validasi
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validator</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Validasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (isset($recent_validations) && !empty($recent_validations)): ?>
                                <?php foreach ($recent_validations as $exam): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($exam['nomor_pemeriksaan']) ?></div>
                                            <div class="text-sm text-gray-500"><?= date('d M Y', strtotime($exam['tanggal_pemeriksaan'])) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($exam['nama_pasien']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($exam['nik']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i data-lucide="shield" class="w-3 h-3 mr-1"></i>
                                                <?= htmlspecialchars($exam['jenis_pemeriksaan']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($exam['nama_petugas'] ?: 'System') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div><?= date('H:i', strtotime($exam['completed_at'])) ?> WIB</div>
                                            <div class="text-xs text-green-600">
                                                <?php
                                                $minutes = round((time() - strtotime($exam['completed_at'])) / 60);
                                                if ($minutes < 60) {
                                                    echo $minutes . ' menit lalu';
                                                } else {
                                                    echo round($minutes / 60) . ' jam lalu';
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                Validated
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <button onclick="viewResult(<?= $exam['pemeriksaan_id'] ?>)" class="text-blue-600 hover:text-blue-800 font-medium">
                                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                                Lihat Hasil
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada validasi hari ini
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Result Detail Modal -->
    <div id="result-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Detail Hasil Pemeriksaan</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]" id="modal-content">
                <!-- Content will be loaded here -->
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Tutup
                </button>
                <button id="modal-validate-btn" onclick="validateFromModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium flex items-center space-x-2">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>Validasi Hasil</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Checkbox functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateValidateButton();
        });

        document.querySelectorAll('.row-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateValidateButton);
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
                this.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Memvalidasi...</span>';
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
                    this.innerHTML = '<i data-lucide="shield-check" class="w-4 h-4"></i><span>Validasi Terpilih</span>';
                    this.disabled = true;
                    lucide.createIcons();
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
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nomor Pemeriksaan</label>
                            <p class="text-lg font-semibold text-gray-900">${examination.nomor_pemeriksaan}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Pasien</label>
                            <p class="text-lg font-semibold text-gray-900">${examination.nama_pasien}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Jenis Pemeriksaan</label>
                            <p class="text-lg font-semibold text-gray-900">${examination.jenis_pemeriksaan}</p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Hasil Pemeriksaan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${Object.entries(results || {}).map(([key, value]) => `
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-600">${key}:</span>
                                    <span class="font-medium text-gray-900">${value || '-'}</span>
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
            
            lucide.createIcons();
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
            closeModal();
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
</body>
</html>