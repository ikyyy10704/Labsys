<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemeriksaan - LabSy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Custom scrollbar - konsisten dengan user management */
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
        .status-pending { @apply bg-yellow-100 text-yellow-800; }
        .status-progress { @apply bg-blue-100 text-blue-800; }
        .status-selesai { @apply bg-green-100 text-green-800; }
        .status-cancelled { @apply bg-red-100 text-red-800; }
    </style>
</head>
<body class="bg-gray-50">

<!-- Header Section - disesuaikan untuk administrasi -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="file-bar-chart" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Laporan Pemeriksaan</h1>
                <p class="text-white">Kelola dan analisis data pemeriksaan laboratorium</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="exportToExcel()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Export Excel</span>
            </button>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="p-6 space-y-6">
    
    <!-- Flash Messages Container -->
    <div id="flash-messages"></div>

    <!-- Statistics Cards - konsisten dengan user management -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pemeriksaan</p>
                    <p id="stat-total" class="text-2xl font-bold text-gray-900">-</p>
                    <p class="text-xs text-gray-500 mt-1">Semua pemeriksaan</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p id="stat-pending" class="text-2xl font-bold text-yellow-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Menunggu proses</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Dalam Proses</p>
                    <p id="stat-progress" class="text-2xl font-bold text-blue-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Sedang dikerjakan</p>
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
                    <p id="stat-completed" class="text-2xl font-bold text-green-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Pemeriksaan selesai</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Dibatalkan</p>
                    <p id="stat-cancelled" class="text-2xl font-bold text-red-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Pemeriksaan batal</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                <span>Trend Pemeriksaan (7 Hari Terakhir)</span>
            </h3>
            <div class="chart-container">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-blue-600"></i>
                <span>Distribusi Status Pemeriksaan</span>
            </h3>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
            <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
            <span>Filter Laporan</span>
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" id="start-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" id="end-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="progress">Dalam Proses</option>
                    <option value="selesai">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pemeriksaan</label>
                <select id="jenis-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="Kimia Darah">Kimia Darah</option>
                    <option value="Hematologi">Hematologi</option>
                    <option value="Urinologi">Urinologi</option>
                    <option value="Serologi">Serologi</option>
                    <option value="TBC">TBC</option>
                    <option value="IMS">IMS</option>
                    <option value="MLS">MLS</option>
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

    <!-- Examination Reports Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="file-bar-chart" class="w-5 h-5 text-blue-600"></i>
                    <span>Data Pemeriksaan</span>
                    <span id="exam-count" class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        0 pemeriksaan
                    </span>
                </h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" 
                               id="search-input"
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64"
                               placeholder="Cari pemeriksaan..."
                               onkeyup="searchExaminations()">
                        <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                    </div>
                    <button onclick="loadExaminationData()" 
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pemeriksaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pemeriksaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="examinations-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- Data akan dimuat secara dinamis -->
                    <tr id="loading-row">
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <i data-lucide="loader-2" class="w-5 h-5 text-blue-600 loading"></i>
                                <span class="text-gray-500">Memuat data pemeriksaan...</span>
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
                    Menampilkan <span id="showing-start">1</span> - <span id="showing-end">10</span> dari <span id="total-records">0</span> pemeriksaan
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
                <h3 class="text-lg font-semibold text-gray-900">Detail Pemeriksaan</h3>
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
let allExaminations = [];
let examinationStats = {};
let currentPage = 1;
let totalPages = 1;
let perPage = 20;
let trendChart = null;
let statusChart = null;

// Base URL - updated untuk administrasi
const BASE_URL = '<?= base_url() ?>';

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadExaminationData();
    initializeCharts();
    
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('end-date').value = today.toISOString().split('T')[0];
    document.getElementById('start-date').value = thirtyDaysAgo.toISOString().split('T')[0];
});

// Load examination data - updated URL
async function loadExaminationData() {
    try {
        const filters = getCurrentFilters();
        const response = await fetch(BASE_URL + 'administrasi_laporan/ajax_get_examination_reports?' + new URLSearchParams({
            ...filters,
            page: currentPage,
            per_page: perPage
        }));
        
        const data = await response.json();
        
        if (data.success) {
            allExaminations = data.examinations;
            examinationStats = data.stats;
            updateStatistics();
            renderExaminationsTable(allExaminations);
            updateExaminationCount(data.total_records);
            updatePagination(data.pagination);
            updateCharts(data.chart_data);
        } else {
            showFlashMessage('error', 'Gagal memuat data pemeriksaan');
        }
    } catch (error) {
        console.error('Error loading examinations:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memuat data');
    }
}

// Get current filters
function getCurrentFilters() {
    return {
        start_date: document.getElementById('start-date').value,
        end_date: document.getElementById('end-date').value,
        status: document.getElementById('status-filter').value,
        jenis_pemeriksaan: document.getElementById('jenis-filter').value,
        search: document.getElementById('search-input').value
    };
}

// Update statistics
function updateStatistics() {
    document.getElementById('stat-total').textContent = examinationStats.total || 0;
    document.getElementById('stat-pending').textContent = examinationStats.pending || 0;
    document.getElementById('stat-progress').textContent = examinationStats.progress || 0;
    document.getElementById('stat-completed').textContent = examinationStats.selesai || 0;
    document.getElementById('stat-cancelled').textContent = examinationStats.cancelled || 0;
}

// Update examination count
function updateExaminationCount(count) {
    document.getElementById('exam-count').textContent = `${count} pemeriksaan`;
}

// Render examinations table
function renderExaminationsTable(examinations) {
    const tbody = document.getElementById('examinations-table-body');
    
    if (examinations.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center space-y-2">
                        <i data-lucide="file-x" class="w-12 h-12 text-gray-300"></i>
                        <span>Tidak ada data pemeriksaan ditemukan</span>
                    </div>
                </td>
            </tr>
        `;
        lucide.createIcons();
        return;
    }

    tbody.innerHTML = examinations.map(exam => {
        const statusColors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'progress': 'bg-blue-100 text-blue-800',
            'selesai': 'bg-green-100 text-green-800',
            'cancelled': 'bg-red-100 text-red-800'
        };

        const statusNames = {
            'pending': 'Pending',
            'progress': 'Dalam Proses',
            'selesai': 'Selesai',
            'cancelled': 'Dibatalkan'
        };
        
        return `
            <tr class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${exam.nomor_pemeriksaan}</div>
                    <div class="text-sm text-gray-500">ID: ${exam.pemeriksaan_id}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${exam.nama_pasien}</div>
                    <div class="text-sm text-gray-500">${exam.nik || 'NIK tidak tersedia'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${exam.jenis_pemeriksaan}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${formatDate(exam.tanggal_pemeriksaan)}</div>
                    ${exam.completed_at ? `<div class="text-sm text-gray-500">Selesai: ${formatDateTime(exam.completed_at)}</div>` : ''}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[exam.status_pemeriksaan]}">
                        ${statusNames[exam.status_pemeriksaan]}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${exam.nama_petugas || 'Belum ditugaskan'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        <button onclick="viewDetail(${exam.pemeriksaan_id})" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                            Detail
                        </button>
                        ${exam.status_pemeriksaan === 'selesai' ? `
                            <button onclick="printResult(${exam.pemeriksaan_id})" 
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
}

// Apply filters
function applyFilters() {
    currentPage = 1;
    loadExaminationData();
}

// Reset filters
function resetFilters() {
    document.getElementById('start-date').value = '';
    document.getElementById('end-date').value = '';
    document.getElementById('status-filter').value = '';
    document.getElementById('jenis-filter').value = '';
    document.getElementById('search-input').value = '';
    currentPage = 1;
    loadExaminationData();
}

// Search examinations
function searchExaminations() {
    currentPage = 1;
    loadExaminationData();
}

// Initialize charts
function initializeCharts() {
    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Pemeriksaan per Hari',
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
                        precision: 0
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

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Dalam Proses', 'Selesai', 'Dibatalkan'],
            datasets: [{
                data: [0, 0, 0, 0],
                backgroundColor: [
                    'rgb(251, 191, 36)',
                    'rgb(59, 130, 246)',
                    'rgb(34, 197, 94)',
                    'rgb(239, 68, 68)'
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
    if (chartData && chartData.trend) {
        trendChart.data.labels = chartData.trend.map(item => item.date);
        trendChart.data.datasets[0].data = chartData.trend.map(item => item.count);
        trendChart.update();
    }

    if (chartData && chartData.status) {
        statusChart.data.datasets[0].data = [
            chartData.status.pending || 0,
            chartData.status.progress || 0,
            chartData.status.selesai || 0,
            chartData.status.cancelled || 0
        ];
        statusChart.update();
    }
}

// View examination detail - updated URL
async function viewDetail(examinationId) {
    try {
        const response = await fetch(BASE_URL + `administrasi_laporan/ajax_get_examination_detail/${examinationId}`);
        const data = await response.json();
        
        if (data.success) {
            populateDetailModal(data.examination, data.results, data.timeline);
            document.getElementById('detail-modal').classList.remove('hidden');
        } else {
            showFlashMessage('error', data.message);
        }
    } catch (error) {
        console.error('Error loading examination detail:', error);
        showFlashMessage('error', 'Gagal memuat detail pemeriksaan');
    }
}

// Populate detail modal
function populateDetailModal(examination, results, timeline) {
    const content = `
        <div class="space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Informasi Pemeriksaan</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Nomor:</span> ${examination.nomor_pemeriksaan}</div>
                        <div><span class="font-medium">Tanggal:</span> ${formatDate(examination.tanggal_pemeriksaan)}</div>
                        <div><span class="font-medium">Jenis:</span> ${examination.jenis_pemeriksaan}</div>
                        <div><span class="font-medium">Status:</span> 
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium status-${examination.status_pemeriksaan}">
                                ${examination.status_pemeriksaan}
                            </span>
                        </div>
                        <div><span class="font-medium">Biaya:</span> ${formatCurrency(examination.biaya)}</div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Informasi Pasien</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Nama:</span> ${examination.nama_pasien}</div>
                        <div><span class="font-medium">NIK:</span> ${examination.nik || 'Tidak tersedia'}</div>
                        <div><span class="font-medium">Umur:</span> ${examination.umur || 'Tidak tersedia'} tahun</div>
                        <div><span class="font-medium">Dokter Perujuk:</span> ${examination.dokter_perujuk || 'Tidak tersedia'}</div>
                        <div><span class="font-medium">Asal Rujukan:</span> ${examination.asal_rujukan || 'Tidak tersedia'}</div>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            ${results ? `
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Hasil Pemeriksaan</h4>
                    <div class="text-sm">
                        ${formatResults(results, examination.jenis_pemeriksaan)}
                    </div>
                </div>
            ` : ''}

            <!-- Timeline -->
            ${timeline && timeline.length > 0 ? `
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Timeline Progress</h4>
                    <div class="space-y-3">
                        ${timeline.map(item => `
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">${item.status}</div>
                                    <div class="text-sm text-gray-600">${item.keterangan || 'Tidak ada keterangan'}</div>
                                    <div class="text-xs text-gray-500">${formatDateTime(item.tanggal_update)}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('detail-content').innerHTML = content;
}

// Format results based on examination type
// Format results based on examination type
function formatResults(results, type) {
    if (!results) return '<div class="text-gray-500">Hasil pemeriksaan belum tersedia</div>';
    
    const displayField = (label, value, unit = '') => {
        return value ? `<div><span class="font-medium text-gray-700">${label}:</span> ${value}${unit ? ' ' + unit : ''}</div>` : '';
    };
    
    switch(type) {
        case 'Kimia Darah':
            return `
                <div class="grid grid-cols-2 gap-4">
                    ${displayField('Gula Darah Puasa', results.gula_darah_puasa, 'mg/dL')}
                    ${displayField('Kolesterol Total', results.cholesterol_total, 'mg/dL')}
                    ${displayField('Asam Urat', results.asam_urat, 'mg/dL')}
                    ${displayField('Ureum', results.ureum, 'mg/dL')}
                    ${displayField('Kreatinin', results.creatinin, 'mg/dL')}
                    ${displayField('SGPT', results.sgpt, 'U/L')}
                    ${displayField('SGOT', results.sgot, 'U/L')}
                    ${displayField('HDL', results.hdl, 'mg/dL')}
                    ${displayField('LDL', results.ldl, 'mg/dL')}
                    ${displayField('Trigliserida', results.trigliserida, 'mg/dL')}
                </div>
            `;
            
        case 'Hematologi':
            return `
                <div class="grid grid-cols-2 gap-4">
                    ${displayField('Hemoglobin', results.hemoglobin, 'g/dL')}
                    ${displayField('Hematokrit', results.hematokrit, '%')}
                    ${displayField('Eritrosit', results.eritrosit, 'juta/µL')}
                    ${displayField('Leukosit', results.leukosit, 'ribu/µL')}
                    ${displayField('Trombosit', results.trombosit, 'ribu/µL')}
                    ${displayField('LED', results.laju_endap_darah, 'mm/jam')}
                    ${displayField('MCV', results.mcv, 'fL')}
                    ${displayField('MCH', results.mch, 'pg')}
                    ${displayField('MCHC', results.mchc, 'g/dL')}
                    ${displayField('Golongan Darah', results.golongan_darah ? results.golongan_darah + (results.rhesus || '') : null)}
                </div>
            `;
            
        case 'Urinologi':
            return `
                <div class="grid grid-cols-2 gap-4">
                    ${displayField('Warna', results.warna)}
                    ${displayField('Kejernihan', results.kejernihan)}
                    ${displayField('pH', results.ph)}
                    ${displayField('Berat Jenis', results.berat_jenis)}
                    ${displayField('Protein', results.protein)}
                    ${displayField('Glukosa', results.glukosa)}
                    ${displayField('Keton', results.keton)}
                    ${displayField('Bilirubin', results.bilirubin)}
                    ${displayField('Urobilinogen', results.urobilinogen)}
                    ${displayField('Blood', results.blood)}
                    ${displayField('Nitrit', results.nitrit)}
                    ${displayField('Leukosit Esterase', results.leukosit_esterase)}
                    ${displayField('Sedimen Eritrosit', results.sedimen_eritrosit, '/LPB')}
                    ${displayField('Sedimen Leukosit', results.sedimen_leukosit, '/LPB')}
                    ${displayField('Epitel', results.epitel)}
                    ${displayField('Silinder', results.silinder)}
                    ${displayField('Kristal', results.kristal)}
                    ${displayField('Bakteri', results.bakteri)}
                </div>
            `;
            
        case 'Serologi':
        case 'Serologi Imunologi':
            return `
                <div class="grid grid-cols-2 gap-4">
                    ${displayField('HBsAg', results.hbsag)}
                    ${displayField('Anti-HBs', results.anti_hbs)}
                    ${displayField('Anti-HCV', results.anti_hcv)}
                    ${displayField('HIV', results.hiv)}
                    ${displayField('VDRL/RPR', results.vdrl_rpr)}
                    ${displayField('TPHA', results.tpha)}
                    ${displayField('Widal O', results.widal_o)}
                    ${displayField('Widal H', results.widal_h)}
                    ${displayField('Widal AH', results.widal_ah)}
                    ${displayField('Widal BH', results.widal_bh)}
                    ${displayField('Dengue IgG', results.dengue_igg)}
                    ${displayField('Dengue IgM', results.dengue_igm)}
                    ${displayField('NS1 Antigen', results.ns1_antigen)}
                </div>
            `;
            
        case 'TBC':
            return `
                <div class="space-y-3">
                    ${displayField('Metode Pemeriksaan', results.metode_pemeriksaan)}
                    ${displayField('Jenis Specimen', results.jenis_specimen)}
                    ${displayField('Tanggal Pengambilan', results.tanggal_pengambilan)}
                    ${displayField('Hasil BTA (Mikroskopis)', results.hasil_bta)}
                    ${displayField('TCM/GeneXpert', results.hasil_tcm)}
                    ${displayField('Rifampicin Resistance', results.rifampicin_resistance)}
                    ${displayField('Biakan (Culture)', results.hasil_biakan)}
                    ${displayField('Kesimpulan', results.kesimpulan)}
                    ${results.catatan ? `<div class="col-span-2 pt-2 border-t"><span class="font-medium text-gray-700">Catatan:</span><br/>${results.catatan}</div>` : ''}
                </div>
            `;
            
        case 'IMS':
            return `
                <div class="grid grid-cols-2 gap-4">
                    ${displayField('Jenis Specimen', results.jenis_specimen)}
                    ${displayField('Gram Stain', results.gram_stain)}
                    ${displayField('Diplokokus', results.diplokokus)}
                    ${displayField('Trichomonas', results.trichomonas)}
                    ${displayField('Candida', results.candida)}
                    ${displayField('Clue Cells', results.clue_cells)}
                    ${displayField('pH Vagina', results.ph_vagina)}
                    ${displayField('Whiff Test', results.whiff_test)}
                    ${displayField('KOH Test', results.koh_test)}
                    ${displayField('Kesimpulan', results.kesimpulan)}
                    ${results.catatan ? `<div class="col-span-2 pt-2 border-t"><span class="font-medium text-gray-700">Catatan:</span><br/>${results.catatan}</div>` : ''}
                </div>
            `;
            
        case 'MLS':
            return `
                <div class="space-y-3">
                    ${displayField('Jenis Specimen', results.jenis_specimen)}
                    ${displayField('Metode Pemeriksaan', results.metode_pemeriksaan)}
                    ${displayField('Hasil Mikroskopis', results.hasil_mikroskopis)}
                    ${displayField('Plasmodium Species', results.plasmodium_species)}
                    ${displayField('Stadium', results.stadium)}
                    ${displayField('Densitas Parasit', results.densitas_parasit, '/µL')}
                    ${displayField('RDT Result', results.rdt_result)}
                    ${displayField('Kesimpulan', results.kesimpulan)}
                    ${results.catatan ? `<div class="col-span-2 pt-2 border-t"><span class="font-medium text-gray-700">Catatan:</span><br/>${results.catatan}</div>` : ''}
                </div>
            `;
            
        default:
            // Fallback untuk jenis pemeriksaan yang belum terdefinisi
            const entries = Object.entries(results).filter(([key, val]) => 
                val !== null && val !== '' && !key.includes('_id') && !key.includes('created') && !key.includes('updated')
            );
            
            if (entries.length === 0) {
                return '<div class="text-gray-500">Data hasil pemeriksaan kosong</div>';
            }
            
            return `
                <div class="grid grid-cols-2 gap-4">
                    ${entries.map(([key, value]) => `
                        <div>
                            <span class="font-medium text-gray-700">${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}:</span> ${value}
                        </div>
                    `).join('')}
                </div>
            `;
    }
}

// Close detail modal
function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

// Print result
function printResult(examinationId) {
    window.open(BASE_URL + `PDF_Controller/print_examination_result/${examinationId}`, '_blank');
}

// Export functions - updated URL
function exportToExcel() {
    const filters = getCurrentFilters();
    const params = new URLSearchParams(filters);
    window.open(BASE_URL + `excel_controller/export_examination_reports?${params}`, '_blank');
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
    button.className = `px-3 py-1 text-sm rounded-md ${isActive ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'} border border-gray-300`;
    button.onclick = () => {
        currentPage = page;
        loadExaminationData();
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

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
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