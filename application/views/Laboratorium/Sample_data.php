<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - LabSy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            width: 100%;
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
        /* Smooth transitions for collapsible */
#optionalFieldsContainer {
    transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
}

#optionalFieldsContainer.hidden {
    max-height: 0 !important;
    opacity: 0;
}

/* Icon rotation animation */
#optionalToggleIcon {
    transition: transform 0.3s ease;
}

/* Pulse animation for requested fields */
@keyframes pulse-blue {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
    }
    50% {
        box-shadow: 0 0 0 6px rgba(59, 130, 246, 0);
    }
}

.border-blue-400:focus {
    animation: pulse-blue 2s infinite;
}

/* Slide down animation */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slideDown {
    animation: slideDown 0.3s ease-out;
}

/* Gradient background for requested sections */
.bg-gradient-to-br {
    background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

/* Hover effect for accordion button */
.group:hover .group-hover\:bg-gray-200 {
    background-color: rgb(229 231 235);
}
    </style>
</head>
<body class="bg-gray-50">

<!-- Header Section -->
<div class="w-full bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="test-tube" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Data Sampel / Pelacakan Spesimen</h1>
                    <p class="text-blue-100">Monitor progress dan status pemeriksaan sampel laboratorium</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 px-4 py-3 shadow-lg">
                    <p class="text-blue-100 text-xs font-medium mb-0.5">Total Sampel</p>
                    <p class="text-xl font-bold text-white"><?= $total_samples ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>

<script>
// Toast Notification System (Standardized)
function showToast(type, message) {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2 pointer-events-none';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const iconName = type === 'success' ? 'check-circle' : type === 'info' ? 'info' : type === 'warning' ? 'alert-triangle' : 'alert-circle';
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : type === 'info' ? 'bg-blue-50 border-blue-200' : type === 'warning' ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200';
    const iconColor = type === 'success' ? 'text-green-600' : type === 'info' ? 'text-blue-600' : type === 'warning' ? 'text-yellow-600' : 'text-red-600';
    const textColor = type === 'success' ? 'text-green-800' : type === 'info' ? 'text-blue-800' : type === 'warning' ? 'text-yellow-800' : 'text-red-800';
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${bgColor} ${textColor} border rounded-lg p-4 max-w-sm shadow-lg transform transition-all duration-500 ease-out translate-x-full opacity-0 pointer-events-auto`;
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

// Check for PHP Flashdata on load
document.addEventListener('DOMContentLoaded', () => {
    // Check for sessionStorage toasts (Client-side)
    const successMsg = sessionStorage.getItem('toast_success');
    const errorMsg = sessionStorage.getItem('toast_error');
    
    if (successMsg) {
        setTimeout(() => showToast('success', successMsg), 500);
        sessionStorage.removeItem('toast_success');
    }
    
    if (errorMsg) {
        setTimeout(() => showToast('error', errorMsg), 500);
        sessionStorage.removeItem('toast_error');
    }

    // Check for PHP Flashdata (Server-side)
    <?php if($this->session->flashdata('success')): ?>
    setTimeout(() => showToast('success', '<?= $this->session->flashdata('success') ?>'), 500);
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
    setTimeout(() => showToast('error', '<?= $this->session->flashdata('error') ?>'), 500);
    <?php endif; ?>
});
</script>

<!-- Custom Modal (Neon Style) -->
<div id="custom-modal" class="fixed inset-0 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md transform scale-95 transition-all duration-300 relative z-10 border border-gray-100">
        <div class="text-center">
            <div id="modal-icon-container" class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-6 bg-blue-50">
                <i id="modal-icon" data-lucide="alert-circle" class="h-8 w-8 text-blue-600"></i>
            </div>
            <h3 id="modal-title" class="text-xl font-bold text-gray-900 mb-2">Konfirmasi</h3>
            <p id="modal-message" class="text-gray-600 mb-8">Apakah Anda yakin ingin melakukan tindakan ini?</p>
            <div class="flex gap-3 justify-center">
                <button onclick="closeModal()" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-medium transition-colors duration-200">
                    Batal
                </button>
                <button id="modal-confirm-btn" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium shadow-lg shadow-blue-200 transition-all duration-200">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Custom Modal Logic
let modalConfirmCallback = null;

function showModal(config) {
    const modal = document.getElementById('custom-modal');
    const modalContent = modal.querySelector('div.bg-white');
    const title = document.getElementById('modal-title');
    const message = document.getElementById('modal-message');
    const iconContainer = document.getElementById('modal-icon-container');
    const icon = document.getElementById('modal-icon');
    const confirmBtn = document.getElementById('modal-confirm-btn');

    // Default configuration for consistent styling
    const defaultConfig = {
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin?',
        type: 'info', // info, success, warning, danger
        confirmText: 'Ya, Lanjutkan',
        onConfirm: () => {}
    };
    
    // Merge config
    const finalConfig = { ...defaultConfig, ...config };

    // Update Content
    title.textContent = finalConfig.title;
    message.textContent = finalConfig.message;
    confirmBtn.textContent = finalConfig.confirmText;
    modalConfirmCallback = finalConfig.onConfirm;

    // Style based on type
    // Reset classes
    iconContainer.className = 'mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-6 transition-colors duration-300';
    confirmBtn.className = 'px-6 py-2.5 text-white rounded-xl font-medium shadow-lg transition-all duration-200 transform hover:scale-105 focus:ring-4';

    if (finalConfig.type === 'danger') {
        iconContainer.classList.add('bg-red-50');
        icon.className = 'h-8 w-8 text-red-600';
        confirmBtn.classList.add('bg-gradient-to-r', 'from-red-600', 'to-red-700', 'hover:from-red-700', 'hover:to-red-800', 'shadow-red-200', 'focus:ring-red-200');
        icon.setAttribute('data-lucide', 'alert-triangle');
    } else if (finalConfig.type === 'success') {
        iconContainer.classList.add('bg-green-50');
        icon.className = 'h-8 w-8 text-green-600';
        confirmBtn.classList.add('bg-gradient-to-r', 'from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800', 'shadow-green-200', 'focus:ring-green-200');
        icon.setAttribute('data-lucide', 'check-circle');
    } else if (finalConfig.type === 'warning') {
        iconContainer.classList.add('bg-yellow-50');
        icon.className = 'h-8 w-8 text-yellow-600';
        confirmBtn.classList.add('bg-gradient-to-r', 'from-yellow-500', 'to-yellow-600', 'hover:from-yellow-600', 'hover:to-yellow-700', 'shadow-yellow-200', 'focus:ring-yellow-200');
        icon.setAttribute('data-lucide', 'alert-circle');
    } else {
        // default/info/blue
        iconContainer.classList.add('bg-blue-50');
        icon.className = 'h-8 w-8 text-blue-600';
        confirmBtn.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'hover:from-blue-700', 'hover:to-blue-800', 'shadow-blue-200', 'focus:ring-blue-200');
        icon.setAttribute('data-lucide', 'info');
    }

    // Show Modal with Animation
    modal.classList.remove('pointer-events-none', 'opacity-0');
    modalContent.classList.remove('scale-95');
    modalContent.classList.add('scale-100');
    
    lucide.createIcons();
}

function closeModal() {
    const modal = document.getElementById('custom-modal');
    const modalContent = modal.querySelector('div.bg-white');
    
    modal.classList.add('opacity-0');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('pointer-events-none');
        modalConfirmCallback = null;
    }, 300);
}

// Bind Confirm Button
document.getElementById('modal-confirm-btn').addEventListener('click', () => {
    if (modalConfirmCallback) {
        modalConfirmCallback();
    }
    closeModal();
});
</script>

<!-- Filters Section -->
<div class="w-full px-6 py-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                <span>Filter & Pencarian</span>
            </h2>
        </div>
        
        <div class="p-6">
            <form method="GET" action="<?= base_url('sample_data') ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php foreach ($status_options as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $filters['status'] == $key ? 'selected' : '' ?>><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                        <input type="date" name="date_from" value="<?= $filters['date_from'] ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                        <input type="date" name="date_to" value="<?= $filters['date_to'] ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Examination Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pemeriksaan</label>
                        <select name="jenis_pemeriksaan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Jenis</option>
                            <?php foreach ($examination_types as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $filters['jenis_pemeriksaan'] == $key ? 'selected' : '' ?>><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                        <input type="text" name="search" value="<?= $filters['search'] ?>" placeholder="Nama pasien, NIK, atau nomor" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-100">
                    <div class="flex items-center space-x-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                            <span>Terapkan</span>
                        </button>
                        <a href="<?= base_url('sample_data') ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            <span>Reset</span>
                        </a>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-semibold text-gray-900"><?= $total_samples ?></span> sampel ditemukan
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="w-full px-6 pb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-orange-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Sedang Diproses</p>
                    <p class="text-lg font-bold text-gray-900"><?= count(array_filter($samples, function($s) { return $s['status_pemeriksaan'] == 'progress'; })) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-lg font-bold text-gray-900"><?= count(array_filter($samples, function($s) { return $s['status_pemeriksaan'] == 'selesai'; })) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Petugas Aktif</p>
                    <p class="text-lg font-bold text-gray-900"><?= count(array_unique(array_column($samples, 'petugas_id'))) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata Waktu</p>
                    <p class="text-lg font-bold text-gray-900"><?= !empty($samples) ? round(array_sum(array_column($samples, 'processing_hours')) / count($samples)) : 0 ?> jam</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Samples List -->
<div class="w-full px-6 pb-6">
    <?php if (empty($samples)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="test-tube" class="w-12 h-12 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Sampel</h3>
        <p class="text-gray-500">Tidak ada sampel yang sesuai dengan filter yang dipilih.</p>
    </div>
    <?php else: ?>
    <div class="space-y-4">

<?php foreach ($samples as $sample): ?>                     
<div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4 flex-1">
                <!-- Status Icon -->
                <div class="w-12 h-12 rounded-full flex items-center justify-center shadow-sm
                    <?php 
                    $status = $sample['status_pemeriksaan'] ?? 'pending';
                    if ($status == 'progress'): ?>
                        bg-gradient-to-br from-orange-500 to-orange-600
                    <?php elseif ($status == 'selesai'): ?>
                        bg-gradient-to-br from-green-500 to-green-600
                    <?php else: ?>
                        bg-gradient-to-br from-blue-500 to-gray-600
                    <?php endif; ?>">
                    <?php if ($status == 'progress'): ?>
                    <i data-lucide="loader" class="w-6 h-6 text-white"></i>
                    <?php elseif ($status == 'selesai'): ?>
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                    <?php else: ?>
                    <i data-lucide="x-circle" class="w-6 h-6 text-white"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Sample Details -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3 mb-2">
                        <h3 class="text-lg font-semibold text-blue-900">
                            <?= $sample['jenis_pemeriksaan'] ?? $sample['jenis_pemeriksaan_display'] ?? '-' ?>
                        </h3>
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                            <?php 
                            if ($status == 'progress'): ?>
                                bg-orange-100 text-orange-800
                            <?php elseif ($status == 'selesai'): ?>
                                bg-green-100 text-green-800
                            <?php else: ?>
                                bg-blue-100 text-blue-800
                            <?php endif; ?>">
                            <?= strtoupper($status) ?>
                        </span>
                    </div>

                    <!-- STATUS PASIEN CARD - NEW SECTION -->
                    <?php if (!empty($sample['status_pasien'])): ?>
                    <div class="mb-3 p-3 rounded-lg border-l-4 <?php
                        $status_pasien = $sample['status_pasien'];
                        if ($status_pasien == 'puasa'): ?>
                            bg-gradient-to-r from-green-50 to-emerald-50 border-green-500
                        <?php elseif ($status_pasien == 'minum_obat'): ?>
                            bg-gradient-to-r from-red-50 to-rose-50 border-red-500
                        <?php else: ?>
                            bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-500
                        <?php endif; ?>">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <?php if ($status_pasien == 'puasa'): ?>
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="coffee" class="w-4 h-4 text-green-600"></i>
                                    </div>
                                <?php elseif ($status_pasien == 'minum_obat'): ?>
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="pill" class="w-4 h-4 text-red-600"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="utensils" class="w-4 h-4 text-yellow-600"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold <?php
                                    if ($status_pasien == 'puasa'): ?>
                                        text-green-900
                                    <?php elseif ($status_pasien == 'minum_obat'): ?>
                                        text-red-900
                                    <?php else: ?>
                                        text-yellow-900
                                    <?php endif; ?>">
                                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                    Status Pasien: 
                                    <?php 
                                    if ($status_pasien == 'puasa'): ?>
                                        Puasa
                                    <?php elseif ($status_pasien == 'minum_obat'): ?>
                                        Sedang Minum Obat
                                    <?php else: ?>
                                        Belum Puasa
                                    <?php endif; ?>
                                </p>
                                
                                <?php if ($status_pasien == 'puasa'): ?>
                                    <p class="text-xs text-green-700 mt-1">
                                        <i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i>
                                        Pasien telah berpuasa sesuai persyaratan pemeriksaan
                                    </p>
                                <?php elseif ($status_pasien == 'minum_obat' && !empty($sample['keterangan_obat'])): ?>
                                    <p class="text-xs text-red-700 mt-1">
                                        <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                        <strong>Obat yang dikonsumsi:</strong> <?= htmlspecialchars($sample['keterangan_obat']) ?>
                                    </p>
                                    <p class="text-xs text-red-600 mt-1 italic">
                                        Perhatian: Konsumsi obat dapat mempengaruhi hasil pemeriksaan
                                    </p>
                                <?php elseif ($status_pasien == 'belum_puasa'): ?>
                                    <p class="text-xs text-yellow-700 mt-1">
                                        <i data-lucide="alert-circle" class="w-3 h-3 inline mr-1"></i>
                                        Pasien belum berpuasa - hasil dapat terpengaruh untuk pemeriksaan tertentu
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <!-- END STATUS PASIEN CARD -->
                   
                    <!-- EXAMINATION DETAILS DISPLAY -->
                   <?php 
$has_multiple = isset($sample['examination_details']) 
                && is_array($sample['examination_details']) 
                && count($sample['examination_details']) > 1;

// Check if single examination has sub_pemeriksaan
$has_single_subs = false;
if (!$has_multiple && !empty($sample['sub_pemeriksaan'])) {
    $single_subs = json_decode($sample['sub_pemeriksaan'], true);
    if (is_array($single_subs) && count($single_subs) > 0) {
        $has_single_subs = true;
    }
}
?>

<?php if ($has_multiple): ?>
<!-- MULTIPLE EXAMINATION DISPLAY -->
<div class="mb-3 space-y-2">
    <div class="flex items-center mb-2">
        <i data-lucide="layers" class="w-4 h-4 text-blue-600 mr-2"></i>
        <span class="text-xs font-semibold text-blue-900">
            Multiple Jenis Pemeriksaan (<?= count($sample['examination_details']) ?>)
        </span>
    </div>
    <?php foreach ($sample['examination_details'] as $detail): ?>
    <div class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-blue-900 flex items-center">
                    <i data-lucide="clipboard-list" class="w-3 h-3 inline mr-1"></i>
                    <?= $detail['jenis_pemeriksaan'] ?>
                </p>
                <?php if (!empty($detail['sub_pemeriksaan_display'])): ?>
                <p class="text-xs text-blue-700 mt-1 ml-4">
                    <i data-lucide="corner-down-right" class="w-3 h-3 inline mr-1"></i>
                    <?= $detail['sub_pemeriksaan_display'] ?>
                </p>
                <?php endif; ?>
            </div>
            <span class="ml-2 px-2 py-0.5 bg-blue-600 text-white text-xs rounded-full">
                <?= $detail['urutan'] ?>
            </span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php elseif ($has_single_subs): ?>
<!-- SINGLE EXAMINATION WITH SUB PEMERIKSAAN DISPLAY -->
<div class="mb-3 space-y-2">
    <div class="flex items-center mb-2">
        <i data-lucide="clipboard-check" class="w-4 h-4 text-blue-600 mr-2"></i>
        <span class="text-xs font-semibold text-blue-900">
            Single Jenis Pemeriksaan dengan Sub-Pemeriksaan Spesifik
        </span>
    </div>
    <div class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-blue-900 flex items-center">
                    <i data-lucide="clipboard-list" class="w-3 h-3 inline mr-1"></i>
                    <?= $sample['jenis_pemeriksaan'] ?>
                </p>
                <div class="mt-2 ml-4">
                    <?php
                    // Use pre-processed labels from controller
                    if (isset($sample['sub_pemeriksaan_labels']) && !empty($sample['sub_pemeriksaan_labels'])):
                        $sub_labels = $sample['sub_pemeriksaan_labels'];
                    else:
                        $sub_labels = array();
                    endif;
                    ?>
                    <p class="text-xs text-blue-700">
                        <i data-lucide="corner-down-right" class="w-3 h-3 inline mr-1"></i>
                        <strong>Sub Pemeriksaan:</strong>
                    </p>
                    <div class="flex flex-wrap gap-1 mt-1 ml-4">
                        <?php foreach ($sub_labels as $label): ?>
                        <span class="inline-flex items-center px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">
                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                            <?= $label ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <span class="ml-2 px-2 py-0.5 bg-blue-600 text-white text-xs rounded-full">
                1
            </span>
        </div>
    </div>
</div>

<?php else: ?>
<!-- SIMPLE SINGLE EXAMINATION WITHOUT SUB PEMERIKSAAN -->
<div class="mb-3 space-y-2">
    <div class="flex items-center mb-2">
        <i data-lucide="clipboard-check" class="w-4 h-4 text-blue-600 mr-2"></i>
        <span class="text-xs font-semibold text-blue-900">
            Single Jenis Pemeriksaan
        </span>
    </div>
    <div class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-xs font-medium text-blue-900 flex items-center">
                    <i data-lucide="clipboard-list" class="w-3 h-3 inline mr-1"></i>
                    <?= $sample['jenis_pemeriksaan'] ?>
                </p>
                <p class="text-xs text-blue-600 italic mt-2 ml-4">
                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                    Pemeriksaan standar lengkap
                </p>
            </div>
            <span class="ml-2 px-2 py-0.5 bg-blue-600 text-white text-xs rounded-full">
                1
            </span>
        </div>
    </div>
</div>
<?php endif; ?>

                    <!-- SAMPLE INFORMATION SECTION -->
                    <?php 
                    $exam_id = $sample['pemeriksaan_id'] ?? $sample['id'] ?? 0;
                    if ($exam_id > 0) {
                        $this->db->select('
                            ps.*,
                            pt_pengambil.nama_petugas as petugas_pengambil_nama,
                            pt_evaluasi.nama_petugas as petugas_evaluasi_nama
                        ');
                        $this->db->from('pemeriksaan_sampel ps');
                        $this->db->join('petugas_lab pt_pengambil', 'ps.petugas_pengambil_id = pt_pengambil.petugas_id', 'left');
                        $this->db->join('petugas_lab pt_evaluasi', 'ps.petugas_evaluasi_id = pt_evaluasi.petugas_id', 'left');
                        $this->db->where('ps.pemeriksaan_id', $exam_id);
                        $exam_samples = $this->db->get()->result_array();
                        
                        if (!empty($exam_samples)):
                    ?>
                    <div class="mb-3 p-3 bg-gradient-to-r from-teal-50 to-cyan-50 border-l-4 border-teal-500 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i data-lucide="test-tubes" class="w-4 h-4 text-teal-600 mr-2"></i>
                            <span class="text-xs font-semibold text-teal-900">
                                Informasi Sampel (<?= count($exam_samples) ?> jenis)
                            </span>
                        </div>
                        <div class="space-y-2">
                            <?php 
                            $jenis_sampel_map = array(
                                'whole_blood' => 'Whole Blood',
                                'serum' => 'Serum',
                                'plasma' => 'Plasma',
                                'urin' => 'Urin',
                                'feses' => 'Feses',
                                'sputum' => 'Sputum'
                            );
                            
                            foreach ($exam_samples as $exam_sample): 
                                $status_sampel = $exam_sample['status_sampel'] ?? 'belum_diambil';
                                $jenis_label = $jenis_sampel_map[$exam_sample['jenis_sampel']] ?? $exam_sample['jenis_sampel'];
                            ?>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="droplet" class="w-3 h-3 text-teal-600"></i>
                                    <span class="font-medium text-teal-900"><?= $jenis_label ?></span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    <?php 
                                    switch($status_sampel) {
                                        case 'belum_diambil':
                                            echo 'bg-gray-100 text-gray-700';
                                            break;
                                        case 'sudah_diambil':
                                            echo 'bg-blue-100 text-blue-700';
                                            break;
                                        case 'diterima':
                                            echo 'bg-green-100 text-green-700';
                                            break;
                                        case 'ditolak':
                                            echo 'bg-red-100 text-red-700';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-700';
                                    }
                                    ?>">
                                    <?php
                                    switch($status_sampel) {
                                        case 'belum_diambil': echo 'Belum Diambil'; break;
                                        case 'sudah_diambil': echo 'Sudah Diambil'; break;
                                        case 'diterima': echo 'Diterima'; break;
                                        case 'ditolak': echo 'Ditolak'; break;
                                        default: echo ucfirst($status_sampel);
                                    }
                                    ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Sample Details Info -->
                        <?php 
                        $total_samples = count($exam_samples);
                        $samples_diterima = count(array_filter($exam_samples, function($s) { return $s['status_sampel'] == 'diterima'; }));
                        $samples_ditolak = count(array_filter($exam_samples, function($s) { return $s['status_sampel'] == 'ditolak'; }));
                        $samples_pending = $total_samples - $samples_diterima - $samples_ditolak;
                        ?>
                        <div class="mt-2 pt-2 border-t border-teal-200 flex items-center justify-between text-xs text-teal-700">
                            <div class="flex items-center space-x-3">
                                <span><i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i><?= $samples_diterima ?> Diterima</span>
                                <?php if ($samples_ditolak > 0): ?>
                                <span><i data-lucide="x-circle" class="w-3 h-3 inline mr-1"></i><?= $samples_ditolak ?> Ditolak</span>
                                <?php endif; ?>
                                <?php if ($samples_pending > 0): ?>
                                <span><i data-lucide="clock" class="w-3 h-3 inline mr-1"></i><?= $samples_pending ?> Pending</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endif;
                    }
                    ?>

                    <!-- Patient Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-x-8 gap-y-2 text-xs text-gray-600 mt-3">
                        <div class="flex items-center gap-1">
                            <i data-lucide="user" class="w-3 h-3 text-gray-500"></i>
                            <span class="font-medium text-gray-700">Pasien:</span> 
                            <span><?= $sample['nama_pasien'] ?? '-' ?></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i data-lucide="credit-card" class="w-3 h-3 text-gray-500"></i>
                            <span class="font-medium text-gray-700">NIK:</span> 
                            <span><?= $sample['nik'] ?? $sample['nik_pasien'] ?? '-' ?></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i data-lucide="hash" class="w-3 h-3 text-gray-500"></i>
                            <span class="font-medium text-gray-700">No. Pemeriksaan:</span> 
                            <span class="font-mono"><?= $sample['nomor_pemeriksaan'] ?? '-' ?></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i data-lucide="calendar" class="w-3 h-3 text-gray-500"></i>
                            <span class="font-medium text-gray-700">Tanggal:</span> 
                            <span><?= isset($sample['tanggal_pemeriksaan']) && $sample['tanggal_pemeriksaan'] ? date('d/m/Y', strtotime($sample['tanggal_pemeriksaan'])) : '-' ?></span>
                        </div>
                        <?php if (!empty($sample['nama_petugas'])): ?>
                        <div class="flex items-center gap-1">
                            <i data-lucide="user-round" class="w-3 h-3 text-gray-500"></i>
                            <span class="font-medium text-gray-700">Petugas:</span> 
                            <span class="truncate"><?= $sample['nama_petugas'] ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex items-center gap-1">
                            <i data-lucide="clock" class="w-3 h-3 text-orange-500"></i>
                            <span class="font-medium text-orange-600">Proses:</span> 
                            <span class="font-semibold"><?= $sample['processing_hours'] ?? '0' ?> jam</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i data-lucide="activity" class="w-3 h-3 text-gray-500"></i>
                            <span class="font-medium text-gray-700">Update:</span> 
                            <span><?= $sample['timeline_count'] ?? '0' ?> kejadian</span>
                        </div>
                    </div>
                    
                    <?php if (!empty($sample['latest_status']['keterangan'])): ?>
                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-700">
                            <strong>Update Terakhir:</strong> <?= $sample['latest_status']['keterangan'] ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col space-y-2 ml-4">
                <?php 
                $exam_id = $sample['pemeriksaan_id'] ?? $sample['id'] ?? 0; 
                $jenis_type = $sample['jenis_pemeriksaan'] ?? '';
                ?>
                
                <?php if ($exam_id): ?>
                <a href="<?= base_url('sample_data/manage_samples/' . $exam_id) ?>" 
                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-teal-700 bg-teal-100 hover:bg-teal-200 transition-colors duration-200">
                        <i data-lucide="test-tubes" class="w-3 h-3 mr-1"></i>
                        <span>Kelola Sampel</span>
                    </a>
                
                <button type="button" onclick="viewTimeline(<?= $exam_id ?>)" 
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                    <span>Timeline</span>
                </button>
                
                <?php if ($status == 'progress'): ?>
                <button type="button" onclick="updateStatus(<?= $exam_id ?>)" 
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 transition-colors duration-200">
                    <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                    <span>Update</span>
                </button>
                
                <?php 
                // Check sample readiness
                $is_sample_ready = false;
                if (isset($exam_samples) && !empty($exam_samples)) {
                    foreach ($exam_samples as $es) {
                        if (($es['status_sampel'] ?? '') == 'diterima') {
                            $is_sample_ready = true;
                            break;
                        }
                    }
                }
                ?>
                <button onclick="smartInputResults(<?= $exam_id ?>, '<?= addslashes($jenis_type) ?>', <?= $has_multiple ? 'true' : 'false' ?>, <?= $is_sample_ready ? 'true' : 'false' ?>)"
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                    <i data-lucide="plus-circle" class="w-3 h-3 mr-1"></i>
                    <span>Input Hasil</span>
                </button>
                
                <?php elseif ($status == 'selesai'): ?>
                <button type="button" onclick="viewResults(<?= $exam_id ?>)" 
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 transition-colors duration-200">
                    <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                    <span>Lihat Hasil</span>
                </button>
                <?php endif; ?>
                <?php endif; ?>
                 <div class="mt-4 pt-4 border-t border-gray-200">
        <!-- Timeline Stats -->
        <div class="mb-3 p-2 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium text-blue-700">Timeline</span>
                <i data-lucide="activity" class="w-3 h-3 text-blue-600"></i>
            </div>
            <p class="text-lg font-bold text-blue-900"><?= $sample['timeline_count'] ?? 0 ?></p>
            <p class="text-xs text-blue-600">kejadian tercatat</p>
        </div>
        
        <!-- Processing Time -->
        <div class="mb-3 p-2 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border border-orange-200">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium text-orange-700">Durasi</span>
                <i data-lucide="clock" class="w-3 h-3 text-orange-600"></i>
            </div>
            <p class="text-lg font-bold text-orange-900"><?= $sample['processing_hours'] ?? 0 ?></p>
            <p class="text-xs text-orange-600">jam proses</p>
        </div>
        
        <!-- Sample Completion (jika ada data sampel) -->
        <?php
        if ($exam_id > 0) {
            $this->db->select('
                COUNT(*) as total,
                SUM(CASE WHEN status_sampel = "diterima" THEN 1 ELSE 0 END) as completed
            ');
            $this->db->from('pemeriksaan_sampel');
            $this->db->where('pemeriksaan_id', $exam_id);
            $sample_stats = $this->db->get()->row_array();
            
            if ($sample_stats && $sample_stats['total'] > 0):
                $completion_percent = round(($sample_stats['completed'] / $sample_stats['total']) * 100);
        ?>
        <div class="p-2 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium text-green-700">Sampel</span>
                <i data-lucide="test-tube" class="w-3 h-3 text-green-600"></i>
            </div>
            <p class="text-lg font-bold text-green-900"><?= $completion_percent ?>%</p>
            <p class="text-xs text-green-600"><?= $sample_stats['completed'] ?>/<?= $sample_stats['total'] ?> diterima</p>
        </div>
        <?php 
            endif;
        }
        ?>
    </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="mt-8 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Menampilkan halaman <?= $current_page ?> dari <?= $total_pages ?> (<?= $total_samples ?> total)
        </div>
        <div class="flex gap-1">
            <?php if ($has_prev): ?>
            <a href="<?= base_url('sample_data/sample_data') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page - 1])) ?>" 
               class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-50 text-xs">
                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
            </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
            <a href="<?= base_url('sample_data/sample_data') ?>?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>" 
               class="px-2.5 py-1 border text-xs <?= $i == $current_page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?> rounded">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($has_next): ?>
            <a href="<?= base_url('sample_data/sample_data') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page + 1])) ?>" 
               class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-50 text-xs">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Timeline Modal -->
<div id="timelineModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden fade-in">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 p-6 border-b border-blue-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Timeline Sampel</h2>
                            <p class="text-sm text-blue-100" id="timelineModalSubtitle">Loading...</p>
                        </div>
                    </div>
                    <button onclick="closeTimelineModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            
            <div class="overflow-y-auto max-h-[calc(90vh-120px)] p-6" id="timelineModalContent">
                <div class="flex items-center justify-center py-12">
                    <i data-lucide="loader" class="w-8 h-8 text-blue-600 loading"></i>
                    <span class="ml-3 text-gray-600">Memuat timeline...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Update Status Sampel</h3>
                <button type="button" onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <form id="updateStatusForm" onsubmit="submitStatusUpdate(event)">
                <input type="hidden" id="updateExamId" value="">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                    <select id="newStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="progress">Sedang Diproses</option>
                        <option value="selesai">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea id="statusKeterangan" name="keterangan" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                              placeholder="Masukkan keterangan update status..." required></textarea>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200">
                        Update Status
                    </button>
                    <button type="button" onclick="closeUpdateModal()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-all duration-200">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endif; ?>
<!-- Input Results Modal -->
<div id="inputResultsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Input Hasil Pemeriksaan</h3>
                    <p id="modalSubtitle" class="text-sm text-gray-500 mt-1">Loading...</p>
                </div>
                <button type="button" onclick="closeInputModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div id="modalLoading" class="text-center py-8">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-4">
                    <i data-lucide="loader" class="w-6 h-6 text-blue-600 loading"></i>
                </div>
                <p class="text-gray-500">Memuat data pemeriksaan...</p>
            </div>
            
            <div id="modalFormContainer" class="hidden">
                <form id="inputResultsForm" onsubmit="submitResults(event)">
                    <input type="hidden" id="modalExamId" name="examination_id" value="">
                    <input type="hidden" id="modalResultType" name="result_type" value="">
                    
                    <div id="dynamicFormContent"></div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" onclick="closeInputModal()" 
                                class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-all duration-200">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 inline-flex items-center">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Simpan Hasil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentExaminationId = null;
let currentExaminationType = null;
let currentExaminationDetails = [];
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

function smartInputResults(examId, jenisType, hasMultiple, isReady = true) {
    console.log('smartInputResults called:', {examId, jenisType, hasMultiple, isReady});
    
    // Check validation first
    if (!isReady) {
        showModal({
            type: 'warning',
            title: 'Sampel Belum Siap',
            message: 'Hasil pemeriksaan tidak dapat diisi karena data sampel belum tersedia atau belum dievaluasi (diterima). Pastikan data sampel sudah diinput dan diverifikasi dengan benar.',
            confirmText: 'Kelola Sampel',
            cancelText: 'Tutup',
            onConfirm: () => {
                window.location.href = '<?= base_url('sample_data/manage_samples/') ?>' + examId;
            }
        });
        return;
    }
    
    if (hasMultiple === true || hasMultiple === 'true') {
        // Multiple examinations
        inputResultsMultiple(examId);
    } else {
        // Single examination
        inputResults(examId, jenisType);
    }
}
// View timeline
function viewTimeline(examId) {
    document.getElementById('timelineModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    fetch('<?= base_url('sample_data/get_sample_timeline_data') ?>/' + examId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTimelineContent(data.examination, data.timeline);
            } else {
                document.getElementById('timelineModalContent').innerHTML = `
                    <div class="text-center py-12">
                        <i data-lucide="alert-circle" class="w-16 h-16 text-red-500 mx-auto mb-4"></i>
                        <p class="text-gray-600">${data.message || 'Gagal memuat timeline'}</p>
                    </div>
                `;
            }
            lucide.createIcons();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('timelineModalContent').innerHTML = `
                <div class="text-center py-12">
                    <i data-lucide="wifi-off" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-600">Terjadi kesalahan koneksi</p>
                </div>
            `;
            lucide.createIcons();
        });
}

function closeTimelineModal() {
    document.getElementById('timelineModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function renderTimelineContent(examination, timeline) {
    const subtitle = `${examination.nomor_pemeriksaan} - ${examination.nama_pasien}`;
    document.getElementById('timelineModalSubtitle').textContent = subtitle;
    
    let content = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <p class="text-xs text-gray-500 mb-1">Jenis Pemeriksaan</p>
                <p class="text-sm font-semibold text-gray-900">${examination.jenis_pemeriksaan}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Status</p>
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full ${
                    examination.status_pemeriksaan === 'progress' ? 'bg-orange-100 text-orange-800' :
                    examination.status_pemeriksaan === 'selesai' ? 'bg-green-100 text-green-800' :
                    'bg-gray-100 text-gray-800'
                }">
                    ${examination.status_pemeriksaan.toUpperCase()}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Total Update</p>
                <p class="text-sm font-semibold text-gray-900">${timeline.length} kejadian</p>
            </div>
        </div>
    `;
    
    if (timeline.length === 0) {
        content += `
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clock" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum Ada Update</h3>
                <p class="text-sm text-gray-500">Timeline progres akan muncul setelah ada update status</p>
            </div>
        `;
    } else {
        content += `<div class="relative"><div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div><div class="space-y-6">`;
        
        timeline.forEach((item, index) => {
            const statusLower = item.status.toLowerCase();
            let bgColor = 'bg-orange-500';
            let icon = 'clock';
            
            if (statusLower.includes('diterima') || statusLower.includes('mulai')) {
                bgColor = 'bg-blue-500';
                icon = statusLower.includes('diterima') ? 'package' : 'play';
            } else if (statusLower.includes('selesai') || statusLower.includes('divalidasi')) {
                bgColor = 'bg-green-500';
                icon = statusLower.includes('divalidasi') ? 'shield-check' : 'check';
            } else if (statusLower.includes('dibatalkan') || statusLower.includes('gagal')) {
                bgColor = 'bg-red-500';
                icon = 'x';
            }
            
            content += `
                <div class="relative flex items-start space-x-4">
                    <div class="relative flex items-center justify-center w-12 h-12 rounded-full shadow-sm z-10 ${bgColor}">
                        <i data-lucide="${icon}" class="w-5 h-5 text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0 bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-base font-semibold text-gray-900">${item.status}</h3>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500">${new Date(item.tanggal_update).toLocaleDateString('id-ID')}</span>
                                    <span class="text-xs font-medium text-gray-700">${new Date(item.tanggal_update).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</span>
                                </div>
                            </div>
                            ${item.keterangan ? `<p class="text-sm text-gray-700 mb-3">${item.keterangan}</p>` : ''}
                            <div class="flex items-center justify-between text-xs">
                                <div class="text-gray-500">${index === 0 ? '<span class="font-medium text-green-600">Status terbaru</span>' : ''}</div>
                                ${item.nama_petugas ? `<div class="flex items-center space-x-1 text-gray-600"><i data-lucide="user" class="w-3 h-3"></i><span>${item.nama_petugas}</span></div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        content += `</div></div>`;
    }
    
    document.getElementById('timelineModalContent').innerHTML = content;
    lucide.createIcons();
}

// Input results
function inputResults(examId, examinationType) {
    currentExaminationId = examId;
    currentExaminationType = examinationType;
    document.getElementById('inputResultsModal').classList.remove('hidden');
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalFormContainer').classList.add('hidden');
    
    loadExaminationData(examId, examinationType);
}

function closeInputModal() {
    document.getElementById('inputResultsModal').classList.add('hidden');
    document.getElementById('inputResultsForm').reset();
    currentExaminationId = null;
    currentExaminationType = null;
}

function loadExaminationData(examId, examinationType) {
    fetch(`<?= base_url('laboratorium/get_examination_data') ?>/${examId}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'}
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.is_multiple) {
                // Populate untuk multi examination
                populateModalMultiple(data.examination, data.examination_details, data.existing_results);
            } else {
                // Populate untuk single examination
                const subPemeriksaan = data.examination.sub_pemeriksaan || null;
                populateModal(data.examination, data.existing_results, examinationType, subPemeriksaan);
            }
        } else {
            alert('Error: ' + data.message);
            closeInputModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat memuat data');
        closeInputModal();
    });
}

function populateModal(examination, existingResults = null, examinationType = null, subPemeriksaan = null) {
    const subtitle = `${examination.nomor_pemeriksaan} - ${examination.nama_pasien} (${examination.jenis_pemeriksaan})`;
    document.getElementById('modalSubtitle').textContent = subtitle;
    document.getElementById('modalExamId').value = examination.pemeriksaan_id;
    
    const examType = examinationType || examination.jenis_pemeriksaan;
    const resultType = getResultTypeFromExamination(examType);
    document.getElementById('modalResultType').value = resultType;
    currentExaminationType = examType;
    
    // Parse sub pemeriksaan
    let selectedSubs = [];
    if (subPemeriksaan) {
        try {
            selectedSubs = JSON.parse(subPemeriksaan);
            console.log('Selected subs:', selectedSubs); // Debug
        } catch(e) {
            console.log('Sub pemeriksaan tidak valid:', e);
        }
    }
    
    // Generate form dengan filter
    generateFormFields(examType, existingResults, selectedSubs);
    
    document.getElementById('modalLoading').classList.add('hidden');
    document.getElementById('modalFormContainer').classList.remove('hidden');
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}
// showFlashMessage removed in favor of showToast
function getResultTypeFromExamination(jenisType) {
    const typeMapping = {
        'Kimia Darah': 'kimia_darah',
        'Hematologi': 'hematologi',
        'Urinologi': 'urinologi',
        'Serologi': 'serologi',
        'Serologi Imunologi': 'serologi',
        'TBC': 'tbc',
        'IMS': 'ims'
    };
    return typeMapping[jenisType] || 'mls';
}

function generateFormFields(jenisType, existingResults, selectedSubs = []) {
    const container = document.getElementById('dynamicFormContent');
    let html = '';
    
    // Show info banner if there are specific sub pemeriksaan
    if (selectedSubs && selectedSubs.length > 0) {
        html += `
            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-blue-900 mb-2">
                            Pemeriksaan Spesifik yang Diminta:
                        </p>
                        <div class="flex flex-wrap gap-2 mb-3">
                            ${selectedSubs.map(sub => {
                                const label = getSubPemeriksaanLabel(sub, jenisType);
                                return `<span class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full shadow-sm">
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    ${label}
                                </span>`;
                            }).join('')}
                        </div>
                        <p class="text-xs text-blue-700">
                            <i data-lucide="lightbulb" class="w-3 h-3 inline mr-1"></i>
                            <strong>Tips:</strong> Field yang diminta akan ditampilkan di bagian atas dengan highlight biru. 
                            Parameter tambahan tersedia di bagian bawah jika diperlukan.
                        </p>
                    </div>
                </div>
            </div>
        `;
    }
    
    switch (jenisType.toLowerCase()) {
        case 'kimia darah':
            html += generateKimiaDarahFormHybrid(existingResults, selectedSubs);
            break;
        case 'hematologi':
            html += generateHematologiFormHybrid(existingResults, selectedSubs);
            break;
        case 'urinologi':
            html += generateUrinologiFormHybrid(existingResults, selectedSubs);
            break;
        case 'serologi':
        case 'serologi imunologi':
            html += generateSerologiFormHybrid(existingResults, selectedSubs);
            break;
        case 'tbc':
            html += generateTbcFormHybrid(existingResults, selectedSubs);
            break;
        case 'ims':
            html += generateImsFormHybrid(existingResults, selectedSubs);
            break;
        default:
            html += generateMlsForm(existingResults);
            break;
    }
    
    container.innerHTML = html;
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}
/**
 * Get sub pemeriksaan label
 */
function getSubPemeriksaanLabel(subKey, jenisType) {
    const labelMaps = {
        'kimia darah': {
            'gula_darah_sewaktu': 'Gula Darah Sewaktu',
            'gula_darah_puasa': 'Gula Darah Puasa',
            'gula_darah_2jam_pp': 'Gula Darah 2 Jam PP',
            'cholesterol_total': 'Kolesterol Total',
            'cholesterol_hdl': 'Kolesterol HDL',
            'cholesterol_ldl': 'Kolesterol LDL',
            'trigliserida': 'Trigliserida',
            'asam_urat': 'Asam Urat',
            'ureum': 'Ureum',
            'creatinin': 'Kreatinin',
            'sgpt': 'SGPT',
            'sgot': 'SGOT'
        },
        'hematologi': {
            'paket_darah_rutin': 'Paket Darah Rutin (Numerik)',
            'laju_endap_darah': 'Laju Endap Darah (LED)',
            'clotting_time': 'Clotting Time',
            'bleeding_time': 'Bleeding Time',
            'golongan_darah': 'Golongan Darah + Rhesus',
            'malaria': 'Malaria'
        },
        'urinologi': {
            'urin_rutin': 'Urin Rutin',
            'protein': 'Protein Urin (Kuantitatif)',
            'tes_kehamilan': 'Tes',
                  'tes_kehamilan': 'Tes Kehamilan'
        },
        'serologi': {
            'rdt_antigen': 'RDT Antigen',
            'widal': 'Widal',
            'hbsag': 'HBsAg',
            'ns1': 'NS1 (Dengue)',
            'hiv': 'HIV'
        },
        'tbc': {
            'dahak': 'Dahak (BTA)',
            'tcm': 'TCM (GeneXpert)'
        },
        'ims': {
            'sifilis': 'Sifilis',
            'duh_tubuh': 'Duh Tubuh'
        }
    };
    
    const typeMap = labelMaps[jenisType.toLowerCase()];
    return typeMap && typeMap[subKey] ? typeMap[subKey] : subKey;
}
function toggleOptionalFields(uniqueId = '') {
    const containerId = 'optionalFieldsContainer' + uniqueId;
    const iconId = 'optionalToggleIcon' + uniqueId;
    
    const container = document.getElementById(containerId);
    const icon = document.getElementById(iconId);
    const button = icon ? icon.closest('button') : null;
    
    if (!container) {
        console.error('Container not found:', containerId);
        return;
    }
    
    if (container.classList.contains('hidden')) {
        // Expand
        container.classList.remove('hidden');
        container.style.maxHeight = container.scrollHeight + 'px';
        if (icon) icon.style.transform = 'rotate(180deg)';
        if (button) button.classList.add('bg-gray-50');
    } else {
        // Collapse
        container.style.maxHeight = '0px';
        setTimeout(() => {
            container.classList.add('hidden');
        }, 300);
        if (icon) icon.style.transform = 'rotate(0deg)';
        if (button) button.classList.remove('bg-gray-50');
    }
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}


/**
 * Check if field is in requested subs
 */
function isFieldRequested(fieldKey, selectedSubs, packageMap = null) {
    if (!selectedSubs || selectedSubs.length === 0) {
        return false;
    }
    if (selectedSubs.includes(fieldKey)) {
        return true;
    }

    if (packageMap) {
        for (const sub of selectedSubs) {
            if (packageMap[sub] && packageMap[sub].includes(fieldKey)) {
                return true;
            }
        }
    }
    
    return false;
}
/**
 * Get sub pemeriksaan label - JavaScript version
 */
function getSubPemeriksaanLabel(subKey, jenisType) {
    const labelMaps = {
        'kimia darah': {
            'gula_darah_sewaktu': 'Gula Darah Sewaktu',
            'gula_darah_puasa': 'Gula Darah Puasa',
            'gula_darah_2jam_pp': 'Gula Darah 2 Jam PP',
            'cholesterol_total': 'Kolesterol Total',
            'cholesterol_hdl': 'Kolesterol HDL',
            'cholesterol_ldl': 'Kolesterol LDL',
            'trigliserida': 'Trigliserida',
            'asam_urat': 'Asam Urat',
            'ureum': 'Ureum',
            'creatinin': 'Kreatinin',
            'sgpt': 'SGPT',
            'sgot': 'SGOT'
        },
        'hematologi': {
            'paket_darah_rutin': 'Paket Darah Rutin (Numerik)',
            'laju_endap_darah': 'Laju Endap Darah (LED)',
            'clotting_time': 'Clotting Time',
            'bleeding_time': 'Bleeding Time',
            'golongan_darah': 'Golongan Darah + Rhesus',
            'malaria': 'Malaria'
        },
        'urinologi': {
            'urin_rutin': 'Urin Rutin',
            'protein': 'Protein Urin (Kuantitatif)',
            'tes_kehamilan': 'Tes Kehamilan'
        },
        'serologi': {
            'rdt_antigen': 'RDT Antigen',
            'widal': 'Widal',
            'hbsag': 'HBsAg',
            'ns1': 'NS1 (Dengue)',
            'hiv': 'HIV'
        },
        'serologi imunologi': {
            'rdt_antigen': 'RDT Antigen',
            'widal': 'Widal',
            'hbsag': 'HBsAg',
            'ns1': 'NS1 (Dengue)',
            'hiv': 'HIV'
        },
        'tbc': {
            'dahak': 'Dahak (BTA)',
            'tcm': 'TCM (GeneXpert)'
        },
        'ims': {
            'sifilis': 'Sifilis',
            'duh_tubuh': 'Duh Tubuh'
        }
    };
    
    const jenisLower = jenisType.toLowerCase();
    if (labelMaps[jenisLower] && labelMaps[jenisLower][subKey]) {
        return labelMaps[jenisLower][subKey];
    }
    
    return subKey.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}
/**
 * ============================================
 * COMPLETE FIXED FORM GENERATORS
 * Dengan uniqueId untuk toggle dan fieldPrefix untuk field names
 * ============================================
 */

// ============================================
// KIMIA DARAH - FIXED
// ============================================
function generateKimiaDarahFormHybrid(existingResults, selectedSubs = [], fieldPrefix = '', uniqueId = '') {
    const values = existingResults || {};
    const hasFilter = selectedSubs && selectedSubs.length > 0;
    
    const allFields = [
        { key: 'gula_darah_sewaktu', label: 'Gula Darah Sewaktu', unit: 'mg/dL', normal: '70-200' },
        { key: 'gula_darah_puasa', label: 'Gula Darah Puasa', unit: 'mg/dL', normal: '70-110' },
        { key: 'gula_darah_2jam_pp', label: 'Gula Darah 2 Jam PP', unit: 'mg/dL', normal: '< 140' },
        { key: 'cholesterol_total', label: 'Kolesterol Total', unit: 'mg/dL', normal: '< 200' },
        { key: 'cholesterol_hdl', label: 'Kolesterol HDL', unit: 'mg/dL', normal: '> 40' },
        { key: 'cholesterol_ldl', label: 'Kolesterol LDL', unit: 'mg/dL', normal: '< 130' },
        { key: 'trigliserida', label: 'Trigliserida', unit: 'mg/dL', normal: '< 150' },
        { key: 'asam_urat', label: 'Asam Urat', unit: 'mg/dL', normal: 'L: 3.5-7.0, P: 2.5-6.0' },
        { key: 'ureum', label: 'Ureum', unit: 'mg/dL', normal: '10-50' },
        { key: 'creatinin', label: 'Kreatinin', unit: 'mg/dL', normal: 'L: 0.7-1.3, P: 0.6-1.1' },
        { key: 'sgpt', label: 'SGPT', unit: 'U/L', normal: '< 41' },
        { key: 'sgot', label: 'SGOT', unit: 'U/L', normal: '< 37' }
    ];
    
    const requestedFields = [];
    const optionalFields = [];
    
    allFields.forEach(field => {
        if (hasFilter && selectedSubs.includes(field.key)) {
            requestedFields.push(field);
        } else if (hasFilter) {
            optionalFields.push(field);
        } else {
            requestedFields.push(field);
        }
    });
    
    let html = '';
    
    // REQUESTED SECTION
    if (requestedFields.length > 0) {
        html += `
            <div class="mb-6 bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-6 shadow-md">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                        <i data-lucide="clipboard-check" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-blue-900">
                            ${hasFilter ? 'Pemeriksaan yang Diminta' : 'Parameter Kimia Darah'}
                        </h4>
                        <p class="text-xs text-blue-700">
                            ${hasFilter ? `${requestedFields.length} parameter sesuai permintaan` : 'Semua parameter tersedia'}
                        </p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${requestedFields.map(field => renderKimiaDarahField(field, values, true, fieldPrefix)).join('')}
                </div>
            </div>
        `;
    }
    
    // OPTIONAL SECTION - Hanya tampil jika TIDAK ada filter spesifik sub_pemeriksaan
    // Untuk single examination dengan sub tertentu, kita hanya tampilkan yang diminta
    if (optionalFields.length > 0 && !hasFilter) {
        html += `
            <div class="mb-6 bg-white border border-gray-300 rounded-xl overflow-hidden shadow-sm">
                <button type="button" 
                        onclick="toggleOptionalFields('${uniqueId}')" 
                        class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-all duration-200 group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-100 group-hover:bg-gray-200 rounded-lg flex items-center justify-center mr-3 transition-colors">
                            <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="text-base font-semibold text-gray-900">
                                Parameter Tambahan (Opsional)
                            </h4>
                            <p class="text-xs text-gray-600">
                                ${optionalFields.length} parameter tersedia - Klik untuk membuka
                            </p>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" 
                       id="optionalToggleIcon${uniqueId}" 
                       class="w-5 h-5 text-gray-500 transition-transform duration-300"></i>
                </button>
                
                <div id="optionalFieldsContainer${uniqueId}" 
                     class="hidden border-t border-gray-200 transition-all duration-300"
                     style="overflow: hidden; max-height: 0;">
                    <div class="p-6 bg-gray-50">
                        <div class="mb-4 p-3 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i data-lucide="info" class="w-4 h-4 text-amber-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs text-amber-800">
                                        <strong>Informasi:</strong> Parameter ini dapat diisi jika sampel mencukupi dan relevan secara klinis. 
                                        Kosongkan jika tidak diperiksa.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${optionalFields.map(field => renderKimiaDarahField(field, values, false, fieldPrefix)).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    return html;
}

function renderKimiaDarahField(field, values, isRequested, fieldPrefix = '') {
    const fieldName = fieldPrefix + field.key;
    const value = values[field.key] || '';
    const fieldClass = isRequested 
        ? 'w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all'
        : 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
    
    const labelClass = isRequested ? 'text-blue-900 font-semibold' : 'text-gray-700 font-medium';
    const badge = isRequested 
        ? '<span class="ml-2 px-2 py-0.5 bg-blue-600 text-white text-xs rounded-full shadow-sm">Diminta</span>'
        : '<span class="ml-2 px-2 py-0.5 bg-gray-400 text-white text-xs rounded-full">Opsional</span>';
    
    const borderClass = isRequested ? 'border-l-4 border-blue-600 pl-3' : '';
    
    return `
        <div class="${borderClass}">
            <label class="block text-sm ${labelClass} mb-2">
                ${field.label} (${field.unit})
                ${badge}
                <span class="block text-xs text-gray-500 font-normal mt-1">Normal: ${field.normal}</span>
            </label>
            <input type="number" 
                   name="${fieldName}" 
                   value="${value}" 
                   class="${fieldClass}" 
                   placeholder="${field.normal}" 
                   step="0.01">
        </div>
    `;
}

// ============================================
// HEMATOLOGI - FIXED
// ============================================
function generateHematologiFormHybrid(existingResults, selectedSubs = [], fieldPrefix = '', uniqueId = '') {
    const values = existingResults || {};
    const hasFilter = selectedSubs && selectedSubs.length > 0;
    
    // Paket Darah Rutin - berisi SEMUA field numerik (13 field)
    const paketDarahRutinFields = [
        { key: 'hemoglobin', label: 'Hemoglobin Hb', unit: 'g/dL', normal: 'L:13-17, P:12-15' },
        { key: 'hematokrit', label: 'Hematokrit Ht', unit: '%', normal: 'L:40-50, P:35-45' },
        { key: 'leukosit', label: 'Leukosit WBC', unit: '/µL', normal: '4000-11000' },
        { key: 'trombosit', label: 'Trombosit PLT', unit: '/µL', normal: '150000-400000' },
        { key: 'eritrosit', label: 'Eritrosit RBC', unit: '/µL', normal: 'L:4.5-5.5jt, P:4.0-5.0jt' },
        { key: 'mcv', label: 'MCV', unit: 'fL', normal: '80-100' },
        { key: 'mch', label: 'MCH', unit: 'pg', normal: '27-31' },
        { key: 'mchc', label: 'MCHC', unit: 'g/dL', normal: '32-36' },
        { key: 'eosinofil', label: 'Eosinofil', unit: '%', normal: '1-3' },
        { key: 'basofil', label: 'Basofil', unit: '%', normal: '0-1' },
        { key: 'neutrofil', label: 'Neutrofil', unit: '%', normal: '50-70' },
        { key: 'limfosit', label: 'Limfosit', unit: '%', normal: '20-40' },
        { key: 'monosit', label: 'Monosit', unit: '%', normal: '2-8' }
    ];
    
    // Individual fields - masing-masing terpisah
    const individualFields = [
        { key: 'laju_endap_darah', label: 'Laju Endap Darah (LED)', unit: 'mm/jam', normal: 'L:<15, P:<20', type: 'number' },
        { key: 'clotting_time', label: 'Clotting Time', unit: 'menit', normal: '5-15', type: 'number' },
        { key: 'bleeding_time', label: 'Bleeding Time', unit: 'menit', normal: '1-6', type: 'number' },
        { key: 'golongan_darah', label: 'Golongan Darah', type: 'select', options: ['', 'A', 'B', 'AB', 'O'] },
        { key: 'rhesus', label: 'Rhesus', type: 'select', options: ['', '+', '-'] },
        { key: 'malaria', label: 'Malaria', type: 'textarea' }
    ];
    
    let html = '<div class="space-y-6">';
    
    // Check if paket_darah_rutin is requested
    const paketRequested = !hasFilter || selectedSubs.includes('paket_darah_rutin');
    
    // Render Paket Darah Rutin Section
    if (paketRequested) {
        html += `
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-6 shadow-md">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                        <i data-lucide="activity" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-blue-900">
                            Paket Darah Rutin (Numerik)
                            <span class="ml-2 px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full shadow-sm">DIMINTA</span>
                        </h4>
                        <p class="text-xs text-blue-700">13 parameter pemeriksaan</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    ${paketDarahRutinFields.map(field => renderHematologiField(field, values, true, fieldPrefix)).join('')}
                </div>
            </div>
        `;
    }
    
    // Render Individual Fields - hanya yang diminta
    const requestedIndividualFields = hasFilter 
        ? individualFields.filter(f => selectedSubs.includes(f.key))
        : individualFields;
    
    // Render each individual field as separate card
    requestedIndividualFields.forEach(field => {
        const isGolDarahRhesus = field.key === 'golongan_darah';
        
        html += `
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-4 shadow-md">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 shadow">
                        <i data-lucide="${getFieldIcon(field.key)}" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-blue-900">
                            ${field.label}
                            <span class="ml-2 px-2 py-0.5 bg-blue-600 text-white text-xs font-medium rounded-full">DIMINTA</span>
                        </h4>
                        ${field.unit ? `<p class="text-xs text-blue-700">Satuan: ${field.unit}</p>` : ''}
                    </div>
                </div>
                
                <div class="${isGolDarahRhesus ? 'grid grid-cols-2 gap-3' : ''}">
                    ${renderHematologiField(field, values, true, fieldPrefix)}
                    ${isGolDarahRhesus ? renderHematologiField(individualFields.find(f => f.key === 'rhesus'), values, true, fieldPrefix) : ''}
                </div>
            </div>
        `;
        
        // Skip rhesus if we already rendered it with golongan_darah
        if (isGolDarahRhesus) {
            // Rhesus sudah di-render bersama golongan_darah
        }
    });
    
    html += '</div>';
    return html;
}

// Helper function untuk icon individual field
function getFieldIcon(fieldKey) {
    const iconMap = {
        'laju_endap_darah': 'timer',
        'clotting_time': 'clock',
        'bleeding_time': 'droplet',
        'golongan_darah': 'heart',
        'rhesus': 'plus-circle',
        'malaria': 'bug'
    };
    return iconMap[fieldKey] || 'activity';
}



function renderHematologiField(field, values, isRequested, fieldPrefix = '') {
    const fieldName = fieldPrefix + field.key;
    const value = values[field.key] || '';
    const fieldClass = isRequested 
        ? 'w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600'
        : 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500';
    
    const labelClass = isRequested ? 'text-blue-900 font-semibold' : 'text-gray-700 font-medium';
    const borderClass = isRequested ? 'border-l-4 border-blue-600 pl-3' : '';
    
    let inputHtml = '';
    
    if (field.type === 'select') {
        inputHtml = `
            <select name="${fieldName}" class="${fieldClass}">
                ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>
        `;
    } else if (field.type === 'textarea') {
        inputHtml = `<textarea name="${fieldName}" rows="2" class="${fieldClass}" placeholder="Hasil pemeriksaan...">${value}</textarea>`;
    } else {
        inputHtml = `<input type="number" name="${fieldName}" value="${value}" class="${fieldClass}" placeholder="${field.normal || ''}" step="0.1">`;
    }
    
    return `
        <div class="${borderClass}">
            <label class="block text-sm ${labelClass} mb-2">
                ${field.label}${field.unit ? ` (${field.unit})` : ''}
                ${field.normal ? `<span class="block text-xs text-gray-500 font-normal mt-1">Normal: ${field.normal}</span>` : ''}
            </label>
            ${inputHtml}
        </div>
    `;
}

// ============================================
// URINOLOGI - FIXED
// ============================================
function generateUrinologiFormHybrid(existingResults, selectedSubs = [], fieldPrefix = '', uniqueId = '') {
    const values = existingResults || {};
    const hasFilter = selectedSubs && selectedSubs.length > 0;
    
    const urinRutinRequested = !hasFilter || selectedSubs.includes('urin_rutin');
    const proteinRequested = !hasFilter || selectedSubs.includes('protein');
    const tesKehamilanRequested = !hasFilter || selectedSubs.includes('tes_kehamilan');
    
    let html = '<div class="space-y-6">';
    
    // URIN RUTIN SECTION
    if (urinRutinRequested) {
        html += `
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-6 shadow-md">
                <h4 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                    Urin Rutin (Lengkap)
                    ${hasFilter ? '<span class="ml-3 px-3 py-1 bg-blue-600 text-white text-xs rounded-full">DIMINTA</span>' : ''}
                </h4>
                
                <!-- Fisik -->
                <div class="mb-6">
                    <h5 class="text-sm font-semibold text-blue-800 mb-3">Pemeriksaan Fisik</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2 border-l-4 border-blue-600 pl-3">
                            <label class="block text-sm font-semibold text-blue-900 mb-2">Makroskopis</label>
                            <textarea name="${fieldPrefix}makroskopis" rows="2" 
                                      class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600" 
                                      placeholder="Warna, kejernihan, bau">${values.makroskopis || ''}</textarea>
                        </div>
                        <div class="border-l-4 border-blue-600 pl-3">
                            <label class="block text-sm font-semibold text-blue-900 mb-2">Berat Jenis</label>
                            <input type="number" name="${fieldPrefix}berat_jenis" value="${values.berat_jenis || ''}" 
                                   class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600" 
                                   placeholder="1.015" step="0.001">
                        </div>
                        <div class="border-l-4 border-blue-600 pl-3">
                            <label class="block text-sm font-semibold text-blue-900 mb-2">pH</label>
                            <input type="number" name="${fieldPrefix}kimia_ph" value="${values.kimia_ph || ''}" 
                                   class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600" 
                                   placeholder="6.0" step="0.1">
                        </div>
                    </div>
                </div>
                
                <!-- Kimia -->
                <div class="mb-6">
                    <h5 class="text-sm font-semibold text-blue-800 mb-3">Pemeriksaan Kimia</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="border-l-4 border-blue-600 pl-3">
                            <label class="block text-sm font-semibold text-blue-900 mb-2">
                                Protein (Kualitatif)
                                <span class="block text-xs text-gray-500 font-normal mt-1">Bagian dari paket rutin</span>
                            </label>
                            <select name="${fieldPrefix}protein_regular" class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600">
                                <option value="">Pilih</option>
                                ${['Negatif', '+1', '+2', '+3', '+4'].map(opt => 
                                    `<option value="${opt}" ${values.protein_regular === opt ? 'selected' : ''}>${opt}</option>`
                                ).join('')}
                            </select>
                        </div>
                        ${['glukosa', 'keton', 'bilirubin', 'urobilinogen'].map(field => `
                            <div class="border-l-4 border-blue-600 pl-3">
                                <label class="block text-sm font-semibold text-blue-900 mb-2">${field.charAt(0).toUpperCase() + field.slice(1)}</label>
                                <select name="${fieldPrefix}${field}" class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600">
                                    <option value="">Pilih</option>
                                    ${['Negatif', '+1', '+2', '+3', '+4'].map(opt => 
                                        `<option value="${opt}" ${values[field] === opt ? 'selected' : ''}>${opt}</option>`
                                    ).join('')}
                                </select>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <!-- Mikroskopis -->
                <div>
                    <h5 class="text-sm font-semibold text-blue-800 mb-3">Pemeriksaan Mikroskopis</h5>
                    <div class="border-l-4 border-blue-600 pl-3">
                        <label class="block text-sm font-semibold text-blue-900 mb-2">Mikroskopis (Sedimen)</label>
                        <textarea name="${fieldPrefix}mikroskopis" rows="4" 
                                  class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600" 
                                  placeholder="Eritrosit, leukosit, epitel, silinder, kristal, bakteri...">${values.mikroskopis || ''}</textarea>
                    </div>
                </div>
            </div>
        `;
    }
    
    // PROTEIN KUANTITATIF SECTION
    if (proteinRequested) {
        const sectionClass = urinRutinRequested 
            ? 'bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-6 shadow-md'
            : 'bg-white border border-gray-200 rounded-xl p-6';
        
        html += `
            <div class="${sectionClass}">
                <h4 class="text-lg font-bold ${urinRutinRequested ? 'text-blue-900' : 'text-gray-900'} mb-4 flex items-center">
                    <i data-lucide="droplet" class="w-5 h-5 mr-2"></i>
                    Protein Urin (Kuantitatif)
                    ${hasFilter && proteinRequested ? '<span class="ml-3 px-3 py-1 bg-blue-600 text-white text-xs rounded-full">DIMINTA</span>' : ''}
                </h4>
                <div class="border-l-4 border-blue-600 pl-3">
                    <label class="block text-sm font-semibold ${urinRutinRequested ? 'text-blue-900' : 'text-gray-700'} mb-2">
                        Hasil Protein Kuantitatif
                        <span class="block text-xs text-gray-500 font-normal mt-1">Normal: < 150 mg/24jam atau < 10 mg/dL</span>
                    </label>
                    <input type="text" 
                           name="${fieldPrefix}protein" 
                           value="${values.protein || ''}" 
                           class="w-full px-3 py-2 border-2 ${urinRutinRequested ? 'border-blue-400 bg-blue-50' : 'border-gray-300'} rounded-lg focus:ring-2 focus:ring-blue-600" 
                           placeholder="Contoh: 25.5 mg/dL atau 180 mg/24jam">
                </div>
            </div>
        `;
    }
    
    // TES KEHAMILAN SECTION
    if (tesKehamilanRequested) {
        const isInRequested = urinRutinRequested || proteinRequested;
        const sectionClass = isInRequested 
            ? 'bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-6 shadow-md'
            : 'bg-white border border-gray-200 rounded-xl p-6';
        
        html += `
            <div class="${sectionClass}">
                <h4 class="text-lg font-bold ${isInRequested ? 'text-blue-900' : 'text-gray-900'} mb-4 flex items-center">
                    <i data-lucide="baby" class="w-5 h-5 mr-2"></i>
                    Tes Kehamilan (HCG)
                    ${hasFilter && tesKehamilanRequested ? '<span class="ml-3 px-3 py-1 bg-blue-600 text-white text-xs rounded-full">DIMINTA</span>' : ''}
                </h4>
                <div class="border-l-4 border-blue-600 pl-3">
                    <label class="block text-sm font-semibold ${isInRequested ? 'text-blue-900' : 'text-gray-700'} mb-2">Hasil</label>
                    <select name="${fieldPrefix}tes_kehamilan" class="w-full px-3 py-2 border-2 ${isInRequested ? 'border-blue-400 bg-blue-50' : 'border-gray-300'} rounded-lg focus:ring-2 focus:ring-blue-600">
                        <option value="">Pilih Hasil</option>
                        <option value="Positif" ${values.tes_kehamilan === 'Positif' ? 'selected' : ''}>Positif</option>
                        <option value="Negatif" ${values.tes_kehamilan === 'Negatif' ? 'selected' : ''}>Negatif</option>
                    </select>
                </div>
            </div>
        `;
    }
    
    // OPTIONAL SECTION - HANYA tampil jika TIDAK ada filter spesifik
    // Untuk single examination dengan sub tertentu, JANGAN tampilkan optional
    if (!hasFilter) {
        const optionalItems = [];
        if (!urinRutinRequested) optionalItems.push('urin_rutin');
        if (!proteinRequested) optionalItems.push('protein');
        if (!tesKehamilanRequested) optionalItems.push('tes_kehamilan');
        
        if (optionalItems.length > 0) {
            html += `
                <div class="bg-white border border-gray-300 rounded-xl overflow-hidden shadow-sm">
                    <button type="button" onclick="toggleOptionalFields('${uniqueId}')" 
                            class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-all duration-200 group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-100 group-hover:bg-gray-200 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="text-base font-semibold text-gray-900">Parameter Tambahan (Opsional)</h4>
                                <p class="text-xs text-gray-600">${optionalItems.length} parameter tersedia</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-down" id="optionalToggleIcon${uniqueId}" class="w-5 h-5 text-gray-500 transition-transform duration-300"></i>
                    </button>
                    
                    <div id="optionalFieldsContainer${uniqueId}" class="hidden border-t border-gray-200 transition-all duration-300" style="overflow: hidden; max-height: 0;">
                        <div class="p-6 bg-gray-50 space-y-6">
                            ${!urinRutinRequested ? `
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Urin Rutin (Opsional)</h4>
                                    <div class="space-y-4">
                                        <textarea name="${fieldPrefix}makroskopis" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Makroskopis">${values.makroskopis || ''}</textarea>
                                        <textarea name="${fieldPrefix}mikroskopis" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Mikroskopis">${values.mikroskopis || ''}</textarea>
                                    </div>
                                </div>
                            ` : ''}
                            ${!proteinRequested ? `
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Protein Kuantitatif (Opsional)</h4>
                                    <input type="text" name="${fieldPrefix}protein" value="${values.protein || ''}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg" 
                                           placeholder="Hasil protein kuantitatif">
                                </div>
                            ` : ''}
                            ${!tesKehamilanRequested ? `
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Tes Kehamilan (Opsional)</h4>
                                    <select name="${fieldPrefix}tes_kehamilan" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="">Pilih</option>
                                        <option value="Positif" ${values.tes_kehamilan === 'Positif' ? 'selected' : ''}>Positif</option>
                                        <option value="Negatif" ${values.tes_kehamilan === 'Negatif' ? 'selected' : ''}>Negatif</option>
                                    </select>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    html += '</div>';
    return html;
}

// ============================================
// SEROLOGI - FIXED
// ============================================
function generateSerologiFormHybrid(existingResults, selectedSubs = [], fieldPrefix = '', uniqueId = '') {
    const values = existingResults || {};
    const hasFilter = selectedSubs && selectedSubs.length > 0;
    
    const allFields = [
        { key: 'rdt_antigen', label: 'RDT Antigen', type: 'select', options: ['', 'Positif', 'Negatif'] },
        { key: 'widal', label: 'Widal', type: 'textarea' },
        { key: 'hbsag', label: 'HBsAg', type: 'select', options: ['', 'Reaktif', 'Non-Reaktif'] },
        { key: 'ns1', label: 'NS1 (Dengue)', type: 'select', options: ['', 'Positif', 'Negatif'] },
        { key: 'hiv', label: 'HIV', type: 'select', options: ['', 'Reaktif', 'Non-Reaktif'] }
    ];
    
    const requestedFields = [];
    const optionalFields = [];
    
    allFields.forEach(field => {
        if (hasFilter && selectedSubs.includes(field.key)) {
            requestedFields.push(field);
        } else if (hasFilter) {
            optionalFields.push(field);
        } else {
            requestedFields.push(field);
        }
    });
    
    let html = '';
    
    if (requestedFields.length > 0) {
        html += `
            <div class="mb-6 bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-6 shadow-md">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                        <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-blue-900">
                            ${hasFilter ? 'Pemeriksaan yang Diminta' : 'Parameter Serologi & Imunologi'}
                        </h4>
                        <p class="text-xs text-blue-700">${requestedFields.length} parameter</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${requestedFields.map(field => renderSerologiField(field, values, true, fieldPrefix)).join('')}
                </div>
            </div>
        `;
    }
    
    // OPTIONAL - Hanya jika TIDAK ada filter
    if (optionalFields.length > 0 && !hasFilter) {
        html += `
            <div class="mb-6 bg-white border border-gray-300 rounded-xl overflow-hidden shadow-sm">
                <button type="button" onclick="toggleOptionalFields('${uniqueId}')" 
                        class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-all">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="text-base font-semibold text-gray-900">Parameter Tambahan</h4>
                            <p class="text-xs text-gray-600">${optionalFields.length} parameter</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" id="optionalToggleIcon${uniqueId}" class="w-5 h-5 text-gray-500 transition-transform"></i>
                </button>
                
                <div id="optionalFieldsContainer${uniqueId}" class="hidden border-t border-gray-200" style="overflow: hidden;">
                    <div class="p-6 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${optionalFields.map(field => renderSerologiField(field, values, false, fieldPrefix)).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    return html;
}

function renderSerologiField(field, values, isRequested, fieldPrefix = '') {
    const fieldName = fieldPrefix + field.key;
    const value = values[field.key] || '';
    const fieldClass = isRequested 
        ? 'w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600'
        : 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500';
    
    const labelClass = isRequested ? 'text-blue-900 font-semibold' : 'text-gray-700 font-medium';
    const borderClass = isRequested ? 'border-l-4 border-blue-600 pl-3' : '';
    const colSpan = field.type === 'textarea' ? 'md:col-span-2' : '';
    
    let inputHtml = '';
    
    if (field.type === 'select') {
        inputHtml = `
            <select name="${fieldName}" class="${fieldClass}">
                ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>
        `;
    } else if (field.type === 'textarea') {
        inputHtml = `<textarea name="${fieldName}" rows="3" class="${fieldClass}" placeholder="Hasil pemeriksaan...">${value}</textarea>`;
    }
    
    return `
        <div class="${borderClass} ${colSpan}">
            <label class="block text-sm ${labelClass} mb-2">${field.label}</label>
            ${inputHtml}
        </div>
    `;
}

// ============================================
// TBC - FIXED
// ============================================
function generateTbcFormHybrid(existingResults, selectedSubs = [], fieldPrefix = '', uniqueId = '') {
    const values = existingResults || {};
    const hasFilter = selectedSubs && selectedSubs.length > 0;
    
    const allFields = [
        { key: 'dahak', label: 'Dahak (BTA)', type: 'select', options: ['', 'Negatif', 'Scanty', '+1', '+2', '+3'] },
        { key: 'tcm', label: 'TCM (GeneXpert)', type: 'select', options: ['', 'Detected', 'Not Detected'] }
    ];
    
    const requestedFields = [];
    const optionalFields = [];
    
    allFields.forEach(field => {
        if (hasFilter && selectedSubs.includes(field.key)) {
            requestedFields.push(field);
        } else if (hasFilter) {
            optionalFields.push(field);
        } else {
            requestedFields.push(field);
        }
    });
    
    let html = '';
    
    if (requestedFields.length > 0) {
        html += `
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-6 shadow-md">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-3">
                        <i data-lucide="activity" class="w-6 h-6 text-white"></i>
                    </div>
                    <h4 class="text-lg font-bold text-blue-900">Parameter TBC</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${requestedFields.map(field => renderTbcField(field, values, true, fieldPrefix)).join('')}
                </div>
            </div>
        `;
    }
    
    // OPTIONAL - Hanya jika TIDAK ada filter
    if (optionalFields.length > 0 && !hasFilter) {
        html += `
            <div class="bg-white border border-gray-300 rounded-xl overflow-hidden mt-6">
                <button type="button" onclick="toggleOptionalFields('${uniqueId}')" class="w-full p-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600 mr-3"></i>
                        <h4 class="text-base font-semibold text-gray-900">Parameter Tambahan</h4>
                    </div>
                    <i data-lucide="chevron-down" id="optionalToggleIcon${uniqueId}" class="w-5 h-5 text-gray-500 transition-transform"></i>
                </button>
                <div id="optionalFieldsContainer${uniqueId}" class="hidden border-t p-6 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${optionalFields.map(field => renderTbcField(field, values, false, fieldPrefix)).join('')}
                    </div>
                </div>
            </div>
        `;
    }
    
    return html;
}

function renderTbcField(field, values, isRequested, fieldPrefix = '') {
    const fieldName = fieldPrefix + field.key;
    const value = values[field.key] || '';
    const fieldClass = isRequested 
        ? 'w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600'
        : 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500';
    
    const labelClass = isRequested ? 'text-blue-900 font-semibold' : 'text-gray-700 font-medium';
    const borderClass = isRequested ? 'border-l-4 border-blue-600 pl-3' : '';
    
    return `
        <div class="${borderClass}">
            <label class="block text-sm ${labelClass} mb-2">${field.label}</label>
            <select name="${fieldName}" class="${fieldClass}">
                ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>
        </div>
    `;
}

// ============================================
// IMS - FIXED
// ============================================
function generateImsFormHybrid(existingResults, selectedSubs = [], fieldPrefix = '', uniqueId = '') {
    const values = existingResults || {};
    const hasFilter = selectedSubs && selectedSubs.length > 0;
    
    const allFields = [
        { key: 'sifilis', label: 'Sifilis', type: 'select', options: ['', 'Reaktif', 'Non-Reaktif'] },
        { key: 'duh_tubuh', label: 'Duh Tubuh', type: 'textarea' }
    ];
    
    const requestedFields = [];
    const optionalFields = [];
    
    allFields.forEach(field => {
        if (hasFilter && selectedSubs.includes(field.key)) {
            requestedFields.push(field);
        } else if (hasFilter) {
            optionalFields.push(field);
        } else {
            requestedFields.push(field);
        }
    });
    
    let html = '';
    
    if (requestedFields.length > 0) {
        html += `
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-6 shadow-md">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-3">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                    </div>
                    <h4 class="text-lg font-bold text-blue-900">Parameter IMS</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${requestedFields.map(field => renderImsField(field, values, true, fieldPrefix)).join('')}
                </div>
            </div>
        `;
    }
    
    // OPTIONAL - Hanya jika TIDAK ada filter
    if (optionalFields.length > 0 && !hasFilter) {
        html += `
            <div class="bg-white border border-gray-300 rounded-xl overflow-hidden mt-6">
                <button type="button" onclick="toggleOptionalFields('${uniqueId}')" class="w-full p-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center">
                        <i data-lucide="plus-circle" class="w-5 h-5 mr-3"></i>
                        <h4 class="font-semibold">Parameter Tambahan</h4>
                    </div>
                    <i data-lucide="chevron-down" id="optionalToggleIcon${uniqueId}" class="w-5 h-5 transition-transform"></i>
                </button>
                <div id="optionalFieldsContainer${uniqueId}" class="hidden border-t p-6 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${optionalFields.map(field => renderImsField(field, values, false, fieldPrefix)).join('')}
                    </div>
                </div>
            </div>
        `;
    }
    
    return html;
}

function renderImsField(field, values, isRequested, fieldPrefix = '') {
    const fieldName = fieldPrefix + field.key;
    const value = values[field.key] || '';
    const fieldClass = isRequested 
        ? 'w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600'
        : 'w-full px-3 py-2 border border-gray-300 rounded-lg';
    
    const labelClass = isRequested ? 'text-blue-900 font-semibold' : 'text-gray-700 font-medium';
    const borderClass = isRequested ? 'border-l-4 border-blue-600 pl-3' : '';
    const colSpan = field.type === 'textarea' ? 'md:col-span-2' : '';
    
    let inputHtml = '';
    if (field.type === 'select') {
        inputHtml = `
            <select name="${fieldName}" class="${fieldClass}">
                ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>
        `;
    } else {
        inputHtml = `<textarea name="${fieldName}" rows="3" class="${fieldClass}" placeholder="Hasil...">${value}</textarea>`;
    }
    
    return `
        <div class="${borderClass} ${colSpan}">
            <label class="block text-sm ${labelClass} mb-2">${field.label}</label>
            ${inputHtml}
        </div>
    `;
}

// ============================================
// MAIN TOGGLE FUNCTION
// ============================================
function toggleOptionalFields(uniqueId = '') {
    const containerId = 'optionalFieldsContainer' + uniqueId;
    const iconId = 'optionalToggleIcon' + uniqueId;
    
    const container = document.getElementById(containerId);
    const icon = document.getElementById(iconId);
    const button = icon ? icon.closest('button') : null;
    
    if (!container) {
        console.error('Container not found:', containerId);
        return;
    }
    
    if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        container.style.maxHeight = container.scrollHeight + 'px';
        if (icon) icon.style.transform = 'rotate(180deg)';
        if (button) button.classList.add('bg-gray-50');
    } else {
        container.style.maxHeight = '0px';
        setTimeout(() => {
            container.classList.add('hidden');
        }, 300);
        if (icon) icon.style.transform = 'rotate(0deg)';
        if (button) button.classList.remove('bg-gray-50');
    }
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

console.log(' All form generators fixed with uniqueId and fieldPrefix support');

function submitResults(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 loading"></i>Menyimpan...';
    
    // Clear previous alerts
    const existingAlert = document.querySelector('.form-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    fetch('<?= base_url('sample_data/save_examination_results') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            // Store message for next page load
            sessionStorage.setItem('toast_success', 'Hasil pemeriksaan berhasil di submit');
            closeInputModal();
            location.reload();
        } else {
            showToast('error', 'Error: ' + (data.message || 'Gagal menyimpan hasil'));
            console.error('Save error:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan jaringan atau server. Silakan coba lagi.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
}
function detectAndRouteInput(examId, jenisType) {
    // Cek apakah ada examination_details (multi examination)
    // Ini bisa dicek dari DOM atau dari data
    const sampleCard = document.querySelector(`[data-exam-id="${examId}"]`);
    const hasMultipleExams = sampleCard && sampleCard.dataset.multipleExams === 'true';
    
    if (hasMultipleExams) {
        inputResultsMultiple(examId);
    } else {
        inputResults(examId, jenisType);
    }
}



function updateStatus(examId) {
    document.getElementById('updateExamId').value = examId;
    document.getElementById('updateStatusModal').classList.remove('hidden');
}

function closeUpdateModal() {
    document.getElementById('updateStatusModal').classList.add('hidden');
    document.getElementById('updateStatusForm').reset();
}

function submitStatusUpdate(event) {
    event.preventDefault();
    const form = event.target;
    
    // Check form validity before showing confirmation
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    showModal({
        title: 'Konfirmasi Update Status',
        message: 'Apakah Anda yakin ingin memperbarui status sampel ini?',
        type: 'warning',
        confirmText: 'Ya, Perbarui',
        onConfirm: () => {
            const examId = document.getElementById('updateExamId').value;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 loading"></i> Memperbarui...';
            lucide.createIcons();
            
            fetch(`<?= base_url('sample_data/update_sample_status') ?>/${examId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeUpdateModal();
                    closeUpdateModal();
                    sessionStorage.setItem('toast_success', data.message || 'Status berhasil diperbarui');
                    location.reload();
                } else {
                    showToast('error', data.message || 'Gagal memperbarui status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Terjadi kesalahan saat memperbarui status');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                lucide.createIcons();
            });
        }
    });
}

function viewResults(examId) {
    window.location.href = '<?= base_url('sample_data/view_results') ?>/' + examId;
}

// Close modals
document.getElementById('timelineModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeTimelineModal();
});

document.getElementById('inputResultsModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeInputModal();
});

document.getElementById('updateStatusModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeUpdateModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('timelineModal').classList.contains('hidden')) closeTimelineModal();
        if (!document.getElementById('inputResultsModal').classList.contains('hidden')) closeInputModal();
        if (!document.getElementById('updateStatusModal').classList.contains('hidden')) closeUpdateModal();
    }
});
function inputResultsMultiple(examId) {
    currentExaminationId = examId;
    document.getElementById('inputResultsModal').classList.remove('hidden');
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalFormContainer').classList.add('hidden');
    
    loadExaminationDataMultiple(examId);
}

/**
 * Load examination data untuk multiple types
 */
function loadExaminationDataMultiple(examId) {
    fetch(`<?= base_url('sample_data/get_examination_data_multiple') ?>/${examId}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'}
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentExaminationDetails = data.examination_details;
            populateModalMultiple(data.examination, data.examination_details, data.existing_results);
        } else {
            alert('Error: ' + data.message);
            closeInputModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data');
        closeInputModal();
    });
}

/**
 * Populate modal dengan multiple examination forms
 */
function populateModalMultiple(examination, examinationDetails, existingResults = {}) {
    const subtitle = `${examination.nomor_pemeriksaan} - ${examination.nama_pasien}`;
    document.getElementById('modalSubtitle').textContent = subtitle;
    document.getElementById('modalExamId').value = examination.pemeriksaan_id;
    
    // Generate forms untuk setiap examination type
    const container = document.getElementById('dynamicFormContent');
    let html = '';
    
    // Info banner
    html += `
        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-blue-900 mb-2">
                        Pemeriksaan Multi-Jenis
                    </p>
                    <p class="text-xs text-blue-700">
                        Pasien ini memiliki <strong>${examinationDetails.length} jenis pemeriksaan</strong>. 
                        Isi hasil untuk setiap jenis pemeriksaan di bawah ini.
                    </p>
                </div>
            </div>
        </div>
    `;
    
    // Hidden field untuk tracking result types
    const resultTypes = examinationDetails.map(d => getResultTypeFromExamination(d.jenis_pemeriksaan));
    html += `<input type="hidden" name="result_types" id="resultTypes" value="${JSON.stringify(resultTypes).replace(/"/g, '&quot;')}">`;
    
    // Generate form untuk setiap detail
    examinationDetails.forEach((detail, index) => {
        const jenisType = detail.jenis_pemeriksaan;
        const resultType = getResultTypeFromExamination(jenisType);
        const existingData = existingResults[jenisType] || null;
        const selectedSubs = detail.sub_pemeriksaan_array || [];
        
        html += `
            <div class="examination-type-card mb-6 bg-white border-2 border-blue-300 rounded-xl overflow-hidden shadow-lg">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                <i data-lucide="${getExaminationIcon(jenisType)}" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">${jenisType}</h3>
                                <p class="text-xs text-blue-100">Jenis ${index + 1} dari ${examinationDetails.length}</p>
                            </div>
                        </div>
                        ${detail.sub_pemeriksaan_display ? `
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium text-white">
                            ${detail.sub_pemeriksaan_display}
                        </span>
                        ` : ''}
                    </div>
                </div>
                
                <div class="p-6">
                    ${generateFormFieldsByType(jenisType, resultType, existingData, selectedSubs, index)}
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    document.getElementById('modalLoading').classList.add('hidden');
    document.getElementById('modalFormContainer').classList.remove('hidden');
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}
function generateFormFieldsByType(jenisType, resultType, existingData, selectedSubs, prefix) {
    const fieldPrefix = prefix !== undefined ? resultType + '_' : '';
    const uniqueId = prefix !== undefined ? `_${prefix}` : '';
    
    switch (jenisType.toLowerCase()) {
        case 'kimia darah':
            return generateKimiaDarahFormHybrid(existingData, selectedSubs, fieldPrefix, uniqueId);
        case 'hematologi':
            return generateHematologiFormHybrid(existingData, selectedSubs, fieldPrefix, uniqueId);
        case 'urinologi':
            return generateUrinologiFormHybrid(existingData, selectedSubs, fieldPrefix, uniqueId);
        case 'serologi':
        case 'serologi imunologi':
            return generateSerologiFormHybrid(existingData, selectedSubs, fieldPrefix, uniqueId);
        case 'tbc':
            return generateTbcFormHybrid(existingData, selectedSubs, fieldPrefix, uniqueId);
        case 'ims':
            return generateImsFormHybrid(existingData, selectedSubs, fieldPrefix, uniqueId);
        default:
            return generateMlsForm(existingData, fieldPrefix);
    }
}

function getExaminationIcon(jenisType) {
    const iconMap = {
        'Kimia Darah': 'droplet',
        'Hematologi': 'activity',
        'Urinologi': 'beaker',
        'Serologi': 'shield-check',
        'Serologi Imunologi': 'shield-check',
        'TBC': 'wind',
        'IMS': 'alert-triangle'
    };
    
    return iconMap[jenisType] || 'clipboard';
}
function submitResultsMultiple(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 loading"></i>Menyimpan...';
    
    // Clear previous alerts
    const existingAlert = document.querySelector('.form-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Show loading indicator
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'form-alert p-4 mb-4 bg-blue-100 text-blue-700 rounded-lg border border-blue-300';
    loadingDiv.innerHTML = '<div class="flex items-center"><i data-lucide="loader" class="w-5 h-5 mr-2 loading"></i><span>Menyimpan data...</span></div>';
    form.parentNode.insertBefore(loadingDiv, form);
    lucide.createIcons();
    
    // DEBUG: Log FormData before sending
    console.log('=== DEBUG: Form Data Being Sent ===');
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
    console.log('=== END DEBUG ===');
    
    fetch('<?= base_url('sample_data/save_examination_results_multiple') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // First check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        
        // Try to parse as JSON
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', text);
                throw new Error('Server returned invalid JSON. Check console for details.');
            }
        });
    })
    .then(data => {
        // Remove loading indicator
        if (loadingDiv.parentNode) {
            loadingDiv.parentNode.removeChild(loadingDiv);
        }
        
        if (data.success) {
            sessionStorage.setItem('toast_success', 'Hasil pemeriksaan berhasil di submit');
            
            if (data.errors && data.errors.length > 0) {
                console.warn('Some errors occurred:', data.errors);
            }
            
            closeInputModal();
            location.reload();
        } else {
            let errorMsg = data.message || 'Gagal menyimpan hasil';
            if (data.errors && data.errors.length > 0) {
                errorMsg += '\n\nDetail:\n' + data.errors.join('\n');
            }
            showToast('error', errorMsg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Remove loading indicator
        if (loadingDiv.parentNode) {
            loadingDiv.parentNode.removeChild(loadingDiv);
        }
        
        showToast('error', 'Terjadi kesalahan: ' + error.message);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
}

// detectAndRouteInput removed (duplicate)

// Override form submit handler untuk support both single and multiple
document.getElementById('inputResultsForm').onsubmit = function(event) {
    event.preventDefault();
    
    // Check if this is multiple examination
    const resultTypesInput = document.getElementById('resultTypes');
    if (resultTypesInput && resultTypesInput.value) {
        // Multiple examination
        submitResultsMultiple(event);
    } else {
        // Single examination
        submitResults(event);
    }
};

// Add CSS for examination type card
const style = document.createElement('style');
style.textContent = `
    .examination-type-card {
        transition: all 0.2s ease;
    }
    
    .examination-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
`;
document.head.appendChild(style);

console.log('Multi-examination support loaded');
</script>



</body>
</html>