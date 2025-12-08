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
        /* Custom scrollbar - konsisten dengan design sebelumnya */
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
    </style>
</head>
<body class="bg-gray-50 fullwidth-container">

<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
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
            <button onclick="openCreateModal()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Tambah Pasien</span>
            </button>
            <button onclick="exportToExcel()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Export Data</span>
            </button>
        </div>
    </div>
</div>

<!-- Main Content - Full Width -->
<div class="p-6 space-y-6 fullwidth-container">
    
    <!-- Flash Messages Container -->
    <div id="flash-messages"></div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pasien</p>
                    <p id="stat-total" class="text-2xl font-bold text-gray-900">-</p>
                    <p class="text-xs text-gray-500 mt-1">Seluruh pasien terdaftar</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pasien Hari Ini</p>
                    <p id="stat-today" class="text-2xl font-bold text-green-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Registrasi hari ini</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Laki-laki</p>
                    <p id="stat-male" class="text-2xl font-bold text-purple-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Jenis kelamin L</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Perempuan</p>
                    <p id="stat-female" class="text-2xl font-bold text-pink-600">-</p>
                    <p class="text-xs text-gray-500 mt-1">Jenis kelamin P</p>
                </div>
                <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-6 h-6 text-pink-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Patients Table - Full Width -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 fullwidth-container">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                    <span>Daftar Pasien</span>
                    <span id="patient-count" class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        0 pasien
                    </span>
                </h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" 
                               id="search-input"
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64"
                               placeholder="Cari pasien..."
                               onkeyup="searchPatients()">
                        <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                    </div>
                    <select id="gender-filter" 
                            class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            onchange="filterPatients()">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <button onclick="loadPatientsData()" 
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            title="Refresh Data">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto table-container">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Umur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asal Rujukan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="patients-table-body" class="bg-white divide-y divide-gray-200">
                    <tr id="loading-row">
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <i data-lucide="loader" class="w-5 h-5 text-blue-600 loading"></i>
                                <span class="text-gray-600">Memuat data pasien...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal dengan Validasi NIK yang Diperbaiki -->
<div id="create-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="p-6 border-b border-gray-100">
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
        <form id="create-form" class="p-6">
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
                            <input type="text" name="nama" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   required>
                        </div>
                        
                        <!-- PERBAIKAN: Input NIK dengan validasi lengkap -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                NIK (16 digit) *
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="nik-create" 
                                       name="nik" 
                                       class="w-full px-3 py-2 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       maxlength="16" 
                                       inputmode="numeric"
                                       pattern="[0-9]{16}"
                                       required
                                       oninput="validateNIK(this); updateNIKCounter(this, 'nik-counter-create')"
                                       onblur="checkNIKExists(this.value, 'nik-message-create', null)"
                                       placeholder="Masukkan 16 digit NIK"
                                       autocomplete="off">
                                <span id="nik-counter-create" 
                                      class="absolute right-3 top-2.5 text-xs font-medium text-gray-500">
                                    0/16
                                </span>
                            </div>
                            <div id="nik-message-create" class="mt-1 text-xs"></div>
                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                NIK harus 16 digit angka dan unik
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                            <select name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   required onchange="calculateAge()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                            <input type="text" name="pekerjaan" 
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
                            <input type="tel" name="telepon" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Darurat</label>
                            <input type="text" name="kontak_darurat" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                            <textarea name="alamat_domisili" rows="3" 
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
                            <textarea name="riwayat_pasien" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Permintaan Pemeriksaan</label>
                            <textarea name="permintaan_pemeriksaan" rows="2" 
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
                            <input type="text" name="dokter_perujuk" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Asal Rujukan</label>
                            <input type="text" name="asal_rujukan" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rujukan</label>
                            <input type="text" name="nomor_rujukan" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Rujukan</label>
                            <input type="date" name="tanggal_rujukan" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Awal</label>
                            <textarea name="diagnosis_awal" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi Pemeriksaan</label>
                            <textarea name="rekomendasi_pemeriksaan" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 mt-6">
                <button type="button" onclick="closeCreateModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" id="submit-create-btn"
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
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Edit Data Pasien</h3>
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

<!-- View Patient Details Modal -->
<div id="view-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Detail Data Pasien</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div id="view-content">
                <!-- Patient details will be populated here -->
            </div>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url() ?>';
let allPatients = [];
let patientStats = {};
let nikCheckTimeout = null; // Global timeout untuk debouncing

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadPatientsData();
    ensureFullwidthLayout();
    
    // Setup form validation untuk create form
    setupCreateFormValidation();
});

// Ensure fullwidth layout
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

// Calculate age from birth date
function calculateAge() {
    const birthDate = new Date(document.getElementById('tanggal_lahir').value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    // Store age for later use
    if (age >= 0) {
        console.log('Calculated age:', age);
    }
}

// Open create modal
function openCreateModal() {
    document.getElementById('create-modal').classList.remove('hidden');
    document.getElementById('create-form').reset();
    
    // Reset NIK validation UI
    const nikInput = document.getElementById('nik-create');
    const nikMessage = document.getElementById('nik-message-create');
    const nikCounter = document.getElementById('nik-counter-create');
    
    if (nikInput) {
        nikInput.classList.remove('border-red-300', 'border-yellow-300', 'border-green-500');
        nikInput.classList.add('border-gray-300');
    }
    if (nikMessage) nikMessage.innerHTML = '';
    if (nikCounter) {
        nikCounter.textContent = '0/16';
        nikCounter.classList.remove('text-red-600', 'text-yellow-600', 'text-green-600');
        nikCounter.classList.add('text-gray-500');
    }
    
    lucide.createIcons();
}

// Close create modal
function closeCreateModal() {
    document.getElementById('create-modal').classList.add('hidden');
    ensureFullwidthLayout();
}

/**
 * PERBAIKAN: Setup validation untuk create form
 */
function setupCreateFormValidation() {
    const form = document.getElementById('create-form');
    const nikInput = document.getElementById('nik-create');
    const submitBtn = document.getElementById('submit-create-btn');
    
    if (!form || !nikInput) return;
    
    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validasi NIK harus 16 digit
        if (nikInput.value.length !== 16) {
            showFlashMessage('error', 'NIK harus 16 digit! Saat ini: ' + nikInput.value.length + ' digit');
            nikInput.focus();
            nikInput.select();
            return false;
        }
        
        // Check if submit button is disabled (NIK already exists)
        if (submitBtn && submitBtn.disabled) {
            showFlashMessage('error', 'NIK sudah terdaftar! Silakan gunakan NIK yang berbeda.');
            nikInput.focus();
            nikInput.select();
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
            const formData = new FormData(form);
            
            const response = await fetch(BASE_URL + 'pasien/ajax_create_patient', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showFlashMessage('success', data.message);
                closeCreateModal();
                loadPatientsData();
            } else {
                showFlashMessage('error', data.message || 'Gagal menambahkan pasien');
                
                // Show validation errors if available
                if (data.errors) {
                    let errorMsg = '<ul class="list-disc ml-5">';
                    for (let field in data.errors) {
                        errorMsg += `<li>${data.errors[field]}</li>`;
                    }
                    errorMsg += '</ul>';
                    showFlashMessage('error', errorMsg);
                }
            }
        } catch (error) {
            console.error('Error creating patient:', error);
            showFlashMessage('error', 'Terjadi kesalahan sistem');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan</span>
            `;
            lucide.createIcons();
        }
    });
}

/**
 * PERBAIKAN: Validasi NIK - hanya angka, max 16 digit
 * Sesuai dengan controller Administrasi line 1765
 */
function validateNIK(input) {
    // Remove non-numeric characters
    let value = input.value.replace(/[^0-9]/g, '');
    
    // Limit to 16 digits
    if (value.length > 16) {
        value = value.substring(0, 16);
    }
    
    input.value = value;
    
    // Visual border feedback
    input.classList.remove('border-gray-300', 'border-red-300', 'border-yellow-300', 'border-green-500');
    
    if (value.length === 16) {
        input.classList.add('border-green-500');
    } else if (value.length > 10) {
        input.classList.add('border-yellow-300');
    } else if (value.length > 0) {
        input.classList.add('border-red-300');
    } else {
        input.classList.add('border-gray-300');
    }
}

/**
 * PERBAIKAN: Update NIK counter display (0/16 format)
 */
function updateNIKCounter(input, counterId) {
    const counter = document.getElementById(counterId);
    if (!counter) return;
    
    const length = input.value.length;
    const maxLength = 16;
    
    // Update counter text
    counter.textContent = `${length}/${maxLength}`;
    
    // Update counter color based on length
    counter.classList.remove('text-gray-500', 'text-red-600', 'text-yellow-600', 'text-green-600');
    
    if (length === maxLength) {
        counter.classList.add('text-green-600', 'font-bold');
    } else if (length > 10) {
        counter.classList.add('text-yellow-600');
    } else if (length > 0) {
        counter.classList.add('text-red-600');
    } else {
        counter.classList.add('text-gray-500');
    }
}

/**
 * PERBAIKAN: Check if NIK already exists via AJAX
 * Sesuai dengan function di Administrasi
 */
async function checkNIKExists(nik, messageElementId, excludePatientId = null) {
    const messageElement = document.getElementById(messageElementId);
    const submitBtn = document.getElementById('submit-create-btn') || 
                      document.querySelector('#edit-form button[type="submit"]');
    
    if (!messageElement) return;
    
    // Clear previous timeout
    if (nikCheckTimeout) {
        clearTimeout(nikCheckTimeout);
    }
    
    // Reset message if NIK is empty or not 16 digits
    if (!nik || nik.length !== 16) {
        messageElement.innerHTML = '';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
        return;
    }
    
    // Show checking status
    messageElement.innerHTML = `
        <span class="flex items-center text-blue-600 animate-pulse">
            <svg class="animate-spin h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memeriksa NIK...
        </span>
    `;
    
    // Delay to avoid too many requests (debouncing)
    nikCheckTimeout = setTimeout(async () => {
        try {
            // Build URL with exclude_id parameter if provided
            let url = BASE_URL + `pasien/check_nik_exists?nik=${encodeURIComponent(nik)}`;
            if (excludePatientId) {
                url += `&exclude_id=${excludePatientId}`;
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.exists) {
                // NIK already exists
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
                
                // Disable submit button
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                    submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                }
            } else {
                // NIK available
                messageElement.innerHTML = `
                    <span class="flex items-center text-green-600 bg-green-50 px-2 py-1 rounded mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-sm">NIK tersedia</span>
                    </span>
                `;
                
                // Enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                    submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }
            }
            
        } catch (error) {
            console.error('Error checking NIK:', error);
            messageElement.innerHTML = `
                <span class="flex items-center text-yellow-600 bg-yellow-50 px-2 py-1 rounded mt-1">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs font-medium">Gagal memeriksa NIK, coba lagi</span>
                </span>
            `;
            
            // Enable submit button on error (allow user to proceed)
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }
        }
    }, 500); // 500ms delay for debouncing
}

// Load patients data
async function loadPatientsData() {
    try {
        const response = await fetch(BASE_URL + 'pasien/get_patients_data');
        const data = await response.json();
        
        if (data.success) {
            allPatients = data.patients || [];
            patientStats = data.stats || {};
            
            updateStatistics();
            displayPatients(allPatients);
        } else {
            showFlashMessage('error', 'Gagal memuat data pasien');
        }
    } catch (error) {
        console.error('Error loading patients:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memuat data');
    }
}

// Update statistics
function updateStatistics() {
    document.getElementById('stat-total').textContent = patientStats.total || 0;
    document.getElementById('stat-today').textContent = patientStats.today || 0;
    document.getElementById('stat-male').textContent = patientStats.male || 0;
    document.getElementById('stat-female').textContent = patientStats.female || 0;
}

// Display patients in table
function displayPatients(patients) {
    const tbody = document.getElementById('patients-table-body');
    const patientCount = document.getElementById('patient-count');
    
    if (!patients || patients.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center space-y-2">
                        <i data-lucide="inbox" class="w-12 h-12 text-gray-400"></i>
                        <p class="text-gray-600">Tidak ada data pasien</p>
                    </div>
                </td>
            </tr>
        `;
        patientCount.textContent = '0 pasien';
        lucide.createIcons();
        return;
    }
    
    patientCount.textContent = `${patients.length} pasien`;
    
    tbody.innerHTML = patients.map(patient => `
        <tr class="hover:bg-gray-50 transition-colors duration-150">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                        ${patient.nama.charAt(0).toUpperCase()}
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${patient.nama}</div>
                        <div class="text-xs text-gray-500">${patient.nomor_registrasi}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${patient.nik || '-'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                    patient.jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'
                }">
                    ${patient.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${patient.umur || '-'} tahun
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${patient.asal_rujukan || '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${formatDate(patient.created_at)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button onclick="viewPatient(${patient.pasien_id})" 
                        class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                        title="Lihat Detail">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                </button>
                <button onclick="editPatient(${patient.pasien_id})" 
                        class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200"
                        title="Edit">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                </button>
                <button onclick="deletePatient(${patient.pasien_id}, '${patient.nama}')" 
                        class="text-red-600 hover:text-red-900 transition-colors duration-200"
                        title="Hapus">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        </tr>
    `).join('');
    
    lucide.createIcons();
}

// View patient details
async function viewPatient(patientId) {
    try {
        const response = await fetch(BASE_URL + `pasien/ajax_get_patient_details/${patientId}`);
        const data = await response.json();
        
        if (data.success) {
            displayPatientDetails(data.patient);
        } else {
            showFlashMessage('error', data.message);
        }
    } catch (error) {
        console.error('Error loading patient details:', error);
        showFlashMessage('error', 'Gagal memuat detail pasien');
    }
}

// Display patient details
function displayPatientDetails(patient) {
    const viewContent = document.getElementById('view-content');
    
    const detailsHTML = `
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                        ${patient.nama.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">${patient.nama}</h2>
                        <p class="text-blue-600 font-medium">${patient.nomor_registrasi}</p>
                        <p class="text-sm text-gray-600">Terdaftar: ${formatDate(patient.created_at)}</p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                    <span>Informasi Pribadi</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">NIK:</span>
                        <span class="text-gray-900">${patient.nik || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Jenis Kelamin:</span>
                        <span class="text-gray-900">${patient.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Tempat, Tanggal Lahir:</span>
                        <span class="text-gray-900">${patient.tempat_lahir || '-'}, ${formatDate(patient.tanggal_lahir)}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Umur:</span>
                        <span class="text-gray-900">${patient.umur || '-'} tahun</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Pekerjaan:</span>
                        <span class="text-gray-900">${patient.pekerjaan || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Alamat:</span>
                        <span class="text-gray-900">${patient.alamat_domisili || '-'}</span>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <i data-lucide="phone" class="w-5 h-5 text-blue-600"></i>
                    <span>Informasi Kontak</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Telepon:</span>
                        <span class="text-gray-900">${patient.telepon || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Kontak Darurat:</span>
                        <span class="text-gray-900">${patient.kontak_darurat || '-'}</span>
                    </div>
                </div>
            </div>

            <!-- Medical Info -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <i data-lucide="clipboard" class="w-5 h-5 text-blue-600"></i>
                    <span>Informasi Medis</span>
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="font-medium text-gray-700 block mb-2">Riwayat Penyakit:</span>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">${patient.riwayat_pasien || 'Tidak ada riwayat penyakit'}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 block mb-2">Permintaan Pemeriksaan:</span>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">${patient.permintaan_pemeriksaan || 'Tidak ada permintaan khusus'}</p>
                    </div>
                </div>
            </div>

            <!-- Referral Info -->
            ${patient.dokter_perujuk || patient.asal_rujukan ? `
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <i data-lucide="external-link" class="w-5 h-5 text-blue-600"></i>
                    <span>Informasi Rujukan</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Dokter Perujuk:</span>
                        <span class="text-gray-900">${patient.dokter_perujuk || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Asal Rujukan:</span>
                        <span class="text-gray-900">${patient.asal_rujukan || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">No. Rujukan:</span>
                        <span class="text-gray-900">${patient.nomor_rujukan || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Tgl. Rujukan:</span>
                        <span class="text-gray-900">${patient.tanggal_rujukan ? formatDate(patient.tanggal_rujukan) : '-'}</span>
                    </div>
                </div>

                ${patient.diagnosis_awal ? `
                <div class="mt-4">
                    <span class="font-medium text-gray-700 block mb-2">Diagnosis Awal:</span>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">${patient.diagnosis_awal}</p>
                </div>
                ` : ''}

                ${patient.rekomendasi_pemeriksaan ? `
                <div class="mt-4">
                    <span class="font-medium text-gray-700 block mb-2">Rekomendasi Pemeriksaan:</span>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">${patient.rekomendasi_pemeriksaan}</p>
                </div>
                ` : ''}
            </div>
            ` : ''}

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <button onclick="closeViewModal()" 
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Tutup
                </button>
                <button onclick="closeViewModal(); editPatient(${patient.pasien_id})" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors duration-200">
                    Edit Data
                </button>
            </div>
        </div>
    `;
    
    viewContent.innerHTML = detailsHTML;
    document.getElementById('view-modal').classList.remove('hidden');
    lucide.createIcons();
}

function closeViewModal() {
    document.getElementById('view-modal').classList.add('hidden');
    ensureFullwidthLayout();
}

// Edit patient
async function editPatient(patientId) {
    try {
        const response = await fetch(BASE_URL + `pasien/ajax_get_patient_details/${patientId}`);
        const data = await response.json();
        
        if (data.success) {
            populateEditForm(data.patient);
            document.getElementById('edit-modal').classList.remove('hidden');
        } else {
            showFlashMessage('error', data.message);
        }
    } catch (error) {
        console.error('Error loading patient details:', error);
        showFlashMessage('error', 'Gagal memuat detail pasien');
    }
}

function populateEditForm(patient) {
    const formContent = `
        <input type="hidden" name="patient_id" value="${patient.pasien_id}">
        
        <div class="space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">Data Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="nama" value="${patient.nama}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <!-- PERBAIKAN: Input NIK dengan validasi untuk Edit Form -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            NIK (16 digit) *
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="nik-edit" 
                                   name="nik" 
                                   value="${patient.nik || ''}"
                                   class="w-full px-3 py-2 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   maxlength="16" 
                                   inputmode="numeric"
                                   pattern="[0-9]{16}"
                                   required
                                   oninput="validateNIK(this); updateNIKCounter(this, 'nik-counter-edit')"
                                   onblur="checkNIKExists(this.value, 'nik-message-edit', ${patient.pasien_id})"
                                   placeholder="Masukkan 16 digit NIK"
                                   autocomplete="off">
                            <span id="nik-counter-edit" 
                                  class="absolute right-3 top-2.5 text-xs font-medium text-green-600">
                                ${(patient.nik || '').length}/16
                            </span>
                        </div>
                        <div id="nik-message-edit" class="mt-1 text-xs"></div>
                        <p class="text-xs text-gray-500 mt-1 flex items-center">
                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                            NIK harus 16 digit angka dan unik
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                            <option value="L" ${patient.jenis_kelamin === 'L' ? 'selected' : ''}>Laki-laki</option>
                            <option value="P" ${patient.jenis_kelamin === 'P' ? 'selected' : ''}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="${patient.tempat_lahir || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" value="${patient.tanggal_lahir || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                               onchange="calculateEditAge()" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Umur (tahun)</label>
                        <input type="number" name="umur" value="${patient.umur || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="${patient.pekerjaan || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <!-- Kontak & Alamat -->
            <div class="bg-green-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800 mb-4">Kontak & Alamat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon *</label>
                        <input type="tel" name="telepon" value="${patient.telepon || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Darurat</label>
                        <input type="text" name="kontak_darurat" value="${patient.kontak_darurat || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat_domisili" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">${patient.alamat_domisili || ''}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Data Medis -->
            <div class="bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-800 mb-4">Data Medis</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Riwayat Pasien</label>
                        <textarea name="riwayat_pasien" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">${patient.riwayat_pasien || ''}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Permintaan Pemeriksaan</label>
                        <textarea name="permintaan_pemeriksaan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">${patient.permintaan_pemeriksaan || ''}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Data Rujukan -->
            <div class="bg-orange-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-orange-800 mb-4">Data Rujukan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dokter Perujuk</label>
                        <input type="text" name="dokter_perujuk" value="${patient.dokter_perujuk || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asal Rujukan</label>
                        <input type="text" name="asal_rujukan" value="${patient.asal_rujukan || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rujukan</label>
                        <input type="text" name="nomor_rujukan" value="${patient.nomor_rujukan || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Rujukan</label>
                        <input type="date" name="tanggal_rujukan" value="${patient.tanggal_rujukan || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Awal</label>
                        <textarea name="diagnosis_awal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">${patient.diagnosis_awal || ''}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi Pemeriksaan</label>
                        <textarea name="rekomendasi_pemeriksaan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">${patient.rekomendasi_pemeriksaan || ''}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" 
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" id="submit-edit-btn"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('edit-form').innerHTML = formContent;
    lucide.createIcons();
    
    // Initialize NIK counter for edit form
    const nikEditInput = document.getElementById('nik-edit');
    if (nikEditInput) {
        updateNIKCounter(nikEditInput, 'nik-counter-edit');
    }
    
    // Handle edit form submission
    document.getElementById('edit-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const patientId = formData.get('patient_id');
        const nikInput = document.getElementById('nik-edit');
        const submitBtn = document.getElementById('submit-edit-btn');
        
        // Validasi NIK harus 16 digit
        if (nikInput.value.length !== 16) {
            showFlashMessage('error', 'NIK harus 16 digit! Saat ini: ' + nikInput.value.length + ' digit');
            nikInput.focus();
            nikInput.select();
            return false;
        }
        
        // Check if submit button is disabled (NIK already exists)
        if (submitBtn && submitBtn.disabled) {
            showFlashMessage('error', 'NIK sudah terdaftar! Silakan gunakan NIK yang berbeda.');
            nikInput.focus();
            nikInput.select();
            return false;
        }
        
        // Calculate and add age
        const birthDate = formData.get('tanggal_lahir');
        if (birthDate) {
            const birth = new Date(birthDate);
            const today = new Date();
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            formData.set('umur', age);
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
            const response = await fetch(BASE_URL + `pasien/ajax_update_patient/${patientId}`, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showFlashMessage('success', data.message);
                closeEditModal();
                loadPatientsData();
            } else {
                showFlashMessage('error', data.message || 'Gagal mengupdate pasien');
                
                // Show validation errors if available
                if (data.errors) {
                    let errorMsg = '<ul class="list-disc ml-5">';
                    for (let field in data.errors) {
                        errorMsg += `<li>${data.errors[field]}</li>`;
                    }
                    errorMsg += '</ul>';
                    showFlashMessage('error', errorMsg);
                }
            }
        } catch (error) {
            console.error('Error updating patient:', error);
            showFlashMessage('error', 'Terjadi kesalahan sistem');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Simpan Perubahan';
        }
    });
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
    ensureFullwidthLayout();
}

function calculateEditAge() {
    const birthDateInput = document.querySelector('#edit-form input[name="tanggal_lahir"]');
    const umurInput = document.querySelector('#edit-form input[name="umur"]');
    
    if (!birthDateInput || !umurInput) return;
    
    const birthDate = new Date(birthDateInput.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    if (age >= 0) {
        umurInput.value = age;
    }
}

// Delete patient
async function deletePatient(patientId, patientName) {
    if (!confirm(`Apakah Anda yakin ingin menghapus pasien "${patientName}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
        return;
    }
    
    try {
        const response = await fetch(BASE_URL + `pasien/ajax_delete_patient/${patientId}`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showFlashMessage('success', data.message);
            loadPatientsData();
        } else {
            showFlashMessage('error', data.message);
        }
    } catch (error) {
        console.error('Error deleting patient:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat menghapus pasien');
    }
}

// Search patients
function searchPatients() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const genderFilter = document.getElementById('gender-filter').value;
    
    let filteredPatients = allPatients.filter(patient => {
        const matchesSearch = !searchTerm || 
            patient.nama.toLowerCase().includes(searchTerm) ||
            (patient.nik && patient.nik.includes(searchTerm)) ||
            (patient.nomor_registrasi && patient.nomor_registrasi.toLowerCase().includes(searchTerm));
        
        const matchesGender = !genderFilter || patient.jenis_kelamin === genderFilter;
        
        return matchesSearch && matchesGender;
    });
    
    displayPatients(filteredPatients);
}

// Filter patients
function filterPatients() {
    searchPatients();
}

// Export to Excel
function exportToExcel() {
    showFlashMessage('info', 'Fitur export sedang dalam pengembangan');
}

// Show flash message
function showFlashMessage(type, message) {
    const container = document.getElementById('flash-messages');
    
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800'
    };
    
    const icons = {
        success: 'check-circle',
        error: 'x-circle',
        info: 'info',
        warning: 'alert-triangle'
    };
    
    const messageId = 'flash-' + Date.now();
    const messageHTML = `
        <div id="${messageId}" class="fade-in ${colors[type]} border rounded-lg p-4 mb-4 flex items-start space-x-3">
            <i data-lucide="${icons[type]}" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <div class="flex-1">${message}</div>
            <button onclick="document.getElementById('${messageId}').remove()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', messageHTML);
    lucide.createIcons();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const element = document.getElementById(messageId);
        if (element) {
            element.style.opacity = '0';
            element.style.transition = 'opacity 0.3s';
            setTimeout(() => element.remove(), 300);
        }
    }, 5000);
}

// Format date
function formatDate(dateString) {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}
</script>

</body>
</html>