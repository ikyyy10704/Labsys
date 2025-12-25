<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadangkan & Pulihkan Database - Labsy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        /* Custom scrollbar - konsisten dengan style lainnya */
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

        /* Fullwidth container - ENHANCED untuk konsistensi */
        .fullwidth-container {
            min-height: 100vh;
            width: 100vw;
            min-width: 100vw;
            max-width: 100vw;
            overflow-x: auto;
        }

        .force-fullwidth {
            width: 100% !important;
            min-width: 100% !important;
            max-width: none !important;
        }

        /* Responsive table - ENHANCED */
        .table-container {
            min-width: 100%;
            width: 100%;
            overflow-x: auto;
        }

        /* Table wrapper untuk memastikan fullwidth */
        .table-wrapper {
            width: 100%;
            min-width: 100%;
            overflow-x: auto;
        }

        /* Progress bar animation */
        .progress-bar {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            animation: progress 2s ease-in-out infinite;
        }
        @keyframes progress {
            0%, 100% { transform: scaleX(1); }
            50% { transform: scaleX(1.1); }
        }

        /* Empty state container */
        .empty-state-container {
            width: 100% !important;
            min-width: 100% !important;
            padding: 3rem 1rem;
        }
    </style>
</head>
<body class="bg-gray-50 fullwidth-container">

<!-- Backup Loading Overlay -->
<div id="backup-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 shadow-2xl max-w-md w-full mx-4 text-center">
        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="database" class="w-10 h-10 text-blue-600 loading"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Membuat Backup Database</h3>
        <p id="backup-status-text" class="text-gray-600 mb-4">Harap tunggu, proses backup sedang berjalan...</p>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
            <div id="backup-progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
        <p class="text-xs text-gray-500">Jangan tutup atau refresh halaman ini</p>
    </div>
</div>

<!-- Header Section - konsisten dengan activity_reports -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500 force-fullwidth">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="database" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Cadangkan & Pulihkan Database</h1>
                <p class="text-blue-100">Kelola backup dan restore database sistem</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content - Full Width -->
<div class="p-6 space-y-6 fullwidth-container force-fullwidth">
    
    <!-- Flash Messages Container -->
    <div id="flash-messages" class="force-fullwidth"></div>

    <!-- Database Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 force-fullwidth">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Database Size</p>
                    <p id="db-size" class="text-2xl font-bold text-blue-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Total ukuran database</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="hard-drive" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tables</p>
                    <p id="table-count" class="text-2xl font-bold text-green-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Jumlah tabel database</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="layers" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Records</p>
                    <p id="record-count" class="text-2xl font-bold text-orange-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Total records database</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Available Backups</p>
                    <p id="backup-count" class="text-2xl font-bold text-purple-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">File backup tersedia</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="archive" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup & Restore Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 force-fullwidth">
        <!-- Create Backup Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="download" class="w-5 h-5 text-blue-600"></i>
                    <span>Buat Backup Database</span>
                </h3>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label for="backup-name" class="block text-sm font-medium text-gray-700 mb-2">Nama Backup</label>
                    <input type="text" id="backup-name" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="backup_<?= date('Y-m-d_H-i-s') ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Backup</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="include-structure" checked 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Struktur Database</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="include-data" checked 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Data Database</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="compress-backup" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Kompresi File (.zip)</span>
                        </label>
                    </div>
                </div>
                
                <div class="pt-4">
                    <button onclick="createBackup()" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 font-medium">
                        <i data-lucide="download" class="w-5 h-5"></i>
                        <span>Buat Backup Sekarang</span>
                    </button>
                </div>
                
                <!-- Progress Bar (Hidden by default) -->
                <div id="backup-progress" class="hidden">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Membuat backup...</span>
                        <span class="text-sm text-gray-500">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="progress-bar h-2 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restore Database Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="upload" class="w-5 h-5 text-green-600"></i>
                    <span>Pulihkan Database</span>
                </h3>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Backup</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                        <input type="file" id="backup-file" accept=".sql,.zip" class="hidden" onchange="handleFileSelect(this)">
                        <label for="backup-file" class="cursor-pointer">
                            <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                            <p class="text-sm text-gray-600">Klik untuk upload file backup</p>
                            <p class="text-xs text-gray-500 mt-1">Mendukung format .sql dan .zip</p>
                        </label>
                    </div>
                    <div id="selected-file" class="hidden mt-2 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="file" class="w-4 h-4 text-gray-500"></i>
                                <span class="text-sm text-gray-700" id="file-name"></span>
                            </div>
                            <button onclick="clearFileSelection()" class="text-red-500 hover:text-red-700">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mt-0.5"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-medium">Peringatan!</p>
                            <p>Proses restore akan menggantikan seluruh data yang ada. Pastikan Anda telah membuat backup terlebih dahulu.</p>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4">
                    <button onclick="confirmRestore()" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 font-medium">
                        <i data-lucide="upload" class="w-5 h-5"></i>
                        <span>Pulihkan Database</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup History - Full Width -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 fullwidth-container force-fullwidth">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="history" class="w-5 h-5 text-blue-600"></i>
                    <span>Riwayat Backup</span>
                    <span id="backup-list-count" class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        0 backup
                    </span>
                </h2>
                <div class="flex items-center space-x-2">
                    <button onclick="cleanOldBackups()" 
                            class="px-3 py-2 text-sm text-orange-600 hover:bg-orange-50 rounded-lg transition-colors duration-200">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                        Bersihkan Backup Lama
                    </button>
                </div>
            </div>
        </div>
        
        <div class="table-wrapper">
            <div class="table-container">
                <table class="w-full min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="backup-table-body" class="bg-white divide-y divide-gray-200">
                        <tr id="loading-backups">
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <i data-lucide="loader-2" class="w-5 h-5 text-blue-600 loading"></i>
                                    <span class="text-gray-500">Memuat daftar backup...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div id="restore-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Restore Database</h3>
                <button onclick="closeRestoreModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-3 mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 mt-1"></i>
                <div>
                    <p class="text-gray-900 font-medium">Apakah Anda yakin?</p>
                    <p class="text-gray-600 text-sm mt-1">Proses ini akan menggantikan seluruh data database yang ada. Pastikan Anda telah membuat backup terbaru.</p>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-4">
                <button onclick="closeRestoreModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </button>
                <button onclick="executeRestore()" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200">
                    Ya, Pulihkan Database
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Custom Confirmation Modal -->
    <div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[70] hidden">
        <div class="bg-white rounded-xl p-6 w-96 fade-in transform scale-100 transition-all">
            <h2 class="text-lg font-semibold" id="modal-title">Konfirmasi</h2>
            <p class="text-sm text-gray-600 mt-2" id="modal-message">
                Apakah Anda yakin?
            </p>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeModal()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition-colors">Batal</button>
                <button id="modal-confirm-btn" onclick="confirmAction()" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition-colors shadow-sm">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>

<script>
// Global variables
let selectedBackupFile = null;
let currentConfirmCallback = null;

// Modal Logic
function openModal(title, message, confirmText, confirmCallback, confirmColorClass = 'bg-red-600 text-white') {
    document.getElementById('modal-title').textContent = title || 'Konfirmasi';
    document.getElementById('modal-message').textContent = message || 'Apakah Anda yakin?';
    
    const confirmBtn = document.getElementById('modal-confirm-btn');
    confirmBtn.textContent = confirmText || 'Konfirmasi';
    
    // Reset and add color class.
    confirmBtn.className = `px-4 py-2 rounded-lg font-medium transition-colors duration-200 shadow-sm ${confirmColorClass}`;
    
    currentConfirmCallback = confirmCallback;
    document.getElementById('modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    currentConfirmCallback = null;
}

function confirmAction() {
    if (currentConfirmCallback) {
        currentConfirmCallback();
    }
    closeModal();
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadDatabaseInfo();
    loadBackupList();
    ensureFullwidthLayout();
});

// Ensure fullwidth layout - ENHANCED
function ensureFullwidthLayout() {
    const containers = document.querySelectorAll('.fullwidth-container, .force-fullwidth');
    containers.forEach(container => {
        container.style.width = '100%';
        container.style.minWidth = '100%';
        container.style.maxWidth = 'none';
    });
    
    const tableContainer = document.querySelector('.table-container');
    if (tableContainer) {
        tableContainer.style.width = '100%';
        tableContainer.style.minWidth = '100%';
    }
    
    const tableWrapper = document.querySelector('.table-wrapper');
    if (tableWrapper) {
        tableWrapper.style.width = '100%';
        tableWrapper.style.minWidth = '100%';
    }
    
    document.body.style.width = '100%';
    document.body.style.minWidth = '100%';
    document.body.style.maxWidth = 'none';
    document.body.style.overflowX = 'auto';
}

// Load database info
async function loadDatabaseInfo() {
    try {
        const response = await fetch('<?= base_url("admin/ajax_get_database_info") ?>');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('db-size').textContent = data.info.size;
            document.getElementById('table-count').textContent = data.info.table_count;
            document.getElementById('record-count').textContent = data.info.record_count.toLocaleString();
        }
    } catch (error) {
        console.error('Error loading database info:', error);
    }
}

// Load backup list
async function loadBackupList() {
    try {
        const response = await fetch('<?= base_url("admin/ajax_get_backup_list") ?>');
        const data = await response.json();
        
        if (data.success) {
            renderBackupList(data.backups);
            document.getElementById('backup-count').textContent = data.backups.length;
            document.getElementById('backup-list-count').textContent = `${data.backups.length} backup`;
        } else {
            showEmptyBackupList();
        }
    } catch (error) {
        console.error('Error loading backup list:', error);
        showEmptyBackupList();
    }
}

// Render backup list
function renderBackupList(backups) {
    const tbody = document.getElementById('backup-table-body');
    
    if (backups.length === 0) {
        showEmptyBackupList();
        return;
    }

    tbody.innerHTML = backups.map(backup => `
        <tr class="hover:bg-gray-50 transition-colors duration-200">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-3">
                    <i data-lucide="file-archive" class="w-5 h-5 text-blue-600"></i>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${backup.filename}</div>
                        <div class="text-sm text-gray-500">${backup.type || 'SQL Database'}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${formatDate(backup.created_at)}</div>
                <div class="text-xs text-gray-500">${formatTime(backup.created_at)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${backup.size}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${backup.is_valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${backup.is_valid ? 'Valid' : 'Error'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2">
                    <button onclick="downloadBackup('${backup.filename}')" 
                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                        <i data-lucide="download" class="w-3 h-3 mr-1"></i>
                        Download
                    </button>
                    <button onclick="restoreFromBackup('${backup.filename}')" 
                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                        <i data-lucide="upload" class="w-3 h-3 mr-1"></i>
                        Restore
                    </button>
                    <button onclick="deleteBackup('${backup.filename}')" 
                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 transition-colors duration-200">
                        <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                        Hapus
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    lucide.createIcons();
    ensureFullwidthLayout();
}

// Show empty backup list with fullwidth
function showEmptyBackupList() {
    const tbody = document.getElementById('backup-table-body');
    tbody.innerHTML = `
        <tr class="fullwidth-container">
            <td colspan="5" class="px-6 py-12 text-center empty-state-container">
                <div class="flex flex-col items-center space-y-4 w-full">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i data-lucide="archive" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada backup tersedia</h3>
                        <p class="text-gray-500 mb-4">Belum ada file backup database yang dibuat</p>
                        <button onclick="createBackup()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 mx-auto">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Buat Backup Pertama</span>
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    `;
    lucide.createIcons();
    ensureFullwidthLayout();
}

// Backup overlay functions
function showBackupOverlay() {
    const overlay = document.getElementById('backup-overlay');
    overlay.classList.remove('hidden');
    lucide.createIcons();
}

function hideBackupOverlay() {
    document.getElementById('backup-overlay').classList.add('hidden');
}

function updateBackupStatus(text, progress) {
    document.getElementById('backup-status-text').textContent = text;
    document.getElementById('backup-progress-bar').style.width = progress + '%';
}

// Poll for new backup file
async function pollForNewBackup(initialCount) {
    const maxAttempts = 30; // Check for 30 seconds
    let attempts = 0;
    
    while (attempts < maxAttempts) {
        await new Promise(resolve => setTimeout(resolve, 1000));
        attempts++;
        
        try {
            const response = await fetch('<?= base_url("admin/ajax_get_backup_list") ?>');
            const data = await response.json();
            
            if (data.success && data.backups.length > initialCount) {
                // New backup found!
                updateBackupStatus('Backup berhasil dibuat!', 100);
                showFlashMessage('success', 'Backup database berhasil dibuat!');
                
                setTimeout(() => {
                    hideBackupOverlay();
                    loadBackupList();
                    loadDatabaseInfo();
                }, 1000);
                return true;
            }
            
            updateBackupStatus(`Menunggu proses selesai... (${attempts}s)`, 70 + (attempts / maxAttempts * 30));
        } catch (e) {
            console.log('Polling attempt failed:', e);
        }
    }
    
    // Timeout - still refresh the list
    hideBackupOverlay();
    showFlashMessage('info', 'Proses backup mungkin masih berjalan. Refresh halaman untuk cek hasil.');
    loadBackupList();
    loadDatabaseInfo();
    return false;
}

// Get current backup count
async function getBackupCount() {
    try {
        const response = await fetch('<?= base_url("admin/ajax_get_backup_list") ?>');
        const data = await response.json();
        return data.success ? data.backups.length : 0;
    } catch (e) {
        return 0;
    }
}

// Create backup
async function createBackup() {
    const name = document.getElementById('backup-name').value || `backup_${new Date().toISOString().slice(0,19).replace(/:/g,'-')}`;
    const includeStructure = document.getElementById('include-structure').checked;
    const includeData = document.getElementById('include-data').checked;
    const compress = document.getElementById('compress-backup').checked;
    
    if (!includeStructure && !includeData) {
        showFlashMessage('error', 'Pilih minimal struktur atau data untuk di-backup');
        return;
    }
    
    // Get initial backup count for comparison
    const initialBackupCount = await getBackupCount();
    
    // Show overlay
    showBackupOverlay();
    updateBackupStatus('Memulai proses backup...', 10);
    
    // Show progress
    const progressContainer = document.getElementById('backup-progress');
    progressContainer.classList.remove('hidden');
    
    // Disable backup button
    const backupBtn = document.querySelector('button[onclick="createBackup()"]');
    if (backupBtn) {
        backupBtn.disabled = true;
        backupBtn.innerHTML = '<i data-lucide="loader" class="w-5 h-5 loading"></i><span>Membuat Backup...</span>';
        lucide.createIcons();
    }
    
    updateBackupStatus('Mengekspor database...', 30);

    try {
        // Create AbortController with 5 minute timeout for large databases
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 300000); // 5 minutes
        
        const response = await fetch('<?= base_url("admin/ajax_create_backup") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                include_structure: includeStructure,
                include_data: includeData,
                compress: compress
            }),
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Server mengembalikan response tidak valid. Kemungkinan backup tetap berhasil, cek tabel di bawah.');
        }
        
        const data = await response.json();
        
        if (data.success) {
            updateBackupStatus('Backup berhasil dibuat!', 100);
            showFlashMessage('success', `Backup berhasil dibuat: ${data.filename || name}`);
            
            setTimeout(() => {
                hideBackupOverlay();
                loadBackupList();
                loadDatabaseInfo();
            }, 1000);
            
            // Reset form
            document.getElementById('backup-name').value = '';
        } else {
            hideBackupOverlay();
            showFlashMessage('error', data.message || 'Gagal membuat backup');
        }
    } catch (error) {
        console.error('Error creating backup:', error);
        
        // Start polling to check if backup completed
        updateBackupStatus('Memeriksa hasil backup...', 70);
        await pollForNewBackup(initialBackupCount);
    } finally {
        progressContainer.classList.add('hidden');
        hideBackupOverlay();
        
        // Re-enable backup button
        if (backupBtn) {
            backupBtn.disabled = false;
            backupBtn.innerHTML = '<i data-lucide="download" class="w-5 h-5"></i><span>Buat Backup Sekarang</span>';
            lucide.createIcons();
        }
    }
}

// Handle file selection
function handleFileSelect(input) {
    const file = input.files[0];
    if (file) {
        selectedBackupFile = file;
        document.getElementById('selected-file').classList.remove('hidden');
        document.getElementById('file-name').textContent = file.name;
    }
}

// Clear file selection
function clearFileSelection() {
    selectedBackupFile = null;
    document.getElementById('backup-file').value = '';
    document.getElementById('selected-file').classList.add('hidden');
}

// Confirm restore
function confirmRestore() {
    if (!selectedBackupFile) {
        showFlashMessage('error', 'Pilih file backup terlebih dahulu');
        return;
    }
    
    document.getElementById('restore-modal').classList.remove('hidden');
}

// Close restore modal
function closeRestoreModal() {
    document.getElementById('restore-modal').classList.add('hidden');
}

// Execute restore
async function executeRestore() {
    if (!selectedBackupFile) return;
    
    const formData = new FormData();
    formData.append('backup_file', selectedBackupFile);
    
    try {
        closeRestoreModal();
        showFlashMessage('info', 'Proses restore dimulai, mohon tunggu...');
        
        const response = await fetch('<?= base_url("admin/ajax_restore_backup") ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showFlashMessage('success', 'Database berhasil dipulihkan');
            clearFileSelection();
            loadDatabaseInfo();
        } else {
            showFlashMessage('error', data.message || 'Gagal memulihkan database');
        }
    } catch (error) {
        console.error('Error restoring backup:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memulihkan database');
    }
}

// Download backup
function downloadBackup(filename) {
    window.open(`<?= base_url("admin/download_backup") ?>/${encodeURIComponent(filename)}`, '_blank');
}

// Restore from backup
function restoreFromBackup(filename) {
    openModal(
        'Restore Database',
        'Apakah Anda yakin ingin memulihkan database dari backup ini? Tindakan ini akan menimpa data saat ini.',
        'Pulihkan',
        () => {
            fetch('<?= base_url("admin/ajax_restore_from_backup") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({filename: filename})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFlashMessage('success', 'Database berhasil dipulihkan');
                    loadDatabaseInfo();
                } else {
                    showFlashMessage('error', data.message || 'Gagal memulihkan database');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashMessage('error', 'Terjadi kesalahan saat memulihkan database');
            });
        },
        'bg-green-100 text-green-700 hover:bg-green-200 border border-transparent'
    );
}

// Delete backup
function deleteBackup(filename) {
    openModal(
        'Hapus Backup',
        'Apakah Anda yakin ingin menghapus backup ini? Tindakan ini tidak dapat dibatalkan.',
        'Hapus',
        async () => {
            try {
                const response = await fetch('<?= base_url("admin/ajax_delete_backup") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({filename: filename})
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showFlashMessage('success', 'Backup berhasil dihapus');
                    loadBackupList();
                    loadDatabaseInfo();
                } else {
                    showFlashMessage('error', data.message || 'Gagal menghapus backup');
                }
            } catch (error) {
                console.error('Error deleting backup:', error);
                showFlashMessage('error', 'Terjadi kesalahan saat menghapus backup');
            }
        },
        'bg-red-100 text-red-700 hover:bg-red-200 border border-transparent'
    );
}

// Clean old backups
function cleanOldBackups() {
    openModal(
        'Bersihkan Backup Lama',
        'Apakah Anda yakin ingin menghapus backup yang lebih lama dari 30 hari?',
        'Bersihkan',
        async () => {
            try {
                const response = await fetch('<?= base_url("admin/ajax_clean_old_backups") ?>', {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showFlashMessage('success', `${data.deleted_count} backup lama berhasil dihapus`);
                    loadBackupList();
                    loadDatabaseInfo();
                } else {
                    showFlashMessage('error', data.message || 'Gagal membersihkan backup lama');
                }
            } catch (error) {
                console.error('Error cleaning old backups:', error);
                showFlashMessage('error', 'Terjadi kesalahan saat membersihkan backup lama');
            }
        },
        'bg-orange-100 text-orange-700 hover:bg-orange-200 border border-transparent'
    );
}

// Refresh backup list
function refreshBackupList() {
    loadBackupList();
    loadDatabaseInfo();
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

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showFlashMessage(type, message) {
    const container = document.getElementById('flash-messages');
    const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 
                      type === 'info' ? 'bg-blue-50 border-blue-200 text-blue-800' :
                      'bg-red-50 border-red-200 text-red-800';
    const iconName = type === 'success' ? 'check-circle' : 
                     type === 'info' ? 'info' :
                     'alert-circle';
    const iconClass = type === 'success' ? 'text-green-600' : 
                      type === 'info' ? 'text-blue-600' :
                      'text-red-600';
    
    const alert = document.createElement('div');
    alert.className = `${alertClass} border rounded-lg p-4 flex items-center space-x-3 fade-in force-fullwidth`;
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
    
    ensureFullwidthLayout();
}

// Window resize handler
window.addEventListener('resize', function() {
    ensureFullwidthLayout();
});

// Page load handler
window.addEventListener('load', function() {
    ensureFullwidthLayout();
});
</script>

</body>
</html>