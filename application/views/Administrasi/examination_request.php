<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Pemeriksaan - LabSy</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .loading { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        select[size] { height: auto; overflow-y: auto; }
        select[size] option { padding: 8px 12px; cursor: pointer; }
        select[size] option:hover { background-color: #e0f2fe; }
        select[size] option:checked { background-color: #3b82f6; color: white; }
        .pemeriksaan-row { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; margin-bottom: 0.75rem; }
        .remove-btn { opacity: 0; transition: opacity 0.2s; }
        .pemeriksaan-row:hover .remove-btn { opacity: 1; }
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
            <button onclick="openCreateModal()" 
               class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Buat Permintaan Pemeriksaan</span>
            </button>
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
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="empty-state-default">
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
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

<!-- Create Modal - ENHANCED -->
<div id="create-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
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
                <!-- Pilih Pasien -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Pasien *</label>
                    <div class="relative mb-2">
                        <input type="text" id="patient-search" 
                               class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="Cari nama pasien atau NIK..." autocomplete="off">
                        <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                    </div>
                    <select name="pasien_id" id="pasien_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                            size="6" required>
                        <option value="">-- Pilih Pasien --</option>
                        <?php if(isset($patients) && !empty($patients)): ?>
                            <?php foreach($patients as $patient): ?>
                                <option value="<?= $patient['pasien_id'] ?>" 
                                        data-name="<?= strtolower(htmlspecialchars($patient['nama'])) ?>" 
                                        data-nik="<?= $patient['nik'] ?>">
                                    <?= htmlspecialchars($patient['nama']) ?> - <?= $patient['nik'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- Tanggal Pemeriksaan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pemeriksaan *</label>
                    <input type="date" name="tanggal_pemeriksaan" id="tanggal_pemeriksaan" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           min="<?= date('Y-m-d') ?>" required>
                </div>
                
                <!-- STATUS PASIEN - NEW -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Pasien Saat Pemeriksaan *</label>
                    <select name="status_pasien" id="status_pasien" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                            required onchange="toggleObatField()">
                        <option value="">-- Pilih Status Pasien --</option>
                        <option value="puasa">Puasa</option>
                        <option value="belum_puasa">Belum Puasa</option>
                        <option value="minum_obat">Minum Obat Tertentu</option>
                    </select>
                </div>
                
                <!-- Keterangan Obat (muncul jika pilih minum obat) -->
                <div id="obat-container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Obat yang Diminum *</label>
                    <textarea name="keterangan_obat" id="keterangan_obat" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                              placeholder="Contoh: Metformin 500mg, Amlodipine 5mg"></textarea>
                </div>
                
                <!-- JENIS SAMPEL - NEW -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Sampel yang Diambil *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-blue-50 cursor-pointer">
                            <input type="checkbox" name="sampel[]" value="whole_blood" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm">Whole Blood (Darah Lengkap)</span>
                        </label>
                        <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-blue-50 cursor-pointer">
                            <input type="checkbox" name="sampel[]" value="serum" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm">Serum</span>
                        </label>
                        <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-blue-50 cursor-pointer">
                            <input type="checkbox" name="sampel[]" value="plasma" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm">Plasma</span>
                        </label>
                        <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-blue-50 cursor-pointer">
                            <input type="checkbox" name="sampel[]" value="feses" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm">Feses</span>
                        </label>
                        <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-blue-50 cursor-pointer">
                            <input type="checkbox" name="sampel[]" value="urin" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm">Urin</span>
                        </label>
                        <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-blue-50 cursor-pointer">
                            <input type="checkbox" name="sampel[]" value="sputum" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm">Sputum (Dahak)</span>
                        </label>
                        <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-blue-50 cursor-pointer">
                            <input type="checkbox" name="sampel[]" value="lain" class="w-4 h-4 text-blue-600 rounded" onchange="toggleSampelLain()">
                            <span class="text-sm">Sampel Lain</span>
                        </label>
                    </div>
                    
                    <!-- Keterangan Sampel Lain -->
                    <div id="sampel-lain-container" class="hidden mt-2">
                        <input type="text" name="keterangan_sampel_lain" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="Sebutkan jenis sampel lain...">
                    </div>
                </div>
                
                <!-- MULTIPLE JENIS PEMERIKSAAN - NEW -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pemeriksaan *</label>
                    <div id="pemeriksaan-container">
                        <!-- Pemeriksaan row pertama -->
                        <div class="pemeriksaan-row" data-row="1">
                            <div class="flex items-start space-x-3">
                                <div class="flex-1">
                                    <select name="jenis_pemeriksaan[]" class="jenis-pemeriksaan-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                            required onchange="loadSubPemeriksaan(this, 1)">
                                        <option value="">-- Pilih Jenis Pemeriksaan --</option>
                                        <option value="Kimia Darah">Kimia Darah</option>
                                        <option value="Hematologi">Hematologi</option>
                                        <option value="Urinologi">Urinologi</option>
                                        <option value="Serologi">Serologi</option>
                                        <option value="TBC">TBC</option>
                                        <option value="IMS">IMS</option>
                                    </select>
                                    
                                    <!-- Sub pemeriksaan container -->
                                    <div id="sub-pemeriksaan-1" class="mt-3 hidden">
                                        <label class="block text-sm font-medium text-gray-600 mb-2">Sub Pemeriksaan (Opsional)</label>
                                        <div id="sub-pemeriksaan-checkboxes-1" class="space-y-2 max-h-48 overflow-y-auto border rounded-lg p-3"></div>
                                    </div>
                                </div>
                                
                                <button type="button" class="remove-btn mt-2 p-2 text-red-600 hover:bg-red-50 rounded-lg" 
                                        onclick="removePemeriksaan(1)" style="opacity: 0;">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Tambah Pemeriksaan -->
                    <button type="button" onclick="addPemeriksaan()" 
                            class="mt-3 w-full px-4 py-2 border-2 border-dashed border-blue-300 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors flex items-center justify-center space-x-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Pemeriksaan Lain</span>
                    </button>
                </div>
                
                <!-- Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan Tambahan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                              placeholder="Tambahkan keterangan atau catatan khusus..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 mt-6">
                <button type="button" onclick="closeCreateModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Permintaan</span>
                </button>
            </div>
        </form>
    </div>
</div>

</script>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
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
        
        <div class="p-6" id="detail-content">
            <div class="flex justify-center py-8">
                <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 loading"></i>
            </div>
        </div>
        
        <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end">
            <button onclick="closeDetailModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url() ?>';
let pemeriksaanCounter = 1;
let isEditMode = false;
let currentExamId = null;

// Sub Pemeriksaan Map
const subPemeriksaanMap = {
    'Kimia Darah': [
        {id: 'gula_darah_sewaktu', label: 'Gula Darah Sewaktu (GDS)'},
        {id: 'gula_darah_puasa', label: 'Gula Darah Puasa (GDP)'},
        {id: 'gula_darah_2jam_pp', label: 'Gula Darah 2 Jam PP (GD2PP)'},
        {id: 'cholesterol_total', label: 'Cholesterol Total'},
        {id: 'cholesterol_hdl', label: 'Cholesterol HDL'},
        {id: 'cholesterol_ldl', label: 'Cholesterol LDL'},
        {id: 'trigliserida', label: 'Trigliserida'},
        {id: 'asam_urat', label: 'Asam Urat'},
        {id: 'ureum', label: 'Ureum'},
        {id: 'creatinin', label: 'Creatinin'},
        {id: 'sgpt', label: 'SGPT'},
        {id: 'sgot', label: 'SGOT'}
    ],
    'Hematologi': [
        {id: 'paket_darah_rutin', label: 'Paket Darah Rutin'},
        {id: 'laju_endap_darah', label: 'Laju Endap Darah'},
        {id: 'clotting_time', label: 'Clotting Time'},
        {id: 'bleeding_time', label: 'Bleeding Time'},
        {id: 'golongan_darah', label: 'Golongan Darah + Rhesus'},
        {id: 'malaria', label: 'Malaria'}
    ],
    'Urinologi': [
        {id: 'urin_rutin', label: 'Urin Rutin'},
        {id: 'protein', label: 'Protein'},
        {id: 'tes_kehamilan', label: 'Tes Kehamilan'}
    ],
    'Serologi': [
        {id: 'rdt_antigen', label: 'RDT Antigen'},
        {id: 'widal', label: 'Widal'},
        {id: 'hbsag', label: 'HBsAg'},
        {id: 'ns1', label: 'NS1'},
        {id: 'hiv', label: 'HIV'}
    ],
    'IMS': [
        {id: 'sifilis', label: 'Sifilis'},
        {id: 'duh_tubuh', label: 'Duh Tubuh'}
    ],
    'TBC': [
        {id: 'dahak', label: 'Dahak'},
        {id: 'tcm', label: 'TCM'}
    ]
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});

// View Detail Function
function viewRequestDetail(examId) {
    const modal = document.getElementById('detail-modal');
    const content = document.getElementById('detail-content');
    
    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex flex-col items-center justify-center py-8">
            <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 loading mb-2"></i>
            <span class="text-gray-500">Memuat data...</span>
        </div>
    `;
    lucide.createIcons();
    
    fetch(BASE_URL + 'administrasi/get_examination_data/' + examId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const exam = data.examination;
                
                // Build content
                let html = `
                    <div class="space-y-6">
                        <!-- Header Info -->
                        <div class="bg-blue-50 p-4 rounded-lg flex justify-between items-start">
                            <div>
                                <p class="text-sm text-blue-600 font-medium mb-1">Nomor Pemeriksaan</p>
                                <p class="text-xl font-bold text-blue-900 font-mono">${exam.nomor_pemeriksaan}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    ${exam.status_pemeriksaan === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                      (exam.status_pemeriksaan === 'progress' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')}">
                                    ${exam.status_pemeriksaan.toUpperCase()}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Pasien Info -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Nama Pasien</p>
                                <p class="font-medium text-gray-900">${exam.nama_pasien}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">NIK</p>
                                <p class="font-medium text-gray-900">${exam.nik}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Tanggal Pemeriksaan</p>
                                <p class="font-medium text-gray-900">${new Date(exam.tanggal_pemeriksaan).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Status Pasien</p>
                                <p class="font-medium text-gray-900">
                                    ${exam.status_pasien === 'puasa' ? 'Puasa' : 
                                      (exam.status_pasien === 'minum_obat' ? 'Minum Obat' : 'Belum Puasa')}
                                </p>
                                ${exam.keterangan_obat ? `<p class="text-xs text-red-600 mt-1">Obat: ${exam.keterangan_obat}</p>` : ''}
                            </div>
                        </div>
                        
                        <!-- Detail Pemeriksaan -->
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2 border-b pb-1">Detail Pemeriksaan</h4>
                            <div class="space-y-3">
                `;
                
                if (exam.detail && exam.detail.length > 0) {
                    exam.detail.forEach(det => {
                        html += `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="font-medium text-blue-700">${det.jenis_pemeriksaan}</p>
                        `;
                        
                        if (det.sub_pemeriksaan) {
                            try {
                                const subs = JSON.parse(det.sub_pemeriksaan);
                                if (subs.length > 0) {
                                    html += `<ul class="mt-1 ml-4 list-disc text-sm text-gray-600">`;
                                    
                                    // Helper function to get label
                                    const getLabel = (jenis, id) => {
                                        const found = (subPemeriksaanMap[jenis] || []).find(x => x.id === id);
                                        return found ? found.label : id;
                                    };
                                    
                                    subs.forEach(s => {
                                        html += `<li>${getLabel(det.jenis_pemeriksaan, s)}</li>`;
                                    });
                                    html += `</ul>`;
                                }
                            } catch(e) {}
                        }
                        html += `</div>`;
                    });
                }
                
                html += `
                            </div>
                        </div>
                        
                        <!-- Sampel Info -->
                         <div>
                            <h4 class="font-medium text-gray-900 mb-2 border-b pb-1">Sampel</h4>
                            <div class="flex flex-wrap gap-2">
                `;
                
                if (exam.sampel && exam.sampel.length > 0) {
                    exam.sampel.forEach(samp => {
                        html += `
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-50 text-purple-700 border border-purple-200">
                                ${samp.jenis_sampel === 'lain' ? (samp.keterangan_sampel || 'Lainnya') : samp.jenis_sampel.replace('_', ' ').toUpperCase()}
                            </span>
                        `;
                    });
                }
                
                html += `
                            </div>
                        </div>
                        
                        <!-- Keterangan -->
                        ${exam.keterangan ? `
                        <div>
                            <h4 class="font-medium text-gray-900 mb-1">Keterangan Tambahan</h4>
                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                ${exam.keterangan}
                            </p>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                content.innerHTML = html;
            } else {
                content.innerHTML = `
                    <div class="text-center text-red-500 py-8">
                        <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2"></i>
                        <p>${data.message || 'Gagal memuat data'}</p>
                    </div>
                `;
                lucide.createIcons();
            }
        })
        .catch(error => {
            console.error(error);
            content.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <p>Terjadi kesalahan jaringan</p>
                </div>
            `;
        });
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

// Edit Examination Function
function editExamination(examId) {
    // Show loading or similar if needed, but we can just open modal and load data
    const modal = document.getElementById('create-modal');
    const form = document.getElementById('create-form');
    
    // Reset form first
    form.reset();
    document.getElementById('pemeriksaan-container').innerHTML = ''; // Clear rows
    pemeriksaanCounter = 0; // Will increment
    
    // Set Edit Mode
    isEditMode = true;
    currentExamId = examId;
    modal.classList.remove('hidden');
    document.querySelector('#create-modal h3 span').textContent = 'Edit Permintaan Pemeriksaan';
    
    // Show loader in form? Or just fill it.
    // Fetch Data
    fetch(BASE_URL + 'administrasi/get_examination_data/' + examId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const exam = data.examination;
                
                // Populate Basic Fields
                // Handle Select2/Choosen if exists? No, standard select here.
                // We need to match the patient option text or value
                const patientSelect = document.getElementById('pasien_id');
                patientSelect.value = exam.pasien_id;
                
                document.getElementById('tanggal_pemeriksaan').value = exam.tanggal_pemeriksaan;
                document.getElementById('status_pasien').value = exam.status_pasien;
                toggleObatField();
                
                if (exam.status_pasien === 'minum_obat') {
                    document.getElementById('keterangan_obat').value = exam.keterangan_obat;
                }
                
                document.getElementById('keterangan').value = exam.keterangan;
                
                // Populate Samples
                if (exam.sampel) {
                    exam.sampel.forEach(s => {
                        const cb = document.querySelector(`input[name="sampel[]"][value="${s.jenis_sampel}"]`);
                        if (cb) {
                            cb.checked = true;
                            if (s.jenis_sampel === 'lain') {
                                toggleSampelLain();
                                document.querySelector('input[name="keterangan_sampel_lain"]').value = s.keterangan_sampel;
                            }
                        }
                    });
                }
                
                // Populate Examination Details (Rows)
                if (exam.detail && exam.detail.length > 0) {
                    exam.detail.forEach((det, index) => {
                        addPemeriksaan(); // Adds a row, increments counter
                        // Current counter is pemeriksaanCounter
                        const rowNum = pemeriksaanCounter;
                        
                        // Set Type
                        const select = document.querySelector(`.pemeriksaan-row[data-row="${rowNum}"] .jenis-pemeriksaan-select`);
                        if (select) {
                            select.value = det.jenis_pemeriksaan;
                            // Trigger change to load sub options
                            loadSubPemeriksaan(select, rowNum);
                            
                            // Check Sub-exams
                            if (det.sub_pemeriksaan) {
                                try {
                                    const subs = JSON.parse(det.sub_pemeriksaan);
                                    if (Array.isArray(subs)) {
                                        subs.forEach(subId => {
                                            // Need to wait for loadSubPemeriksaan? starts synchronous so ok.
                                            const subCb = document.querySelector(`input[name="sub_pemeriksaan_${rowNum}[]"][value="${subId}"]`);
                                            if (subCb) subCb.checked = true;
                                        });
                                    }
                                } catch(e) {}
                            }
                        }
                    });
                } else {
                    // Always at least one row if empty (shouldnt happen)
                    addPemeriksaan();
                }
                
            } else {
                alert('Gagal memuat data: ' + data.message);
                closeCreateModal();
            }
        })
        .catch(e => {
            console.error(e);
            alert('Gagal memuat data pemeriksaan');
            closeCreateModal();
        });
}

// Toggle field obat
function toggleObatField() {
    const status = document.getElementById('status_pasien').value;
    const container = document.getElementById('obat-container');
    const input = document.getElementById('keterangan_obat');
    
    if (status === 'minum_obat') {
        container.classList.remove('hidden');
        input.required = true;
    } else {
        container.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}

// Toggle sampel lain
function toggleSampelLain() {
    const checkbox = document.querySelector('input[value="lain"]');
    const container = document.getElementById('sampel-lain-container');
    
    if (checkbox.checked) {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

// Patient Search Filter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('patient-search');
    const patientSelect = document.getElementById('pasien_id');
    
    if (searchInput && patientSelect) {
        // Function to update option display
        function updateOptionLabels() {
            const selectedValue = patientSelect.value;
            const options = patientSelect.options;
            
            for (let i = 1; i < options.length; i++) {
                const option = options[i];
                const originalText = option.getAttribute('data-original-text');
                
                // Store original text first time
                if (!originalText) {
                    option.setAttribute('data-original-text', option.textContent);
                }
                
                // Reset to original
                const baseText = option.getAttribute('data-original-text');
                
                // Add indicator if selected
                if (option.value === selectedValue && selectedValue !== '') {
                    option.textContent = '✓ ' + baseText + ' [TERPILIH]';
                    option.style.backgroundColor = '#dbeafe'; // Light blue
                    option.style.fontWeight = 'bold';
                } else {
                    option.textContent = baseText;
                    option.style.backgroundColor = '';
                    option.style.fontWeight = '';
                }
            }
        }
        
        // Update on change
        patientSelect.addEventListener('change', updateOptionLabels);
        
        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = patientSelect.options;
            
            for (let i = 1; i < options.length; i++) { // Skip first "-- Pilih Pasien --"
                const option = options[i];
                const name = option.getAttribute('data-name') || '';
                const nik = option.getAttribute('data-nik') || '';
                const originalText = option.getAttribute('data-original-text') || option.textContent;
                const text = originalText.toLowerCase();
                
                const matches = name.includes(searchTerm) || 
                               nik.includes(searchTerm) || 
                               text.includes(searchTerm);
                
                option.style.display = matches ? '' : 'none';
            }
            
            // Auto select if only one match
            const visibleOptions = Array.from(options).filter(opt => 
                opt.value !== '' && opt.style.display !== 'none'
            );
            
            if (visibleOptions.length === 1) {
                patientSelect.value = visibleOptions[0].value;
                updateOptionLabels();
            }
        });
        
        // Initial update
        updateOptionLabels();
    }
});


// Load sub pemeriksaan
function loadSubPemeriksaan(select, rowNum) {
    const jenis = select.value;
    const container = document.getElementById(`sub-pemeriksaan-${rowNum}`);
    const checkboxContainer = document.getElementById(`sub-pemeriksaan-checkboxes-${rowNum}`);
    
    if (jenis && subPemeriksaanMap[jenis]) {
        container.classList.remove('hidden');
        checkboxContainer.innerHTML = '';
        
        subPemeriksaanMap[jenis].forEach(sub => {
            const div = document.createElement('div');
            div.className = 'flex items-center';
            div.innerHTML = `
                <input type="checkbox" 
                       name="sub_pemeriksaan_${rowNum}[]" 
                       value="${sub.id}"
                       class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <label class="ml-2 text-sm text-gray-700">${sub.label}</label>
            `;
            checkboxContainer.appendChild(div);
        });
    } else {
        container.classList.add('hidden');
    }
}

// Add pemeriksaan
function addPemeriksaan() {
    pemeriksaanCounter++;
    const container = document.getElementById('pemeriksaan-container');
    
    const newRow = document.createElement('div');
    newRow.className = 'pemeriksaan-row';
    newRow.setAttribute('data-row', pemeriksaanCounter);
    newRow.innerHTML = `
        <div class="flex items-start space-x-3">
            <div class="flex-1">
                <select name="jenis_pemeriksaan[]" class="jenis-pemeriksaan-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                        required onchange="loadSubPemeriksaan(this, ${pemeriksaanCounter})">
                    <option value="">-- Pilih Jenis Pemeriksaan --</option>
                    <option value="Kimia Darah">Kimia Darah</option>
                    <option value="Hematologi">Hematologi</option>
                    <option value="Urinologi">Urinologi</option>
                    <option value="Serologi">Serologi</option>
                    <option value="TBC">TBC</option>
                    <option value="IMS">IMS</option>
                </select>
                
                <div id="sub-pemeriksaan-${pemeriksaanCounter}" class="mt-3 hidden">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Sub Pemeriksaan (Opsional)</label>
                    <div id="sub-pemeriksaan-checkboxes-${pemeriksaanCounter}" class="space-y-2 max-h-48 overflow-y-auto border rounded-lg p-3"></div>
                </div>
            </div>
            
            <button type="button" class="remove-btn mt-2 p-2 text-red-600 hover:bg-red-50 rounded-lg" 
                    onclick="removePemeriksaan(${pemeriksaanCounter})">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newRow);
    lucide.createIcons();
}

// Remove pemeriksaan
function removePemeriksaan(rowNum) {
    const rows = document.querySelectorAll('.pemeriksaan-row');
    if (rows.length > 1) {
        const row = document.querySelector(`[data-row="${rowNum}"]`);
        if (row) {
            row.remove();
        }
    } else {
        alert('Minimal harus ada 1 jenis pemeriksaan');
    }
}

// Submit form
document.getElementById('create-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validasi minimal 1 sampel
    const sampelChecked = document.querySelectorAll('input[name="sampel[]"]:checked');
    if (sampelChecked.length === 0) {
        alert('Pilih minimal 1 jenis sampel');
        return;
    }
    
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    try {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 loading"></i> Menyimpan...';
        lucide.createIcons();
        
        const formData = new FormData(this);
        const url = isEditMode 
            ? BASE_URL + 'administrasi/edit_examination/' + currentExamId
            : BASE_URL + 'administrasi/examination_request';
            
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if(result.success) {
            showFlashMessage('success', result.message);
            closeCreateModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showFlashMessage('error', result.message || 'Gagal menyimpan data');
        }
    } catch(error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan jaringan');
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
        lucide.createIcons();
    }
});

function openCreateModal() {
    isEditMode = false;
    currentExamId = null;
    document.getElementById('create-modal').classList.remove('hidden');
    document.getElementById('create-form').reset();
    document.querySelector('#create-modal h3 span').textContent = 'Buat Permintaan Pemeriksaan Baru';
    
    // Reset pemeriksaan rows completely
    const container = document.getElementById('pemeriksaan-container');
    container.innerHTML = '';
    pemeriksaanCounter = 0;
    
    // Add first default row
    addPemeriksaan();
    
    // Reset hidden fields
    toggleObatField();
    toggleSampelLain();
    
    lucide.createIcons();
}

function closeCreateModal() {
    document.getElementById('create-modal').classList.add('hidden');
}

function showFlashMessage(type, message) {
    const container = document.getElementById('flash-messages');
    const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const iconName = type === 'success' ? 'check-circle' : 'alert-circle';
    
    const alert = document.createElement('div');
    alert.className = `${alertClass} border rounded-lg p-4 flex items-center space-x-3 fade-in`;
    alert.innerHTML = `
        <i data-lucide="${iconName}" class="w-5 h-5"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    `;
    
    container.appendChild(alert);
    lucide.createIcons();
    
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
}
</script>

</body>
</html>