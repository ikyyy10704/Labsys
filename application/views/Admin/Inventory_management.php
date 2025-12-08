<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Inventory - Labsy</title>
    
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

/* Pastikan table container tetap fullwidth */
.table-container {
    min-width: 100% !important;
    width: 100% !important;
    overflow-x: auto;
    box-sizing: border-box;
}

/* Pastikan table tetap fullwidth */
.table-container table {
    width: 100% !important;
    min-width: 100% !important;
    table-layout: auto;
}

/* Force fullwidth untuk body dan html */
html, body {
    width: 100% !important;
    min-width: 100% !important;
    overflow-x: auto;
    box-sizing: border-box;
}

/* Pastikan main content area tetap fullwidth */
.p-6.space-y-6.fullwidth-container {
    width: 100% !important;
    min-width: 100% !important;
}

/* Pastikan semua card dan section tetap responsive */
.bg-white.rounded-xl.shadow-sm.border.border-gray-200.fullwidth-container {
    width: 100% !important;
    min-width: 100% !important;
}

/* Override max-width restrictions untuk fullwidth elements */
.fullwidth-container,
.fullwidth-container * {
    max-width: none !important;
}

/* Pastikan table rows tetap fullwidth */
.fullwidth-container tr {
    width: 100% !important;
    min-width: 100% !important;
}

/* Responsive table untuk mobile */
@media (max-width: 768px) {
    .table-container {
        overflow-x: scroll;
        -webkit-overflow-scrolling: touch;
    }
    
    .fullwidth-container {
        padding: 0.5rem;
    }
    
    /* Pastikan buttons tetap accessible di mobile */
    .table-container table td .flex.items-center.space-x-2 {
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .table-container table td .flex.items-center.space-x-2 button {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Pastikan modal tidak mempengaruhi fullwidth */
.fixed.inset-0 {
    width: 100vw !important;
    height: 100vh !important;
}

/* Stability untuk empty state */
#inventory-tbody tr td[colspan="7"] {
    width: 100% !important;
    min-width: 100% !important;
}

/* Prevent layout shifts */
.table-container table thead th,
.table-container table tbody td {
    min-width: fit-content;
    white-space: nowrap;
}

/* Kolom aksi tetap cukup lebar */
.table-container table tbody td:last-child {
    min-width: 200px;
}

/* Loading state styling */
.loading-state {
    width: 100% !important;
    min-width: 100% !important;
    display: table-row !important;
}

.loading-state td {
    width: 100% !important;
    min-width: 100% !important;
}
    </style>
</head>
<body class="bg-gray-50 fullwidth-container">

<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="box" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Kelola Inventory</h1>
                <p class="text-blue-100">Manajemen Alat Laboratorium dan Reagen</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="openAddModal()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Item</span>
            </button>
            <div class="relative">
                <button onclick="toggleExportMenu()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Export</span>
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>
                <div id="export-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <a href="#" onclick="exportInventory('all')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 inline mr-2"></i>Semua Data
                    </a>
                    <a href="#" onclick="exportInventory('alat')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i data-lucide="wrench" class="w-4 h-4 inline mr-2"></i>Alat Lab
                    </a>
                    <a href="#" onclick="exportInventory('reagen')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i data-lucide="flask-conical" class="w-4 h-4 inline mr-2"></i>Reagen
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="p-4 space-y-6 fullwidth-container">
    
    <!-- Flash Messages Container -->
    <div id="flash-messages"></div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
                    <p class="text-sm font-medium text-gray-600">Butuh Perhatian</p>
                    <p id="total-alerts" class="text-2xl font-bold text-yellow-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Item perlu ditindaklanjuti</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Kritis</p>
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
                    <button onclick="applyFilters()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    <button onclick="resetFilters()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        <span>Reset</span>
                    </button>
                </div>
                <button onclick="refreshInventory()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
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
                        <td colspan="9" class="px-6 py-12 text-center">
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

<script>
// Global variables
let allInventory = [];
let inventoryStats = {};

// Helper function to get correct property name for item name
function getItemName(item, type) {
    // Handle different data structures
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

// Helper function to get correct status property
function getItemStatus(item, type) {
    if (item.status) {
        return item.status;
    } else if (type === 'alat' && item.status_alat) {
        return item.status_alat;
    } else {
        return 'Unknown';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    // Debounce search input
    let searchTimeout;
    const searchInput = document.getElementById('search-term');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500); // 500ms delay
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

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadInventoryData();
    loadStatistics();
    
    // Ensure fullwidth layout
    ensureFullwidthLayout();
    
    // Close export menu when clicking outside
    document.addEventListener('click', function(e) {
        const exportMenu = document.getElementById('export-menu');
        const exportButton = e.target.closest('button[onclick="toggleExportMenu()"]');
        
        if (!exportButton && !exportMenu.contains(e.target)) {
            exportMenu.classList.add('hidden');
        }
    });
});

// Ensure fullwidth layout
function ensureFullwidthLayout() {
    // Panggil fungsi maintenance
    forceFullwidthMaintenance();
    
    // Tambahan untuk memastikan responsive
    const body = document.querySelector('body');
    if (body) {
        body.style.width = '100%';
        body.style.minWidth = '100%';
        body.style.overflowX = 'auto';
    }
    
    // Pastikan tidak ada element yang membatasi width
    const restrictiveElements = document.querySelectorAll('[style*="max-width"], [class*="max-w"]');
    restrictiveElements.forEach(el => {
        if (el.classList.contains('fullwidth-container') || el.closest('.fullwidth-container')) {
            el.style.maxWidth = 'none';
        }
    });
}

// Toggle export menu
function toggleExportMenu() {
    const menu = document.getElementById('export-menu');
    menu.classList.toggle('hidden');
}

// Load inventory data
async function loadInventoryData() {
    try {
        const response = await fetch('<?= base_url("inventory/get_inventory_data") ?>');
        const data = await response.json();
        
        if (data.success) {
            allInventory = data.inventory;
            renderInventoryTable(allInventory);
            updateInventoryCount(allInventory.length);
        } else {
            showToast('error', 'Gagal memuat data inventory');
            renderEmptyState();
        }
    } catch (error) {
        console.error('Error loading inventory:', error);
        showToast('error', 'Terjadi kesalahan saat memuat data');
        renderEmptyState();
    } finally {
        ensureFullwidthLayout();
    }
}

// Load statistics
async function loadStatistics() {
    try {
        const response = await fetch('<?= base_url("inventory/get_statistics") ?>');
        const data = await response.json();
        
        if (data.success) {
            inventoryStats = data.stats;
            updateStatistics();
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Update statistics
function updateStatistics() {
    document.getElementById('total-alat').textContent = inventoryStats.total_alat || 0;
    document.getElementById('total-reagen').textContent = inventoryStats.total_reagen || 0;
    document.getElementById('total-alerts').textContent = inventoryStats.total_alerts || 0;
    document.getElementById('total-critical').textContent = inventoryStats.total_critical || 0;
}

// Update inventory count
function updateInventoryCount(count) {
    document.getElementById('inventory-count').textContent = `${count} item`;
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
    
    // Pastikan icons di-render
    lucide.createIcons();
    
    // Force fullwidth layout setelah render
    setTimeout(() => {
        ensureFullwidthLayout();
    }, 10);
}
function forceFullwidthMaintenance() {
    // Pastikan table container tetap fullwidth
    const tableContainer = document.querySelector('.table-container');
    if (tableContainer) {
        tableContainer.style.width = '100%';
        tableContainer.style.minWidth = '100%';
        tableContainer.style.overflowX = 'auto';
    }
    
    // Pastikan table tetap fullwidth
    const table = document.querySelector('.table-container table');
    if (table) {
        table.style.width = '100%';
        table.style.minWidth = '100%';
    }
    
    // Pastikan semua container fullwidth tetap konsisten
    const containers = document.querySelectorAll('.fullwidth-container');
    containers.forEach(container => {
        container.style.width = '100%';
        container.style.minWidth = '100%';
        container.style.boxSizing = 'border-box';
    });
    
    // Pastikan main content area tetap fullwidth
    const mainContent = document.querySelector('.p-6.space-y-6.fullwidth-container');
    if (mainContent) {
        mainContent.style.width = '100%';
        mainContent.style.minWidth = '100%';
    }
}

// Render inventory table
function renderInventoryTable(inventory) {
    const tbody = document.getElementById('inventory-tbody');
    
    if (!inventory || inventory.length === 0) {
        renderEmptyState();
        return;
    }

    tbody.innerHTML = inventory.map((item, index) => {
        // ... kode yang sama seperti sebelumnya untuk generate row ...
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
        
        return `
            <tr class="hover:bg-gray-50 transition-colors duration-200 fullwidth-container">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-semibold text-sm">
                            ${avatar}
                        </div>
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
                        <button onclick="viewDetail(${itemId}, '${tipeInventory}')" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                            Lihat
                        </button>
                        <button onclick="editItem(${itemId}, '${tipeInventory}')" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                            <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                            Edit
                        </button>
                        <button onclick="deleteItem(${itemId}, '${tipeInventory}', '${itemName.replace(/'/g, "\\\'")}')" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 transition-colors duration-200">
                            <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    // Render icons dan ensure fullwidth
    lucide.createIcons();
    setTimeout(() => {
        ensureFullwidthLayout();
    }, 10);
}
function rebuildInventoryView() {
    if (confirm('Rebuild inventory view? Ini akan memakan waktu beberapa detik.')) {
        fetch('<?= base_url("inventory/rebuild_inventory_view") ?>', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
                loadInventoryData(); // Reload data
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => {
            console.error('Rebuild error:', error);
            showToast('error', 'Gagal rebuild inventory view');
        });
    }
}
function debugFilter() {
    fetch('<?= base_url("inventory/debug_filter") ?>')
        .then(response => response.json())
        .then(data => {
            console.log('Debug filter data:', data);
            alert('Debug info logged to console');
        })
        .catch(error => {
            console.error('Debug error:', error);
        });
}
// Helper functions for badges and info
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
    return '-';
}


function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
}

// Form management
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
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 loading mr-2"></i>Menyimpan...';
    lucide.createIcons();
    
    try {
        const formData = new FormData(this);
        const isEdit = document.getElementById('edit-mode').value === '1';
        const url = isEdit ? 
            '<?= base_url("inventory/ajax_update_item/") ?>' + document.getElementById('item-id').value :
            '<?= base_url("inventory/ajax_create_item") ?>';
        
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
        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
        lucide.createIcons();
    }
});

// Search and filter functions
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
    
    console.log('Applying filters:', filters);
    
    // Show loading state
    showLoadingState();
    
    try {
        const params = new URLSearchParams();
        Object.keys(filters).forEach(key => {
            if (filters[key] && filters[key] !== '') {
                params.append(key, filters[key]);
            }
        });
        
        const url = '<?= base_url("inventory/get_filtered_inventory") ?>' + 
                   (params.toString() ? '?' + params.toString() : '');
        
        console.log('Filter URL:', url);
        
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
        console.log('Filter response:', data);
        
        if (data.success) {
            renderInventoryTable(data.inventory || []);
            updateInventoryCount(data.count || 0);
            
            // Show filter applied message
            if (Object.values(filters).some(f => f !== '')) {
                if (data.count > 0) {
                    showToast('success', `Filter diterapkan - ${data.count} item ditemukan`);
                } else {
                    showToast('info', 'Filter diterapkan - tidak ada data yang sesuai');
                }
            }
        } else {
            console.error('Filter error:', data.message);
            showToast('error', data.message || 'Gagal memfilter data');
            renderEmptyState();
        }
    } catch (error) {
        console.error('Error applying filters:', error);
        showToast('error', 'Terjadi kesalahan saat memfilter data: ' + error.message);
        renderEmptyState();
    } finally {
        hideLoadingState();
        // Ensure fullwidth setelah semua operasi selesai
        setTimeout(() => {
            ensureFullwidthLayout();
        }, 50);
    }
}

function hideLoadingState() {
    // Loading state akan otomatis terganti oleh hasil filter
}

function showLoadingState() {
    const tbody = document.getElementById('inventory-tbody');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="px-6 py-12 text-center">
                <div class="flex items-center justify-center space-x-2">
                    <i data-lucide="loader-2" class="w-5 h-5 text-blue-600 loading"></i>
                    <span class="text-gray-500">Memfilter data...</span>
                </div>
            </td>
        </tr>
    `;
    lucide.createIcons();
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

// Edit item with proper error handling
async function editItem(itemId, type) {
    try {
        const response = await fetch(`<?= base_url("inventory/ajax_get_item_details/") ?>${itemId}/${type}`);
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
    
    // Handle different property names
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

// View detail with better error handling
async function viewDetail(itemId, type) {
    try {
        const response = await fetch(`<?= base_url("inventory/ajax_get_item_details/") ?>${itemId}/${type}`);
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
    // Safely get item name and status
    const itemName = getItemName(item, type);
    const itemStatus = getItemStatus(item, type);
    const itemId = item.item_id || item.alat_id || item.reagen_id;
    
    if (!itemName || !itemId) {
        showToast('error', 'Data item tidak lengkap');
        return;
    }
    
    let content = `
        <div class="space-y-6">
            <!-- Item Header -->
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

            <!-- Basic Info -->
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
            <!-- Alat Details -->
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
            <!-- Reagen Details -->
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
            <!-- Action Buttons -->
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

// Delete item
async function deleteItem(itemId, type, name) {
    const result = await Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus item "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`<?= base_url("inventory/ajax_delete_item/") ?>${itemId}/${type}`, {
                method: 'POST'
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('success', data.message);
                loadInventoryData();
                loadStatistics();
            } else {
                showToast('error', data.message);
            }
        } catch (error) {
            console.error('Error deleting item:', error);
            showToast('error', 'Gagal menghapus item');
        }
    }
}

// Export functions
function exportInventory(type) {
    const filters = {
        type: type === 'all' ? '' : type,
        export_type: type
    };
    
    const params = new URLSearchParams(filters);
    const url = '<?= base_url("excel_inventory/export_inventory") ?>?' + params.toString();
    window.open(url, '_blank');
    
    // Close export menu
    document.getElementById('export-menu').classList.add('hidden');
}

// Utility functions
function showToast(type, message) {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const iconName = type === 'success' ? 'check-circle' : 'alert-circle';
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
    const iconColor = type === 'success' ? 'text-green-600' : 'text-red-600';
    const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
    
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
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    // Auto remove after 5 seconds
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

// Window resize handler to maintain fullwidth
window.addEventListener('resize', function() {
    ensureFullwidthLayout();
});
if (typeof MutationObserver !== 'undefined') {
    const layoutObserver = new MutationObserver(function(mutations) {
        let needsLayoutFix = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'attributes') {
                // Check if changes affect fullwidth elements
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
    
    // Start observing
    layoutObserver.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class']
    });
}
</script>

</body>
</html>