<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - LabSy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        /* Custom scrollbar - konsisten dengan activity_reports */
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
                .notification {
            max-width: 400px;         /* Sesuaikan lebar maksimal */
            padding: 10px 20px;       /* Mengurangi padding untuk membuatnya lebih kompak */
            font-size: 14px;          /* Ukuran font yang lebih kecil */
            line-height: 1.4;         /* Mengatur jarak antar baris */
        }

        /* Jika menggunakan Toast di Bootstrap */
        .toast {
            max-width: 400px;         /* Lebar maksimal Toast */
            padding: 10px 15px;
            font-size: 14px;
            line-height: 1.4;
        }

        /* Jika notifikasi menggunakan class success/error */
        .notification-success {
            background-color: #28a745; /* Warna hijau */
        }
        .notification-error {
            background-color: #dc3545; /* Warna merah */
        }
    </style>
</head>
<body class="bg-gray-50 fullwidth-container">

<!-- Header Section - konsisten dengan activity_reports -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="users" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Manajemen Pengguna</h1>
                <p class="text-blue-100">Kelola pengguna sistem dan hak aksesnya</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="toggleAddForm()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>
</div>

<!-- Main Content - Full Width -->
<div class="p-5 space-y-5 fullwidth-container">
    
    <!-- Flash Messages Container -->
    <div id="flash-messages"></div>

    <!-- Statistics Cards - konsisten dengan activity_reports -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pengguna</p>
                    <p id="stat-total" class="text-2xl font-bold text-gray-900">-</p>
                    <p class="text-xs text-gray-500 mt-1">Semua pengguna sistem</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pengguna Aktif</p>
                    <p id="stat-active" class="text-2xl font-bold text-green-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Status aktif</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Administrator</p>
                    <p id="stat-admin" class="text-2xl font-bold text-purple-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Role admin</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="shield" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Petugas Lab</p>
                    <p id="stat-lab" class="text-2xl font-bold text-orange-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Role lab technician</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="flask-conical" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Form -->
    <div id="add-user-form" class="bg-white rounded-xl shadow-sm border border-gray-200 hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="user-plus" class="w-5 h-5 text-blue-600"></i>
                    <span>Tambah Pengguna Baru</span>
                </h2>
                <button onclick="closeAddForm()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <form id="user-form" class="space-y-6">
                
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                   placeholder="Masukkan username"
                                   required>
                            <i data-lucide="user" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                        </div>
                        <div class="text-red-500 text-xs mt-1 hidden" id="username-error"></div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password"
                                   class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                   placeholder="Masukkan password"
                                   required>
                            <i data-lucide="lock" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                        </div>
                        <div class="text-red-500 text-xs mt-1 hidden" id="password-error"></div>
                        <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                    </div>
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Role/Peran <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="role" 
                                name="role" 
                                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                required
                                onchange="toggleRoleFields()">
                            <option value="">Pilih Role</option>
                            <option value="admin">Administrator</option>
                            <option value="administrasi">Staff Administrasi</option>
                            <option value="petugas_lab">Petugas Laboratorium</option>
                            <option value="supervisor">supervisor</option>
                        </select>
                        <i data-lucide="shield" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                    </div>
                    <div class="text-red-500 text-xs mt-1 hidden" id="role-error"></div>
                </div>

                <!-- Name Field -->
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="nama_lengkap" 
                               name="nama_lengkap" 
                               class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Masukkan nama lengkap"
                               required>
                        <i data-lucide="user-check" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                    </div>
                    <div class="text-red-500 text-xs mt-1 hidden" id="nama_lengkap-error"></div>
                </div>

                <!-- Role-specific Fields -->
                <!-- Fields for Administrasi -->
                <div id="administrasi_fields" class="space-y-4 hidden">
                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Telepon
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="telepon" 
                                   name="telepon" 
                                   class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                   placeholder="Contoh: 08123456789">
                            <i data-lucide="phone" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Fields for Petugas Lab -->
                <div id="petugas_lab_fields" class="space-y-4 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="jenis_keahlian" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Keahlian
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="jenis_keahlian" 
                                       name="jenis_keahlian" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="Contoh: Analis Laboratorium">
                                <i data-lucide="graduation-cap" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label for="telepon_lab" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="telepon_lab" 
                                       name="telepon_lab" 
                                       class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="Contoh: 08123456789">
                                <i data-lucide="phone" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat
                        </label>
                        <div class="relative">
                            <textarea id="alamat" 
                                      name="alamat" 
                                      rows="3"
                                      class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                      placeholder="Masukkan alamat lengkap"></textarea>
                            <i data-lucide="map-pin" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                    <!-- Fields for Supervisor -->
                <div id="supervisor_fields" class="space-y-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="jenis_keahlian_supervisor" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Keahlian
                        </label>
                        <div class="relative">
                            <input type="text" 
                                id="jenis_keahlian_supervisor" 
                                name="jenis_keahlian_supervisor" 
                                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                placeholder="Contoh: Quality Control & Assurance">
                            <i data-lucide="graduation-cap" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div>
                        <label for="telepon_supervisor" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Telepon
                        </label>
                        <div class="relative">
                            <input type="text" 
                                id="telepon_supervisor" 
                                name="telepon_supervisor" 
                                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                placeholder="Contoh: 08123456789">
                            <i data-lucide="phone" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="alamat_supervisor" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat
                    </label>
                    <div class="relative">
                        <textarea id="alamat_supervisor" 
                                name="alamat_supervisor" 
                                rows="3"
                                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                placeholder="Masukkan alamat lengkap"></textarea>
                        <i data-lucide="map-pin" class="absolute left-3 top-3.5 w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
            </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                    <button type="button" 
                            onclick="closeAddForm()"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Batal</span>
                    </button>
                    
                    <button type="submit" 
                            id="submit-btn"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Tambah Pengguna</span>
                    </button>
                </div>
                
            </form>
        </div>
    </div>

    <!-- Users Table - Full Width -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 fullwidth-container">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                    <span>Daftar Pengguna</span>
                    <span id="user-count" class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        0 users
                    </span>
                </h2>
            </div>
        </div>
        
        <div class="overflow-x-auto table-container">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- Data akan dimuat secara dinamis -->
                    <tr id="loading-row">
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <i data-lucide="loader-2" class="w-5 h-5 text-blue-600 loading"></i>
                                <span class="text-gray-500">Memuat data pengguna...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Custom Confirmation Modal -->
    <!-- Modal HTML as requested by user, with added ID for dynamic content -->
    <div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[70]">
        <div class="bg-white rounded-xl p-6 w-96 fade-in transform scale-100 transition-all">
            <h2 class="text-lg font-semibold" id="modal-title">Konfirmasi</h2>
            <p class="text-sm text-gray-600 mt-2" id="modal-message">
                Apakah Anda yakin ingin menonaktifkan pengguna ini?
            </p>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeModal()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition-colors">Batal</button>
                <button id="modal-confirm-btn" onclick="confirmAction()" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition-colors shadow-sm">
                    Nonaktifkan
                </button>
            </div>
        </div>
    </div>

<!-- Edit User Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Edit Pengguna</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form id="edit-form">
                <!-- Edit form content will be populated dynamically -->
            </form>
        </div>
    </div>
</div>

<script>
// Global variables
let allUsers = [];
let userStats = {};
let currentConfirmCallback = null;

// Modal Logic
function openModal(title, message, confirmText, confirmCallback, confirmColorClass = 'bg-red-600') {
    document.getElementById('modal-title').textContent = title || 'Konfirmasi';
    document.getElementById('modal-message').textContent = message || 'Apakah Anda yakin?';
    
    const confirmBtn = document.getElementById('modal-confirm-btn');
    confirmBtn.textContent = confirmText || 'Konfirmasi';
    
    // Reset and add color class. Removed hardcoded text-white to allow "neon" style
    confirmBtn.className = `px-4 py-2 rounded font-medium transition-colors shadow-sm ${confirmColorClass}`;
    
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
    closeModal(); // Ensure hidden on load
    lucide.createIcons();
    loadUsersData();
    
    // Ensure fullwidth layout
    ensureFullwidthLayout();
});

// Ensure fullwidth layout regardless of data state
function ensureFullwidthLayout() {
    const containers = document.querySelectorAll('.fullwidth-container');
    containers.forEach(container => {
        container.style.width = '100%';
        container.style.minWidth = '100%';
    });
    
    const tableContainer = document.querySelector('.table-container');
    if (tableContainer) {
        tableContainer.style.width = '100%';
        tableContainer.style.minWidth = '100%';
    }
}

// Load users data
async function loadUsersData() {
    try {
        const response = await fetch('<?= base_url("admin/get_users_data") ?>');
        const data = await response.json();
        
        if (data.success) {
            allUsers = data.users;
            userStats = data.stats;
            updateStatistics();
            renderUsersTable(allUsers);
            updateUserCount(allUsers.length);
        } else {
            showFlashMessage('error', 'Gagal memuat data pengguna');
            renderEmptyState();
        }
    } catch (error) {
        console.error('Error loading users:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memuat data');
        renderEmptyState();
    } finally {
        ensureFullwidthLayout();
    }
}

// Render empty state with fullwidth
function renderEmptyState() {
    const tbody = document.getElementById('users-table-body');
    tbody.innerHTML = `
        <tr class="fullwidth-container">
            <td colspan="8" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i data-lucide="users" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pengguna</h3>
                        <p class="text-gray-500 mb-4">Belum ada data pengguna yang tersedia</p>
                        <button onclick="toggleAddForm()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 mx-auto">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            <span>Tambah Pengguna Pertama</span>
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    `;
    lucide.createIcons();
    ensureFullwidthLayout();
}

// Update statistics
function updateStatistics() {
    document.getElementById('stat-total').textContent = userStats.total || 0;
    document.getElementById('stat-active').textContent = userStats.active || 0;
    document.getElementById('stat-admin').textContent = userStats.by_role?.admin || 0;
    document.getElementById('stat-lab').textContent = userStats.by_role?.petugas_lab || 0;
}

// Update user count
function updateUserCount(count) {
    document.getElementById('user-count').textContent = `${count} users`;
}

// Get user phone number based on role
function getUserPhone(user, details) {
    if (user.role === 'administrasi' || user.role === 'petugas_lab'|| user.role === 'supervisor') {
        return details?.telepon || 'Tidak tersedia';
    }
    return 'Tidak tersedia';
}

// Render users table with fullwidth support
function renderUsersTable(users) {
    const tbody = document.getElementById('users-table-body');
    
    if (users.length === 0) {
        renderEmptyState();
        return;
    }

    tbody.innerHTML = users.map(user => {
        const roleNames = {
            'admin': 'Administrator',
            'administrasi': 'Staff Administrasi',
            'petugas_lab': 'Petugas Lab',
            'supervisor': 'Supervisor'
        };
        
        const roleColors = {
            'admin': 'bg-purple-100 text-purple-800',
            'administrasi': 'bg-blue-100 text-blue-800',
            'petugas_lab': 'bg-green-100 text-green-800',
            'supervisor': 'bg-yellow-100 text-yellow-800'
        };

        const isCurrentUser = user.user_id == '<?= $this->session->userdata("user_id") ?>';
        const avatar = user.nama_lengkap ? user.nama_lengkap.substring(0, 2).toUpperCase() : user.username.substring(0, 2).toUpperCase();
        
        return `
            <tr class="hover:bg-gray-50 transition-colors duration-200 fullwidth-container">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-semibold text-sm">
                            ${avatar}
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${user.nama_lengkap || user.username}</div>
                            <div class="text-sm text-gray-500">ID: ${user.user_id}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${user.username}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${roleColors[user.role]}">
                        ${roleNames[user.role]}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${user.telepon || 'Tidak tersedia'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${user.is_active == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        <i data-lucide="${user.is_active == 1 ? 'check' : 'x'}" class="w-3 h-3 mr-1"></i>
                        ${user.is_active == 1 ? 'Aktif' : 'Nonaktif'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${formatDate(user.created_at)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        <button onclick="editUser(${user.user_id})" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                            <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                            Edit
                        </button>
                        ${!isCurrentUser ? `
                            <button onclick="toggleUserStatus(${user.user_id}, ${user.is_active})" 
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md ${user.is_active == 1 ? 'text-orange-700 bg-orange-100 hover:bg-orange-200' : 'text-green-700 bg-green-100 hover:bg-green-200'} transition-colors duration-200">
                                <i data-lucide="${user.is_active == 1 ? 'user-x' : 'user-check'}" class="w-3 h-3 mr-1"></i>
                                ${user.is_active == 1 ? 'Nonaktifkan' : 'Aktifkan'}
                            </button>
                            <button onclick="deleteUser(${user.user_id})" 
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 transition-colors duration-200">
                                <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                Hapus
                            </button>
                        ` : `
                            <span class="text-xs text-gray-500">Akun Anda</span>
                        `}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    lucide.createIcons();
    ensureFullwidthLayout();
}

// Reset password function
async function resetPassword(userId) {
    openModal(
        'Reset Password',
        'Apakah Anda yakin ingin mereset password pengguna ini ke default?',
        'Reset Password',
        async () => {
            try {
                const response = await fetch(`<?= base_url("admin/ajax_reset_password") ?>/${userId}`, {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showFlashMessage('success', data.message + (data.new_password ? ' Password baru: ' + data.new_password : ''));
                } else {
                    showFlashMessage('error', data.message);
                }
            } catch (error) {
                console.error('Error resetting password:', error);
                showFlashMessage('error', 'Gagal mereset password');
            }
        },
        'bg-orange-100 text-orange-700 hover:bg-orange-200 border border-transparent'
    );
}

// Toggle add form
function toggleAddForm() {
    const form = document.getElementById('add-user-form');
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        form.classList.add('slide-down');
    } else {
        form.classList.add('hidden');
    }
    ensureFullwidthLayout();
}

function closeAddForm() {
    const form = document.getElementById('add-user-form');
    form.classList.add('hidden');
    document.getElementById('user-form').reset();
    clearValidationErrors();
    document.getElementById('administrasi_fields').classList.add('hidden');
    document.getElementById('petugas_lab_fields').classList.add('hidden');
     document.getElementById('supervisor_fields').classList.add('hidden');
    ensureFullwidthLayout();
}

// Toggle role fields
function toggleRoleFields() {
    const role = document.getElementById('role').value;
    const administrasiFields = document.getElementById('administrasi_fields');
    const petugasLabFields = document.getElementById('petugas_lab_fields');
    const supervisorFields = document.getElementById('supervisor_fields');
    
    // Hide all role-specific fields
    administrasiFields.classList.add('hidden');
    petugasLabFields.classList.add('hidden');
    supervisorFields.classList.add('hidden');
    
    // Show relevant fields based on role
    if (role === 'administrasi') {
        administrasiFields.classList.remove('hidden');
    } else if (role === 'petugas_lab') {
        petugasLabFields.classList.remove('hidden');
    } else if (role === 'supervisor') {
        supervisorFields.classList.remove('hidden');
    }
    ensureFullwidthLayout();
}

// Handle form submission
document.getElementById('user-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    const originalContent = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 loading mr-2"></i>Menyimpan...';
    lucide.createIcons();
    
    try {
        const formData = new FormData(this);
        const response = await fetch('<?= base_url("admin/ajax_create_user") ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showFlashMessage('success', data.message);
            closeAddForm();
            loadUsersData(); // Reload data
        } else {
            if (data.errors) {
                displayValidationErrors(data.errors);
            } else {
                showFlashMessage('error', data.message);
            }
        }
    } catch (error) {
        console.error('Error creating user:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat menyimpan data');
    } finally {
        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalContent;
        lucide.createIcons();
    }
});

// Search users
function searchUsers() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const roleFilter = document.getElementById('role-filter').value;
    
    let filteredUsers = allUsers;
    
    if (searchTerm) {
        filteredUsers = filteredUsers.filter(user => 
            user.username.toLowerCase().includes(searchTerm) ||
            (user.nama_lengkap && user.nama_lengkap.toLowerCase().includes(searchTerm)) ||
            (user.telepon && user.telepon.toLowerCase().includes(searchTerm))
        );
    }
    
    if (roleFilter) {
        filteredUsers = filteredUsers.filter(user => user.role === roleFilter);
    }
    
    renderUsersTable(filteredUsers);
    updateUserCount(filteredUsers.length);
    ensureFullwidthLayout();
}

// Filter users
function filterUsers() {
    searchUsers(); // Use the same logic as search
}

// Edit user
async function editUser(userId) {
    try {
        const response = await fetch(`<?= base_url("admin/ajax_get_user_details") ?>/${userId}`);
        const data = await response.json();
        
        if (data.success) {
            populateEditForm(data.user, data.details);
            document.getElementById('edit-modal').classList.remove('hidden');
        } else {
            showFlashMessage('error', data.message);
        }
    } catch (error) {
        console.error('Error loading user details:', error);
        showFlashMessage('error', 'Gagal memuat detail pengguna');
    }
}

function populateEditForm(user, details) {
    const userPhone = getUserPhone(user, details);
    
    const formContent = `
        <input type="hidden" name="user_id" value="${user.user_id}">
        
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" value="${user.username}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" name="password" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan password baru">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="${details.nama_lengkap || ''}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            
            ${user.role === 'administrasi' ? `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" name="telepon" value="${details.telepon || ''}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            ` : ''}
            
            ${user.role === 'petugas_lab' ? `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Keahlian</label>
                        <input type="text" name="jenis_keahlian" value="${details.jenis_keahlian || ''}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" name="telepon" value="${details.telepon || ''}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">${details.alamat || ''}</textarea>
                </div>
            ` : ''}
            
            ${user.role === 'supervisor' ? `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Keahlian</label>
                        <input type="text" name="jenis_keahlian" value="${details.jenis_keahlian || ''}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" name="telepon" value="${details.telepon || ''}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">${details.alamat || ''}</textarea>
                </div>
            ` : ''}
            
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" 
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    `;
    document.getElementById('edit-form').innerHTML = formContent;
    
    // Handle edit form submission
    document.getElementById('edit-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const userId = formData.get('user_id');
        
        try {
            const response = await fetch(`<?= base_url("admin/ajax_update_user") ?>/${userId}`, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showFlashMessage('success', data.message);
                closeEditModal();
                loadUsersData();
            } else {
                showFlashMessage('error', data.message);
            }
        } catch (error) {
            console.error('Error updating user:', error);
            showFlashMessage('error', 'Gagal mengupdate pengguna');
        }
    });
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
    ensureFullwidthLayout();
}

// Toggle user status
async function toggleUserStatus(userId, currentStatus) {
    const action = currentStatus == 1 ? 'menonaktifkan' : 'mengaktifkan';
    const confirmBtnText = currentStatus == 1 ? 'Nonaktifkan' : 'Aktifkan';
    const colorClass = currentStatus == 1 
        ? 'bg-red-100 text-red-700 hover:bg-red-200 border border-transparent' 
        : 'bg-green-100 text-green-700 hover:bg-green-200 border border-transparent';
    
    openModal(
        'Konfirmasi Status',
        `Apakah Anda yakin ingin ${action} pengguna ini?`,
        confirmBtnText,
        async () => {
            try {
                const response = await fetch(`<?= base_url("admin/ajax_toggle_user_status") ?>/${userId}`, {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showFlashMessage('success', data.message);
                    loadUsersData();
                } else {
                    showFlashMessage('error', data.message);
                }
            } catch (error) {
                console.error('Error toggling user status:', error);
                showFlashMessage('error', 'Gagal mengubah status pengguna');
            }
        },
        colorClass
    );
}

// Delete user
async function deleteUser(userId) {
    openModal(
        'Hapus Pengguna',
        'Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.',
        'Hapus Permanen',
        async () => {
            try {
                const response = await fetch(`<?= base_url("admin/ajax_delete_user") ?>/${userId}`, {
                    method: 'DELETE'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showFlashMessage('success', data.message);
                    loadUsersData();
                } else {
                    showFlashMessage('error', data.message);
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                showFlashMessage('error', 'Gagal menghapus pengguna');
            }
        },
        'bg-red-100 text-red-700 hover:bg-red-200 border border-transparent'
    );
}

// Export users
function exportUsers() {
    // Simple CSV export
    const csv = ['Username,Nama Lengkap,Role,Nomor Telepon,Status,Tanggal Dibuat'];
    
    allUsers.forEach(user => {
        const roleNames = {
            'admin': 'Administrator',
            'administrasi': 'Staff Administrasi',
            'petugas_lab': 'Petugas Lab'
        };
        
        csv.push([
            user.username,
            user.nama_lengkap || '',
            roleNames[user.role],
            user.telepon || 'Tidak tersedia',
            user.is_active == 1 ? 'Aktif' : 'Nonaktif',
            formatDate(user.created_at)
        ].join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `users_export_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
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

function showFlashMessage(type, message) {
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
    // Clear previous errors
    clearValidationErrors();
    
    Object.keys(errors).forEach(field => {
        const errorElement = document.getElementById(`${field}-error`);
        if (errorElement) {
            errorElement.textContent = errors[field];
            errorElement.classList.remove('hidden');
        }
    });
}

function clearValidationErrors() {
    const errorElements = document.querySelectorAll('[id$="-error"]');
    errorElements.forEach(element => {
        element.textContent = '';
        element.classList.add('hidden');
    });
}

// Window resize handler to maintain fullwidth
window.addEventListener('resize', function() {
    ensureFullwidthLayout();
});
</script>

</body>
</html>