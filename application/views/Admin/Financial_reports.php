<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Labsys</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Custom scrollbar - konsisten dengan examination reports */
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

        /* Chart container */
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        /* Status badges */
        .status-lunas { @apply bg-green-100 text-green-800; }
        .status-belum_bayar { @apply bg-red-100 text-red-800; }
        .status-cicilan { @apply bg-yellow-100 text-yellow-800; }
    </style>
</head>
<body class="bg-gray-50">

<!-- Header Section - konsisten dengan examination reports -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="dollar-sign" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Laporan Keuangan</h1>
                <p class="text-emerald-100">Kelola dan analisis data keuangan laboratorium</p>
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

    <!-- Financial Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                    <p id="stat-total-revenue" class="text-2xl font-bold text-gray-900">-</p>
                    <p class="text-xs text-gray-500 mt-1">Keseluruhan</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-emerald-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendapatan Lunas</p>
                    <p id="stat-paid-revenue" class="text-2xl font-bold text-green-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Sudah dibayar</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Piutang</p>
                    <p id="stat-unpaid-revenue" class="text-2xl font-bold text-red-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Belum dibayar</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Cicilan</p>
                    <p id="stat-installment-revenue" class="text-2xl font-bold text-yellow-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Pembayaran cicilan</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Invoice</p>
                    <p id="stat-total-invoices" class="text-2xl font-bold text-blue-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Jumlah invoice</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tingkat Pelunasan</p>
                    <p id="stat-payment-rate" class="text-2xl font-bold text-purple-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Persentase lunas</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="percent" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i>
                <span>Trend Pendapatan (7 Hari Terakhir)</span>
            </h3>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Payment Status Distribution Chart -->
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
            <i data-lucide="filter" class="w-5 h-5 text-emerald-600"></i>
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
            <button onclick="applyFilters()" class="bg-blue-600 hover:bg-gray-100 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Terapkan Filter</span>
            </button>
            
            <button onclick="resetFilters()" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <!-- Financial Reports Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="dollar-sign" class="w-5 h-5 text-emerald-600"></i>
                    <span>Data Keuangan</span>
                    <span id="invoice-count" class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        0 invoice
                    </span>
                </h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" 
                               id="search-input"
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-64"
                               placeholder="Cari invoice..."
                               onkeyup="searchInvoices()">
                        <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                    </div>
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
                    <!-- Data akan dimuat secara dinamis -->
                    <tr id="loading-row">
                        <td colspan="9" class="px-6 py-12 text-center">
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
                <div class="flex items-center space-x-2" id="pagination-buttons">
                    <!-- Pagination buttons will be generated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Detail Invoice</h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div id="detail-content" class="p-6">
            <!-- Detail content will be populated dynamically -->
        </div>
    </div>
</div>

<script>
// Global variables
let allInvoices = [];
let financialStats = {};
let currentPage = 1;
let totalPages = 1;
let perPage = 20;
let revenueChart = null;
let paymentChart = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadFinancialData();
    initializeCharts();
    
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('end-date').value = today.toISOString().split('T')[0];
    document.getElementById('start-date').value = thirtyDaysAgo.toISOString().split('T')[0];
});

// Load financial data
async function loadFinancialData() {
    try {
        const filters = getCurrentFilters();
        const response = await fetch('<?= base_url("admin/ajax_get_financial_reports") ?>?' + new URLSearchParams({
            ...filters,
            page: currentPage,
            per_page: perPage
        }));
        
        const data = await response.json();
        
        if (data.success) {
            allInvoices = data.invoices;
            financialStats = data.stats;
            updateStatistics();
            renderInvoicesTable(allInvoices);
            updateInvoiceCount(data.total_records);
            updatePagination(data.pagination);
            updateCharts(data.chart_data);
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
        metode_pembayaran: document.getElementById('metode-filter').value,
        search: document.getElementById('search-input').value
    };
}

// Update statistics
function updateStatistics() {
    document.getElementById('stat-total-revenue').textContent = formatCurrency(financialStats.total_revenue || 0);
    document.getElementById('stat-paid-revenue').textContent = formatCurrency(financialStats.paid_revenue || 0);
    document.getElementById('stat-unpaid-revenue').textContent = formatCurrency(financialStats.unpaid_revenue || 0);
    document.getElementById('stat-installment-revenue').textContent = formatCurrency(financialStats.installment_revenue || 0);
    document.getElementById('stat-total-invoices').textContent = financialStats.total_invoices || 0;
    document.getElementById('stat-payment-rate').textContent = (financialStats.payment_rate || 0) + '%';
}

// Update invoice count
function updateInvoiceCount(count) {
    document.getElementById('invoice-count').textContent = `${count} invoice`;
}

// Render invoices table
function renderInvoicesTable(invoices) {
    const tbody = document.getElementById('invoices-table-body');
    
    if (invoices.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
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
        
        return `
            <tr class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${invoice.nomor_invoice}</div>
                    <div class="text-sm text-gray-500">ID: ${invoice.invoice_id}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${invoice.nama_pasien}</div>
                    <div class="text-sm text-gray-500">${invoice.nik || 'NIK tidak tersedia'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${invoice.nomor_pemeriksaan}</div>
                    <div class="text-sm text-gray-500">${invoice.jenis_pemeriksaan}</div>
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
                        <button onclick="viewDetail(${invoice.invoice_id})" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 transition-colors duration-200">
                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                            Detail
                        </button>
                        <button onclick="printInvoice(${invoice.invoice_id})" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                            <i data-lucide="printer" class="w-3 h-3 mr-1"></i>
                            Cetak
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    lucide.createIcons();
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
    currentPage = 1;
    loadFinancialData();
}

// Search invoices
function searchInvoices() {
    currentPage = 1;
    loadFinancialData();
}

// Initialize charts
function initializeCharts() {
    // Revenue Chart
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

    // Payment Chart
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

// View invoice detail
async function viewDetail(invoiceId) {
    try {
        const response = await fetch(`<?= base_url("admin/ajax_get_invoice_detail") ?>/${invoiceId}`);
        const data = await response.json();
        
        if (data.success) {
            populateDetailModal(data.invoice, data.examination);
            document.getElementById('detail-modal').classList.remove('hidden');
        } else {
            showFlashMessage('error', data.message);
        }
    } catch (error) {
        console.error('Error loading invoice detail:', error);
        showFlashMessage('error', 'Gagal memuat detail invoice');
    }
}

// Populate detail modal
function populateDetailModal(invoice, examination) {
    const content = `
        <div class="space-y-6">
            <!-- Invoice Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-emerald-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Informasi Invoice</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Nomor Invoice:</span> ${invoice.nomor_invoice}</div>
                        <div><span class="font-medium">Tanggal Invoice:</span> ${formatDate(invoice.tanggal_invoice)}</div>
                        <div><span class="font-medium">Jenis Pembayaran:</span> ${invoice.jenis_pembayaran.toUpperCase()}</div>
                        <div><span class="font-medium">Total Biaya:</span> ${formatCurrency(invoice.total_biaya)}</div>
                        <div><span class="font-medium">Status:</span> 
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium status-${invoice.status_pembayaran}">
                                ${invoice.status_pembayaran}
                            </span>
                        </div>
                        <div><span class="font-medium">Metode Pembayaran:</span> ${invoice.metode_pembayaran || 'Belum ditentukan'}</div>
                        ${invoice.tanggal_pembayaran ? `<div><span class="font-medium">Tanggal Bayar:</span> ${formatDate(invoice.tanggal_pembayaran)}</div>` : ''}
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Informasi Pasien</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Nama:</span> ${invoice.nama_pasien}</div>
                        <div><span class="font-medium">NIK:</span> ${invoice.nik || 'Tidak tersedia'}</div>
                        <div><span class="font-medium">Nomor Pemeriksaan:</span> ${invoice.nomor_pemeriksaan}</div>
                        <div><span class="font-medium">Jenis Pemeriksaan:</span> ${invoice.jenis_pemeriksaan}</div>
                    </div>
                </div>
            </div>

            <!-- BPJS Information (if applicable) -->
            ${invoice.jenis_pembayaran === 'bpjs' ? `
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Informasi BPJS</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="font-medium">Nomor Kartu BPJS:</span> ${invoice.nomor_kartu_bpjs || 'Tidak tersedia'}</div>
                        <div><span class="font-medium">Nomor SEP:</span> ${invoice.nomor_sep || 'Tidak tersedia'}</div>
                    </div>
                </div>
            ` : ''}

            <!-- Notes -->
            ${invoice.keterangan ? `
                <div class="bg-yellow-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Keterangan</h4>
                    <div class="text-sm text-gray-700">${invoice.keterangan}</div>
                </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('detail-content').innerHTML = content;
}

// Close detail modal
function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

// Print invoice
function printInvoice(invoiceId) {
    window.open(`<?= base_url("PDF_Controller/print_invoice") ?>/${invoiceId}`, '_blank');

}

function exportToExcel() {
    const filters = getCurrentFilters();
    const params = new URLSearchParams(filters);
    window.open(`<?= base_url("excel_controller/export_financial_reports") ?>?${params}`, '_blank');
}

// Tambahan untuk export ringkasan
function exportSummaryToExcel() {
    const filters = getCurrentFilters();
    const params = new URLSearchParams(filters);
    window.open(`<?= base_url("excel_controller/export_financial_summary") ?>?${params}`, '_blank');
}

function exportToPDF() {
    const filters = getCurrentFilters();
    const params = new URLSearchParams(filters);
    window.open(`<?= base_url("admin/export_financial_pdf") ?>?${params}`, '_blank');
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

    // Generate pagination buttons
    const buttons = document.getElementById('pagination-buttons');
    buttons.innerHTML = '';

    // Previous button
    if (currentPage > 1) {
        buttons.appendChild(createPaginationButton('‹', currentPage - 1));
    }

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        buttons.appendChild(createPaginationButton(i, i, i === currentPage));
    }

    // Next button
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
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 5000);
}
</script>

</body>
</html>