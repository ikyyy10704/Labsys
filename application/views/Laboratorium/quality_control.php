<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - LabSy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .loading { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body class="bg-gray-50">

<!-- Header -->
<div class="bg-gradient-to-r from-purple-600 to-purple-700 border-b border-purple-500 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="shield-check" class="w-8 h-8 text-purple-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Quality Control (QC)</h1>
                <p class="text-purple-100">Pengendalian Mutu Alat Laboratorium</p>
            </div>
        </div>
        <button onclick="openAddQCModal()" 
                class="bg-white hover:bg-gray-100 text-purple-600 px-4 py-2 rounded-lg transition-colors flex items-center space-x-2 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah QC</span>
        </button>
    </div>
</div>

<!-- Main Content -->
<div class="p-6 space-y-6">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total QC</p>
                    <p id="total-qc" class="text-2xl font-bold text-gray-900">0</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Passed</p>
                    <p id="qc-passed" class="text-2xl font-bold text-green-600">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Failed</p>
                    <p id="qc-failed" class="text-2xl font-bold text-red-600">0</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">QC Hari Ini</p>
                    <p id="qc-today" class="text-2xl font-bold text-blue-600">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alat</label>
                <select id="filter-alat" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">Semua Alat</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hasil QC</label>
                <select id="filter-hasil" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">Semua Hasil</option>
                    <option value="Passed">Passed</option>
                    <option value="Failed">Failed</option>
                    <option value="Conditional">Conditional</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                <input type="date" id="filter-date-from" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                <input type="date" id="filter-date-to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-4">
            <button onclick="resetFilters()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Reset
            </button>
            <button onclick="applyFilters()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Terapkan Filter
            </button>
        </div>
    </div>

    <!-- QC Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Riwayat Quality Control</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal QC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Alat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parameter QC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hasil QC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teknisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="qc-tbody" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i data-lucide="loader-2" class="w-5 h-5 text-purple-600 loading mx-auto"></i>
                            <p class="text-gray-500 mt-2">Memuat data QC...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add QC Modal -->
<div id="qc-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Quality Control</h3>
                <button onclick="closeQCModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <form id="qc-form" class="p-6 space-y-4">
            <input type="hidden" id="qc-id" name="qc_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Alat Laboratorium <span class="text-red-500">*</span>
                </label>
                <select id="qc-alat" name="alat_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Pilih Alat --</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal QC <span class="text-red-500">*</span>
                </label>
                <input type="date" id="qc-tanggal" name="tanggal_qc" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Parameter QC
                </label>
                <input type="text" id="qc-parameter" name="parameter_qc" placeholder="e.g., Akurasi, Presisi, Linearitas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nilai/Hasil Pengukuran
                </label>
                <input type="text" id="qc-nilai" name="nilai_hasil" placeholder="e.g., 98.5%, Within range" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Hasil QC <span class="text-red-500">*</span>
                </label>
                <select id="qc-hasil" name="hasil_qc" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="Passed">✓ Passed</option>
                    <option value="Failed">✗ Failed</option>
                    <option value="Conditional">⚠ Conditional</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Teknisi</label>
                <input type="text" id="qc-teknisi" name="teknisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea id="qc-catatan" name="catatan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeQCModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let qcData = [];
let alatList = [];

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadAlatList();
    loadQCData();
    loadStats();
    
    // Set default date
    document.getElementById('qc-tanggal').value = new Date().toISOString().split('T')[0];
});

async function loadAlatList() {
    try {
        const response = await fetch('<?= base_url("quality_control/get_alat_list") ?>');
        const data = await response.json();
        
        if (data.success) {
            alatList = data.alat_list;
            
            const selectElements = ['qc-alat', 'filter-alat'];
            selectElements.forEach(id => {
                const select = document.getElementById(id);
                if (select) {
                    const currentValue = select.value;
                    select.innerHTML = id === 'filter-alat' ? '<option value="">Semua Alat</option>' : '<option value="">-- Pilih Alat --</option>';
                    
                    alatList.forEach(alat => {
                        const option = document.createElement('option');
                        option.value = alat.alat_id;
                        option.textContent = `${alat.nama_alat} (${alat.kode_unik || '-'})`;
                        select.appendChild(option);
                    });
                    
                    if (currentValue) select.value = currentValue;
                }
            });
        }
    } catch (error) {
        console.error('Error loading alat list:', error);
    }
}

async function loadQCData() {
    try {
        const response = await fetch('<?= base_url("quality_control/get_qc_data") ?>');
        const data = await response.json();
        
        if (data.success) {
            qcData = data.qc_data;
            renderQCTable(qcData);
        }
    } catch (error) {
        console.error('Error loading QC data:', error);
    }
}

async function loadStats() {
    try {
        const response = await fetch('<?= base_url("quality_control/get_stats") ?>');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('total-qc').textContent = data.stats.total || 0;
            document.getElementById('qc-passed').textContent = data.stats.passed || 0;
            document.getElementById('qc-failed').textContent = data.stats.failed || 0;
            document.getElementById('qc-today').textContent = data.stats.today || 0;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

function renderQCTable(data) {
    const tbody = document.getElementById('qc-tbody');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    Tidak ada data QC
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = data.map(qc => {
        const hasilClass = qc.hasil_qc === 'Passed' ? 'bg-green-100 text-green-800' :
                          qc.hasil_qc === 'Failed' ? 'bg-red-100 text-red-800' :
                          'bg-yellow-100 text-yellow-800';
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${formatDate(qc.tanggal_qc)}
                </td>
                <td class="px-6 py-4 text-sm">
                    <div class="font-medium text-gray-900">${qc.nama_alat}</div>
                    <div class="text-gray-500">${qc.kode_unik || '-'}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${qc.parameter_qc || '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${hasilClass}">
                        ${qc.hasil_qc}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${qc.teknisi || '-'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    ${qc.catatan ? qc.catatan.substring(0, 50) + (qc.catatan.length > 50 ? '...' : '') : '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <button onclick="viewQCDetail(${qc.qc_id})" class="text-blue-600 hover:text-blue-800 mr-3">
                        <i data-lucide="eye" class="w-4 h-4 inline"></i>
                    </button>
                    <button onclick="editQC(${qc.qc_id})" class="text-green-600 hover:text-green-800 mr-3">
                        <i data-lucide="edit" class="w-4 h-4 inline"></i>
                    </button>
                    <button onclick="deleteQC(${qc.qc_id})" class="text-red-600 hover:text-red-800">
                        <i data-lucide="trash-2" class="w-4 h-4 inline"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    lucide.createIcons();
}

function openAddQCModal() {
    document.getElementById('qc-form').reset();
    document.getElementById('qc-id').value = '';
    document.getElementById('qc-tanggal').value = new Date().toISOString().split('T')[0];
    document.getElementById('qc-modal').classList.remove('hidden');
    lucide.createIcons();
}

function closeQCModal() {
    document.getElementById('qc-modal').classList.add('hidden');
}

document.getElementById('qc-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const qcId = document.getElementById('qc-id').value;
    const url = qcId ? 
        '<?= base_url("quality_control/update_qc/") ?>' + qcId :
        '<?= base_url("quality_control/create_qc") ?>';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            closeQCModal();
            loadQCData();
            loadStats();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
    }
});

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

async function deleteQC(qcId) {
    if (!confirm('Yakin ingin menghapus data QC ini?')) return;
    
    try {
        const response = await fetch('<?= base_url("quality_control/delete_qc/") ?>' + qcId, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            loadQCData();
            loadStats();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    }
}

function applyFilters() {
    const alatId = document.getElementById('filter-alat').value;
    const hasil = document.getElementById('filter-hasil').value;
    const dateFrom = document.getElementById('filter-date-from').value;
    const dateTo = document.getElementById('filter-date-to').value;
    
    let filtered = qcData;
    
    if (alatId) filtered = filtered.filter(qc => qc.alat_id == alatId);
    if (hasil) filtered = filtered.filter(qc => qc.hasil_qc === hasil);
    if (dateFrom) filtered = filtered.filter(qc => qc.tanggal_qc >= dateFrom);
    if (dateTo) filtered = filtered.filter(qc => qc.tanggal_qc <= dateTo);
    
    renderQCTable(filtered);
}

function resetFilters() {
    document.getElementById('filter-alat').value = '';
    document.getElementById('filter-hasil').value = '';
    document.getElementById('filter-date-from').value = '';
    document.getElementById('filter-date-to').value = '';
    renderQCTable(qcData);
}
</script>

</body>
</html>