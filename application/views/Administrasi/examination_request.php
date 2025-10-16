<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Pemeriksaan - Labsys</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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
        
        /* Highlight search results */
        mark {
            background-color: #fef08a;
            padding: 2px 4px;
            border-radius: 2px;
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Header Section -->
<div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="max-w-full px-6 py-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="clipboard-list" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Permintaan Pemeriksaan</h1>
                    <p class="text-blue-100">Kelola dan pantau semua permintaan pemeriksaan laboratorium</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="openCreateModal()" 
                   class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Buat Permintaan Pemeriksaan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-full px-6 py-6 space-y-6">
    
    <!-- Flash Messages Container -->
    <div id="flash-messages">
        <?php if($this->session->flashdata('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 flex items-center space-x-3 fade-in">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
            <span><?= $this->session->flashdata('success') ?></span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 flex items-center space-x-3 fade-in">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            <span><?= $this->session->flashdata('error') ?></span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600"><?= $counts['pending'] ?? 0 ?></p>
                    <p class="text-xs text-gray-500 mt-1">Menunggu diproses</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Progress</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $counts['progress'] ?? 0 ?></p>
                    <p class="text-xs text-gray-500 mt-1">Sedang diproses</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="activity" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-2xl font-bold text-green-600"><?= $counts['selesai'] ?? 0 ?></p>
                    <p class="text-xs text-gray-500 mt-1">Telah diselesaikan</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Permintaan</p>
                    <p class="text-2xl font-bold text-purple-600"><?= array_sum($counts ?? [0]) ?></p>
                    <p class="text-xs text-gray-500 mt-1">Semua status</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
            <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
            <span>Filter Status</span>
        </h3>
        
        <div class="flex flex-wrap gap-3">
            <a href="<?= base_url('administrasi/examination_request') ?>" 
               class="px-4 py-2 rounded-lg transition-colors duration-200 <?= !isset($current_status) || $current_status === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                <div class="flex items-center space-x-2">
                    <i data-lucide="list" class="w-4 h-4"></i>
                    <span>Semua (<?= array_sum($counts ?? [0]) ?>)</span>
                </div>
            </a>
            
            <a href="<?= base_url('administrasi/examination_request?status=pending') ?>" 
               class="px-4 py-2 rounded-lg transition-colors duration-200 <?= isset($current_status) && $current_status === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                <div class="flex items-center space-x-2">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Pending (<?= $counts['pending'] ?? 0 ?>)</span>
                </div>
            </a>
            
            <a href="<?= base_url('administrasi/examination_request?status=progress') ?>" 
               class="px-4 py-2 rounded-lg transition-colors duration-200 <?= isset($current_status) && $current_status === 'progress' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                <div class="flex items-center space-x-2">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                    <span>Progress (<?= $counts['progress'] ?? 0 ?>)</span>
                </div>
            </a>
            
            <a href="<?= base_url('administrasi/examination_request?status=selesai') ?>" 
               class="px-4 py-2 rounded-lg transition-colors duration-200 <?= isset($current_status) && $current_status === 'selesai' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                <div class="flex items-center space-x-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Selesai (<?= $counts['selesai'] ?? 0 ?>)</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center space-x-4">
            <div class="relative flex-1">
                <input type="text" 
                       id="search-input"
                       class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari nomor pemeriksaan, nama pasien, NIK, atau jenis pemeriksaan..."
                       onkeyup="searchExaminations()">
                <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
            </div>
            <button onclick="resetSearch()" 
                    class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Reset</span>
            </button>
        </div>
        <div id="search-info" class="hidden mt-2 text-sm text-blue-600">
            <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
            <span id="search-result-text"></span>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-blue-600"></i>
                    <span>Daftar Permintaan Pemeriksaan</span>
                    <span id="request-count" class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        <?= $total_requests ?? count($requests ?? []) ?> permintaan
                    </span>
                </h2>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pemeriksaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pemeriksaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pemeriksaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="requests-table-body" class="bg-white divide-y divide-gray-200">
                    <?php if(!empty($requests)): ?>
                        <?php foreach($requests as $request): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200 examination-row">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 exam-number"><?= $request['nomor_pemeriksaan'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 patient-name"><?= htmlspecialchars($request['nama_pasien']) ?></div>
                                        <div class="text-xs text-gray-500 patient-nik"><?= $request['nik'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 exam-type"><?= htmlspecialchars($request['jenis_pemeriksaan']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= date('d M Y', strtotime($request['tanggal_pemeriksaan'])) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp <?= number_format($request['biaya'], 0, ',', '.') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $status_config = [
                                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'clock', 'label' => 'Pending'],
                                    'progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'activity', 'label' => 'Progress'],
                                    'selesai' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'check-circle', 'label' => 'Selesai'],
                                ];
                                $status = $status_config[$request['status_pemeriksaan']] ?? $status_config['pending'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $status['bg'] ?> <?= $status['text'] ?>">
                                    <i data-lucide="<?= $status['icon'] ?>" class="w-3 h-3 mr-1"></i>
                                    <?= $status['label'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewRequestDetail(<?= $request['pemeriksaan_id'] ?>)" 
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200"
                                       title="Lihat Detail">
                                        <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                        Detail
                                    </button>
                                    
                                    <?php if($request['status_pemeriksaan'] !== 'selesai'): ?>
                                    <button onclick="editExamination(<?= $request['pemeriksaan_id'] ?>)" 
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 transition-colors duration-200"
                                       title="Edit">
                                        <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                                        Edit
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if($request['status_pemeriksaan'] === 'pending'): ?>
                                    <button onclick="updateStatus(<?= $request['pemeriksaan_id'] ?>, 'progress')" 
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200"
                                       title="Mulai Proses">
                                        <i data-lucide="play-circle" class="w-3 h-3 mr-1"></i>
                                        Proses
                                    </button>
                                    <?php elseif($request['status_pemeriksaan'] === 'progress'): ?>
                                    <button onclick="updateStatus(<?= $request['pemeriksaan_id'] ?>, 'selesai')" 
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 transition-colors duration-200"
                                       title="Selesaikan">
                                        <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                        Selesai
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="empty-state-default">
                            <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                <div class="flex flex-col items-center space-y-4">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="clipboard-list" class="w-12 h-12 text-gray-300"></i>
                                    </div>
                                    <div>
                                        <span class="text-lg font-medium block mb-1">Tidak ada permintaan pemeriksaan</span>
                                        <span class="text-sm text-gray-400">
                                            <?php if(isset($current_status) && $current_status): ?>
                                                Tidak ditemukan permintaan dengan status "<?= ucfirst($current_status) ?>"
                                            <?php else: ?>
                                                Belum ada permintaan pemeriksaan yang dibuat
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <button onclick="openCreateModal()" 
                                       class="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                        <span>Buat Permintaan Pertama</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if(!empty($requests) && isset($pagination) && $pagination): ?>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan <?= count($requests) ?> dari <?= $total_requests ?? count($requests) ?> permintaan
                </div>
                <div class="pagination">
                    <?= $pagination ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Modal -->
<div id="create-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-blue-600"></i>
                    <span>Buat Permintaan Pemeriksaan Baru</span>
                </h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <form id="create-form" class="p-6">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Pasien *</label>
                    <select name="pasien_id" id="pasien_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Pilih Pasien --</option>
                        <?php if(isset($patients) && !empty($patients)): ?>
                            <?php foreach($patients as $patient): ?>
                                <option value="<?= $patient['pasien_id'] ?>"><?= htmlspecialchars($patient['nama']) ?> - <?= $patient['nik'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pemeriksaan *</label>
                    <select name="jenis_pemeriksaan" id="jenis_pemeriksaan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Pilih Jenis Pemeriksaan --</option>
                        <option value="Kimia Darah">Kimia Darah</option>
                        <option value="Hematologi">Hematologi</option>
                        <option value="Urinologi">Urinologi</option>
                        <option value="Serologi">Serologi</option>
                        <option value="TBC">TBC</option>
                        <option value="IMS">IMS</option>
                        <option value="MLS">MLS</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pemeriksaan *</label>
                    <input type="date" name="tanggal_pemeriksaan" id="tanggal_pemeriksaan" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           min="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Biaya (Rp) *</label>
                    <input type="number" name="biaya" id="biaya" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Masukkan biaya pemeriksaan" min="0" step="1000" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Tambahkan keterangan atau catatan khusus..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 mt-6">
                <button type="button" onclick="closeCreateModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="edit" class="w-5 h-5 text-orange-600"></i>
                    <span>Edit Permintaan Pemeriksaan</span>
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <form id="edit-form" class="p-6">
            <input type="hidden" id="edit-pemeriksaan-id" name="pemeriksaan_id">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Pasien *</label>
                    <select name="pasien_id" id="edit-pasien-id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Pilih Pasien --</option>
                        <?php if(isset($patients) && !empty($patients)): ?>
                            <?php foreach($patients as $patient): ?>
                                <option value="<?= $patient['pasien_id'] ?>"><?= htmlspecialchars($patient['nama']) ?> - <?= $patient['nik'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pemeriksaan *</label>
                    <select name="jenis_pemeriksaan" id="edit-jenis-pemeriksaan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Pilih Jenis Pemeriksaan --</option>
                        <option value="Kimia Darah">Kimia Darah</option>
                        <option value="Hematologi">Hematologi</option>
                        <option value="Urinologi">Urinologi</option>
                        <option value="Serologi">Serologi</option>
                        <option value="TBC">TBC</option>
                        <option value="IMS">IMS</option>
                        <option value="MLS">MLS</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pemeriksaan *</label>
                    <input type="date" name="tanggal_pemeriksaan" id="edit-tanggal-pemeriksaan" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           min="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Biaya (Rp) *</label>
                    <input type="number" name="biaya" id="edit-biaya" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Masukkan biaya pemeriksaan" min="0" step="1000" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" id="edit-keterangan" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Tambahkan keterangan atau catatan khusus..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 mt-6">
                <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Update</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                    <span>Detail Permintaan Pemeriksaan</span>
                </h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div id="detail-content" class="p-6"></div>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url() ?>';

// Search variables
let searchTimeout;
let currentSearch = '';
let totalRequests = <?= $total_requests ?? count($requests ?? []) ?>;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Auto-hide flash messages
    setTimeout(() => {
        const flashMessages = document.querySelectorAll('#flash-messages > div');
        flashMessages.forEach(msg => {
            msg.style.transition = 'opacity 0.5s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        });
    }, 5000);
    
    // Check URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam) {
        document.getElementById('search-input').value = searchParam;
        currentSearch = searchParam;
        filterTable();
    }
});

// Search with debouncing
function searchExaminations() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const searchValue = document.getElementById('search-input').value.trim();
        
        if (searchValue.length > 0 && searchValue.length < 2) {
            return;
        }
        
        currentSearch = searchValue;
        filterTable();
    }, 500);
}

// Filter table
function filterTable() {
    const searchTerm = currentSearch.toLowerCase();
    const tbody = document.getElementById('requests-table-body');
    const rows = tbody.querySelectorAll('.examination-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const examNumber = row.querySelector('.exam-number').textContent.toLowerCase();
        const patientName = row.querySelector('.patient-name').textContent.toLowerCase();
        const patientNik = row.querySelector('.patient-nik').textContent.toLowerCase();
        const examType = row.querySelector('.exam-type').textContent.toLowerCase();
        
        const match = examNumber.includes(searchTerm) || 
                     patientName.includes(searchTerm) || 
                     patientNik.includes(searchTerm) ||
                     examType.includes(searchTerm);
        
        if (match) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    updateResultCount(visibleCount);
    showSearchInfo(visibleCount);
    showEmptyState(visibleCount === 0 && rows.length > 0);
}

// Update count
function updateResultCount(count) {
    const countElement = document.getElementById('request-count');
    if (countElement) {
        countElement.textContent = `${count} permintaan`;
    }
}

// Show search info
function showSearchInfo(count) {
    const searchInfo = document.getElementById('search-info');
    const searchText = document.getElementById('search-result-text');
    
    if (currentSearch) {
        searchInfo.classList.remove('hidden');
        searchText.textContent = `Ditemukan ${count} hasil untuk "${currentSearch}"`;
    } else {
        searchInfo.classList.add('hidden');
    }
}

// Show empty state
function showEmptyState(show) {
    const tbody = document.getElementById('requests-table-body');
    let emptyRow = tbody.querySelector('.search-empty-state');
    const defaultEmpty = tbody.querySelector('#empty-state-default');
    
    if (defaultEmpty) {
        defaultEmpty.style.display = show ? 'none' : '';
    }
    
    if (show && !emptyRow) {
        emptyRow = document.createElement('tr');
        emptyRow.className = 'search-empty-state';
        emptyRow.innerHTML = `
            <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                        <i data-lucide="search-x" class="w-12 h-12 text-gray-300"></i>
                    </div>
                    <div>
                        <span class="text-lg font-medium block mb-1">Tidak ada hasil ditemukan</span>
                        <span class="text-sm text-gray-400">Coba kata kunci lain atau reset pencarian</span>
                    </div>
                    <button onclick="resetSearch()" 
                       class="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        <span>Reset Pencarian</span>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(emptyRow);
        lucide.createIcons();
    } else if (!show && emptyRow) {
        emptyRow.remove();
    }
}

// Reset search
function resetSearch() {
    document.getElementById('search-input').value = '';
    currentSearch = '';
    filterTable();
}

// Modal functions
function openCreateModal() {
    document.getElementById('create-modal').classList.remove('hidden');
    document.getElementById('create-form').reset();
    lucide.createIcons();
}

function closeCreateModal() {
    document.getElementById('create-modal').classList.add('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

// Create form submit
document.getElementById('create-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(BASE_URL + 'administrasi/examination_request', {
            method: 'POST',
            body: formData
        });
        
        if(response.ok) {
            showFlashMessage('success', 'Permintaan pemeriksaan berhasil dibuat');
            closeCreateModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showFlashMessage('error', 'Gagal membuat permintaan pemeriksaan');
        }
    } catch(error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat membuat permintaan');
    }
});

// Edit examination
async function editExamination(examId) {
    document.getElementById('edit-modal').classList.remove('hidden');
    document.getElementById('edit-pemeriksaan-id').value = examId;
    
    try {
        const response = await fetch(BASE_URL + `administrasi/get_examination_data/${examId}`);
        const data = await response.json();
        
        if(data.success) {
            document.getElementById('edit-pasien-id').value = data.examination.pasien_id;
            document.getElementById('edit-jenis-pemeriksaan').value = data.examination.jenis_pemeriksaan;
            document.getElementById('edit-tanggal-pemeriksaan').value = data.examination.tanggal_pemeriksaan;
            document.getElementById('edit-biaya').value = data.examination.biaya;
            document.getElementById('edit-keterangan').value = data.examination.keterangan || '';
        } else {
            showFlashMessage('error', 'Gagal memuat data pemeriksaan');
            closeEditModal();
        }
    } catch(error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memuat data');
        closeEditModal();
    }
    
    lucide.createIcons();
}

// Edit form submit
document.getElementById('edit-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const examId = document.getElementById('edit-pemeriksaan-id').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch(BASE_URL + `administrasi/edit_examination/${examId}`, {
            method: 'POST',
            body: formData
        });
        
        if(response.ok) {
            showFlashMessage('success', 'Permintaan pemeriksaan berhasil diperbarui');
            closeEditModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showFlashMessage('error', 'Gagal memperbarui permintaan pemeriksaan');
        }
    } catch(error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memperbarui data');
    }
});

// View detail
async function viewRequestDetail(examId) {
    document.getElementById('detail-content').innerHTML = `
        <div class="flex justify-center py-8">
            <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 loading"></i>
        </div>
    `;
    
    document.getElementById('detail-modal').classList.remove('hidden');
    lucide.createIcons();
    
    try {
        const response = await fetch(BASE_URL + `administrasi/get_examination_data/${examId}`);
        const data = await response.json();
        
        if(data.success) {
            const exam = data.examination;
            const statusConfig = {
                'pending': { bg: 'bg-yellow-100', text: 'text-yellow-800', label: 'Pending' },
                'progress': { bg: 'bg-blue-100', text: 'text-blue-800', label: 'Progress' },
                'selesai': { bg: 'bg-green-100', text: 'text-green-800', label: 'Selesai' }
            };
            const status = statusConfig[exam.status_pemeriksaan] || statusConfig['pending'];
            
            document.getElementById('detail-content').innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                Informasi Pemeriksaan
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">No. Pemeriksaan:</span>
                                    <span class="font-medium text-gray-900">${exam.nomor_pemeriksaan}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jenis Pemeriksaan:</span>
                                    <span class="font-medium text-gray-900">${exam.jenis_pemeriksaan}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tanggal:</span>
                                    <span class="font-medium text-gray-900">${new Date(exam.tanggal_pemeriksaan).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${status.bg} ${status.text}">
                                        ${status.label}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-green-900 mb-3 flex items-center">
                                <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                Informasi Pasien
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nama:</span>
                                    <span class="font-medium text-gray-900">${exam.nama_pasien}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">NIK:</span>
                                    <span class="font-medium text-gray-900">${exam.nik}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">No. Registrasi:</span>
                                    <span class="font-medium text-gray-900">${exam.nomor_registrasi}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="text-sm font-semibold text-purple-900 mb-3 flex items-center">
                            <i data-lucide="dollar-sign" class="w-4 h-4 mr-2"></i>
                            Biaya Pemeriksaan
                        </h4>
                        <div class="text-2xl font-bold text-purple-900">
                            Rp ${parseInt(exam.biaya).toLocaleString('id-ID')}
                        </div>
                    </div>
                    
                    ${exam.keterangan ? `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                            <i data-lucide="message-square" class="w-4 h-4 mr-2"></i>
                            Keterangan
                        </h4>
                        <p class="text-sm text-gray-700">${exam.keterangan}</p>
                    </div>
                    ` : ''}
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                            <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                            Riwayat
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Dibuat:</span>
                                <span class="text-gray-900">${new Date(exam.created_at).toLocaleString('id-ID')}</span>
                            </div>
                            ${exam.updated_at ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Terakhir Diupdate:</span>
                                <span class="text-gray-900">${new Date(exam.updated_at).toLocaleString('id-ID')}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('detail-content').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-3"></i>
                    <p>Gagal memuat data pemeriksaan</p>
                </div>
            `;
        }
    } catch(error) {
        console.error('Error:', error);
        document.getElementById('detail-content').innerHTML = `
            <div class="text-center py-8 text-red-600">
                <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-3"></i>
                <p>Terjadi kesalahan saat memuat data</p>
            </div>
        `;
    }
    
    lucide.createIcons();
}

// Update status
async function updateStatus(examId, status) {
    if (!confirm('Apakah Anda yakin ingin mengubah status pemeriksaan ini?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('status', status);
        
        const response = await fetch(BASE_URL + `administrasi/update_examination_status/${examId}`, {
            method: 'POST',
            body: formData
        });
        
        if(response.ok) {
            showFlashMessage('success', 'Status pemeriksaan berhasil diperbarui');
            setTimeout(() => location.reload(), 1000);
        } else {
            showFlashMessage('error', 'Gagal memperbarui status pemeriksaan');
        }
    } catch(error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memperbarui status');
    }
}

// Flash message
function showFlashMessage(type, message) {
    const container = document.getElementById('flash-messages');
    const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const iconName = type === 'success' ? 'check-circle' : 'alert-circle';
    const iconClass = type === 'success' ? 'text-green-600' : 'text-red-600';
    
    const alert = document.createElement('div');
    alert.className = `${alertClass} border rounded-lg p-4 flex items-center space-x-3 fade-in`;
    alert.innerHTML = `
        <i data-lucide="${iconName}" class="w-5 h-5 ${iconClass}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    `;
    
    container.appendChild(alert);
    lucide.createIcons();
    
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 5000);
}

// ESC key & backdrop close
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
        closeDetailModal();
    }
});

['create-modal', 'edit-modal', 'detail-modal'].forEach(modalId => {
    document.getElementById(modalId).addEventListener('click', function(e) {
        if (e.target === this) {
            eval(`close${modalId.split('-')[0].charAt(0).toUpperCase() + modalId.split('-')[0].slice(1)}Modal()`);
        }
    });
});
</script>

</body>
</html>