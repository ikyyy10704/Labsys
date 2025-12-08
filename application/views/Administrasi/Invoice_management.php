<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management - Labsy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
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
    </style>
</head>
<body class="bg-gray-50">

<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-emerald-600 via-emerald-700 to-emerald-800 border-b border-emerald-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="file-text" class="w-8 h-8 text-emerald-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Invoice Management</h1>
                <p class="text-emerald-100">Detail dan cetak invoice dengan breakdown biaya</p>
            </div>
        </div>
        <button onclick="window.history.back()" class="bg-white hover:bg-gray-100 text-emerald-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </button>
    </div>
</div>

<!-- Main Content -->
<div class="p-6">
    <!-- Invoice Detail Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                    <span id="invoice-title">Detail Invoice</span>
                </h2>
                <button id="print-button" onclick="printInvoice()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Invoice</span>
                </button>
            </div>
        </div>

        <div id="invoice-content" class="p-6">
            <!-- Loading State -->
            <div id="loading-state" class="flex items-center justify-center py-12">
                <div class="text-center">
                    <i data-lucide="loader-2" class="w-8 h-8 text-emerald-600 loading mx-auto mb-4"></i>
                    <p class="text-gray-500">Memuat detail invoice...</p>
                </div>
            </div>

            <!-- Invoice Details will be loaded here -->
            <div id="invoice-details" class="hidden">
                <!-- Header Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Patient Info -->
                    <div class="bg-emerald-50 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                            <i data-lucide="user" class="w-5 h-5 text-emerald-600"></i>
                            <span>Informasi Pasien</span>
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">Nama:</span>
                                <span id="patient-name" class="text-gray-900">-</span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">NIK:</span>
                                <span id="patient-nik" class="text-gray-900">-</span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">Umur:</span>
                                <span id="patient-age" class="text-gray-900">-</span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">Telepon:</span>
                                <span id="patient-phone" class="text-gray-900">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Info -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                            <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                            <span>Informasi Invoice</span>
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex">
                                <span class="font-medium w-40 text-gray-700">No. Invoice:</span>
                                <span id="invoice-number" class="text-gray-900 font-bold">-</span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-40 text-gray-700">No. Pemeriksaan:</span>
                                <span id="exam-number" class="text-gray-900">-</span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-40 text-gray-700">Jenis Pemeriksaan:</span>
                                <span id="exam-type" class="text-gray-900">-</span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-40 text-gray-700">Tanggal Invoice:</span>
                                <span id="invoice-date" class="text-gray-900">-</span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-40 text-gray-700">Status:</span>
                                <span id="payment-status">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Breakdown Table -->
                <div class="mb-6">
                    <div class="bg-gray-100 px-4 py-3 rounded-t-lg border-b-2 border-emerald-600">
                        <h3 class="font-semibold text-gray-900 flex items-center space-x-2">
                            <i data-lucide="list" class="w-5 h-5 text-emerald-600"></i>
                            <span>Rincian Biaya Pemeriksaan</span>
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-emerald-600 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase">Item Pemeriksaan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase">Hasil</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase">Harga</th>
                                </tr>
                            </thead>
                            <tbody id="breakdown-table" class="bg-white divide-y divide-gray-200">
                                <!-- Breakdown items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total Summary -->
                <div class="flex justify-end mb-6">
                    <div class="w-96 bg-gray-50 rounded-lg border-2 border-gray-200 overflow-hidden">
                        <div class="px-6 py-3 bg-white border-b border-gray-200">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span id="subtotal" class="font-semibold text-gray-900">Rp 0</span>
                            </div>
                        </div>
                        <div class="px-6 py-3 bg-white border-b border-gray-200">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Pajak (0%):</span>
                                <span class="font-semibold text-gray-900">Rp 0</span>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-emerald-600 text-white">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-lg">TOTAL:</span>
                                <span id="total-amount" class="font-bold text-2xl">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info (if paid) -->
                <div id="payment-info" class="hidden bg-blue-50 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                        <i data-lucide="credit-card" class="w-5 h-5 text-blue-600"></i>
                        <span>Informasi Pembayaran</span>
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="flex">
                            <span class="font-medium w-40 text-gray-700">Metode Pembayaran:</span>
                            <span id="payment-method" class="text-gray-900">-</span>
                        </div>
                        <div class="flex">
                            <span class="font-medium w-40 text-gray-700">Tanggal Pembayaran:</span>
                            <span id="payment-date" class="text-gray-900">-</span>
                        </div>
                    </div>
                </div>

                <!-- Notes (if any) -->
                <div id="notes-section" class="hidden bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center space-x-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-yellow-600"></i>
                        <span>Catatan</span>
                    </h3>
                    <p id="invoice-notes" class="text-sm text-gray-700"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variable untuk menyimpan invoice ID
let currentInvoiceId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Get invoice ID from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    currentInvoiceId = urlParams.get('invoice_id');
    
    if (currentInvoiceId) {
        loadInvoiceDetail(currentInvoiceId);
    } else {
        showError('Invoice ID tidak ditemukan');
    }
});

// Load invoice detail
async function loadInvoiceDetail(invoiceId) {
    try {
        const response = await fetch(`<?= base_url("administrasi/ajax_get_invoice_breakdown") ?>/${invoiceId}`);
        const data = await response.json();
        
        if (data.success && data.invoice) {
            displayInvoiceDetail(data.invoice);
        } else {
            showError(data.message || 'Gagal memuat detail invoice');
        }
    } catch (error) {
        console.error('Error loading invoice:', error);
        showError('Terjadi kesalahan saat memuat data');
    }
}

// Display invoice detail
function displayInvoiceDetail(invoice) {
    // Hide loading, show content
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('invoice-details').classList.remove('hidden');
    
    // Update title
    document.getElementById('invoice-title').textContent = `Detail Invoice - ${invoice.nomor_invoice}`;
    
    // Patient Info
    document.getElementById('patient-name').textContent = invoice.nama_pasien;
    document.getElementById('patient-nik').textContent = invoice.nik || 'Tidak tersedia';
    document.getElementById('patient-age').textContent = invoice.umur ? `${invoice.umur} tahun` : '-';
    document.getElementById('patient-phone').textContent = invoice.telepon || '-';
    
    // Invoice Info
    document.getElementById('invoice-number').textContent = invoice.nomor_invoice;
    document.getElementById('exam-number').textContent = invoice.nomor_pemeriksaan;
    document.getElementById('exam-type').textContent = invoice.jenis_pemeriksaan;
    document.getElementById('invoice-date').textContent = formatDate(invoice.tanggal_invoice);
    
    // Status Badge
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
    document.getElementById('payment-status').innerHTML = `
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${statusColors[invoice.status_pembayaran]}">
            ${statusNames[invoice.status_pembayaran]}
        </span>
    `;
    
    // Breakdown Table
    const breakdownTable = document.getElementById('breakdown-table');
    if (invoice.cost_breakdown && invoice.cost_breakdown.length > 0) {
        breakdownTable.innerHTML = invoice.cost_breakdown.map(item => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${item.item}</td>
                <td class="px-4 py-3 text-sm text-gray-700">${item.hasil || '-'}</td>
                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">Rp ${formatNumber(item.harga)}</td>
            </tr>
        `).join('');
    } else {
        breakdownTable.innerHTML = `
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                    Tidak ada detail breakdown biaya
                </td>
            </tr>
        `;
    }
    
    // Total
    const totalBiaya = invoice.total_biaya || 0;
    document.getElementById('subtotal').textContent = formatCurrency(totalBiaya);
    document.getElementById('total-amount').textContent = formatCurrency(totalBiaya);
    
    // Payment Info (if paid)
    if (invoice.status_pembayaran === 'lunas') {
        document.getElementById('payment-info').classList.remove('hidden');
        document.getElementById('payment-method').textContent = invoice.metode_pembayaran || 'Tidak ditentukan';
        document.getElementById('payment-date').textContent = invoice.tanggal_pembayaran ? formatDate(invoice.tanggal_pembayaran) : '-';
    }
    
    // Notes (if any)
    if (invoice.keterangan) {
        document.getElementById('notes-section').classList.remove('hidden');
        document.getElementById('invoice-notes').textContent = invoice.keterangan;
    }
    
    // Reinitialize icons
    lucide.createIcons();
}

// Print invoice
function printInvoice() {
    if (currentInvoiceId) {
        window.open(`<?= base_url("PDF_Controller/print_invoice") ?>/${currentInvoiceId}`, '_blank');
    }
}

// Helper functions
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { day: '2-digit', month: 'long', year: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function showError(message) {
    document.getElementById('loading-state').innerHTML = `
        <div class="text-center py-12">
            <i data-lucide="alert-circle" class="w-12 h-12 text-red-500 mx-auto mb-4"></i>
            <p class="text-gray-700 font-medium">${message}</p>
            <button onclick="window.history.back()" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Kembali
            </button>
        </div>
    `;
    lucide.createIcons();
}
</script>

</body>
</html>