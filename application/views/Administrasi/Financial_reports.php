<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - LabSy</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        .status-lunas { @apply bg-green-100 text-green-800; }
        .status-belum_bayar { @apply bg-red-100 text-red-800; }
        .status-cicilan { @apply bg-yellow-100 text-yellow-800; }
        
        mark {
            background-color: #fef08a;
            padding: 2px 4px;
            border-radius: 2px;
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="dollar-sign" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Laporan Keuangan</h1>
                <p class="text-blue-100">Kelola dan analisis data keuangan laboratorium</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="exportToExcel()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Export Data</span>
            </button>
            <button onclick="exportSummaryToExcel()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                <span>Export Ringkasan</span>
            </button>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="p-6 space-y-6">
    
    <!-- Flash Messages Container -->
    <div id="flash-messages"></div>

>
<div class="grid grid-cols-1 md:grid-cols-5 gap-6">
    <!-- Total Pendapatan -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 card-hover">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-600 mb-1.5">Total Pendapatan</p>
                <p id="stat-total-revenue" class="responsive-number font-bold text-gray-900 mb-1">-</p>
                <p class="text-xs text-gray-500 truncate">Keseluruhan</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                <i data-lucide="trending-up" class="w-6 h-6 text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Pendapatan Lunas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 card-hover">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-600 mb-1.5">Pendapatan Lunas</p>
                <p id="stat-paid-revenue" class="responsive-number font-bold text-emerald-600 mb-1">-</p>
                <p class="text-xs text-gray-500 truncate">Sudah dibayar</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Piutang -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 card-hover">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-600 mb-1.5">Piutang</p>
                <p id="stat-unpaid-revenue" class="responsive-number font-bold text-red-600 mb-1">-</p>
                <p class="text-xs text-gray-500 truncate">Belum dibayar</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Invoice -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 card-hover">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-600 mb-1.5">Total Invoice</p>
                <p id="stat-total-invoices" class="responsive-number font-bold text-purple-600 mb-1">-</p>
                <p class="text-xs text-gray-500 truncate">Jumlah invoice</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                <i data-lucide="file-text" class="w-6 h-6 text-purple-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Tingkat Pelunasan -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 card-hover">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-600 mb-1.5">Tingkat Pelunasan</p>
                <p id="stat-payment-rate" class="responsive-number font-bold text-orange-600 mb-1">-</p>
                <p class="text-xs text-gray-500 truncate">Persentase lunas</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                <i data-lucide="percent" class="w-6 h-6 text-orange-600"></i>
            </div>
        </div>
    </div>
</div>
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i>
                <span>Trend Pendapatan (7 Hari Terakhir)</span>
            </h3>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-emerald-600"></i>
                <span>Distribusi Status Pembayaran</span>
            </h3>
            <div class="chart-container">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
            <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
            <span>Filter Laporan Keuangan</span>
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" id="start-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" id="end-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="lunas">Lunas</option>
                    <option value="belum_bayar">Belum Bayar</option>
                    <option value="cicilan">Cicilan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pembayaran</label>
                <select id="jenis-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Jenis</option>
                    <option value="umum">Umum</option>
                    <option value="bpjs">BPJS</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                <select id="metode-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Metode</option>
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                    <option value="debit">Debit</option>
                    <option value="credit">Credit Card</option>
                </select>
            </div>
        </div>
        
        <div class="mt-4 flex items-center space-x-4">
            <button onclick="applyFilters()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Terapkan Filter</span>
            </button>
            
            <button onclick="resetFilters()" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center space-x-4">
            <div class="relative flex-1">
                <input type="text" 
                       id="search-input"
                       class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                       placeholder="Cari nomor invoice, nama pasien, NIK, atau nomor pemeriksaan..."
                       onkeyup="searchInvoices()">
                <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
            </div>
            <button onclick="resetSearch()" 
                    class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Reset</span>
            </button>
        </div>
        <div id="search-info" class="hidden mt-2 text-sm text-emerald-600">
            <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
            <span id="search-result-text"></span>
        </div>
    </div>

    <!-- Financial Reports Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="dollar-sign" class="w-5 h-5 text-blue-600"></i>
                    <span>Data Keuangan</span>
                    <span id="invoice-count" class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        0 invoice
                    </span>
                </h2>
                <div class="flex items-center space-x-4">
                    <button onclick="loadFinancialData()" 
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            title="Refresh Data">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="invoices-table-body" class="bg-white divide-y divide-gray-200">
                    <tr id="loading-row">
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <i data-lucide="loader-2" class="w-5 h-5 text-emerald-600 loading"></i>
                                <span class="text-gray-500">Memuat data keuangan...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination-container" class="px-6 py-4 border-t border-gray-100 bg-gray-50 hidden">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan <span id="showing-start">1</span> - <span id="showing-end">10</span> dari <span id="total-records">0</span> invoice
                </div>
                <div class="flex items-center space-x-2" id="pagination-buttons"></div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Invoice Modal (NEW) -->
<div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-5xl w-full max-h-[95vh] overflow-y-auto custom-scrollbar">
        <div class="p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Preview Invoice</h3>
                <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <div id="preview-content" class="p-6">
            <!-- Loading state -->
            <div id="preview-loading" class="flex items-center justify-center py-12">
                <div class="text-center">
                    <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 loading mx-auto mb-4"></i>
                    <p class="text-gray-500">Memuat detail invoice...</p>
                </div>
            </div>

            <!-- Invoice content will be loaded here -->
            <div id="preview-invoice-content" class="hidden space-y-6">
                <!-- Header Info -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-blue-100 text-sm mb-1">Nomor Invoice</p>
                            <p id="preview-invoice-number" class="text-xl font-bold">-</p>
                        </div>
                        <div>
                            <p class="text-blue-100 text-sm mb-1">Tanggal Invoice</p>
                            <p id="preview-invoice-date" class="text-xl font-bold">-</p>
                        </div>
                        <div>
                            <p class="text-blue-100 text-sm mb-1">Status Pembayaran</p>
                            <p id="preview-payment-status" class="text-xl font-bold">-</p>
                        </div>
                    </div>
                </div>

                <!-- Patient & Exam Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-emerald-50 rounded-lg p-6 border border-emerald-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                            <i data-lucide="user" class="w-5 h-5 text-emerald-600"></i>
                            <span>Informasi Pasien</span>
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama:</span>
                                <span id="preview-patient-name" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">NIK:</span>
                                <span id="preview-patient-nik" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Umur:</span>
                                <span id="preview-patient-age" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Telepon:</span>
                                <span id="preview-patient-phone" class="font-medium text-gray-900">-</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                            <i data-lucide="clipboard-list" class="w-5 h-5 text-blue-600"></i>
                            <span>Informasi Pemeriksaan</span>
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor:</span>
                                <span id="preview-exam-number" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jenis:</span>
                                <span id="preview-exam-type" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span id="preview-exam-date" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jenis Pembayaran:</span>
                                <span id="preview-payment-type" class="font-medium text-gray-900">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-700 to-gray-800 text-white px-6 py-3">
                        <h4 class="font-semibold flex items-center space-x-2">
                            <i data-lucide="list" class="w-5 h-5"></i>
                            <span>Rincian Biaya Pemeriksaan</span>
                        </h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Item Pemeriksaan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Hasil</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">Harga</th>
                                </tr>
                            </thead>
                            <tbody id="preview-breakdown-table" class="divide-y divide-gray-200">
                                <!-- Breakdown items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total -->
                <div class="flex justify-end">
                    <div class="w-96 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg p-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold">TOTAL BIAYA:</span>
                            <span id="preview-total-amount" class="text-3xl font-bold">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Form (if not confirmed) -->
                <div id="confirmation-form-section" class="hidden border-t-4 border-blue-600 bg-blue-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-blue-600"></i>
                        <span>Konfirmasi Pembayaran</span>
                    </h4>
                    <form id="confirmation-form" class="space-y-4">
                        <input type="hidden" id="confirm-invoice-id">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran *</label>
                                <select id="confirm-status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Pilih Status</option>
                                    <option value="lunas">Lunas</option>
                                    <option value="belum_bayar">Belum Bayar</option>
                                    <option value="cicilan">Cicilan</option>
                                </select>
                            </div>
                            
                            <div id="confirm-method-group" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran *</label>
                                <select id="confirm-method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Metode</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="debit">Debit</option>
                                    <option value="credit">Credit Card</option>
                                </select>
                            </div>
                        </div>
                        
                        <div id="confirm-date-group" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran *</label>
                            <input type="date" id="confirm-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <textarea id="confirm-notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Keterangan tambahan (opsional)"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closePreviewModal()" class="px-6 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span>Konfirmasi Pembayaran</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Payment Info (if already confirmed) -->
                <div id="payment-info-section" class="hidden bg-green-50 border border-green-200 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                        <i data-lucide="credit-card" class="w-5 h-5 text-green-600"></i>
                        <span>Informasi Pembayaran</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <span class="text-sm text-gray-600">Status:</span>
                            <p id="info-payment-status" class="font-medium text-gray-900 mt-1">-</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Metode Pembayaran:</span>
                            <p id="info-payment-method" class="font-medium text-gray-900 mt-1">-</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Tanggal Pembayaran:</span>
                            <p id="info-payment-date" class="font-medium text-gray-900 mt-1">-</p>
                        </div>
                    </div>
                    
                    <!-- Print Button (only if confirmed with method) -->
                    <div id="print-button-section" class="hidden flex justify-end pt-4 border-t border-green-200">
                        <button onclick="printInvoiceFromPreview()" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-lg">
                            <i data-lucide="printer" class="w-5 h-5"></i>
                            <span>Cetak Invoice</span>
                        </button>
                    </div>
                </div>

                <!-- BPJS Info (if applicable) -->
                <div id="bpjs-info-section" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                        <i data-lucide="hospital" class="w-5 h-5 text-yellow-600"></i>
                        <span>Informasi BPJS</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-600">Nomor Kartu BPJS:</span>
                            <p id="info-bpjs-card" class="font-medium text-gray-900 mt-1">-</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Nomor SEP:</span>
                            <p id="info-bpjs-sep" class="font-medium text-gray-900 mt-1">-</p>
                        </div>
                    </div>
                </div>

                <!-- Notes (if any) -->
                <div id="notes-section" class="hidden bg-gray-50 border-l-4 border-gray-400 rounded-r-lg p-6">
                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center space-x-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-gray-600"></i>
                        <span>Catatan</span>
                    </h4>
                    <p id="info-notes" class="text-sm text-gray-700 whitespace-pre-wrap"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url() ?>';

// Global variables
let allInvoices = [];
let financialStats = {};
let currentPage = 1;
let totalPages = 1;
let perPage = 20;
let revenueChart = null;
let paymentChart = null;
let searchTimeout;
let currentSearch = '';
let totalInvoices = 0;
let currentPreviewInvoiceId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadFinancialData();
    initializeCharts();
    setupConfirmationFormHandlers();
    
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('end-date').value = today.toISOString().split('T')[0];
    document.getElementById('start-date').value = thirtyDaysAgo.toISOString().split('T')[0];
    
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam) {
        document.getElementById('search-input').value = searchParam;
        currentSearch = searchParam;
    }
});

// Setup confirmation form handlers
function setupConfirmationFormHandlers() {
    document.getElementById('confirm-status').addEventListener('change', function() {
        const status = this.value;
        const methodGroup = document.getElementById('confirm-method-group');
        const dateGroup = document.getElementById('confirm-date-group');
        
        if (status === 'lunas') {
            methodGroup.classList.remove('hidden');
            dateGroup.classList.remove('hidden');
            document.getElementById('confirm-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('confirm-method').required = true;
            document.getElementById('confirm-date').required = true;
        } else {
            methodGroup.classList.add('hidden');
            dateGroup.classList.add('hidden');
            document.getElementById('confirm-method').value = '';
            document.getElementById('confirm-date').value = '';
            document.getElementById('confirm-method').required = false;
            document.getElementById('confirm-date').required = false;
        }
    });
    
    document.getElementById('confirmation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        confirmPayment();
    });
}

// Load financial data
async function loadFinancialData() {
    try {
        const filters = getCurrentFilters();
        
        // DEBUG: Log filters being applied
        console.log('Loading financial data with filters:', filters);
        
        const response = await fetch(BASE_URL + 'administrasi_laporan/ajax_get_financial_reports?' + new URLSearchParams({
            ...filters,
            page: currentPage,
            per_page: perPage
        }));
        
        const data = await response.json();
        
        // DEBUG: Log received data
        console.log('Received data:', {
            success: data.success,
            invoice_count: data.invoices ? data.invoices.length : 0,
            total_records: data.total_records
        });
        
        if (data.success) {
            allInvoices = data.invoices;
            financialStats = data.stats;
            totalInvoices = data.total_records;
            updateStatistics();
            renderInvoicesTable(allInvoices);
            updateInvoiceCount(data.total_records);
            updatePagination(data.pagination);
            updateCharts(data.chart_data);
            
            // Don't auto-apply search after data load unless explicitly set
            // This was causing the filtering issue
        } else {
            showFlashMessage('error', 'Gagal memuat data keuangan');
        }
    } catch (error) {
        console.error('Error loading financial data:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memuat data');
    }
}

// Get current filters
function getCurrentFilters() {
    return {
        start_date: document.getElementById('start-date').value,
        end_date: document.getElementById('end-date').value,
        status: document.getElementById('status-filter').value,
        jenis_pembayaran: document.getElementById('jenis-filter').value,
        metode_pembayaran: document.getElementById('metode-filter').value
    };
}

// Update statistics
function updateStatistics() {
    const elements = {
        'stat-total-revenue': formatCurrency(financialStats.total_revenue || 0),
        'stat-paid-revenue': formatCurrency(financialStats.paid_revenue || 0),
        'stat-unpaid-revenue': formatCurrency(financialStats.unpaid_revenue || 0),
        'stat-installment-revenue': formatCurrency(financialStats.installment_revenue || 0),
        'stat-total-invoices': financialStats.total_invoices || 0,
        'stat-payment-rate': (financialStats.payment_rate || 0) + '%'
    };
    
    for (const [id, value] of Object.entries(elements)) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    }
}

// Update invoice count
function updateInvoiceCount(count) {
    const element = document.getElementById('invoice-count');
    if (element) {
        element.textContent = `${count} invoice`;
    }
}

// Render invoices table
function renderInvoicesTable(invoices) {
    const tbody = document.getElementById('invoices-table-body');
    
    // DEBUG: Log rendering
    console.log('renderInvoicesTable() called with', invoices.length, 'invoices');
    
    if (invoices.length === 0) {
        tbody.innerHTML = `
            <tr id="empty-state-default">
                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center space-y-2">
                        <i data-lucide="file-x" class="w-12 h-12 text-gray-300"></i>
                        <span>Tidak ada data invoice ditemukan</span>
                    </div>
                </td>
            </tr>
        `;
        lucide.createIcons();
        return;
    }

    tbody.innerHTML = invoices.map(invoice => {
        const statusColors = {
            'lunas': 'bg-green-100 text-green-800',
            'belum_bayar': 'bg-red-100 text-red-800',
            'cicilan': 'bg-yellow-100 text-yellow-800'
        };

        const statusNames = {
            'lunas': 'Lunas',
            'belum_bayar': 'Belum Bayar',
            'cicilan': 'Cicilan'
        };
        
        // Check if can print: lunas + has payment method
        const canPrint = invoice.status_pembayaran === 'lunas' && invoice.metode_pembayaran;
        
        return `
            <tr class="hover:bg-gray-50 transition-colors duration-200 invoice-row">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 invoice-number">${invoice.nomor_invoice}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 patient-name">${invoice.nama_pasien}</div>
                    <div class="text-sm text-gray-500 patient-nik">${invoice.nik || 'NIK tidak tersedia'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 exam-number">${invoice.nomor_pemeriksaan}</div>
                    <div class="text-sm text-gray-500 exam-type">${invoice.jenis_pemeriksaan}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${formatDate(invoice.tanggal_invoice)}</div>
                    ${invoice.tanggal_pembayaran ? `<div class="text-sm text-gray-500">Bayar: ${formatDate(invoice.tanggal_pembayaran)}</div>` : ''}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${formatCurrency(invoice.total_biaya)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[invoice.status_pembayaran]}">
                        ${statusNames[invoice.status_pembayaran]}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${invoice.metode_pembayaran || '-'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        <button onclick="viewPreview(${invoice.invoice_id})" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                            Preview
                        </button>
                        ${canPrint ? `
                        <button onclick="printInvoice(${invoice.invoice_id})" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                            <i data-lucide="printer" class="w-3 h-3 mr-1"></i>
                            Cetak
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    lucide.createIcons();
    
    // Only re-apply search filter if there's an active search
    // and only after icons are initialized
    if (currentSearch && currentSearch.length > 0) {
        console.log('Re-applying search filter for:', currentSearch);
        setTimeout(() => filterTable(), 100);
    }
}

// View invoice preview
async function viewPreview(invoiceId) {
    try {
        currentPreviewInvoiceId = invoiceId;
        
        // Show modal
        document.getElementById('preview-modal').classList.remove('hidden');
        document.getElementById('preview-loading').classList.remove('hidden');
        document.getElementById('preview-invoice-content').classList.add('hidden');
        
        // Fetch invoice data with breakdown
        const response = await fetch(BASE_URL + `administrasi/ajax_get_invoice_breakdown/${invoiceId}`);
        const data = await response.json();
        
        if (data.success && data.invoice) {
            populatePreviewModal(data.invoice);
        } else {
            showFlashMessage('error', data.message || 'Gagal memuat detail invoice');
            closePreviewModal();
        }
    } catch (error) {
        console.error('Error loading invoice preview:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memuat preview');
        closePreviewModal();
    }
}

// Populate preview modal
function populatePreviewModal(invoice) {
    // Hide loading, show content
    document.getElementById('preview-loading').classList.add('hidden');
    document.getElementById('preview-invoice-content').classList.remove('hidden');
    
    // Header info
    document.getElementById('preview-invoice-number').textContent = invoice.nomor_invoice;
    document.getElementById('preview-invoice-date').textContent = formatDate(invoice.tanggal_invoice);
    
    const statusNames = {
        'lunas': 'LUNAS',
        'belum_bayar': 'BELUM BAYAR',
        'cicilan': 'CICILAN'
    };
    document.getElementById('preview-payment-status').textContent = statusNames[invoice.status_pembayaran] || invoice.status_pembayaran.toUpperCase();
    
    // Patient info
    document.getElementById('preview-patient-name').textContent = invoice.nama_pasien;
    document.getElementById('preview-patient-nik').textContent = invoice.nik || 'Tidak tersedia';
    document.getElementById('preview-patient-age').textContent = invoice.umur ? `${invoice.umur} tahun` : '-';
    document.getElementById('preview-patient-phone').textContent = invoice.telepon || '-';
    
    // Exam info
    document.getElementById('preview-exam-number').textContent = invoice.nomor_pemeriksaan;
    document.getElementById('preview-exam-type').textContent = invoice.jenis_pemeriksaan;
    document.getElementById('preview-exam-date').textContent = formatDate(invoice.tanggal_pemeriksaan);
    document.getElementById('preview-payment-type').textContent = invoice.jenis_pembayaran.toUpperCase();
    
    // Breakdown table
    const breakdownTable = document.getElementById('preview-breakdown-table');
    if (invoice.cost_breakdown && invoice.cost_breakdown.length > 0) {
        breakdownTable.innerHTML = invoice.cost_breakdown.map(item => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${item.item}</td>
                <td class="px-4 py-3 text-sm text-emerald-600 font-medium">${item.hasil || '-'}</td>
                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">${formatCurrency(item.harga)}</td>
            </tr>
        `).join('');
    } else {
        breakdownTable.innerHTML = `
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-gray-500 italic">
                    Belum ada detail breakdown biaya
                </td>
            </tr>
        `;
    }
    
    // Total
    document.getElementById('preview-total-amount').textContent = formatCurrency(invoice.total_biaya);
    
    // Check if payment confirmed
    const isConfirmed = invoice.status_pembayaran === 'lunas' && invoice.metode_pembayaran;
    
    if (isConfirmed) {
        // Show payment info section
        document.getElementById('confirmation-form-section').classList.add('hidden');
        document.getElementById('payment-info-section').classList.remove('hidden');
        
        document.getElementById('info-payment-status').textContent = statusNames[invoice.status_pembayaran];
        document.getElementById('info-payment-method').textContent = invoice.metode_pembayaran || '-';
        document.getElementById('info-payment-date').textContent = invoice.tanggal_pembayaran ? formatDate(invoice.tanggal_pembayaran) : '-';
        
        // Show print button
        document.getElementById('print-button-section').classList.remove('hidden');
    } else {
        // Show confirmation form
        document.getElementById('confirmation-form-section').classList.remove('hidden');
        document.getElementById('payment-info-section').classList.add('hidden');
        
        document.getElementById('confirm-invoice-id').value = invoice.invoice_id;
        document.getElementById('confirm-status').value = invoice.status_pembayaran;
        document.getElementById('confirm-method').value = invoice.metode_pembayaran || '';
        document.getElementById('confirm-date').value = invoice.tanggal_pembayaran || '';
        document.getElementById('confirm-notes').value = invoice.keterangan || '';
        
        // Trigger change event to show/hide method and date fields
        document.getElementById('confirm-status').dispatchEvent(new Event('change'));
    }
    
    // BPJS info
    if (invoice.jenis_pembayaran === 'bpjs') {
        document.getElementById('bpjs-info-section').classList.remove('hidden');
        document.getElementById('info-bpjs-card').textContent = invoice.nomor_kartu_bpjs || 'Tidak tersedia';
        document.getElementById('info-bpjs-sep').textContent = invoice.nomor_sep || 'Tidak tersedia';
    } else {
        document.getElementById('bpjs-info-section').classList.add('hidden');
    }
    
    // Notes
    if (invoice.keterangan) {
        document.getElementById('notes-section').classList.remove('hidden');
        document.getElementById('info-notes').textContent = invoice.keterangan;
    } else {
        document.getElementById('notes-section').classList.add('hidden');
    }
    
    // Reinitialize icons
    lucide.createIcons();
}

// Confirm payment
async function confirmPayment() {
    try {
        const formData = {
            invoice_id: document.getElementById('confirm-invoice-id').value,
            status: document.getElementById('confirm-status').value,
            metode_pembayaran: document.getElementById('confirm-method').value,
            tanggal_pembayaran: document.getElementById('confirm-date').value,
            keterangan: document.getElementById('confirm-notes').value
        };
        
        // Validation
        if (!formData.status) {
            showFlashMessage('error', 'Status pembayaran harus dipilih');
            return;
        }
        
        if (formData.status === 'lunas' && !formData.metode_pembayaran) {
            showFlashMessage('error', 'Metode pembayaran harus dipilih untuk status lunas');
            return;
        }
        
        const response = await fetch(BASE_URL + 'administrasi_laporan/ajax_update_payment_status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            // 1. TUTUP MODAL TERLEBIH DAHULU
            closePreviewModal();
            
            // 2. TAMPILKAN NOTIFIKASI BERHASIL (ALERT)
            showFlashMessage('success', 'Pembayaran berhasil dikonfirmasi!');
            
            // 3. RELOAD DATA (AUTO REFRESH) - menampilkan data terbaru
            await loadFinancialData();
            
            // 4. Reset search dan pastikan semua rows tampil
            currentSearch = '';
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.value = '';
            }
            
            // 5. Scroll ke atas untuk melihat notifikasi
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            console.log('Payment confirmed and data refreshed successfully');
        } else {
            showFlashMessage('error', data.message || 'Gagal mengkonfirmasi pembayaran');
        }
    } catch (error) {
        console.error('Error confirming payment:', error);
        showFlashMessage('error', 'Terjadi kesalahan sistem');
    }
}

// Close preview modal
function closePreviewModal() {
    document.getElementById('preview-modal').classList.add('hidden');
    currentPreviewInvoiceId = null;
}

// Print invoice from preview
function printInvoiceFromPreview() {
    if (currentPreviewInvoiceId) {
        printInvoice(currentPreviewInvoiceId);
    }
}

// Print invoice
function printInvoice(invoiceId) {
    window.open(BASE_URL + `PDF_Controller/print_invoice/${invoiceId}`, '_blank');
}

// Search with debouncing
function searchInvoices() {
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
    const tbody = document.getElementById('invoices-table-body');
    const rows = tbody.querySelectorAll('.invoice-row');
    
    // DEBUG: Log filter execution
    console.log('filterTable() called with searchTerm:', searchTerm, 'Total rows:', rows.length);
    
    // If no search term, show all rows
    if (!searchTerm || searchTerm.length === 0) {
        rows.forEach(row => {
            row.style.display = '';
        });
        updateInvoiceCount(rows.length);
        showSearchInfo(0);
        showEmptyState(false);
        return;
    }
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        const invoiceNumber = row.querySelector('.invoice-number')?.textContent.toLowerCase() || '';
        const patientName = row.querySelector('.patient-name')?.textContent.toLowerCase() || '';
        const patientNik = row.querySelector('.patient-nik')?.textContent.toLowerCase() || '';
        const examNumber = row.querySelector('.exam-number')?.textContent.toLowerCase() || '';
        const examType = row.querySelector('.exam-type')?.textContent.toLowerCase() || '';
        
        const match = invoiceNumber.includes(searchTerm) || 
                     patientName.includes(searchTerm) || 
                     patientNik.includes(searchTerm) ||
                     examNumber.includes(searchTerm) ||
                     examType.includes(searchTerm);
        
        if (match) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    console.log('Visible rows after filter:', visibleCount);
    
    updateInvoiceCount(visibleCount);
    showSearchInfo(visibleCount);
    showEmptyState(visibleCount === 0 && rows.length > 0);
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
    const tbody = document.getElementById('invoices-table-body');
    let emptyRow = tbody.querySelector('.search-empty-state');
    const defaultEmpty = tbody.querySelector('#empty-state-default');
    
    if (defaultEmpty) {
        defaultEmpty.style.display = show ? 'none' : '';
    }
    
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
                       class="mt-4 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
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

// Apply filters
function applyFilters() {
    currentPage = 1;
    loadFinancialData();
}

// Reset filters
function resetFilters() {
    document.getElementById('start-date').value = '';
    document.getElementById('end-date').value = '';
    document.getElementById('status-filter').value = '';
    document.getElementById('jenis-filter').value = '';
    document.getElementById('metode-filter').value = '';
    document.getElementById('search-input').value = '';
    currentSearch = '';
    currentPage = 1;
    loadFinancialData();
}

// Initialize charts
function initializeCharts() {
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Pendapatan per Hari',
                data: [],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    paymentChart = new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Belum Bayar', 'Cicilan'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: [
                    'rgb(34, 197, 94)',
                    'rgb(239, 68, 68)',
                    'rgb(251, 191, 36)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Update charts
function updateCharts(chartData) {
    if (chartData && chartData.revenue) {
        revenueChart.data.labels = chartData.revenue.map(item => item.date);
        revenueChart.data.datasets[0].data = chartData.revenue.map(item => item.total);
        revenueChart.update();
    }

    if (chartData && chartData.payment_status) {
        paymentChart.data.datasets[0].data = [
            chartData.payment_status.lunas || 0,
            chartData.payment_status.belum_bayar || 0,
            chartData.payment_status.cicilan || 0
        ];
        paymentChart.update();
    }
}

// Export functions
function exportToExcel() {
    const filters = getCurrentFilters();
    const params = new URLSearchParams(filters);
    window.open(BASE_URL + `excel_controller/export_financial_reports?${params}`, '_blank');
}

function exportSummaryToExcel() {
    const filters = getCurrentFilters();
    const params = new URLSearchParams(filters);
    window.open(BASE_URL + `excel_controller/export_financial_summary?${params}`, '_blank');
}

// Update pagination
function updatePagination(pagination) {
    if (pagination.total_pages <= 1) {
        document.getElementById('pagination-container').classList.add('hidden');
        return;
    }

    document.getElementById('pagination-container').classList.remove('hidden');
    document.getElementById('showing-start').textContent = ((pagination.current_page - 1) * pagination.per_page) + 1;
    document.getElementById('showing-end').textContent = Math.min(pagination.current_page * pagination.per_page, pagination.total_records);
    document.getElementById('total-records').textContent = pagination.total_records;

    currentPage = pagination.current_page;
    totalPages = pagination.total_pages;

    const buttons = document.getElementById('pagination-buttons');
    buttons.innerHTML = '';

    if (currentPage > 1) {
        buttons.appendChild(createPaginationButton('‹', currentPage - 1));
    }

    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        buttons.appendChild(createPaginationButton(i, i, i === currentPage));
    }

    if (currentPage < totalPages) {
        buttons.appendChild(createPaginationButton('›', currentPage + 1));
    }
}

// Create pagination button
function createPaginationButton(text, page, isActive = false) {
    const button = document.createElement('button');
    button.textContent = text;
    button.className = `px-3 py-1 text-sm rounded-md ${isActive ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'} border border-gray-300`;
    button.onclick = () => {
        currentPage = page;
        loadFinancialData();
    };
    return button;
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
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
            alert.remove();
        }
    }, 5000);
}

// ESC key & backdrop close
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePreviewModal();
    }
});

document.getElementById('preview-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreviewModal();
    }
});
</script>

</body>
</html>