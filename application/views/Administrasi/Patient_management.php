<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pasien - LabSy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
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

        .loading {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
                    <i data-lucide="users" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Kelola Pasien</h1>
                    <p class="text-blue-100">Manajemen data pasien dan riwayat medis</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="openCreateModal()" 
                   class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Tambah Pasien</span>
                </button>
                <button onclick="exportPatients()" 
                   class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    <span>Export Data</span>
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
                    <p class="text-sm font-medium text-gray-600">Total Pasien</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total'] ?? 0 ?></p>
                    <p class="text-xs text-gray-500 mt-1">Terdaftar</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['today'] ?? 0 ?></p>
                    <p class="text-xs text-gray-500 mt-1">Registrasi baru</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Laki-laki</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $stats['male'] ?? 0 ?></p>
                    <p class="text-xs text-gray-500 mt-1">Total pasien</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Perempuan</p>
                    <p class="text-2xl font-bold text-pink-600"><?= $stats['female'] ?? 0 ?></p>
                    <p class="text-xs text-gray-500 mt-1">Total pasien</p>
                </div>
                <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-6 h-6 text-pink-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center space-x-4">
            <div class="relative flex-1">
                <input type="text" 
                       id="search-input"
                       class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari nama, NIK, nomor registrasi, atau telepon..."
                       onkeyup="searchPatients()">
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

    <!-- Patients Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                    <span>Daftar Pasien</span>
                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        <?= $total_patients ?? count($patients ?? []) ?> pasien
                    </span>
                </h2>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Registrasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Umur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(!empty($patients)): ?>
                        <?php foreach($patients as $patient): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200 patient-row">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 patient-reg"><?= $patient['nomor_registrasi'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 patient-name"><?= htmlspecialchars($patient['nama']) ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 patient-nik"><?= $patient['nik'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $patient['jenis_kelamin'] == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' ?>">
                                    <?= $patient['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $patient['umur'] ?> tahun</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 patient-phone"><?= $patient['telepon'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= date('d M Y', strtotime($patient['created_at'])) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewPatientDetail(<?= $patient['pasien_id'] ?>)" 
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200"
                                       title="Lihat Detail">
                                        <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                        Detail
                                    </button>
                                    <button onclick="editPatient(<?= $patient['pasien_id'] ?>)" 
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 transition-colors duration-200"
                                       title="Edit">
                                        <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                                <div class="flex flex-col items-center space-y-4">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="users" class="w-12 h-12 text-gray-300"></i>
                                    </div>
                                    <div>
                                        <span class="text-lg font-medium block mb-1">Tidak ada data pasien</span>
                                        <span class="text-sm text-gray-400">Belum ada pasien yang terdaftar</span>
                                    </div>
                                    <button onclick="openCreateModal()" 
                                       class="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                        <span>Tambah Pasien Pertama</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($total_patients > $limit): ?>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan 
                    <?= (($current_page - 1) * $limit) + 1 ?> - 
                    <?= min($current_page * $limit, $total_patients) ?> 
                    dari <?= number_format($total_patients) ?> pasien
                </div>
                <div class="flex items-center space-x-2">
                    <?php
                    $total_pages = ceil($total_patients / $limit);
                    $base_url = base_url('administrasi/patient_management?');
                    if($search) {
                        $base_url .= 'search=' . urlencode($search) . '&';
                    }
                    
                    if($current_page > 1): ?>
                        <a href="<?= $base_url ?>page=<?= $current_page - 1 ?>" 
                           class="px-3 py-1 text-sm rounded-md bg-white text-gray-700 hover:bg-gray-100 border border-gray-300">
                            ‹
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    for($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="<?= $base_url ?>page=<?= $i ?>" 
                           class="px-3 py-1 text-sm rounded-md <?= $i == $current_page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?> border border-gray-300">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if($current_page < $total_pages): ?>
                        <a href="<?= $base_url ?>page=<?= $current_page + 1 ?>" 
                           class="px-3 py-1 text-sm rounded-md bg-white text-gray-700 hover:bg-gray-100 border border-gray-300">
                            ›
                        </a>
                    <?php endif; ?>
                    </div>
                        <div class="pagination-links">
            <?= $pagination ?>
        </div>
    </div>
</div>
<?php endif; ?>
    </div>
</div>

<!-- Create Modal -->
<div id="create-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="user-plus" class="w-5 h-5 text-blue-600"></i>
                    <span>Tambah Pasien Baru</span>
                </h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <form id="create-form" class="p-6" onsubmit="handleCreateSubmit(event)">
            <div class="space-y-6">
                
                <!-- Data Pribadi -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                        <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                        Data Pribadi
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" name="nama" id="create-nama"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK (16 digit) *</label>
                            <div class="relative">
                                <input type="text" 
                                       id="create-nik" 
                                       name="nik" 
                                       class="w-full px-3 py-2 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       maxlength="16" 
                                       required
                                       oninput="validateNIK(this); updateNIKCounter(this, 'create-nik-counter')"
                                       onblur="checkNIKExists(this.value, 'create-nik-message', null)">
                                <span id="create-nik-counter" class="absolute right-3 top-2.5 text-xs font-medium text-gray-500">0/16</span>
                            </div>
                            <div id="create-nik-message" class="mt-1 text-xs"></div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                            <select name="jenis_kelamin" id="create-jenis-kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="create-tempat-lahir"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                            <input type="date" name="tanggal_lahir" id="create-tanggal-lahir"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                            <input type="text" name="pekerjaan" id="create-pekerjaan"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Kontak & Alamat -->
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                        <i data-lucide="phone" class="w-5 h-5 mr-2"></i>
                        Kontak & Alamat
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon/HP *</label>
                            <input type="tel" name="telepon" id="create-telepon"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Darurat</label>
                            <input type="text" name="kontak_darurat" id="create-kontak-darurat"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                            <textarea name="alamat_domisili" id="create-alamat" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Data Medis -->
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
                        <i data-lucide="heart" class="w-5 h-5 mr-2"></i>
                        Data Medis
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Riwayat Pasien</label>
                            <textarea name="riwayat_pasien" id="create-riwayat" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Permintaan Pemeriksaan</label>
                            <textarea name="permintaan_pemeriksaan" id="create-permintaan" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Data Rujukan -->
                <div class="bg-orange-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                        <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                        Data Rujukan (Opsional)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dokter Perujuk</label>
                            <input type="text" name="dokter_perujuk" id="create-dokter-perujuk"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Asal Rujukan</label>
                            <input type="text" name="asal_rujukan" id="create-asal-rujukan"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rujukan</label>
                            <input type="text" name="nomor_rujukan" id="create-nomor-rujukan"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Rujukan</label>
                            <input type="date" name="tanggal_rujukan" id="create-tanggal-rujukan"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Awal</label>
                            <textarea name="diagnosis_awal" id="create-diagnosis" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi Pemeriksaan</label>
                            <textarea name="rekomendasi_pemeriksaan" id="create-rekomendasi" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 mt-6 sticky bottom-0 bg-white">
                <button type="button" onclick="closeCreateModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" id="create-submit-btn"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan</span>
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
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                    <span>Detail Pasien</span>
                </h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div id="detail-content" class="p-6"></div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="edit" class="w-5 h-5 text-orange-600"></i>
                    <span>Edit Data Pasien</span>
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <form id="edit-form" class="p-6" onsubmit="handleEditSubmit(event)">
            <input type="hidden" id="edit-pasien-id" name="pasien_id">
            <div id="edit-content"></div>
        </form>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url() ?>';
let searchTimeout;
let currentSearch = '';
let nikCheckTimeout;
let isNikValid = true;

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    setTimeout(() => {
        const flashMessages = document.querySelectorAll('#flash-messages > div');
        flashMessages.forEach(msg => {
            msg.style.transition = 'opacity 0.5s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        });
    }, 5000);
    
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam) {
        document.getElementById('search-input').value = searchParam;
        currentSearch = searchParam;
        filterTable();
    }
});

// Validate NIK (only numbers)
function validateNIK(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
}

// Update NIK counter
function updateNIKCounter(input, counterId) {
    const counter = document.getElementById(counterId);
    if (!counter) return;
    
    const length = input.value.length;
    counter.textContent = `${length}/16`;
    
    counter.classList.remove('text-gray-500', 'text-red-600', 'text-yellow-600', 'text-green-600', 'font-bold');
    
    if (length === 0) {
        counter.classList.add('text-gray-500');
    } else if (length === 16) {
        counter.classList.add('text-green-600', 'font-bold');
    } else if (length > 10) {
        counter.classList.add('text-yellow-600');
    } else {
        counter.classList.add('text-red-600');
    }
    
    // Update border color
    input.classList.remove('border-gray-300', 'border-red-300', 'border-yellow-300', 'border-green-500');
    if (length === 16) {
        input.classList.add('border-green-500');
    } else if (length > 10) {
        input.classList.add('border-yellow-300');
    } else if (length > 0) {
        input.classList.add('border-red-300');
    } else {
        input.classList.add('border-gray-300');
    }
}

// Check if NIK already exists
async function checkNIKExists(nik, messageElementId, excludePatientId) {
    clearTimeout(nikCheckTimeout);
    
    const messageElement = document.getElementById(messageElementId);
    const modalPrefix = messageElementId.includes('create') ? 'create' : 'edit';
    const submitBtn = document.getElementById(`${modalPrefix}-submit-btn`);
    
    if (!nik || nik.length !== 16) {
        messageElement.innerHTML = '';
        isNikValid = nik.length === 0 || nik.length === 16;
        if (submitBtn) {
            submitBtn.disabled = !isNikValid;
            updateSubmitButton(submitBtn, !isNikValid);
        }
        return;
    }
    
    messageElement.innerHTML = `
        <span class="flex items-center text-blue-600 animate-pulse">
            <svg class="animate-spin h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memeriksa NIK...
        </span>
    `;
    
    nikCheckTimeout = setTimeout(async () => {
        try {
            let url = BASE_URL + `administrasi/check_nik_exists?nik=${encodeURIComponent(nik)}`;
            if (excludePatientId) {
                url += `&exclude_id=${excludePatientId}`;
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.exists) {
                isNikValid = false;
                messageElement.innerHTML = `
                    <div class="flex items-start space-x-1 text-red-600 bg-red-50 p-2 rounded border border-red-200 mt-1">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <span class="font-semibold block">NIK sudah terdaftar!</span>
                            <div class="mt-1 text-sm">
                                <span class="font-medium text-gray-900">${data.patient.nama}</span>
                                <span class="text-gray-600"> - ${data.patient.nomor_registrasi}</span>
                            </div>
                            <span class="text-xs text-gray-600">Telp: ${data.patient.telepon}</span>
                        </div>
                    </div>
                `;
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    updateSubmitButton(submitBtn, true);
                }
            } else {
                isNikValid = true;
                messageElement.innerHTML = `
                    <span class="flex items-center text-green-600 bg-green-50 px-2 py-1 rounded mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-sm">NIK tersedia</span>
                    </span>
                `;
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    updateSubmitButton(submitBtn, false);
                }
            }
            
        } catch (error) {
            console.error('Error checking NIK:', error);
            isNikValid = true;
            messageElement.innerHTML = `
                <span class="flex items-center text-yellow-600 bg-yellow-50 px-2 py-1 rounded mt-1">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs font-medium">Gagal memeriksa NIK</span>
                </span>
            `;
            
            if (submitBtn) {
                submitBtn.disabled = false;
                updateSubmitButton(submitBtn, false);
            }
        }
    }, 500);
}

function updateSubmitButton(btn, disabled) {
    if (disabled) {
        btn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
        btn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'bg-orange-600', 'hover:bg-orange-700');
    } else {
        btn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
        if (btn.id === 'create-submit-btn') {
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        } else {
            btn.classList.add('bg-orange-600', 'hover:bg-orange-700');
        }
    }
}

function searchPatients() {
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

function filterTable() {
    const searchTerm = currentSearch.toLowerCase();
    const tbody = document.querySelector('tbody');
    const rows = tbody.querySelectorAll('.patient-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const regNumber = row.querySelector('.patient-reg')?.textContent.toLowerCase() || '';
        const name = row.querySelector('.patient-name')?.textContent.toLowerCase() || '';
        const nik = row.querySelector('.patient-nik')?.textContent.toLowerCase() || '';
        const phone = row.querySelector('.patient-phone')?.textContent.toLowerCase() || '';
        
        const match = regNumber.includes(searchTerm) || 
                     name.includes(searchTerm) || 
                     nik.includes(searchTerm) ||
                     phone.includes(searchTerm);
        
        if (match) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    showSearchInfo(visibleCount);
    showEmptyState(visibleCount === 0 && rows.length > 0);
}

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

function showEmptyState(show) {
    const tbody = document.querySelector('tbody');
    let emptyRow = tbody.querySelector('.search-empty-state');
    
    if (show && !emptyRow) {
        emptyRow = document.createElement('tr');
        emptyRow.className = 'search-empty-state';
        emptyRow.innerHTML = `
            <td colspan="8" class="px-6 py-16 text-center text-gray-500">
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

function resetSearch() {
    document.getElementById('search-input').value = '';
    currentSearch = '';
    filterTable();
}

function openCreateModal() {
    document.getElementById('create-modal').classList.remove('hidden');
    document.getElementById('create-form').reset();
    document.getElementById('create-nik-message').innerHTML = '';
    document.getElementById('create-nik-counter').textContent = '0/16';
    isNikValid = true;
    lucide.createIcons();
}

function closeCreateModal() {
    document.getElementById('create-modal').classList.add('hidden');
}

async function handleCreateSubmit(e) {
    e.preventDefault();
    
    const nikInput = document.getElementById('create-nik');
    const submitBtn = document.getElementById('create-submit-btn');
    
    // Validasi NIK
    if (nikInput.value.length !== 16) {
        alert('NIK harus 16 digit! Saat ini: ' + nikInput.value.length + ' digit');
        nikInput.focus();
        return false;
    }
    
    if (!isNikValid) {
        alert('NIK sudah terdaftar! Silakan gunakan NIK yang berbeda.');
        nikInput.focus();
        return false;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Menyimpan...</span>
    `;
    
    try {
        const formData = new FormData(e.target);
        
        const response = await fetch(BASE_URL + 'administrasi/patient_management', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showFlashMessage('success', result.message || 'Data pasien berhasil ditambahkan');
            closeCreateModal();
            // Refresh page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            let errorMessage = 'Gagal menambahkan data pasien';
            if (result.errors) {
                errorMessage = result.errors;
            } else if (result.message) {
                errorMessage = result.message;
            }
            showFlashMessage('error', errorMessage);
            
            // Reset submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan</span>
            `;
            lucide.createIcons();
        }
    } catch (error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan jaringan saat menyimpan data');
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <i data-lucide="check" class="w-4 h-4"></i>
            <span>Simpan</span>
        `;
        lucide.createIcons();
    }
}
async function viewPatientDetail(patientId) {
    document.getElementById('detail-content').innerHTML = `
        <div class="flex justify-center py-8">
            <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 loading"></i>
        </div>
    `;
    
    document.getElementById('detail-modal').classList.remove('hidden');
    lucide.createIcons();
    
    try {
        // PERBAIKAN: Gunakan method GET dengan parameter yang jelas
        const response = await fetch(BASE_URL + `administrasi/get_patient_data?patient_id=${patientId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.patient) {
            const patient = data.patient;
            
            // Build detail HTML
            let detailHTML = `
                <div class="space-y-6">
                    <!-- Header Info -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xl font-bold text-blue-900">${patient.nama}</h4>
                                <p class="text-blue-700">${patient.nomor_registrasi}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${patient.jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'}">
                                    ${patient.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}
                                </span>
                                <p class="text-sm text-blue-600 mt-1">${patient.umur} tahun</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                                <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                Data Pribadi
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-gray-600">NIK:</span><span class="font-medium">${patient.nik || '-'}</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Tempat Lahir:</span><span class="font-medium">${patient.tempat_lahir || '-'}</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Tanggal Lahir:</span><span class="font-medium">${patient.tanggal_lahir_formatted || '-'}</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Pekerjaan:</span><span class="font-medium">${patient.pekerjaan || '-'}</span></div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-green-900 mb-3 flex items-center">
                                <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                Kontak
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-gray-600">Telepon:</span><span class="font-medium">${patient.telepon || '-'}</span></div>
                                <div class="flex justify-between"><span class="text-gray-600">Kontak Darurat:</span><span class="font-medium">${patient.kontak_darurat || '-'}</span></div>
                                <div class="flex flex-col"><span class="text-gray-600 mb-1">Alamat:</span><span class="font-medium">${patient.alamat_domisili || '-'}</span></div>
                            </div>
                        </div>
                    </div>`;
            
            // Data Medis jika ada
            if (patient.riwayat_pasien || patient.permintaan_pemeriksaan) {
                detailHTML += `
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="text-sm font-semibold text-purple-900 mb-3 flex items-center">
                            <i data-lucide="heart" class="w-4 h-4 mr-2"></i>
                            Informasi Medis
                        </h4>
                        ${patient.riwayat_pasien ? `<p class="text-sm mb-2"><span class="font-medium">Riwayat:</span> ${patient.riwayat_pasien}</p>` : ''}
                        ${patient.permintaan_pemeriksaan ? `<p class="text-sm"><span class="font-medium">Permintaan:</span> ${patient.permintaan_pemeriksaan}</p>` : ''}
                    </div>`;
            }
            
            // Data Rujukan jika ada
            if (patient.dokter_perujuk || patient.asal_rujukan) {
                detailHTML += `
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <h4 class="text-sm font-semibold text-orange-900 mb-3 flex items-center">
                            <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                            Informasi Rujukan
                        </h4>
                        <div class="space-y-2 text-sm">
                            ${patient.dokter_perujuk ? `<div class="flex justify-between"><span class="text-gray-600">Dokter Perujuk:</span><span class="font-medium">${patient.dokter_perujuk}</span></div>` : ''}
                            ${patient.asal_rujukan ? `<div class="flex justify-between"><span class="text-gray-600">Asal Rujukan:</span><span class="font-medium">${patient.asal_rujukan}</span></div>` : ''}
                            ${patient.nomor_rujukan ? `<div class="flex justify-between"><span class="text-gray-600">No. Rujukan:</span><span class="font-medium">${patient.nomor_rujukan}</span></div>` : ''}
                            ${patient.tanggal_rujukan_formatted ? `<div class="flex justify-between"><span class="text-gray-600">Tgl. Rujukan:</span><span class="font-medium">${patient.tanggal_rujukan_formatted}</span></div>` : ''}
                        </div>
                    </div>`;
            }
            
            detailHTML += `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                            <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                            Informasi Registrasi
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-600">No. Registrasi:</span><span class="font-medium">${patient.nomor_registrasi}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600">Tanggal Daftar:</span><span class="font-medium">${patient.created_at_formatted || '-'}</span></div>
                        </div>
                    </div>
                </div>`;
            
            document.getElementById('detail-content').innerHTML = detailHTML;
        } else {
            document.getElementById('detail-content').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-3"></i>
                    <p class="font-medium">${data.message || 'Gagal memuat data pasien'}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading patient detail:', error);
        document.getElementById('detail-content').innerHTML = `
            <div class="text-center py-8 text-red-600">
                <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-3"></i>
                <p class="font-medium">Terjadi kesalahan saat memuat data</p>
                <p class="text-sm text-gray-600 mt-2">${error.message}</p>
            </div>
        `;
    }
    
    lucide.createIcons();
}
function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

async function editPatient(patientId) {
    document.getElementById('edit-pasien-id').value = patientId;
    isNikValid = true;
    
    try {
        const response = await fetch(BASE_URL + `administrasi/get_patient_data/${patientId}`);
        const data = await response.json();
        
        if (data.success) {
            const patient = data.patient;
            document.getElementById('edit-content').innerHTML = `
                <div class="space-y-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                            <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                            Data Pribadi
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                                <input type="text" name="nama" value="${patient.nama}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK (16 digit) *</label>
                                <div class="relative">
                                    <input type="text" 
                                           id="edit-nik" 
                                           name="nik" 
                                           value="${patient.nik || ''}"
                                           class="w-full px-3 py-2 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                           maxlength="16" 
                                           required
                                           oninput="validateNIK(this); updateNIKCounter(this, 'edit-nik-counter')"
                                           onblur="checkNIKExists(this.value, 'edit-nik-message', ${patientId})">
                                    <span id="edit-nik-counter" class="absolute right-3 top-2.5 text-xs font-medium text-gray-500">${(patient.nik || '').length}/16</span>
                                </div>
                                <div id="edit-nik-message" class="mt-1 text-xs"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="L" ${patient.jenis_kelamin === 'L' ? 'selected' : ''}>Laki-laki</option>
                                    <option value="P" ${patient.jenis_kelamin === 'P' ? 'selected' : ''}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="${patient.tempat_lahir || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" value="${patient.tanggal_lahir || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" name="pekerjaan" value="${patient.pekerjaan || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                            <i data-lucide="phone" class="w-5 h-5 mr-2"></i>
                            Kontak & Alamat
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon/HP *</label>
                                <input type="tel" name="telepon" value="${patient.telepon || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Darurat</label>
                                <input type="text" name="kontak_darurat" value="${patient.kontak_darurat || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                                <textarea name="alamat_domisili" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${patient.alamat_domisili || ''}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
                            <i data-lucide="heart" class="w-5 h-5 mr-2"></i>
                            Data Medis
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Riwayat Pasien</label>
                                <textarea name="riwayat_pasien" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${patient.riwayat_pasien || ''}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Permintaan Pemeriksaan</label>
                                <textarea name="permintaan_pemeriksaan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${patient.permintaan_pemeriksaan || ''}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                            <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                            Data Rujukan (Opsional)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dokter Perujuk</label>
                                <input type="text" name="dokter_perujuk" value="${patient.dokter_perujuk || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Asal Rujukan</label>
                                <input type="text" name="asal_rujukan" value="${patient.asal_rujukan || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rujukan</label>
                                <input type="text" name="nomor_rujukan" value="${patient.nomor_rujukan || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Rujukan</label>
                                <input type="date" name="tanggal_rujukan" value="${patient.tanggal_rujukan || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Awal</label>
                                <textarea name="diagnosis_awal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${patient.diagnosis_awal || ''}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi Pemeriksaan</label>
                                <textarea name="rekomendasi_pemeriksaan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${patient.rekomendasi_pemeriksaan || ''}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 mt-6 sticky bottom-0 bg-white">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" id="edit-submit-btn" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Update</span>
                    </button>
                </div>
            `;
            
            document.getElementById('edit-modal').classList.remove('hidden');
            
            // Initialize NIK counter for edit form
            const editNikInput = document.getElementById('edit-nik');
            if (editNikInput && editNikInput.value) {
                updateNIKCounter(editNikInput, 'edit-nik-counter');
            }
        } else {
            showFlashMessage('error', 'Gagal memuat data pasien');
        }
    } catch (error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memuat data');
    }
    
    lucide.createIcons();
}

async function handleEditSubmit(e) {
    e.preventDefault();
    
    const nikInput = document.getElementById('edit-nik');
    const submitBtn = document.getElementById('edit-submit-btn');
    const patientId = document.getElementById('edit-pasien-id').value;
    
    if (nikInput.value.length !== 16) {
        alert('NIK harus 16 digit! Saat ini: ' + nikInput.value.length + ' digit');
        nikInput.focus();
        return false;
    }
    
    if (!isNikValid) {
        alert('NIK sudah terdaftar pada pasien lain! Silakan gunakan NIK yang berbeda.');
        nikInput.focus();
        return false;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Menyimpan...</span>
    `;
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch(BASE_URL + `administrasi/edit_patient_data/${patientId}`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showFlashMessage('success', 'Data pasien berhasil diperbarui');
            closeEditModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showFlashMessage('error', result.errors || 'Gagal memperbarui data pasien');
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Update</span>
            `;
            lucide.createIcons();
        }
    } catch (error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memperbarui data');
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <i data-lucide="save" class="w-4 h-4"></i>
            <span>Update</span>
        `;
        lucide.createIcons();
    }
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

function exportPatients() {
    const search = '<?= $search ?? "" ?>';
    let url = BASE_URL + 'excel_controller/export_patients';
    if (search) {
        url += '?search=' + encodeURIComponent(search);
    }
    window.open(url, '_blank');
}

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
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCreateModal();
        closeDetailModal();
        closeEditModal();
    }
});

// Close modal on backdrop click
document.getElementById('create-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateModal();
    }
});

document.getElementById('detail-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailModal();
    }
});

document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
document.getElementById('addPatientForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const modalId = 'addPatientModal'; // Sesuaikan dengan ID modal Anda
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // SUCCESS: Show alert
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000
            });
            
            // CLOSE MODAL
            const modal = document.getElementById(modalId);
            if (modal) {
                // Jika pakai Bootstrap 5
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    // Fallback untuk Bootstrap 4 atau manual
                    $(modal).modal('hide'); // jQuery
                    // ATAU
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    document.querySelector('.modal-backdrop')?.remove();
                }
            }
            
            // RESET FORM
            form.reset();
            
            // RELOAD DATA TABLE (jika ada)
            setTimeout(() => {
                location.reload(); // ATAU panggil fungsi reload table
                // reloadPatientTable(); 
            }, 2000);
            
        } else {
            // ERROR: Show error message
            let errorMessage = data.message || 'Gagal menyimpan data';
            
            // Jika ada detail errors
            if (data.errors) {
                if (typeof data.errors === 'object') {
                    errorMessage += ':<br>';
                    Object.values(data.errors).forEach(error => {
                        errorMessage += '- ' + error + '<br>';
                    });
                } else {
                    errorMessage += ':<br>' + data.errors;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: errorMessage,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    });
});

// Untuk modal Edit Patient (jika ada)
document.getElementById('editPatientForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const modalId = 'editPatientModal'; // Sesuaikan dengan ID modal Anda
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000
            });
            
            // CLOSE MODAL
            const modal = document.getElementById(modalId);
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    $(modal).modal('hide');
                }
            }
            
            form.reset();
            
            setTimeout(() => {
                location.reload();
            }, 2000);
            
        } else {
            let errorMessage = data.message || 'Gagal memperbarui data';
            if (data.errors) {
                if (typeof data.errors === 'object') {
                    errorMessage += ':<br>' + Object.values(data.errors).join('<br>');
                } else {
                    errorMessage += ':<br>' + data.errors;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: errorMessage,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan koneksi',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    });
});

/**
 * Helper function: Reload patient table tanpa full reload
 * (Jika menggunakan DataTables atau AJAX table)
 */
function reloadPatientTable() {
    if (typeof window.patientTable !== 'undefined' && window.patientTable) {
        // Jika pakai DataTables
        window.patientTable.ajax.reload(null, false);
    } else {
        // Fallback ke full reload
        location.reload();
    }
}
/**
 * FIX: Auto Close Modal After Successful Submit
 * Tempatkan di view atau tambahkan ke existing JavaScript
 */

// Untuk modal Add Patient
document.getElementById('addPatientForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const modalId = 'addPatientModal'; // Sesuaikan dengan ID modal Anda
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // SUCCESS: Show alert
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000
            });
            
            // CLOSE MODAL
            const modal = document.getElementById(modalId);
            if (modal) {
                // Jika pakai Bootstrap 5
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    // Fallback untuk Bootstrap 4 atau manual
                    $(modal).modal('hide'); // jQuery
                    // ATAU
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    document.querySelector('.modal-backdrop')?.remove();
                }
            }
            
            // RESET FORM
            form.reset();
            
            // RELOAD DATA TABLE (jika ada)
            setTimeout(() => {
                location.reload(); // ATAU panggil fungsi reload table
                // reloadPatientTable(); 
            }, 2000);
            
        } else {
            // ERROR: Show error message
            let errorMessage = data.message || 'Gagal menyimpan data';
            
            // Jika ada detail errors
            if (data.errors) {
                if (typeof data.errors === 'object') {
                    errorMessage += ':<br>';
                    Object.values(data.errors).forEach(error => {
                        errorMessage += '- ' + error + '<br>';
                    });
                } else {
                    errorMessage += ':<br>' + data.errors;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: errorMessage,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    });
});

// Untuk modal Edit Patient (jika ada)
document.getElementById('editPatientForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const modalId = 'editPatientModal'; // Sesuaikan dengan ID modal Anda
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000
            });
            
            // CLOSE MODAL
            const modal = document.getElementById(modalId);
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    $(modal).modal('hide');
                }
            }
            
            form.reset();
            
            setTimeout(() => {
                location.reload();
            }, 2000);
            
        } else {
            let errorMessage = data.message || 'Gagal memperbarui data';
            if (data.errors) {
                if (typeof data.errors === 'object') {
                    errorMessage += ':<br>' + Object.values(data.errors).join('<br>');
                } else {
                    errorMessage += ':<br>' + data.errors;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: errorMessage,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan koneksi',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    });
});

/**
 * Helper function: Reload patient table tanpa full reload
 * (Jika menggunakan DataTables atau AJAX table)
 */
function reloadPatientTable() {
    if (typeof window.patientTable !== 'undefined' && window.patientTable) {
        // Jika pakai DataTables
        window.patientTable.ajax.reload(null, false);
    } else {
        // Fallback ke full reload
        location.reload();
    }
}


</script>

</body>
</html>