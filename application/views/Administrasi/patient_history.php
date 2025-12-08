<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pasien - LabSy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
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
    </style>
</head>
<body class="bg-gray-50">

<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="users" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Riwayat Pasien</h1>
                <p class="text-emerald-100">Kelola dan lihat data seluruh pasien terdaftar</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="exportPatients()" class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Export Data</span>
            </button>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="p-6 space-y-6">
    
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
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($total_patients) ?></p>
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
                    <p class="text-sm font-medium text-gray-600">Halaman</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $current_page ?></p>
                    <p class="text-xs text-gray-500 mt-1">Dari <?= ceil($total_patients / $limit) ?> halaman</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Data Per Halaman</p>
                    <p class="text-2xl font-bold text-green-600"><?= count($patients) ?></p>
                    <p class="text-xs text-gray-500 mt-1">Dari <?= $limit ?> maksimal</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="database" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Hasil Pencarian</p>
                    <p class="text-2xl font-bold text-orange-600"><?= $search ? 'Aktif' : 'Tidak' ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?= $search ? 'Filter diterapkan' : 'Tanpa filter' ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="filter" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar (Ganti bagian Search Section) -->
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
                    <span>Data Pasien</span>
                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        <?= number_format($total_patients) ?> pasien
                    </span>
                </h2>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
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
                                <div class="text-sm text-gray-900"><?= $patient['telepon'] ?></div>
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
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200"
                                       title="Edit Data">
                                        <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                                        Edit
                                    </button>
                  
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center space-y-2">
                                    <i data-lucide="users" class="w-12 h-12 text-gray-300"></i>
                                    <span class="text-lg font-medium">Tidak ada data pasien</span>
                                    <span class="text-sm"><?= $search ? 'Hasil pencarian tidak ditemukan' : 'Belum ada pasien yang terdaftar' ?></span>
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
                    $base_url = base_url('administrasi/patient_history?');
                    if($search) {
                        $base_url .= 'search=' . urlencode($search) . '&';
                    }
                    
                    // Previous button
                    if($current_page > 1): ?>
                        <a href="<?= $base_url ?>page=<?= $current_page - 1 ?>" 
                           class="px-3 py-1 text-sm rounded-md bg-white text-gray-700 hover:bg-gray-100 border border-gray-300">
                            ‹
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    // Page numbers
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    for($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="<?= $base_url ?>page=<?= $i ?>" 
                           class="px-3 py-1 text-sm rounded-md <?= $i == $current_page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?> border border-gray-300">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <!-- Next button -->
                    <?php if($current_page < $total_pages): ?>
                        <a href="<?= $base_url ?>page=<?= $current_page + 1 ?>" 
                           class="px-3 py-1 text-sm rounded-md bg-white text-gray-700 hover:bg-gray-100 border border-gray-300">
                            ›
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Detail Pasien</h3>
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
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Edit Data Pasien</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <form id="edit-form" class="p-6">
            <input type="hidden" id="edit-pasien-id" name="pasien_id">
            <div id="edit-content" class="space-y-6"></div>
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url() ?>';
let searchTimeout;
let currentSearch = '';
// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Auto-hide flash messages after 5 seconds
    setTimeout(() => {
        const flashMessages = document.querySelectorAll('#flash-messages > div');
        flashMessages.forEach(msg => {
            msg.style.transition = 'opacity 0.5s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        });
    }, 5000);
});
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
        
        const match = regNumber.includes(searchTerm) || 
                     name.includes(searchTerm) || 
                     nik.includes(searchTerm);
        
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
                       class="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-1"></i>
                        Reset Pencarian
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
// View patient detail
async function viewPatientDetail(patientId) {
    document.getElementById('detail-content').innerHTML = `
        <div class="flex justify-center py-8">
            <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 loading"></i>
        </div>
    `;
    
    document.getElementById('detail-modal').classList.remove('hidden');
    lucide.createIcons();
    
    try {
        const response = await fetch(BASE_URL + `administrasi/patient_detail/${patientId}`);
        const html = await response.text();
        
        // Extract content from response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const content = doc.querySelector('.container');
        
        if(content) {
            // Remove buttons from content
            const buttons = content.querySelectorAll('a.bg-blue-500, a.bg-gray-500');
            buttons.forEach(btn => btn.remove());
            
            document.getElementById('detail-content').innerHTML = content.innerHTML;
        } else {
            document.getElementById('detail-content').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <p>Gagal memuat data pasien</p>
                </div>
            `;
        }
    } catch(error) {
        console.error('Error:', error);
        document.getElementById('detail-content').innerHTML = `
            <div class="text-center py-8 text-red-600">
                <p>Terjadi kesalahan saat memuat data</p>
            </div>
        `;
    }
    
    lucide.createIcons();
}

// Edit patient
async function editPatient(patientId) {
    document.getElementById('edit-content').innerHTML = `
        <div class="flex justify-center py-8">
            <i data-lucide="loader-2" class="w-8 h-8 text-blue-600 loading"></i>
        </div>
    `;
    
    document.getElementById('edit-modal').classList.remove('hidden');
    document.getElementById('edit-pasien-id').value = patientId;
    lucide.createIcons();
    
    try {
        const response = await fetch(BASE_URL + `administrasi/edit_patient/${patientId}`);
        const html = await response.text();
        
        // Extract form from response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const form = doc.querySelector('form');
        
        if(form) {
            // Get form content without buttons
            const formContent = form.querySelector('.grid');
            if(formContent) {
                document.getElementById('edit-content').innerHTML = formContent.outerHTML;
            }
        } else {
            document.getElementById('edit-content').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <p>Gagal memuat form edit</p>
                </div>
            `;
        }
    } catch(error) {
        console.error('Error:', error);
        document.getElementById('edit-content').innerHTML = `
            <div class="text-center py-8 text-red-600">
                <p>Terjadi kesalahan saat memuat form</p>
            </div>
        `;
    }
    
    lucide.createIcons();
}

// Submit edit form
document.getElementById('edit-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const patientId = document.getElementById('edit-pasien-id').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch(BASE_URL + `administrasi/edit_patient/${patientId}`, {
            method: 'POST',
            body: formData
        });
        
        if(response.ok) {
            showFlashMessage('success', 'Data pasien berhasil diperbarui');
            closeEditModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showFlashMessage('error', 'Gagal memperbarui data pasien');
        }
    } catch(error) {
        console.error('Error:', error);
        showFlashMessage('error', 'Terjadi kesalahan saat memperbarui data');
    }
});

// Close modals
function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

// Confirm delete - DIPERBAIKI
function confirmDelete(event, patientId) {
    // Prevent event bubbling
    event.preventDefault();
    event.stopPropagation();
    
    if (confirm('Apakah Anda yakin ingin menghapus data pasien ini?\n\nPeringatan: Pastikan pasien tidak memiliki data pemeriksaan atau invoice terkait.')) {
        window.location.href = BASE_URL + 'administrasi/delete_patient/' + patientId;
    }
}

// Export patients
function exportPatients() {
    const search = '<?= $search ?>';
    let url = BASE_URL + 'excel_controller/export_patients';
    if (search) {
        url += '?search=' + encodeURIComponent(search);
    }
    window.open(url, '_blank');
}

// Show flash message
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

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDetailModal();
        closeEditModal();
    }
});

// Close modal on backdrop click
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
</script>

</body>
</html>