<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pasien - Labsys</title>
    
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
                                <i data-lucide="loader-2" class="w-5 h-5 text-blue-600 loading"></i>
                                <span class="text-gray-500">Memuat data pasien...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
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
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK (16 digit) *</label>
                            <input type="text" name="nik" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   maxlength="16" required>
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
                <button type="submit" 
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

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadPatientsData();
    ensureFullwidthLayout();
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
    lucide.createIcons();
}

// Close create modal
function closeCreateModal() {
    document.getElementById('create-modal').classList.add('hidden');
}

// Submit create form
document.getElementById('create-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Calculate and add age to form data
    const birthDate = formData.get('tanggal_lahir');
    if (birthDate) {
        const birth = new Date(birthDate);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        formData.append('umur', age);
    }
    
    try {
        const response = await fetch(BASE_URL + 'pasien/ajax_create_patient', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if(data.success) {
            showFlashMessage('success', data.message);
            closeCreateModal();
            loadPatientsData();
        } else {
            if (data.errors) {
                let errorMsg = 'Validasi gagal:\n';
                for (let field in data.errors) {
                    errorMsg += '- ' + data.errors[field] + '\n';
                }
                showFlashMessage('error', errorMsg);
            } else {
                showFlashMessage('error', data.message);
            }
        }
    } catch(error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat menyimpan data');
    }
});

// Load patients data
async function loadPatientsData() {
    try {
        const response = await fetch(BASE_URL + 'pasien/get_patients_data');
        const data = await response.json();
        
        if (data.success) {
            allPatients = data.patients;
            patientStats = data.stats;
            updateStatistics();
            renderPatientsTable(allPatients);
            updatePatientCount(allPatients.length);
        } else {
            showFlashMessage('error', 'Gagal memuat data pasien');
            renderEmptyState();
        }
    } catch (error) {
        console.error('Error loading patients:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memuat data');
        renderEmptyState();
    } finally {
        ensureFullwidthLayout();
    }
}

// Render empty state
function renderEmptyState() {
    const tbody = document.getElementById('patients-table-body');
    tbody.innerHTML = `
        <tr class="fullwidth-container">
            <td colspan="7" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i data-lucide="users" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pasien</h3>
                        <p class="text-gray-500 mb-4">Belum ada data pasien yang tersedia</p>
                        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 mx-auto">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            <span>Tambah Pasien Pertama</span>
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
    document.getElementById('stat-total').textContent = patientStats.total || 0;
    document.getElementById('stat-today').textContent = patientStats.today || 0;
    document.getElementById('stat-male').textContent = patientStats.male || 0;
    document.getElementById('stat-female').textContent = patientStats.female || 0;
}

// Update patient count
function updatePatientCount(count) {
    document.getElementById('patient-count').textContent = `${count} pasien`;
}

// Render patients table
function renderPatientsTable(patients) {
    const tbody = document.getElementById('patients-table-body');
    
    if (patients.length === 0) {
        renderEmptyState();
        return;
    }

    tbody.innerHTML = patients.map(patient => {
        const avatar = patient.nama.substring(0, 2).toUpperCase();
        const genderColor = patient.jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800';
        const genderText = patient.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
        
        return `
            <tr class="hover:bg-gray-50 transition-colors duration-200 fullwidth-container">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-semibold text-sm">
                            ${avatar}
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${patient.nama}</div>
                            <div class="text-sm text-gray-500">REG: ${patient.nomor_registrasi || '-'}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${patient.nik || '-'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${genderColor}">
                        ${genderText}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${patient.umur || '-'} tahun</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${patient.asal_rujukan || 'Tidak ada rujukan'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${formatDate(patient.created_at)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        <button onclick="viewPatient(${patient.pasien_id})" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                            Lihat
                        </button>
                        <button onclick="editPatient(${patient.pasien_id})" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                            <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                            Edit
                        </button>
                        <button onclick="deletePatient(${patient.pasien_id})" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 transition-colors duration-200">
                            <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
           
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    lucide.createIcons();
    ensureFullwidthLayout();
}

// Search patients
function searchPatients() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const genderFilter = document.getElementById('gender-filter').value;
    
    let filteredPatients = allPatients;
    
    if (searchTerm) {
        filteredPatients = filteredPatients.filter(patient => 
            patient.nama.toLowerCase().includes(searchTerm) ||
            (patient.nik && patient.nik.toLowerCase().includes(searchTerm)) ||
            (patient.telepon && patient.telepon.toLowerCase().includes(searchTerm)) ||
            (patient.nomor_registrasi && patient.nomor_registrasi.toLowerCase().includes(searchTerm))
        );
    }
    
    if (genderFilter) {
        filteredPatients = filteredPatients.filter(patient => patient.jenis_kelamin === genderFilter);
    }
    
    renderPatientsTable(filteredPatients);
    updatePatientCount(filteredPatients.length);
    ensureFullwidthLayout();
}

// Filter patients
function filterPatients() {
    searchPatients();
}

// View patient details
async function viewPatient(patientId) {
    try {
        const response = await fetch(BASE_URL + `pasien/ajax_get_patient_details/${patientId}`);
        const data = await response.json();
        
        if (data.success) {
            showPatientDetailsModal(data.patient);
        } else {
            showFlashMessage('error', data.message);
        }
    } catch (error) {
        console.error('Error loading patient details:', error);
        showFlashMessage('error', 'Gagal memuat detail pasien');
    }
}

// Show patient details modal
function showPatientDetailsModal(patient) {
    const viewContent = document.getElementById('view-content');
    
    const detailsHTML = `
        <div class="space-y-6">
            <!-- Patient Avatar & Basic Info -->
            <div class="flex items-center space-x-4 p-4 bg-blue-50 rounded-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                    ${patient.nama.substring(0, 2).toUpperCase()}
                </div>
                <div>
                    <h4 class="text-xl font-bold text-gray-900">${patient.nama}</h4>
                    <p class="text-sm text-gray-600">No. Registrasi: ${patient.nomor_registrasi || '-'}</p>
                    <p class="text-sm text-gray-600">Didaftarkan: ${formatDate(patient.created_at)}</p>
                </div>
            </div>

            <!-- Data Pribadi -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                    <span>Data Pribadi</span>
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
                        <span class="font-medium text-gray-700">Tempat Lahir:</span>
                        <span class="text-gray-900">${patient.tempat_lahir || '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Tanggal Lahir:</span>
                        <span class="text-gray-900">${patient.tanggal_lahir ? formatDate(patient.tanggal_lahir) : '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Umur:</span>
                        <span class="text-gray-900">${patient.umur ? patient.umur + ' tahun' : '-'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Pekerjaan:</span>
                        <span class="text-gray-900">${patient.pekerjaan || '-'}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Alamat:</span>
                        <span class="text-gray-900 text-right">${patient.alamat_domisili || '-'}</span>
                    </div>
                </div>
            </div>

            <!-- Kontak -->
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

            <!-- Informasi Medis -->
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

            <!-- Informasi Rujukan -->
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK *</label>
                        <input type="text" name="nik" value="${patient.nik || ''}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                               maxlength="16" required>
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
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('edit-form').innerHTML = formContent;
    lucide.createIcons();
    
    // Handle edit form submission
    document.getElementById('edit-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const patientId = formData.get('patient_id');
        
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
                if (data.errors) {
                    let errorMsg = 'Validasi gagal:\n';
                    for (let field in data.errors) {
                        errorMsg += '- ' + data.errors[field] + '\n';
                    }
                    showFlashMessage('error', errorMsg);
                } else {
                    showFlashMessage('error', data.message);
                }
            }
        } catch (error) {
            console.error('Error updating patient:', error);
            showFlashMessage('error', 'Gagal mengupdate pasien');
        }
    });
}

// Calculate age for edit form
function calculateEditAge() {
    const birthDateInput = document.querySelector('input[name="tanggal_lahir"]');
    const ageInput = document.querySelector('input[name="umur"]');
    
    if (birthDateInput && ageInput && birthDateInput.value) {
        const birthDate = new Date(birthDateInput.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        ageInput.value = age >= 0 ? age : '';
    }
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
    ensureFullwidthLayout();
}

// Delete patient
async function deletePatient(patientId) {
    if (!confirm('Apakah Anda yakin ingin menghapus data pasien ini? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    try {
        const response = await fetch(BASE_URL + `pasien/ajax_delete_patient/${patientId}`, {
            method: 'DELETE'
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
        showFlashMessage('error', 'Gagal menghapus pasien');
    }
}

function exportToExcel() {
    const filters = getCurrentFilters();
    const params = new URLSearchParams(filters);
    window.open(BASE_URL + 'excel_controller/export_patients?' + params.toString(), '_blank');
}

function getCurrentFilters() {
    return {
        search: document.getElementById('search-input').value || '',
        gender: document.getElementById('gender-filter').value || '',
        start_date: '',
        end_date: ''
    };
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
                <p class="text-sm font-medium whitespace-pre-line">${message}</p>
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

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
        closeViewModal();
    }
});

// Close modal on backdrop click
document.getElementById('create-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateModal();
    }
});

document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

document.getElementById('view-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeViewModal();
    }
});

// Window resize handler
window.addEventListener('resize', function() {
    ensureFullwidthLayout();
});
</script>

</body>
</html>