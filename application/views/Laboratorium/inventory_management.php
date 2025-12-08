<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Inventory - Petugas Lab</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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

        /* Slide animation */
        .slide-down {
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; max-height: 0; }
            to { opacity: 1; max-height: 1000px; }
        }

        /* Fullwidth container */
        .fullwidth-container {
            min-height: 100vh;
            width: 100%;
        }

        /* Responsive table */
        .table-container {
            min-width: 100%;
            overflow-x: auto;
        }
        
        .fullwidth-container {
            min-height: 100vh;
            width: 100% !important;
            min-width: 100% !important;
            box-sizing: border-box;
        }

        .table-container {
            min-width: 100% !important;
            width: 100% !important;
            overflow-x: auto;
            box-sizing: border-box;
        }

        .table-container table {
            width: 100% !important;
            min-width: 100% !important;
            table-layout: auto;
        }

        html, body {
            width: 100% !important;
            min-width: 100% !important;
            overflow-x: auto;
            box-sizing: border-box;
        }

        .p-6.space-y-6.fullwidth-container {
            width: 100% !important;
            min-width: 100% !important;
        }

        .bg-white.rounded-xl.shadow-sm.border.border-gray-200.fullwidth-container {
            width: 100% !important;
            min-width: 100% !important;
        }

        .fullwidth-container,
        .fullwidth-container * {
            max-width: none !important;
        }

        .fullwidth-container tr {
            width: 100% !important;
            min-width: 100% !important;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: scroll;
                -webkit-overflow-scrolling: touch;
            }
            
            .fullwidth-container {
                padding: 0.5rem;
            }
            
            .table-container table td .flex.items-center.space-x-2 {
                flex-wrap: wrap;
                gap: 0.25rem;
            }
            
            .table-container table td .flex.items-center.space-x-2 button {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
        }

        .fixed.inset-0 {
            width: 100vw !important;
            height: 100vh !important;
        }

        #inventory-tbody tr td[colspan="7"] {
            width: 100% !important;
            min-width: 100% !important;
        }

        .table-container table thead th,
        .table-container table tbody td {
            min-width: fit-content;
            white-space: nowrap;
        }

        .table-container table tbody td:last-child {
            min-width: 240px;
        }

        .loading-state {
            width: 100% !important;
            min-width: 100% !important;
            display: table-row !important;
        }

        .loading-state td {
            width: 100% !important;
            min-width: 100% !important;
        }

        /* Active tab styling */
        .schedule-tab.active {
            border-color: currentColor !important;
        }
    </style>
</head>
<body class="bg-gray-50 fullwidth-container">

<!-- Header Section -->
<div class="w-full bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="flask-conical" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Kelola Inventory - Petugas Lab</h1>
                    <p class="text-blue-100">Manajemen Alat Laboratorium dan Reagen</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="openAddModal()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Item</span>
                </button>
                <button onclick="viewCalibrationSchedule()" class="relative bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span>Jadwal Kalibrasi</span>
                    <span id="calibration-badge" class="hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="w-full p-6 space-y-6 fullwidth-container">
    
    <!-- Flash Messages Container -->
    <div id="flash-messages"></div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Alat Lab</p>
                    <p id="total-alat" class="text-2xl font-bold text-gray-900">-</p>
                    <p class="text-xs text-gray-500 mt-1">Semua alat laboratorium</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="wrench" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Reagen</p>
                    <p id="total-reagen" class="text-2xl font-bold text-green-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Semua reagen tersedia</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="flask-conical" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Perlu Kalibrasi</p>
                    <p id="calibration-due" class="text-2xl font-bold text-orange-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Alat perlu kalibrasi</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="settings" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Stok Rendah</p>
                    <p id="low-stock" class="text-2xl font-bold text-yellow-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Reagen stok rendah</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Alert Kritis</p>
                    <p id="total-critical" class="text-2xl font-bold text-red-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Status kritis/urgent</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                <span>Filter & Pencarian</span>
            </h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="filter-type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Inventory</label>
                    <div class="relative">
                        <select id="filter-type" class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Semua Tipe</option>
                            <option value="alat">Alat Laboratorium</option>
                            <option value="reagen">Reagen</option>
                        </select>
                        <i data-lucide="layers" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
                
                <div>
                    <label for="filter-status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="relative">
                        <select id="filter-status" class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Semua Status</option>
                            <option value="Normal">Normal</option>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Hampir Habis">Hampir Habis</option>
                            <option value="Perlu Kalibrasi">Perlu Kalibrasi</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Kadaluarsa">Kadaluarsa</option>
                        </select>
                        <i data-lucide="check-circle" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
                
                <div>
                    <label for="filter-alert" class="block text-sm font-medium text-gray-700 mb-2">Level Alert</label>
                    <div class="relative">
                        <select id="filter-alert" class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Semua Level</option>
                            <option value="OK">OK</option>
                            <option value="Low Stock">Stok Rendah</option>
                            <option value="Warning">Warning</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Calibration Due">Perlu Kalibrasi</option>
                        </select>
                        <i data-lucide="bell" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
                
                <div>
                    <label for="search-term" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <div class="relative">
                        <input type="text" id="search-term" 
                               class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Nama, kode, lokasi..."
                               onkeypress="handleSearchKeypress(event)">
                        <i data-lucide="search" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                        <button onclick="applyFilters()" class="absolute right-3 top-3 px-2 py-1 text-gray-400 hover:text-blue-600 transition-colors duration-200">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-100">
                <div class="flex items-center space-x-4">
                    <button onclick="applyFilters()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    <button onclick="resetFilters()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        <span>Reset</span>
                    </button>
                </div>
                <button onclick="refreshInventory()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm border border-gray-300">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <span>Refresh Data</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 fullwidth-container">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="list" class="w-5 h-5 text-blue-600"></i>
                    <span>Daftar Inventory</span>
                    <span id="inventory-count" class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        0 item
                    </span>
                </h2>
            </div>
        </div>
        
        <div class="overflow-x-auto table-container">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok/Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alert Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exp/Kalibrasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="inventory-tbody" class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded via AJAX -->
                    <tr id="loading-row">
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <i data-lucide="loader-2" class="w-5 h-5 text-blue-600 loading"></i>
                                <span class="text-gray-500">Memuat data inventory...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Item Modal -->
<div id="item-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="plus" class="w-5 h-5 text-blue-600"></i>
                    <span id="modal-title">Tambah Item Inventory</span>
                </h2>
                <button onclick="closeItemModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <form id="inventory-form" class="space-y-6">
                <input type="hidden" id="item-id" name="item_id">
                <input type="hidden" id="edit-mode" name="edit_mode" value="0">
                
                <!-- Basic Info -->
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center space-x-2">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                        <span>Informasi Dasar</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="item-type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Item <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="item-type" name="item_type" 
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        required onchange="toggleFields()">
                                    <option value="">Pilih Tipe</option>
                                    <option value="alat">Alat Laboratorium</option>
                                    <option value="reagen">Reagen</option>
                                </select>
                                <i data-lucide="layers" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="item-kode" class="block text-sm font-medium text-gray-700 mb-2">
                                Kode Unik
                            </label>
                            <div class="relative">
                                <input type="text" id="item-kode" name="kode_unik" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="Auto generate jika kosong">
                                <i data-lucide="hash" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="item-nama" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Item <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="item-nama" name="nama_item" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="Masukkan nama item"
                                       required>
                                <i data-lucide="tag" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fields for Alat Laboratorium -->
                <div id="alat-fields" style="display: none;">
                    <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center space-x-2">
                        <i data-lucide="wrench" class="w-4 h-4 text-blue-600"></i>
                        <span>Detail Alat Laboratorium</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="merek-model" class="block text-sm font-medium text-gray-700 mb-2">Merek/Model</label>
                            <div class="relative">
                                <input type="text" id="merek-model" name="merek_model" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <i data-lucide="settings" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="lokasi-alat" class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                            <div class="relative">
                                <input type="text" id="lokasi-alat" name="lokasi" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <i data-lucide="map-pin" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="status-alat" class="block text-sm font-medium text-gray-700 mb-2">Status Alat</label>
                            <div class="relative">
                                <select id="status-alat" name="status_alat" 
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                    <option value="Normal">Normal</option>
                                    <option value="Perlu Kalibrasi">Perlu Kalibrasi</option>
                                    <option value="Rusak">Rusak</option>
                                    <option value="Sedang Kalibrasi">Sedang Kalibrasi</option>
                                </select>
                                <i data-lucide="check-circle" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="jadwal-kalibrasi" class="block text-sm font-medium text-gray-700 mb-2">Jadwal Kalibrasi</label>
                            <div class="relative">
                                <input type="date" id="jadwal-kalibrasi" name="jadwal_kalibrasi" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <i data-lucide="calendar" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="kalibrasi-terakhir" class="block text-sm font-medium text-gray-700 mb-2">Kalibrasi Terakhir</label>
                            <div class="relative">
                                <input type="date" id="kalibrasi-terakhir" name="tanggal_kalibrasi_terakhir" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <i data-lucide="clock" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="riwayat-perbaikan" class="block text-sm font-medium text-gray-700 mb-2">Riwayat Perbaikan</label>
                        <div class="relative">
                            <textarea id="riwayat-perbaikan" name="riwayat_perbaikan" rows="3"
                                      class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                      placeholder="Catatan riwayat perbaikan atau maintenance"></textarea>
                            <i data-lucide="file-text" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Fields for Reagen -->
                <div id="reagen-fields" style="display: none;">
                    <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center space-x-2">
                        <i data-lucide="flask-conical" class="w-4 h-4 text-blue-600"></i>
                        <span>Detail Reagen</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="jumlah-stok" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Stok <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="jumlah-stok" name="jumlah_stok" min="0"
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <i data-lucide="package" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                            <div class="relative">
                                <input type="text" id="satuan" name="satuan" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="ml, test, pcs">
                                <i data-lucide="ruler" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="stok-minimal" class="block text-sm font-medium text-gray-700 mb-2">Stok Minimal</label>
                            <div class="relative">
                                <input type="number" id="stok-minimal" name="stok_minimal" min="0" value="10"
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <i data-lucide="alert-triangle" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="lokasi-penyimpanan" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Penyimpanan</label>
                            <div class="relative">
                                <input type="text" id="lokasi-penyimpanan" name="lokasi_penyimpanan" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <i data-lucide="map-pin" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="expired-date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
                            <div class="relative">
                                <input type="date" id="expired-date" name="expired_date" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <i data-lucide="calendar-x" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="tanggal-dipakai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai Dipakai</label>
                            <div class="relative">
                                <input type="date" id="tanggal-dipakai" name="tanggal_dipakai" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <i data-lucide="calendar" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div class="md:col-span-3">
                            <label for="status-reagen" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <div class="relative">
                                <select id="status-reagen" name="status" 
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Hampir Habis">Hampir Habis</option>
                                    <option value="Dipesan">Dipesan</option>
                                    <option value="Kadaluarsa">Kadaluarsa</option>
                                </select>
                                <i data-lucide="check-circle" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <div class="relative">
                            <textarea id="catatan" name="catatan" rows="3"
                                      class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                      placeholder="Catatan tambahan untuk reagen"></textarea>
                            <i data-lucide="file-text" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                    <button type="button" onclick="closeItemModal()"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Batal</span>
                    </button>
                    
                    <button type="submit" id="submit-btn"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                    <span>Detail Item</span>
                </h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div id="detail-content">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>
<div id="calibration-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-6 border-b border-blue-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <i data-lucide="settings" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 id="calibration-modal-title" class="text-xl font-bold text-white">Kalibrasi Alat</h3>
                        <p class="text-sm text-blue-100">Kelola kalibrasi alat laboratorium</p>
                    </div>
                </div>
                <button onclick="closeCalibrationModal()" class="text-white hover:text-gray-200 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <form id="calibration-form" class="space-y-6">
                <input type="hidden" id="calibration-alat-id" name="alat_id">
                <input type="hidden" id="calibration-mode" name="mode" value="schedule">
                
                <!-- Mode Selector -->
                <div class="mode-selector mb-4 p-4 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Mode Kalibrasi</label>
                    <div class="flex space-x-4">
                        <button type="button" onclick="toggleCalibrationMode('schedule')" 
                                class="mode-btn flex-1 px-4 py-3 border-2 rounded-lg transition-colors border-blue-600 bg-blue-50 text-blue-700 font-medium"
                                data-mode="schedule">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                            Jadwalkan
                        </button>
                        <button type="button" onclick="toggleCalibrationMode('complete')" 
                                class="mode-btn flex-1 px-4 py-3 border-2 rounded-lg transition-colors border-gray-300 text-gray-700 font-medium"
                                data-mode="complete">
                            <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
                            Selesaikan
                        </button>
                    </div>
                </div>
                
                <!-- Schedule Fields -->
                <div id="schedule-fields">
                    <div class="space-y-4">
                        <div>
                            <label for="calibration-date" class="block text-sm font-medium text-gray-700 mb-2">
                                Jadwal Kalibrasi <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" 
                                       id="calibration-date" 
                                       name="tanggal_kalibrasi" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                <i data-lucide="calendar" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <i data-lucide="info" class="w-3 h-3 inline"></i>
                                Tanggal kapan kalibrasi akan dilakukan
                            </p>
                        </div>
                        <div>
                            <label for="calibration-notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea id="calibration-notes" 
                                      name="catatan" 
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                      placeholder="Catatan tambahan untuk penjadwalan kalibrasi..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Complete Fields -->
                <div id="complete-fields" style="display: none;">
                    <div class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start space-x-3">
                                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-blue-900">Informasi Penjadwalan Otomatis</p>
                                    <p class="text-xs text-blue-700 mt-1">Jika "Jadwal Kalibrasi Berikutnya" tidak diisi, sistem akan otomatis menjadwalkan 1 tahun dari tanggal kalibrasi.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="complete-date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Kalibrasi <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" id="complete-date" name="tanggal_kalibrasi" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <i data-lucide="calendar" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Tanggal kapan kalibrasi dilakukan</p>
                        </div>
                        
                        <div>
                            <label for="calibration-result" class="block text-sm font-medium text-gray-700 mb-2">
                                Hasil Kalibrasi <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="calibration-result" name="hasil_kalibrasi" 
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="Passed">✓ Passed - Lulus</option>
                                    <option value="Failed">✗ Failed - Gagal</option>
                                    <option value="Conditional">⚠ Conditional - Bersyarat</option>
                                </select>
                                <i data-lucide="clipboard-check" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="calibration-technician" class="block text-sm font-medium text-gray-700 mb-2">Teknisi</label>
                            <div class="relative">
                                <input type="text" id="calibration-technician" name="teknisi" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Nama teknisi yang melakukan kalibrasi">
                                <i data-lucide="user" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <!-- NEW FIELD: Next Calibration Date (Optional) -->
                        <div class="border-t border-gray-200 pt-4">
                            <label for="next-calibration-date" class="block text-sm font-medium text-gray-700 mb-2">
                                Jadwal Kalibrasi Berikutnya <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <input type="date" id="next-calibration-date" name="next_calibration_date" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Kosongkan untuk jadwal otomatis 1 tahun">
                                <i data-lucide="calendar-clock" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <i data-lucide="info" class="w-3 h-3 inline"></i>
                                Kosongkan jika ingin sistem otomatis menjadwalkan 1 tahun dari tanggal kalibrasi
                            </p>
                        </div>
                        
                        <div>
                            <label for="complete-notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Hasil</label>
                            <textarea id="complete-notes" name="catatan" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                      placeholder="Detail hasil kalibrasi dan temuan..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                    <button type="button" onclick="closeCalibrationModal()"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Batal</span>
                    </button>
                    <button type="submit" id="calibration-submit-btn"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Jadwalkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Calibration History Modal -->
<div id="calibration-history-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-6 border-b border-blue-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <i data-lucide="history" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Riwayat Kalibrasi</h3>
                        <p id="history-subtitle" class="text-sm text-blue-100">Loading...</p>
                    </div>
                </div>
                <button onclick="closeCalibrationHistoryModal()" class="text-white hover:text-gray-200 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Total Kalibrasi</p>
                            <p id="hist-total" class="text-lg font-bold text-gray-900">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="thumbs-up" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Passed</p>
                            <p id="hist-passed" class="text-lg font-bold text-green-600">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="thumbs-down" class="w-5 h-5 text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Failed</p>
                            <p id="hist-failed" class="text-lg font-bold text-red-600">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="calendar" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Rata-rata Interval</p>
                            <p id="hist-interval" class="text-lg font-bold text-gray-900">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- History Timeline -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-300px)]">
            <div id="calibration-history-content">
                <div class="flex items-center justify-center py-12">
                    <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 loading"></i>
                    <span class="ml-3 text-gray-600">Memuat riwayat...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calibration Schedule Modal -->
<div id="calibration-schedule-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 via-orange-700 to-orange-800 p-6 border-b border-orange-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <i data-lucide="calendar-clock" class="w-6 h-6 text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Jadwal Kalibrasi</h3>
                        <p class="text-sm text-orange-100">Monitor jadwal kalibrasi semua alat</p>
                    </div>
                </div>
                <button onclick="closeCalibrationScheduleModal()" class="text-white hover:text-gray-200 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <!-- Schedule Stats -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Overdue</p>
                            <p id="sched-overdue" class="text-lg font-bold text-red-600">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5 text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Due Soon (30 hari)</p>
                            <p id="sched-due-soon" class="text-lg font-bold text-orange-600">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Up to Date</p>
                            <p id="sched-uptodate" class="text-lg font-bold text-green-600">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="list" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600">Total Alat</p>
                            <p id="sched-total" class="text-lg font-bold text-gray-900">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Schedule Tabs -->
        <div class="border-b border-gray-200">
            <div class="flex space-x-4 px-6">
                <button onclick="switchScheduleTab('overdue')" class="schedule-tab px-4 py-3 font-medium border-b-2 border-transparent hover:border-red-500 transition-colors active" data-tab="overdue">
                    <span class="flex items-center space-x-2 text-red-600">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        <span>Overdue</span>
                    </span>
                </button>
                <button onclick="switchScheduleTab('due_soon')" class="schedule-tab px-4 py-3 font-medium border-b-2 border-transparent hover:border-orange-500 transition-colors" data-tab="due_soon">
                    <span class="flex items-center space-x-2 text-gray-600">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        <span>Due Soon</span>
                    </span>
                </button>
                <button onclick="switchScheduleTab('up_to_date')" class="schedule-tab px-4 py-3 font-medium border-b-2 border-transparent hover:border-green-500 transition-colors" data-tab="up_to_date">
                    <span class="flex items-center space-x-2 text-gray-600">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Up to Date</span>
                    </span>
                </button>
            </div>
        </div>
        
        <!-- Schedule Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-400px)]">
            <div id="calibration-schedule-content">
                <div class="flex items-center justify-center py-12">
                    <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 loading"></i>
                    <span class="ml-3 text-gray-600">Memuat jadwal...</span>
                </div>
            </div>
        </div>
    </div>
</div>
va

<script>
// ==========================================
// GLOBAL VARIABLES
// ==========================================
let allInventory = [];
let inventoryStats = {};
let scheduleData = {};

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadInventoryData();
    loadStatistics();
    loadCalibrationReminders();
    
    // Ensure fullwidth layout
    ensureFullwidthLayout();
    
    // Debounce search input
    let searchTimeout;
    const searchInput = document.getElementById('search-term');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }
    
    // Auto-apply filter when select changes
    ['filter-type', 'filter-status', 'filter-alert'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', applyFilters);
        }
    });
});

// ==========================================
// LAYOUT FUNCTIONS
// ==========================================
function ensureFullwidthLayout() {
    forceFullwidthMaintenance();
    
    const body = document.querySelector('body');
    if (body) {
        body.style.width = '100%';
        body.style.minWidth = '100%';
        body.style.overflowX = 'auto';
    }
    
    const restrictiveElements = document.querySelectorAll('[style*="max-width"], [class*="max-w"]');
    restrictiveElements.forEach(el => {
        if (el.classList.contains('fullwidth-container') || el.closest('.fullwidth-container')) {
            el.style.maxWidth = 'none';
        }
    });
}

function forceFullwidthMaintenance() {
    const tableContainer = document.querySelector('.table-container');
    if (tableContainer) {
        tableContainer.style.width = '100%';
        tableContainer.style.minWidth = '100%';
        tableContainer.style.overflowX = 'auto';
    }
    
    const table = document.querySelector('.table-container table');
    if (table) {
        table.style.width = '100%';
        table.style.minWidth = '100%';
    }
    
    const containers = document.querySelectorAll('.fullwidth-container');
    containers.forEach(container => {
        container.style.width = '100%';
        container.style.minWidth = '100%';
        container.style.boxSizing = 'border-box';
    });
    
    const mainContent = document.querySelector('.p-6.space-y-6.fullwidth-container');
    if (mainContent) {
        mainContent.style.width = '100%';
        mainContent.style.minWidth = '100%';
    }
}

// Window resize handler
window.addEventListener('resize', function() {
    ensureFullwidthLayout();
});

// Mutation observer for layout
if (typeof MutationObserver !== 'undefined') {
    const layoutObserver = new MutationObserver(function(mutations) {
        let needsLayoutFix = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'attributes') {
                const affectedElements = document.querySelectorAll('.fullwidth-container, .table-container');
                if (affectedElements.length > 0) {
                    needsLayoutFix = true;
                }
            }
        });
        
        if (needsLayoutFix) {
            setTimeout(() => {
                ensureFullwidthLayout();
            }, 10);
        }
    });
    
    layoutObserver.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class']
    });
}

// ==========================================
// HELPER FUNCTIONS
// ==========================================
function getItemName(item, type) {
    if (item.nama_item) {
        return item.nama_item;
    } else if (type === 'alat' && item.nama_alat) {
        return item.nama_alat;
    } else if (type === 'reagen' && item.nama_reagen) {
        return item.nama_reagen;
    } else {
        return 'Unknown Item';
    }
}

function getItemStatus(item, type) {
    if (item.status) {
        return item.status;
    } else if (type === 'alat' && item.status_alat) {
        return item.status_alat;
    } else {
        return 'Unknown';
    }
}

function getStatusBadge(status, type) {
    const statusClasses = {
        'Normal': 'bg-green-100 text-green-800',
        'Tersedia': 'bg-green-100 text-green-800',
        'Hampir Habis': 'bg-yellow-100 text-yellow-800',
        'Perlu Kalibrasi': 'bg-orange-100 text-orange-800',
        'Rusak': 'bg-red-100 text-red-800',
        'Kadaluarsa': 'bg-red-100 text-red-800',
        'Sedang Kalibrasi': 'bg-blue-100 text-blue-800',
        'Dipesan': 'bg-purple-100 text-purple-800'
    };
    
    const className = statusClasses[status] || 'bg-gray-100 text-gray-800';
    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${className}">${status}</span>`;
}

function getAlertBadge(level) {
    const badges = {
        'OK': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>',
        'Low Stock': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Stok Rendah</span>',
        'Warning': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Warning</span>',
        'Urgent': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Urgent</span>',
        'Calibration Due': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Perlu Kalibrasi</span>'
    };
    return badges[level] || '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">-</span>';
}

function getStokInfo(item) {
    if (!item) return '-';
    
    if (item.tipe_inventory === 'reagen') {
        const stok = item.jumlah_stok || 0;
        const satuan = item.satuan || 'pcs';
        return `${stok} ${satuan}`;
    }
    return '-';
}

function getExpInfo(item) {
    if (!item) return '-';
    
    if (item.expired_date) {
        const expDate = new Date(item.expired_date);
        const today = new Date();
        const diffTime = expDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 0) {
            return `<span class="text-red-600 font-medium">Expired</span>`;
        } else if (diffDays <= 30) {
            return `<span class="text-yellow-600 font-medium">${diffDays} hari</span>`;
        } else {
            return `<span class="text-green-600">${formatDate(item.expired_date)}</span>`;
        }
    }
    
    if (item.jadwal_kalibrasi && item.tipe_inventory === 'alat') {
        return `<span class="text-blue-600">${formatDate(item.jadwal_kalibrasi)}</span>`;
    }
    
    return '-';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
}

async function loadInventoryData() {
    try {
        showLoadingState();
        
        const response = await fetch('<?= base_url("inventory_lab/get_inventory_data") ?>');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log('Inventory response:', data); // Debug
        
        if (data.success) {
            allInventory = data.inventory || [];
            renderInventoryTable(allInventory);
            updateInventoryCount(data.count || allInventory.length);
            
            if (allInventory.length === 0) {
                showToast('info', 'Tidak ada data inventory ditemukan');
            }
        } else {
            showToast('error', data.message || 'Gagal memuat data inventory');
            renderEmptyState();
        }
    } catch (error) {
        console.error('Error loading inventory:', error);
        showToast('error', 'Terjadi kesalahan saat memuat data: ' + error.message);
        renderEmptyState();
    } finally {
        ensureFullwidthLayout();
    }
}

async function loadStatistics() {
    try {
        const response = await fetch('<?= base_url("inventory_lab/get_statistics") ?>');
        const data = await response.json();
        
        if (data.success) {
            inventoryStats = data.stats;
            updateStatistics();
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

function updateStatistics() {
    document.getElementById('total-alat').textContent = inventoryStats.total_alat || 0;
    document.getElementById('total-reagen').textContent = inventoryStats.total_reagen || 0;
    document.getElementById('calibration-due').textContent = inventoryStats.calibration_due || 0;
    document.getElementById('low-stock').textContent = inventoryStats.low_stock || 0;
    document.getElementById('total-critical').textContent = inventoryStats.total_critical || 0;
}

function updateInventoryCount(count) {
    document.getElementById('inventory-count').textContent = `${count} item`;
}

// ==========================================
// TABLE RENDERING
// ==========================================
function renderInventoryTable(inventory) {
    const tbody = document.getElementById('inventory-tbody');
    
    if (!inventory || inventory.length === 0) {
        renderEmptyState();
        return;
    }

    tbody.innerHTML = inventory.map((item, index) => {
        const itemName = item.nama_item || 'Unknown Item';
        const itemId = item.item_id || item.alat_id || item.reagen_id || 0;
        const kodeUnik = item.kode_unik || '-';
        const tipeInventory = item.tipe_inventory || 'unknown';
        const status = item.status || 'Unknown';
        const alertLevel = item.alert_level || 'OK';
        const merekModel = item.merek_model || '';
        
        const avatar = itemName.substring(0, 2).toUpperCase();
        const typeIcon = tipeInventory === 'alat' ? 'wrench' : 'flask-conical';
        const typeColor = tipeInventory === 'alat' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
        const typeLabel = tipeInventory === 'alat' ? 'Alat' : 'Reagen';
        
        // Build action buttons
        let actionButtons = `
            <button onclick="viewDetail(${itemId}, '${tipeInventory}')" 
                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
            </button>
            <button onclick="editItem(${itemId}, '${tipeInventory}')" 
                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
            </button>
        `;
        
        // Add calibration button for alat
        if (tipeInventory === 'alat') {
            actionButtons += `
                <button onclick="viewCalibrationHistory(${itemId}, '${itemName.replace(/'/g, "\\\'")}')" 
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 transition-colors duration-200">
                    <i data-lucide="history" class="w-3 h-3 mr-1"></i>
                </button>
                <button onclick="openCalibrationModalForItem(${itemId}, '${itemName.replace(/'/g, "\\\'")}')" 
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 transition-colors duration-200">
                    <i data-lucide="settings" class="w-3 h-3 mr-1"></i>
                </button>
            `;
        }
        
        return `
            <tr class="hover:bg-gray-50 transition-colors duration-200 fullwidth-container">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${itemName}</div>
                            <div class="text-sm text-gray-500">${kodeUnik}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${typeColor}">
                        <i data-lucide="${typeIcon}" class="w-3 h-3 mr-1"></i>
                        ${typeLabel}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${getStatusBadge(status, tipeInventory)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${getStokInfo(item)}</div>
                    ${merekModel ? `<div class="text-xs text-gray-500">${merekModel}</div>` : ''}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${getAlertBadge(alertLevel)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${getExpInfo(item)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        ${actionButtons}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    lucide.createIcons();
    setTimeout(() => {
        ensureFullwidthLayout();
    }, 10);
}

function renderEmptyState() {
    const tbody = document.getElementById('inventory-tbody');
    tbody.innerHTML = `
        <tr class="fullwidth-container">
            <td colspan="7" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i data-lucide="package" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data ditemukan</h3>
                        <p class="text-gray-500 mb-4">Tidak ada inventory yang sesuai dengan filter yang dipilih</p>
                        <div class="flex space-x-2">
                            <button onclick="resetFilters()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                <span>Reset Filter</span>
                            </button>
                            <button onclick="openAddModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Tambah Item</span>
                            </button>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    `;
    
    lucide.createIcons();
    
    setTimeout(() => {
        ensureFullwidthLayout();
    }, 10);
}

function showLoadingState() {
    const tbody = document.getElementById('inventory-tbody');
    if (tbody) {
        tbody.innerHTML = `
            <tr class="loading-state">
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex items-center justify-center space-x-2">
                        <i data-lucide="loader-2" class="w-5 h-5 text-blue-600 loading"></i>
                        <span class="text-gray-500">Memuat data inventory...</span>
                    </div>
                </td>
            </tr>
        `;
        lucide.createIcons();
    }
}

function handleSearchKeypress(event) {
    if (event.key === 'Enter') {
        applyFilters();
    }
}

async function applyFilters() {
    const filters = {
        type: document.getElementById('filter-type').value || '',
        status: document.getElementById('filter-status').value || '',
        alert: document.getElementById('filter-alert').value || '',
        search: document.getElementById('search-term').value || ''
    };
    
    showLoadingState();
    
    try {
        const params = new URLSearchParams();
        Object.keys(filters).forEach(key => {
            if (filters[key] && filters[key] !== '') {
                params.append(key, filters[key]);
            }
        });
        
        const url = '<?= base_url("inventory_lab/get_filtered_inventory") ?>' + 
                   (params.toString() ? '?' + params.toString() : '');
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            renderInventoryTable(data.inventory || []);
            updateInventoryCount(data.count || 0);
            
            if (Object.values(filters).some(f => f !== '')) {
                if (data.count > 0) {
                    showToast('success', `Filter diterapkan - ${data.count} item ditemukan`);
                } else {
                    showToast('info', 'Filter diterapkan - tidak ada data yang sesuai');
                }
            }
        } else {
            showToast('error', data.message || 'Gagal memfilter data');
            renderEmptyState();
        }
    } catch (error) {
        console.error('Error applying filters:', error);
        showToast('error', 'Terjadi kesalahan saat memfilter data: ' + error.message);
        renderEmptyState();
    } finally {
        setTimeout(() => {
            ensureFullwidthLayout();
        }, 50);
    }
}

function resetFilters() {
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-alert').value = '';
    document.getElementById('search-term').value = '';
    loadInventoryData();
}

function refreshInventory() {
    loadInventoryData();
    loadStatistics();
    showToast('success', 'Data berhasil diperbarui');
}

// ==========================================
// FORM MANAGEMENT
// ==========================================
function openAddModal() {
    resetForm();
    document.getElementById('modal-title').textContent = 'Tambah Item Inventory';
    document.getElementById('item-modal').classList.remove('hidden');
    ensureFullwidthLayout();
}

function closeItemModal() {
    document.getElementById('item-modal').classList.add('hidden');
    resetForm();
    ensureFullwidthLayout();
}

function toggleFields() {
    const type = document.getElementById('item-type').value;
    
    if (type === 'alat') {
        document.getElementById('alat-fields').style.display = 'block';
        document.getElementById('reagen-fields').style.display = 'none';
    } else if (type === 'reagen') {
        document.getElementById('alat-fields').style.display = 'none';
        document.getElementById('reagen-fields').style.display = 'block';
    } else {
        document.getElementById('alat-fields').style.display = 'none';
        document.getElementById('reagen-fields').style.display = 'none';
    }
}

function resetForm() {
    document.getElementById('inventory-form').reset();
    document.getElementById('edit-mode').value = '0';
    document.getElementById('item-id').value = '';
    document.getElementById('alat-fields').style.display = 'none';
    document.getElementById('reagen-fields').style.display = 'none';
    document.getElementById('modal-title').textContent = 'Tambah Item Inventory';
    clearValidationErrors();
}

// Handle form submission
document.getElementById('inventory-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    const originalContent = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 loading mr-2"></i>Menyimpan...';
    lucide.createIcons();
    
    try {
        const formData = new FormData(this);
        const isEdit = document.getElementById('edit-mode').value === '1';
        const url = isEdit ? 
            '<?= base_url("inventory_lab/ajax_update_item/") ?>' + document.getElementById('item-id').value :
            '<?= base_url("inventory_lab/ajax_create_item") ?>';
        
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('success', data.message);
            closeItemModal();
            loadInventoryData();
            loadStatistics();
        } else {
            showToast('error', data.message);
            if (data.errors) {
                displayValidationErrors(data.errors);
            }
        }
    } catch (error) {
        console.error('Error saving inventory item:', error);
        showToast('error', 'Terjadi kesalahan saat menyimpan data');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
        lucide.createIcons();
    }
});

async function editItem(itemId, type) {
    try {
        const response = await fetch(`<?= base_url("inventory_lab/ajax_get_item_details/") ?>${itemId}/${type}`);
        const data = await response.json();
        
        if (data.success) {
            populateForm(data.item, type);
            document.getElementById('modal-title').textContent = 'Edit Item Inventory';
            openEditModal();
        } else {
            showToast('error', data.message);
        }
    } catch (error) {
        console.error('Error loading item details:', error);
        showToast('error', 'Gagal memuat detail item');
    }
}

function openEditModal() {
    document.getElementById('item-modal').classList.remove('hidden');
    ensureFullwidthLayout();
}

function populateForm(item, type) {
    document.getElementById('edit-mode').value = '1';
    document.getElementById('item-id').value = item.item_id || item.alat_id || item.reagen_id;
    document.getElementById('item-type').value = type;
    document.getElementById('item-kode').value = item.kode_unik || '';
    
    const itemName = getItemName(item, type);
    document.getElementById('item-nama').value = itemName;
    
    if (type === 'alat') {
        document.getElementById('merek-model').value = item.merek_model || '';
        document.getElementById('lokasi-alat').value = item.lokasi || '';
        document.getElementById('status-alat').value = item.status_alat || 'Normal';
        document.getElementById('jadwal-kalibrasi').value = item.jadwal_kalibrasi || '';
        document.getElementById('kalibrasi-terakhir').value = item.tanggal_kalibrasi_terakhir || '';
        document.getElementById('riwayat-perbaikan').value = item.riwayat_perbaikan || '';
    } else {
        document.getElementById('jumlah-stok').value = item.jumlah_stok || 0;
        document.getElementById('satuan').value = item.satuan || '';
        document.getElementById('stok-minimal').value = item.stok_minimal || 10;
        document.getElementById('lokasi-penyimpanan').value = item.lokasi_penyimpanan || '';
        document.getElementById('expired-date').value = item.expired_date || '';
        document.getElementById('tanggal-dipakai').value = item.tanggal_dipakai || '';
        document.getElementById('status-reagen').value = item.status || 'Tersedia';
        document.getElementById('catatan').value = item.catatan || '';
    }
    
    toggleFields();
}

// ==========================================
// DETAIL VIEW
// ==========================================
async function viewDetail(itemId, type) {
    try {
        const response = await fetch(`<?= base_url("inventory_lab/ajax_get_item_details/") ?>${itemId}/${type}`);
        const data = await response.json();
        
        if (data.success) {
            displayDetail(data.item, type);
            document.getElementById('detail-modal').classList.remove('hidden');
        } else {
            showToast('error', data.message);
        }
    } catch (error) {
        console.error('Error loading item details:', error);
        showToast('error', 'Gagal memuat detail item');
    }
}

function displayDetail(item, type) {
    const itemName = getItemName(item, type);
    const itemStatus = getItemStatus(item, type);
    const itemId = item.item_id || item.alat_id || item.reagen_id;
    
    if (!itemName || !itemId) {
        showToast('error', 'Data item tidak lengkap');
        return;
    }
    
    let content = `
        <div class="space-y-6">
            <div class="flex items-center space-x-4 p-4 bg-blue-50 rounded-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                    ${itemName.substring(0, 2).toUpperCase()}
                </div>
                <div>
                    <h4 class="text-xl font-bold text-gray-900">${itemName}</h4>
                    <p class="text-sm text-gray-600">Kode: ${item.kode_unik || '-'}</p>
                    <p class="text-sm text-gray-600">Tipe: ${type === 'alat' ? 'Alat Laboratorium' : 'Reagen'}</p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                    <span>Informasi Dasar</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Status:</span>
                        <div>${getStatusBadge(itemStatus, type)}</div>
                    </div>
                </div>
            </div>
    `;
    
    if (type === 'alat') {
        content += `
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <i data-lucide="wrench" class="w-5 h-5 text-blue-600"></i>
                    <span>Detail Alat</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Merek/Model:</span>
                        <span class="text-gray-900">${item.merek_model || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Lokasi:</span>
                        <span class="text-gray-900">${item.lokasi || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Jadwal Kalibrasi:</span>
                        <span class="text-gray-900">${item.jadwal_kalibrasi ? formatDate(item.jadwal_kalibrasi) : '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Kalibrasi Terakhir:</span>
                        <span class="text-gray-900">${item.tanggal_kalibrasi_terakhir ? formatDate(item.tanggal_kalibrasi_terakhir) : '-'}</span>
                    </div>
                </div>

                ${item.riwayat_perbaikan ? `
                <div class="mt-4">
                    <span class="font-medium text-gray-700 block mb-2">Riwayat Perbaikan:</span>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-gray-900 whitespace-pre-wrap">${item.riwayat_perbaikan}</p>
                    </div>
                </div>` : ''}
            </div>
        `;
    } else {
        content += `
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <i data-lucide="flask-conical" class="w-5 h-5 text-blue-600"></i>
                    <span>Detail Reagen</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Stok:</span>
                        <span class="text-gray-900">${item.jumlah_stok || 0} ${item.satuan || 'pcs'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Stok Minimal:</span>
                        <span class="text-gray-900">${item.stok_minimal || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Lokasi Penyimpanan:</span>
                        <span class="text-gray-900">${item.lokasi_penyimpanan || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Tanggal Expired:</span>
                        <span class="text-gray-900">${item.expired_date ? formatDate(item.expired_date) : '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Tanggal Mulai Dipakai:</span>
                        <span class="text-gray-900">${item.tanggal_dipakai ? formatDate(item.tanggal_dipakai) : '-'}</span>
                    </div>
                </div>

                ${item.catatan ? `
                <div class="mt-4">
                    <span class="font-medium text-gray-700 block mb-2">Catatan:</span>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-gray-900">${item.catatan}</p>
                    </div>
                </div>` : ''}
            </div>
        `;
    }
    
    content += `
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <button onclick="closeDetailModal()" 
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Tutup
                </button>
                <button onclick="closeDetailModal(); editItem(${itemId}, '${type}')" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors duration-200">
                    Edit Data
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('detail-content').innerHTML = content;
    lucide.createIcons();
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
    ensureFullwidthLayout();
}

// ==========================================
// CALIBRATION FUNCTIONS (IMPROVED)
// ==========================================
function openCalibrationModalForItem(alatId, itemName, mode = 'schedule') {
    resetCalibrationForm();
    document.getElementById('calibration-alat-id').value = alatId;
    
    toggleCalibrationMode(mode);
    document.getElementById('calibration-modal-title').textContent = `Kalibrasi - ${itemName}`;
    document.getElementById('calibration-modal').classList.remove('hidden');
    
    lucide.createIcons();
    ensureFullwidthLayout();
}

function closeCalibrationModal() {
    document.getElementById('calibration-modal').classList.add('hidden');
    resetCalibrationForm();
    ensureFullwidthLayout();
}

function resetCalibrationForm() {
    document.getElementById('calibration-form').reset();
    document.getElementById('calibration-mode').value = 'schedule';
    document.getElementById('schedule-fields').style.display = 'block';
    document.getElementById('complete-fields').style.display = 'none';
    
    const submitBtn = document.getElementById('calibration-submit-btn');
    submitBtn.innerHTML = '<i data-lucide="save" class="w-4 h-4 mr-2"></i><span>Jadwalkan</span>';
    
    document.getElementById('calibration-modal-title').textContent = 'Kalibrasi Alat';
    
    // Reset mode button styles
    document.querySelectorAll('.mode-btn').forEach(btn => {
        if (btn.dataset.mode === 'schedule') {
            btn.classList.add('border-blue-600', 'bg-blue-50', 'text-blue-700', 'font-medium');
            btn.classList.remove('border-gray-300', 'text-gray-700');
        } else {
            btn.classList.remove('border-green-600', 'bg-green-50', 'text-green-700', 'font-medium');
            btn.classList.add('border-gray-300', 'text-gray-700', 'font-medium');
        }
    });
    
    // Reset required attributes properly
    const scheduleDate = document.getElementById('calibration-date');
    const completeDate = document.getElementById('complete-date');
    
    scheduleDate.setAttribute('required', 'required');
    scheduleDate.value = '';
    
    completeDate.removeAttribute('required');
    completeDate.value = '';
    
    // Clear next calibration date
    const nextCalDate = document.getElementById('next-calibration-date');
    if (nextCalDate) {
        nextCalDate.value = '';
    }
    
    lucide.createIcons();
}
function toggleCalibrationMode(mode) {
    console.log('Switching to mode:', mode);
    
    const modeInput = document.getElementById('calibration-mode');
    modeInput.value = mode;
    
    const scheduleFields = document.getElementById('schedule-fields');
    const completeFields = document.getElementById('complete-fields');
    const submitBtn = document.getElementById('calibration-submit-btn');
    const scheduleDate = document.getElementById('calibration-date');
    const completeDate = document.getElementById('complete-date');
    
    // Update button styles
    document.querySelectorAll('.mode-btn').forEach(btn => {
        if (btn.dataset.mode === mode) {
            if (mode === 'schedule') {
                btn.classList.add('border-blue-600', 'bg-blue-50', 'text-blue-700', 'font-medium');
                btn.classList.remove('border-gray-300', 'text-gray-700', 'border-green-600', 'bg-green-50', 'text-green-700');
            } else {
                btn.classList.add('border-green-600', 'bg-green-50', 'text-green-700', 'font-medium');
                btn.classList.remove('border-gray-300', 'text-gray-700', 'border-blue-600', 'bg-blue-50', 'text-blue-700');
            }
        } else {
            btn.classList.add('border-gray-300', 'text-gray-700', 'font-medium');
            btn.classList.remove('border-blue-600', 'bg-blue-50', 'text-blue-700', 'border-green-600', 'bg-green-50', 'text-green-700');
        }
    });
    
    if (mode === 'schedule') {
        scheduleFields.style.display = 'block';
        completeFields.style.display = 'none';
        submitBtn.innerHTML = '<i data-lucide="save" class="w-4 h-4 mr-2"></i><span>Jadwalkan</span>';
        
        scheduleDate.setAttribute('required', 'required');
        scheduleDate.removeAttribute('disabled');
        completeDate.removeAttribute('required');
        completeDate.setAttribute('disabled', 'disabled');
        
    } else if (mode === 'complete') {
        scheduleFields.style.display = 'none';
        completeFields.style.display = 'block';
        submitBtn.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-2"></i><span>Selesaikan Kalibrasi</span>';
        
        const today = new Date().toISOString().split('T')[0];
        completeDate.value = today;
        
        completeDate.setAttribute('required', 'required');
        completeDate.removeAttribute('disabled');
        scheduleDate.removeAttribute('required');
        scheduleDate.setAttribute('disabled', 'disabled');
    }
    
    lucide.createIcons();
}
document.getElementById('calibration-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('calibration-submit-btn');
    const originalContent = submitBtn.innerHTML;
    const mode = document.getElementById('calibration-mode').value;
    
    // Get the correct date field based on mode
    let tanggalKalibrasi;
    if (mode === 'schedule') {
        tanggalKalibrasi = document.getElementById('calibration-date').value;
    } else {
        tanggalKalibrasi = document.getElementById('complete-date').value;
    }
    
    // Validation
    if (!tanggalKalibrasi || tanggalKalibrasi.trim() === '') {
        showToast('error', 'Tanggal kalibrasi harus diisi');
        return;
    }
    
    const alatId = document.getElementById('calibration-alat-id').value;
    if (!alatId || alatId.trim() === '') {
        showToast('error', 'ID Alat tidak valid');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 loading mr-2"></i>Memproses...';
    lucide.createIcons();
    
    try {
        const formData = new FormData(this);
        
        // Pastikan mode dan tanggal yang benar di-set
        formData.set('mode', mode);
        formData.set('tanggal_kalibrasi', tanggalKalibrasi);
        formData.set('alat_id', alatId);
        
        console.log('Submitting form with mode:', mode, 'date:', tanggalKalibrasi);
        
        const response = await fetch('<?= base_url("inventory_lab/ajax_save_calibration") ?>', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Server response:', data);
        
        if (data.success) {
            showToast('success', data.message);
            closeCalibrationModal();
            // Refresh data
            await loadInventoryData();
            await loadStatistics();
            await loadCalibrationReminders();
        } else {
            showToast('error', data.message || 'Gagal menyimpan kalibrasi');
        }
    } catch (error) {
        console.error('Error saving calibration:', error);
        showToast('error', 'Terjadi kesalahan saat menyimpan kalibrasi: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
        lucide.createIcons();
    }
});

async function viewCalibrationHistory(alatId, itemName) {
    try {
        document.getElementById('calibration-history-modal').classList.remove('hidden');
        document.getElementById('history-subtitle').textContent = itemName || 'Loading...';
        
        const response = await fetch(`<?= base_url("inventory_lab/ajax_get_calibration_history/") ?>${alatId}`);
        const data = await response.json();
        
        if (data.success) {
            renderCalibrationHistory(data);
        } else {
            showToast('error', data.message);
            closeCalibrationHistoryModal();
        }
    } catch (error) {
        console.error('Error loading calibration history:', error);
        showToast('error', 'Gagal memuat riwayat kalibrasi');
        closeCalibrationHistoryModal();
    }
}

function renderCalibrationHistory(data) {
    const alat = data.alat;
    const history = data.history;
    const stats = data.stats;
    
    document.getElementById('history-subtitle').textContent = `${alat.nama_alat} (${alat.kode_unik || '-'})`;
    
    document.getElementById('hist-total').textContent = stats.total_calibrations;
    document.getElementById('hist-passed').textContent = stats.passed_count;
    document.getElementById('hist-failed').textContent = stats.failed_count;
    document.getElementById('hist-interval').textContent = stats.avg_interval_days + ' hari';
    
    const content = document.getElementById('calibration-history-content');
    
    if (history.length === 0) {
        content.innerHTML = `
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="calendar-x" class="w-12 h-12 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Riwayat Kalibrasi</h3>
                <p class="text-gray-500">Riwayat kalibrasi akan muncul setelah kalibrasi pertama dilakukan</p>
            </div>
        `;
    } else {
        content.innerHTML = `
            <div class="relative">
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                <div class="space-y-6">
                    ${history.map((item, index) => {
                        const resultClass = item.hasil_kalibrasi === 'Passed' ? 'green' : 
                                          item.hasil_kalibrasi === 'Failed' ? 'red' : 'yellow';
                        const resultIcon = item.hasil_kalibrasi === 'Passed' ? 'check-circle' : 
                                         item.hasil_kalibrasi === 'Failed' ? 'x-circle' : 'alert-circle';
                        
                        return `
                            <div class="relative flex items-start space-x-4">
                                <div class="relative flex items-center justify-center w-12 h-12 rounded-full shadow-sm z-10 bg-gradient-to-br from-${resultClass}-500 to-${resultClass}-600">
                                    <i data-lucide="${resultIcon}" class="w-5 h-5 text-white"></i>
                                </div>
                                <div class="flex-1 min-w-0 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-base font-semibold text-gray-900">${item.hasil_kalibrasi || 'Kalibrasi'}</h3>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-500">${formatDate(item.tanggal_kalibrasi)}</span>
                                            </div>
                                        </div>
                                        
                                        ${item.teknisi ? `<p class="text-sm text-gray-700 mb-2"><strong>Teknisi:</strong> ${item.teknisi}</p>` : ''}
                                        ${item.catatan ? `<p class="text-sm text-gray-700 mb-2">${item.catatan}</p>` : ''}
                                        
                                        ${item.next_calibration_date ? `
                                        <div class="mt-3 p-2 bg-blue-50 rounded border border-blue-200">
                                            <p class="text-xs text-blue-700">
                                                <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                                                Jadwal berikutnya: ${formatDate(item.next_calibration_date)}
                                            </p>
                                        </div>` : ''}
                                        
                                        <div class="flex items-center justify-between text-xs mt-3 pt-3 border-t border-gray-100">
                                            <div class="text-gray-500">${index === 0 ? '<span class="font-medium text-green-600">Kalibrasi terbaru</span>' : ''}</div>
                                            ${item.user_name ? `<div class="flex items-center space-x-1 text-gray-600"><i data-lucide="user" class="w-3 h-3"></i><span>${item.user_name}</span></div>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }
    
    lucide.createIcons();
}

function closeCalibrationHistoryModal() {
    document.getElementById('calibration-history-modal').classList.add('hidden');
    ensureFullwidthLayout();
}

// ==========================================
// CALIBRATION SCHEDULE
// ==========================================
async function viewCalibrationSchedule() {
    try {
        document.getElementById('calibration-schedule-modal').classList.remove('hidden');
        
        const response = await fetch('<?= base_url("inventory_lab/ajax_get_calibration_schedule") ?>');
        const data = await response.json();
        
        if (data.success) {
            renderCalibrationSchedule(data);
        } else {
            showToast('error', data.message);
            closeCalibrationScheduleModal();
        }
    } catch (error) {
        console.error('Error loading calibration schedule:', error);
        showToast('error', 'Gagal memuat jadwal kalibrasi');
        closeCalibrationScheduleModal();
    }
}

function renderCalibrationSchedule(data) {
    scheduleData = data.data;
    const stats = data.stats;
    
    document.getElementById('sched-overdue').textContent = stats.overdue;
    document.getElementById('sched-due-soon').textContent = stats.due_soon;
    document.getElementById('sched-uptodate').textContent = stats.up_to_date;
    document.getElementById('sched-total').textContent = stats.total;
    
    switchScheduleTab('overdue');
}

function switchScheduleTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.schedule-tab').forEach(btn => {
        const span = btn.querySelector('span');
        if (btn.dataset.tab === tab) {
            btn.classList.add('active');
            btn.style.borderColor = 'currentColor';
            if (tab === 'overdue') {
                span.classList.remove('text-gray-600');
                span.classList.add('text-red-600');
            } else if (tab === 'due_soon') {
                span.classList.remove('text-gray-600');
                span.classList.add('text-orange-600');
            } else {
                span.classList.remove('text-gray-600');
                span.classList.add('text-green-600');
            }
        } else {
            btn.classList.remove('active');
            btn.style.borderColor = 'transparent';
            span.classList.remove('text-red-600', 'text-orange-600', 'text-green-600');
            span.classList.add('text-gray-600');
        }
    });
    
    // Render content
    const content = document.getElementById('calibration-schedule-content');
    const items = scheduleData[tab] || [];
    
    if (items.length === 0) {
        content.innerHTML = `
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle" class="w-12 h-12 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Alat</h3>
                <p class="text-gray-500">Tidak ada alat dalam kategori ini</p>
            </div>
        `;
    } else {
        content.innerHTML = `
            <div class="space-y-4">
                ${items.map(item => {
                    const priorityClass = item.priority === 'urgent' ? 'border-red-200 bg-red-50' : 
                                        item.priority === 'high' ? 'border-orange-200 bg-orange-50' : 
                                        'border-gray-200 bg-white';
                    
                    return `
                        <div class="rounded-xl border-2 ${priorityClass} p-4 hover:shadow-md transition-all">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                        ${item.nama_alat.substring(0, 2).toUpperCase()}
                                    </div>
                                    
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">${item.nama_alat}</h3>
                                        <p class="text-sm text-gray-600">Kode: ${item.kode_unik || '-'}</p>
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-3">
                                            <div>
                                                <p class="text-xs text-gray-500">Jadwal Kalibrasi</p>
                                                <p class="text-sm font-medium text-gray-900">${formatDate(item.jadwal_kalibrasi)}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Kalibrasi Terakhir</p>
                                                <p class="text-sm font-medium text-gray-900">${item.last_calibration ? formatDate(item.last_calibration) : 'Belum pernah'}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Status</p>
                                                <p class="text-sm font-medium ${item.priority === 'urgent' ? 'text-red-600' : item.priority === 'high' ? 'text-orange-600' : 'text-green-600'}">
                                                    ${item.days_label}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Lokasi</p>
                                                <p class="text-sm font-medium text-gray-900">${item.lokasi || '-'}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col space-y-2 ml-4">
                                    <button onclick="closeCalibrationScheduleModal(); openCalibrationModalForItem(${item.alat_id}, '${item.nama_alat.replace(/'/g, "\\'")}', 'complete')" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        Selesaikan
                                    </button>
                                    <button onclick="closeCalibrationScheduleModal(); viewCalibrationHistory(${item.alat_id}, '${item.nama_alat.replace(/'/g, "\\'")}');" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors">
                                        <i data-lucide="history" class="w-3 h-3 mr-1"></i>
                                        History
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }
    
    lucide.createIcons();
}

function closeCalibrationScheduleModal() {
    document.getElementById('calibration-schedule-modal').classList.add('hidden');
    ensureFullwidthLayout();
}

// ==========================================
// CALIBRATION REMINDERS
// ==========================================
async function loadCalibrationReminders() {
    try {
        const response = await fetch('<?= base_url("inventory_lab/ajax_get_calibration_reminders") ?>');
        const data = await response.json();
        
        if (data.success && data.count > 0) {
            showCalibrationNotificationBadge(data.count);
            
            const urgentReminders = data.reminders.filter(r => r.type === 'danger');
            if (urgentReminders.length > 0) {
                setTimeout(() => {
                    showToast('warning', `${urgentReminders.length} alat memerlukan kalibrasi segera!`);
                }, 2000);
            }
        }
    } catch (error) {
        console.error('Error loading calibration reminders:', error);
    }
}

function showCalibrationNotificationBadge(count) {
    const badge = document.getElementById('calibration-badge');
    if (badge && count > 0) {
        badge.textContent = count > 9 ? '9+' : count;
        badge.classList.remove('hidden');
        badge.classList.add('flex');
    }
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================
function showToast(type, message) {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const iconName = type === 'success' ? 'check-circle' : type === 'info' ? 'info' : type === 'warning' ? 'alert-triangle' : 'alert-circle';
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : type === 'info' ? 'bg-blue-50 border-blue-200' : type === 'warning' ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200';
    const iconColor = type === 'success' ? 'text-green-600' : type === 'info' ? 'text-blue-600' : type === 'warning' ? 'text-yellow-600' : 'text-red-600';
    const textColor = type === 'success' ? 'text-green-800' : type === 'info' ? 'text-blue-800' : type === 'warning' ? 'text-yellow-800' : 'text-red-800';
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${bgColor} ${textColor} border rounded-lg p-4 max-w-sm shadow-lg transform transition-all duration-500 ease-out translate-x-full opacity-0`;
    toast.innerHTML = `
        <div class="flex items-start space-x-3">
            <i data-lucide="${iconName}" class="w-5 h-5 ${iconColor} flex-shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="removeToast('${toastId}')" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    lucide.createIcons();
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    setTimeout(() => {
        removeToast(toastId);
    }, 5000);
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 500);
    }
}

function displayValidationErrors(errors) {
    clearValidationErrors();
    
    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('border-red-500');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-red-500 text-xs mt-1';
            errorDiv.textContent = errors[field];
            input.parentElement.appendChild(errorDiv);
        }
    });
}

function clearValidationErrors() {
    document.querySelectorAll('.border-red-500').forEach(input => {
        input.classList.remove('border-red-500');
    });
    document.querySelectorAll('.text-red-500.text-xs').forEach(error => {
        error.remove();
    });
}
</script>

</body>
</html>