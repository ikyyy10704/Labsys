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
            <form method="GET" action="<?= base_url('laboratorium/sample_data') ?>">
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
                        <a href="<?= base_url('laboratorium/sample_data') ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
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
                                bg-gradient-to-br from-gray-500 to-gray-600
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
                                <h3 class="text-lg font-semibold text-gray-900"><?= $sample['jenis_pemeriksaan'] ?? $sample['jenis_pemeriksaan_display'] ?? '-' ?></h3>
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    <?php 
                                    $status = $sample['status_pemeriksaan'] ?? 'pending';
                                    if ($status == 'progress'): ?>
                                        bg-orange-100 text-orange-800
                                    <?php elseif ($status == 'selesai'): ?>
                                        bg-green-100 text-green-800
                                    <?php else: ?>
                                        bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?= strtoupper($status) ?>
                                </span>
                            </div>
                           <!-- Single Examination Sub Pemeriksaan Display -->
<?php if (!empty($sample['sub_pemeriksaan']) && (empty($sample['examination_details']) || count($sample['examination_details']) <= 1)): ?>
<div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
    <p class="text-xs font-medium text-blue-900 mb-1 flex items-center">
        <i data-lucide="list-checks" class="w-3 h-3 inline mr-1"></i>
        Sub Pemeriksaan yang Diminta:
    </p>
    <p class="text-xs text-blue-700">
        <?= $this->Laboratorium_model->get_sub_pemeriksaan_labels(
            $sample['sub_pemeriksaan'], 
            $sample['jenis_pemeriksaan']
        ) ?>
    </p>
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
                                    bg-gradient-to-br from-gray-400 to-gray-500
                                <?php endif; ?>
                            ">
                                <?php if ($status == 'progress'): ?>
                                    <i data-lucide="loader" class="w-5 h-5 text-white animate-spin"></i>
                                <?php elseif ($status == 'selesai'): ?>
                                    <i data-lucide="check-circle" class="w-5 h-5 text-white"></i>
                                <?php else: ?>
                                    <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate"><?= $sample['nomor_pemeriksaan'] ?></h3>
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded-full"><?= $sample['jenis_pemeriksaan'] ?></span>
                                    <span class="px-2 py-0.5 <?php
                                        if ($status == 'progress'): ?>bg-orange-100 text-orange-800<?php
                                        elseif ($status == 'selesai'): ?>bg-green-100 text-green-800<?php
                                        else: ?>bg-gray-100 text-gray-800<?php
                                        endif; ?>
                                    "><?= strtoupper($status) ?></span>
                                </div>

                                <!-- Single Examination Sub Pemeriksaan Display -->
                                <?php if (!empty($sample['sub_pemeriksaan']) && (empty($sample['examination_details']) || count($sample['examination_details']) <= 1)): ?>
                                    <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-xs font-medium text-blue-900 mb-1 flex items-center">
                                            <i data-lucide="list-checks" class="w-3 h-3 inline mr-1"></i> Sub Pemeriksaan yang Diminta:
                                        </p>
                                        <p class="text-xs text-blue-700"><?= $sample['sub_pemeriksaan'] ?></p>
                                    </div>
                                <?php endif; ?>

                                <!-- Multiple Examination Details Display -->
                                <?php if (!empty($sample['examination_details']) && count($sample['examination_details']) > 1): ?>
                                    <div class="mb-4">
                                        <?php foreach ($sample['examination_details'] as $detail): ?>
                                            <div class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg mb-2">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <p class="text-xs font-medium text-blue-900 flex items-center">
                                                            <i data-lucide="clipboard-list" class="w-3 h-3 inline mr-1"></i> <?= $detail['jenis_pemeriksaan'] ?>
                                                        </p>
                                                        <?php if (!empty($detail['sub_pemeriksaan_display'])): ?>
                                                            <p class="text-xs text-blue-700 mt-1 ml-4">
                                                                <i data-lucide="corner-down-right" class="w-3 h-3 inline mr-1"></i> <?= $detail['sub_pemeriksaan_display'] ?>
                                                            </p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="ml-2 px-2 py-0.5 bg-blue-600 text-white text-xs rounded-full"><?= $detail['urutan'] ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-x-8 gap-y-2 text-xs text-gray-600 mt-3">
                                    <div class="flex items-center gap-1">
                                        <i data-lucide="user" class="w-3 h-3 text-gray-500"></i>
                                        <span class="font-medium text-gray-700">Pasien:</span>
                                        <span class="truncate"><?= $sample['nama_pasien'] ?? '-' ?></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <i data-lucide="id-card" class="w-3 h-3 text-gray-500"></i>
                                        <span class="font-medium text-gray-700">NIK:</span>
                                        <span class="truncate"><?= $sample['nik'] ?? '-' ?></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3 h-3 text-gray-500"></i>
                                        <span class="font-medium text-gray-700">Tanggal:</span>
                                        <span><?= date('d M Y', strtotime($sample['tanggal_pengambilan_sampel'])) ?></span>
                                    </div>
                                    <?php if (!empty($sample['nama_petugas'])): ?>
                                        <div class="flex items-center gap-1">
                                            <i data-lucide="user-cog" class="w-3 h-3 text-gray-500"></i>
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

                                <?php if (!empty($sample['keterangan'])): ?>
                                    <div class="mt-3 text-xs text-gray-600">
                                        <i data-lucide="align-left" class="w-3 h-3 text-gray-500 inline mr-1"></i>
                                        <span class="font-medium text-gray-700">Keterangan:</span>
                                        <span><?= $sample['keterangan'] ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 ml-4">
                            <button type="button" onclick="viewTimeline(<?= $sample['pemeriksaan_id'] ?>)"
                                class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                <i data-lucide="list-timeline" class="w-3 h-3 mr-1"></i>
                                <span>Timeline</span>
                            </button>
                            <?php
                            $status = $sample['status_pemeriksaan'] ?? 'pending';
                            if ($status == 'progress'):
                            ?>
                                <button type="button" onclick="updateStatus(<?= $sample['pemeriksaan_id'] ?>)"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 transition-colors duration-200">
                                    <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                                    <span>Update</span>
                                </button>
                                <button type="button" onclick="detectAndRouteInput(<?= $sample['pemeriksaan_id'] ?>, '<?= $sample['jenis_pemeriksaan'] ?? '' ?>')"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                                    <i data-lucide="file-input" class="w-3 h-3 mr-1"></i>
                                    <span>Input Hasil</span>
                                </button>
                            <?php elseif ($status == 'selesai'): ?>
                                <button type="button" onclick="viewResults(<?= $sample['pemeriksaan_id'] ?>)"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 transition-colors duration-200">
                                    <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                                    <span>Lihat Hasil</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div> <!-- End of space-y-4 div -->
<?php endif; ?> <!-- End of if/else block for $samples -->
    <!-- Pagination -->
    <<?php if ($total_pages > 1): ?>
    <div class="mt-8 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Menampilkan halaman <?= $current_page ?> dari <?= $total_pages ?> (<?= $total_samples ?> total)
        </div>
        <div class="flex gap-1">
            <?php if ($has_prev): ?>
                <a href="<?= base_url('laboratorium/sample_data') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page - 1])) ?>"
                   class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-50 text-xs">
                    <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                </a>
            <?php endif; ?>

            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                <a href="<?= base_url('laboratorium/sample_data') ?>?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"
                   class="px-2.5 py-1 border text-xs <?= $i == $current_page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?> rounded">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($has_next): ?>
                <a href="<?= base_url('laboratorium/sample_data') ?>?<?= http_build_query(array_merge($filters, ['page' => $current_page + 1])) ?>"
                   class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-50 text-xs">
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?> <!-- End of if pagination block -->

</div> <!-- End of main content div -->

<!-- Timeline Modal -->
<div id="timelineModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden fade-in">
            <!-- Modal content akan diisi oleh JavaScript -->
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Timeline Pemeriksaan</h3>
                    <button type="button" onclick="closeTimelineModal()"
                            class="text-gray-400 hover:text-gray-500">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div id="timelineContent">
                    <!-- Timeline content loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Input Results Modal -->
<div id="inputResultsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Input Hasil Pemeriksaan</h3>
                    <p id="modalSubtitle" class="text-sm text-gray-500 mt-1">Loading...</p>
                </div>
                <button type="button" onclick="closeInputModal()"
                        class="text-gray-400 hover:text-gray-500">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form id="inputResultsForm" action="<?= base_url('laboratorium/save_examination_results') ?>" method="post">
                <input type="hidden" name="examination_id" id="modalExamId" value="">
                <input type="hidden" name="result_types" id="resultTypes" value="">
                <div id="modalLoading" class="flex justify-center items-center py-8">
                    <i data-lucide="loader" class="w-8 h-8 text-blue-500 animate-spin"></i>
                </div>
                <div id="modalFormContainer" class="hidden">
                    <!-- Form fields loaded here -->
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeInputModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" id="submitResultsBtn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Hasil
                    </button>
                </div>
            </form>
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

// View timeline
function viewTimeline(examId) {
    document.getElementById('timelineModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    fetch('<?= base_url('laboratorium/get_sample_timeline_data') ?>/' + examId)
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
            const subPemeriksaan = data.examination.sub_pemeriksaan || null;
            populateModal(data.examination, data.existing_results, examinationType, subPemeriksaan);
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
            'paket_darah_rutin': 'Paket Darah Rutin',
            'hitung_jenis_leukosit': 'Hitung Jenis Leukosit',
            'laju_endap_darah': 'Laju Endap Darah',
            'golongan_darah': 'Golongan Darah & Rhesus',
            'hemostasis': 'Hemostasis (CT/BT)',
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

/**
 * Toggle optional fields section
 */
function toggleOptionalFields() {
    const container = document.getElementById('optionalFieldsContainer');
    const icon = document.getElementById('optionalToggleIcon');
    const button = document.querySelector('[onclick="toggleOptionalFields()"]');
    
    if (container.classList.contains('hidden')) {
        // Expand
        container.classList.remove('hidden');
        container.style.maxHeight = container.scrollHeight + 'px';
        icon.style.transform = 'rotate(180deg)';
        button.classList.add('bg-gray-50');
    } else {
        // Collapse
        container.style.maxHeight = '0px';
        setTimeout(() => {
            container.classList.add('hidden');
        }, 300);
        icon.style.transform = 'rotate(0deg)';
        button.classList.remove('bg-gray-50');
    }
    
    // Re-init lucide icons
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
function generateKimiaDarahFormHybrid(existingResults, selectedSubs = []) {
    const values = existingResults || {};
    const hasFilter = selectedSubs && selectedSubs.length > 0;
    
    // Define all fields
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
    
    // Separate into requested and optional
    const requestedFields = [];
    const optionalFields = [];
    
    allFields.forEach(field => {
        if (hasFilter && selectedSubs.includes(field.key)) {
            requestedFields.push(field);
        } else if (hasFilter) {
            optionalFields.push(field);
        } else {
            requestedFields.push(field); // No filter = all requested
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
                    ${requestedFields.map(field => renderKimiaDarahField(field, values, true)).join('')}
                </div>
            </div>
        `;
    }
    
    // OPTIONAL SECTION
    if (optionalFields.length > 0) {
        html += `
            <div class="mb-6 bg-white border border-gray-300 rounded-xl overflow-hidden shadow-sm">
                <!-- Accordion Header -->
                <button type="button" 
                        onclick="toggleOptionalFields()" 
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
                       id="optionalToggleIcon" 
                       class="w-5 h-5 text-gray-500 transition-transform duration-300"></i>
                </button>
                
                <!-- Collapsible Content -->
                <div id="optionalFieldsContainer" 
                     class="hidden border-t border-gray-200 transition-all duration-300"
                     style="overflow: hidden; max-height: 0;">
                    <div class="p-6 bg-gray-50">
                        <!-- Info Banner -->
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
                        
                        <!-- Fields Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${optionalFields.map(field => renderKimiaDarahField(field, values, false)).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    return html;
}

function renderKimiaDarahField(field, values, isRequested) {
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
                   name="${field.key}" 
                   value="${value}" 
                   class="${fieldClass}" 
                   placeholder="${field.normal}" 
                   step="0.01">
        </div>
    `;
}

function generateHematologiFormHybrid(existingResults, selectedSubs = []) {
    const values = existingResults || {};
    const hasFilter = selectedSubs && selectedSubs.length > 0;
    
    // Define package mappings
    const packageMap = {
        'paket_darah_rutin': ['hemoglobin', 'hematokrit', 'eritrosit', 'leukosit', 'trombosit'],
        'hitung_jenis_leukosit': ['neutrofil', 'limfosit', 'monosit', 'eosinofil', 'basofil'],
        'golongan_darah': ['golongan_darah', 'rhesus'],
        'hemostasis': ['clotting_time', 'bleeding_time']
    };
    
    // Define sections
    const sections = [
        {
            id: 'paket_darah_rutin',
            title: 'Paket Darah Rutin',
            fields: [
                { key: 'hemoglobin', label: 'Hemoglobin', unit: 'g/dL', normal: 'L:13-17, P:12-15' },
                { key: 'hematokrit', label: 'Hematokrit', unit: '%', normal: 'L:40-50, P:35-45' },
                { key: 'eritrosit', label: 'Eritrosit', unit: 'juta/µL', normal: 'L:4.5-5.5, P:4.0-5.0' },
                { key: 'leukosit', label: 'Leukosit', unit: 'ribu/µL', normal: '4.0-11.0' },
                { key: 'trombosit', label: 'Trombosit', unit: 'ribu/µL', normal: '150-400' }
            ]
        },
        {
            id: 'indeks_eritrosit',
            title: 'Indeks Eritrosit',
            fields: [
                { key: 'mcv', label: 'MCV', unit: 'fL', normal: '80-100' },
                { key: 'mch', label: 'MCH', unit: 'pg', normal: '27-31' },
                { key: 'mchc', label: 'MCHC', unit: 'g/dL', normal: '32-36' }
            ]
        },
        {
            id: 'hitung_jenis_leukosit',
            title: 'Hitung Jenis Leukosit',
            fields: [
                { key: 'neutrofil', label: 'Neutrofil', unit: '%', normal: '50-70' },
                { key: 'limfosit', label: 'Limfosit', unit: '%', normal: '20-40' },
                { key: 'monosit', label: 'Monosit', unit: '%', normal: '2-8' },
                { key: 'eosinofil', label: 'Eosinofil', unit: '%', normal: '1-3' },
                { key: 'basofil', label: 'Basofil', unit: '%', normal: '0-1' }
            ]
        },
        {
            id: 'other',
            title: 'Pemeriksaan Lainnya',
            fields: [
                { key: 'laju_endap_darah', label: 'Laju Endap Darah', unit: 'mm/jam', normal: 'L:<15, P:<20', isIndividual: true },
                { key: 'golongan_darah', label: 'Golongan Darah', type: 'select', options: ['', 'A', 'B', 'AB', 'O'], isIndividual: true },
                { key: 'rhesus', label: 'Rhesus', type: 'select', options: ['', '+', '-'], isIndividual: true },
                { key: 'clotting_time', label: 'Clotting Time', unit: 'detik', normal: '5-15 menit', isIndividual: true },
                { key: 'bleeding_time', label: 'Bleeding Time', unit: 'detik', normal: '1-6 menit', isIndividual: true },
                { key: 'malaria', label: 'Malaria', type: 'textarea', isIndividual: true }
            ]
        }
    ];
    
    let html = '<div class="space-y-6">';
    
    sections.forEach(section => {
        const isRequested = hasFilter && (
            selectedSubs.includes(section.id) || 
            section.fields.some(field => 
                field.isIndividual && selectedSubs.includes(field.key)
            )
        );
        
        const showAsRequested = !hasFilter || isRequested;
        
        if (showAsRequested) {
            // REQUESTED SECTION
            html += renderHematologiSection(section, values, true, packageMap, selectedSubs);
        } else {
            // Will be in optional
        }
    });
    
    // OPTIONAL SECTIONS
    if (hasFilter) {
        const optionalSections = sections.filter(section => {
            const isRequested = selectedSubs.includes(section.id) || 
                section.fields.some(field => field.isIndividual && selectedSubs.includes(field.key));
            return !isRequested;
        });
        
        if (optionalSections.length > 0) {
            html += `
                <div class="bg-white border border-gray-300 rounded-xl overflow-hidden shadow-sm">
                    <button type="button" 
                            onclick="toggleOptionalFields()" 
                            class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-all duration-200 group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-100 group-hover:bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="text-base font-semibold text-gray-900">Parameter Tambahan (Opsional)</h4>
                                <p class="text-xs text-gray-600">${optionalSections.length} kategori tersedia</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-down" id="optionalToggleIcon" class="w-5 h-5 text-gray-500 transition-transform duration-300"></i>
                    </button>
                    
                    <div id="optionalFieldsContainer" class="hidden border-t border-gray-200" style="overflow: hidden; max-height: 0;">
                        <div class="p-6 bg-gray-50 space-y-6">
                            <div class="p-3 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
                                <p class="text-xs text-amber-800">
                                    <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                    <strong>Info:</strong> Parameter ini dapat diisi jika sampel mencukupi. Kosongkan jika tidak diperiksa.
                                </p>
                            </div>
                            ${optionalSections.map(section => renderHematologiSection(section, values, false, packageMap, selectedSubs)).join('')}
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    html += '</div>';
    return html;
}

function renderHematologiSection(section, values, isRequested, packageMap, selectedSubs) {
    const sectionClass = isRequested 
        ? 'bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl p-6 shadow-md'
        : 'bg-white border border-gray-200 rounded-xl p-6';
    
    const titleClass = isRequested ? 'text-blue-900 font-bold' : 'text-gray-900 font-semibold';
    const badge = isRequested 
        ? '<span class="ml-3 px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full shadow-sm">DIMINTA</span>'
        : '';
    
    let html = `
        <div class="${sectionClass}">
            <h4 class="text-lg ${titleClass} mb-4 flex items-center">
                ${section.title}
                ${badge}
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-${section.fields.length > 3 ? '3' : '2'} gap-4">
    `;
    
    section.fields.forEach(field => {
        const fieldRequested = isRequested || (field.isIndividual && selectedSubs.includes(field.key));
        html += renderHematologiField(field, values, fieldRequested);
    });
    
    html += `
            </div>
        </div>
    `;
    
    return html;
}

function renderHematologiField(field, values, isRequested) {
    const value = values[field.key] || '';
    const fieldClass = isRequested 
        ? 'w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600'
        : 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500';
    
    const labelClass = isRequested ? 'text-blue-900 font-semibold' : 'text-gray-700 font-medium';
    const borderClass = isRequested ? 'border-l-4 border-blue-600 pl-3' : '';
    
    let inputHtml = '';
    
    if (field.type === 'select') {
        inputHtml = `
            <select name="${field.key}" class="${fieldClass}">
                ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>
        `;
    } else if (field.type === 'textarea') {
        inputHtml = `<textarea name="${field.key}" rows="2" class="${fieldClass}" placeholder="Hasil pemeriksaan...">${value}</textarea>`;
    } else {
        inputHtml = `<input type="number" name="${field.key}" value="${value}" class="${fieldClass}" placeholder="${field.normal || ''}" step="0.1">`;
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
function generateUrinologiFormHybrid(existingResults, selectedSubs = []) {
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
                            <textarea name="makroskopis" rows="2" 
                                      class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600" 
                                      placeholder="Warna, kejernihan, bau">${values.makroskopis || ''}</textarea>
                        </div>
                        <div class="border-l-4 border-blue-600 pl-3">
                            <label class="block text-sm font-semibold text-blue-900 mb-2">Berat Jenis</label>
                            <input type="number" name="berat_jenis" value="${values.berat_jenis || ''}" 
                                   class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600" 
                                   placeholder="1.015" step="0.001">
                        </div>
                        <div class="border-l-4 border-blue-600 pl-3">
                            <label class="block text-sm font-semibold text-blue-900 mb-2">pH</label>
                            <input type="number" name="kimia_ph" value="${values.kimia_ph || ''}" 
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
                            <select name="protein_regular" class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600">
                                <option value="">Pilih</option>
                                ${['Negatif', '+1', '+2', '+3', '+4'].map(opt => 
                                    `<option value="${opt}" ${values.protein_regular === opt ? 'selected' : ''}>${opt}</option>`
                                ).join('')}
                            </select>
                        </div>
                        ${['glukosa', 'keton', 'bilirubin', 'urobilinogen'].map(field => `
                            <div class="border-l-4 border-blue-600 pl-3">
                                <label class="block text-sm font-semibold text-blue-900 mb-2">${field.charAt(0).toUpperCase() + field.slice(1)}</label>
                                <select name="${field}" class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600">
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
                        <textarea name="mikroskopis" rows="4" 
                                  class="w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600" 
                                  placeholder="Eritrosit, leukosit, epitel, silinder, kristal, bakteri...">${values.mikroskopis || ''}</textarea>
                    </div>
                </div>
            </div>
        `;
    }
    
    // PROTEIN KUANTITATIF SECTION (Pemeriksaan Terpisah)
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
                <div class="bg-amber-50 border-l-4 border-amber-400 p-3 rounded-r-lg mb-4">
                    <p class="text-xs text-amber-800">
                        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                        <strong>Catatan:</strong> Pemeriksaan protein kuantitatif terpisah dari paket urin rutin. 
                        Masukkan nilai dalam mg/dL atau g/24jam sesuai metode pemeriksaan.
                    </p>
                </div>
                <div class="border-l-4 border-blue-600 pl-3">
                    <label class="block text-sm font-semibold ${urinRutinRequested ? 'text-blue-900' : 'text-gray-700'} mb-2">
                        Hasil Protein Kuantitatif
                        <span class="block text-xs text-gray-500 font-normal mt-1">Normal: < 150 mg/24jam atau < 10 mg/dL</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" 
                               name="protein" 
                               value="${values.protein || ''}" 
                               class="flex-1 px-3 py-2 border-2 ${urinRutinRequested ? 'border-blue-400 bg-blue-50' : 'border-gray-300'} rounded-lg focus:ring-2 focus:ring-blue-600" 
                               placeholder="Contoh: 25.5 mg/dL atau 180 mg/24jam">
                        <select class="px-3 py-2 border-2 ${urinRutinRequested ? 'border-blue-400 bg-blue-50' : 'border-gray-300'} rounded-lg">
                            <option>mg/dL</option>
                            <option>mg/24jam</option>
                            <option>g/24jam</option>
                        </select>
                    </div>
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
                    <select name="tes_kehamilan" class="w-full px-3 py-2 border-2 ${isInRequested ? 'border-blue-400 bg-blue-50' : 'border-gray-300'} rounded-lg focus:ring-2 focus:ring-blue-600">
                        <option value="">Pilih Hasil</option>
                        <option value="Positif" ${values.tes_kehamilan === 'Positif' ? 'selected' : ''}>Positif</option>
                        <option value="Negatif" ${values.tes_kehamilan === 'Negatif' ? 'selected' : ''}>Negatif</option>
                    </select>
                </div>
            </div>
        `;
    }
    
    // OPTIONAL SECTION (untuk yang tidak diminta)
    if (hasFilter) {
        const optionalItems = [];
        if (!urinRutinRequested) optionalItems.push('urin_rutin');
        if (!proteinRequested) optionalItems.push('protein');
        if (!tesKehamilanRequested) optionalItems.push('tes_kehamilan');
        
        if (optionalItems.length > 0) {
            html += `
                <div class="bg-white border border-gray-300 rounded-xl overflow-hidden shadow-sm">
                    <button type="button" onclick="toggleOptionalFields()" 
                            class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-all duration-200 group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-100 group-hover:bg-gray-200 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="text-base font-semibold text-gray-900">Parameter Tambahan (Opsional)</h4>
                                <p class="text-xs text-gray-600">${optionalItems.length} parameter tersedia - Klik untuk membuka</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-down" id="optionalToggleIcon" class="w-5 h-5 text-gray-500 transition-transform duration-300"></i>
                    </button>
                    
                    <div id="optionalFieldsContainer" class="hidden border-t border-gray-200 transition-all duration-300" style="overflow: hidden; max-height: 0;">
                        <div class="p-6 bg-gray-50 space-y-6">
                            <div class="mb-4 p-3 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
                                <p class="text-xs text-amber-800">
                                    <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                    Parameter opsional - Isi jika diperlukan pemeriksaan tambahan
                                </p>
                            </div>
                            
                            ${!urinRutinRequested ? `
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Urin Rutin (Opsional)</h4>
                                    <div class="space-y-4">
                                        <textarea name="makroskopis" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Makroskopis">${values.makroskopis || ''}</textarea>
                                        <textarea name="mikroskopis" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Mikroskopis">${values.mikroskopis || ''}</textarea>
                                        <select name="protein_regular" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="">Protein (Kualitatif)</option>
                                            ${['Negatif', '+1', '+2', '+3', '+4'].map(opt => 
                                                `<option value="${opt}" ${values.protein_regular === opt ? 'selected' : ''}>${opt}</option>`
                                            ).join('')}
                                        </select>
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${!proteinRequested ? `
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Protein Kuantitatif (Opsional)</h4>
                                    <input type="text" name="protein" value="${values.protein || ''}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg" 
                                           placeholder="Hasil protein kuantitatif">
                                </div>
                            ` : ''}
                            
                            ${!tesKehamilanRequested ? `
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Tes Kehamilan (Opsional)</h4>
                                    <select name="tes_kehamilan" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
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

function generateSerologiFormHybrid(existingResults, selectedSubs = []) {
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
    
    // REQUESTED SECTION
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
                        <p class="text-xs text-blue-700">
                            ${hasFilter ? `${requestedFields.length} parameter sesuai permintaan` : 'Semua parameter tersedia'}
                        </p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${requestedFields.map(field => renderSerologiField(field, values, true)).join('')}
                </div>
            </div>
        `;
    }
    
    // OPTIONAL SECTION
    if (optionalFields.length > 0) {
        html += `
            <div class="mb-6 bg-white border border-gray-300 rounded-xl overflow-hidden shadow-sm">
                <button type="button" onclick="toggleOptionalFields()" 
                        class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-all">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="text-base font-semibold text-gray-900">Parameter Tambahan (Opsional)</h4>
                            <p class="text-xs text-gray-600">${optionalFields.length} parameter tersedia</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" id="optionalToggleIcon" class="w-5 h-5 text-gray-500 transition-transform"></i>
                </button>
                
                <div id="optionalFieldsContainer" class="hidden border-t border-gray-200" style="overflow: hidden;">
                    <div class="p-6 bg-gray-50">
                        <div class="mb-4 p-3 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
                            <p class="text-xs text-amber-800">
                                <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                Parameter opsional - Isi jika sampel mencukupi
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${optionalFields.map(field => renderSerologiField(field, values, false)).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    return html;
}

function renderSerologiField(field, values, isRequested) {
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
            <select name="${field.key}" class="${fieldClass}">
                ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>
        `;
    } else if (field.type === 'textarea') {
        inputHtml = `<textarea name="${field.key}" rows="3" class="${fieldClass}" placeholder="Hasil pemeriksaan...">${value}</textarea>`;
    }
    
    return `
        <div class="${borderClass} ${colSpan}">
            <label class="block text-sm ${labelClass} mb-2">${field.label}</label>
            ${inputHtml}
        </div>
    `;
}

function generateTbcFormHybrid(existingResults, selectedSubs = []) {
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
    
    // REQUESTED
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
                    ${requestedFields.map(field => renderTbcField(field, values, true)).join('')}
                </div>
            </div>
        `;
    }
    
    // OPTIONAL
    if (optionalFields.length > 0) {
        html += `
            <div class="bg-white border border-gray-300 rounded-xl overflow-hidden mt-6">
                <button type="button" onclick="toggleOptionalFields()" class="w-full p-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600 mr-3"></i>
                        <h4 class="text-base font-semibold text-gray-900">Parameter Tambahan</h4>
                    </div>
                    <i data-lucide="chevron-down" id="optionalToggleIcon" class="w-5 h-5 text-gray-500 transition-transform"></i>
                </button>
                <div id="optionalFieldsContainer" class="hidden border-t p-6 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${optionalFields.map(field => renderTbcField(field, values, false)).join('')}
                    </div>
                </div>
            </div>
        `;
    }
    
    return html;
}

function renderTbcField(field, values, isRequested) {
    const value = values[field.key] || '';
    const fieldClass = isRequested 
        ? 'w-full px-3 py-2 border-2 border-blue-400 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-600'
        : 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500';
    
    const labelClass = isRequested ? 'text-blue-900 font-semibold' : 'text-gray-700 font-medium';
    const borderClass = isRequested ? 'border-l-4 border-blue-600 pl-3' : '';
    
    return `
        <div class="${borderClass}">
            <label class="block text-sm ${labelClass} mb-2">${field.label}</label>
            <select name="${field.key}" class="${fieldClass}">
                ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>
        </div>
    `;
}

function generateImsFormHybrid(existingResults, selectedSubs = []) {
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
                    ${requestedFields.map(field => renderImsField(field, values, true)).join('')}
                </div>
            </div>
        `;
    }
    
    if (optionalFields.length > 0) {
        html += `
            <div class="bg-white border border-gray-300 rounded-xl overflow-hidden mt-6">
                <button type="button" onclick="toggleOptionalFields()" class="w-full p-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center">
                        <i data-lucide="plus-circle" class="w-5 h-5 mr-3"></i>
                        <h4 class="font-semibold">Parameter Tambahan</h4>
                    </div>
                    <i data-lucide="chevron-down" id="optionalToggleIcon" class="w-5 h-5 transition-transform"></i>
                </button>
                <div id="optionalFieldsContainer" class="hidden border-t p-6 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${optionalFields.map(field => renderImsField(field, values, false)).join('')}
                    </div>
                </div>
            </div>
        `;
    }
    
    return html;
}

function renderImsField(field, values, isRequested) {
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
            <select name="${field.key}" class="${fieldClass}">
                ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>
        `;
    } else {
        inputHtml = `<textarea name="${field.key}" rows="3" class="${fieldClass}" placeholder="Hasil...">${value}</textarea>`;
    }
    
    return `
        <div class="${borderClass} ${colSpan}">
            <label class="block text-sm ${labelClass} mb-2">${field.label}</label>
            ${inputHtml}
        </div>
    `;
}


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
    
    fetch('<?= base_url('laboratorium/save_examination_results') ?>', {
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
            showAlert('Hasil berhasil disimpan!', 'success');
            setTimeout(() => {
                closeInputModal();
                location.reload();
            }, 1500);
        } else {
            showAlert('Error: ' + (data.message || 'Gagal menyimpan hasil'), 'error');
            console.error('Save error:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Terjadi kesalahan jaringan atau server. Silakan coba lagi.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
}

// Helper function to show alerts
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `form-alert p-4 mb-4 rounded-lg ${
        type === 'success' ? 'bg-green-100 text-green-700 border border-green-300' :
        type === 'error' ? 'bg-red-100 text-red-700 border border-red-300' :
        'bg-blue-100 text-blue-700 border border-blue-300'
    }`;
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'}" 
               class="w-5 h-5 mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    const form = document.getElementById('inputResultsForm');
    form.parentNode.insertBefore(alertDiv, form);
    lucide.createIcons();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
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
    const examId = document.getElementById('updateExamId').value;
    const formData = new FormData(event.target);
    
    fetch(`<?= base_url('laboratorium/update_sample_status') ?>/${examId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan saat memperbarui status');
    });
}

function viewResults(examId) {
    window.location.href = '<?= base_url('laboratorium/view_results') ?>/' + examId;
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
    fetch(`<?= base_url('laboratorium/get_examination_data_multiple') ?>/${examId}`, {
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
    html += `<input type="hidden" name="result_types" id="resultTypes" value='${JSON.stringify(resultTypes)}'>`;
    
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

/**
 * Generate form fields dengan prefix untuk multiple types
 */
function generateFormFieldsByType(jenisType, resultType, existingData, selectedSubs, prefix) {
    // Prefix untuk field names agar tidak conflict
    const fieldPrefix = resultType + '_';
    
    switch (jenisType.toLowerCase()) {
        case 'kimia darah':
            return generateKimiaDarahFormWithPrefix(existingData, selectedSubs, fieldPrefix);
        case 'hematologi':
            return generateHematologiFormWithPrefix(existingData, selectedSubs, fieldPrefix);
        case 'urinologi':
            return generateUrinologiFormWithPrefix(existingData, selectedSubs, fieldPrefix);
        case 'serologi':
        case 'serologi imunologi':
            return generateSerologiFormWithPrefix(existingData, selectedSubs, fieldPrefix);
        case 'tbc':
            return generateTbcFormWithPrefix(existingData, selectedSubs, fieldPrefix);
        case 'ims':
            return generateImsFormWithPrefix(existingData, selectedSubs, fieldPrefix);
        default:
            return `<p class="text-gray-500">Form untuk ${jenisType} belum tersedia</p>`;
    }
}

/**
 * Get icon untuk examination type
 */
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

/**
 * Generate Kimia Darah form dengan prefix
 */
function generateKimiaDarahFormWithPrefix(existingResults, selectedSubs, prefix) {
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
    
    const requestedFields = hasFilter ? allFields.filter(f => selectedSubs.includes(f.key)) : allFields;
    
    let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    requestedFields.forEach(field => {
        const value = values[field.key] || '';
        html += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ${field.label} (${field.unit})
                    <span class="block text-xs text-gray-500 mt-1">Normal: ${field.normal}</span>
                </label>
                <input type="number" 
                       name="${prefix}${field.key}" 
                       value="${value}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                       placeholder="${field.normal}" 
                       step="0.01">
            </div>
        `;
    });
    html += '</div>';
    
    return html;
}

/**
 * Generate Hematologi form dengan prefix
 */
function generateHematologiFormWithPrefix(existingResults, selectedSubs, prefix) {
    const values = existingResults || {};
    
    let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    
    const fields = [
        { key: 'hemoglobin', label: 'Hemoglobin', unit: 'g/dL', normal: 'L:13-17, P:12-15' },
        { key: 'hematokrit', label: 'Hematokrit', unit: '%', normal: 'L:40-50, P:35-45' },
        { key: 'leukosit', label: 'Leukosit', unit: 'ribu/µL', normal: '4.0-11.0' },
        { key: 'trombosit', label: 'Trombosit', unit: 'ribu/µL', normal: '150-400' },
        { key: 'eritrosit', label: 'Eritrosit', unit: 'juta/µL', normal: 'L:4.5-5.5, P:4.0-5.0' }
    ];
    
    fields.forEach(field => {
        const value = values[field.key] || '';
        html += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ${field.label} (${field.unit})
                    <span class="block text-xs text-gray-500 mt-1">Normal: ${field.normal}</span>
                </label>
                <input type="number" 
                       name="${prefix}${field.key}" 
                       value="${value}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg" 
                       step="0.1">
            </div>
        `;
    });
    
    html += '</div>';
    return html;
}

/**
 * Generate Urinologi form dengan prefix
 */
function generateUrinologiFormWithPrefix(existingResults, selectedSubs, prefix) {
    const values = existingResults || {};
    
    return `
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Makroskopis</label>
                <textarea name="${prefix}makroskopis" rows="2" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg">${values.makroskopis || ''}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mikroskopis</label>
                <textarea name="${prefix}mikroskopis" rows="2" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg">${values.mikroskopis || ''}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Protein</label>
                    <select name="${prefix}protein_regular" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih</option>
                        ${['Negatif', '+1', '+2', '+3', '+4'].map(opt => 
                            `<option value="${opt}" ${values.protein_regular === opt ? 'selected' : ''}>${opt}</option>`
                        ).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tes Kehamilan</label>
                    <select name="${prefix}tes_kehamilan" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih</option>
                        <option value="Positif" ${values.tes_kehamilan === 'Positif' ? 'selected' : ''}>Positif</option>
                        <option value="Negatif" ${values.tes_kehamilan === 'Negatif' ? 'selected' : ''}>Negatif</option>
                    </select>
                </div>
            </div>
        </div>
    `;
}

/**
 * Generate Serologi form dengan prefix
 */
function generateSerologiFormWithPrefix(existingResults, selectedSubs, prefix) {
    const values = existingResults || {};
    
    const fields = [
        { key: 'rdt_antigen', label: 'RDT Antigen', options: ['', 'Positif', 'Negatif'] },
        { key: 'hbsag', label: 'HBsAg', options: ['', 'Reaktif', 'Non-Reaktif'] },
        { key: 'ns1', label: 'NS1 (Dengue)', options: ['', 'Positif', 'Negatif'] },
        { key: 'hiv', label: 'HIV', options: ['', 'Reaktif', 'Non-Reaktif'] }
    ];
    
    let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    fields.forEach(field => {
        const value = values[field.key] || '';
        html += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">${field.label}</label>
                <select name="${prefix}${field.key}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
                </select>
            </div>
        `;
    });
    html += '</div>';
    
    return html;
}

/**
 * Generate TBC form dengan prefix
 */
function generateTbcFormWithPrefix(existingResults, selectedSubs, prefix) {
    const values = existingResults || {};
    
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dahak (BTA)</label>
                <select name="${prefix}dahak" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Pilih</option>
                    ${['Negatif', 'Scanty', '+1', '+2', '+3'].map(opt => 
                        `<option value="${opt}" ${values.dahak === opt ? 'selected' : ''}>${opt}</option>`
                    ).join('')}
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">TCM (GeneXpert)</label>
                <select name="${prefix}tcm" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Pilih</option>
                    <option value="Detected" ${values.tcm === 'Detected' ? 'selected' : ''}>Detected</option>
                    <option value="Not Detected" ${values.tcm === 'Not Detected' ? 'selected' : ''}>Not Detected</option>
                </select>
            </div>
        </div>
    `;
}

/**
 * Generate IMS form dengan prefix
 */
function generateImsFormWithPrefix(existingResults, selectedSubs, prefix) {
    const values = existingResults || {};
    
    return `
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sifilis</label>
                <select name="${prefix}sifilis" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Pilih</option>
                    <option value="Reaktif" ${values.sifilis === 'Reaktif' ? 'selected' : ''}>Reaktif</option>
                    <option value="Non-Reaktif" ${values.sifilis === 'Non-Reaktif' ? 'selected' : ''}>Non-Reaktif</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Duh Tubuh</label>
                <textarea name="${prefix}duh_tubuh" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg">${values.duh_tubuh || ''}</textarea>
            </div>
        </div>
    `;
}

/**
 * Submit results untuk multiple types
 */
function submitResultsMultiple(event) {
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
    
    fetch('<?= base_url('laboratorium/save_examination_results_multiple') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert(`Berhasil! ${data.saved_count || 'Semua'} jenis pemeriksaan disimpan`, 'success');
            setTimeout(() => {
                closeInputModal();
                location.reload();
            }, 1500);
        } else {
            showAlert('Error: ' + (data.message || 'Gagal menyimpan hasil'), 'error');
            if (data.errors) {
                console.error('Errors:', data.errors);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Terjadi kesalahan jaringan atau server. Silakan coba lagi.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
}

/**
 * Detect if examination has multiple types dan route accordingly
 */
function detectAndRouteInput(examId, jenisType) {
    // Check if jenis_type contains comma (multiple types)
    if (jenisType && jenisType.includes(',')) {
        inputResultsMultiple(examId);
    } else {
        inputResults(examId, jenisType);
    }
}

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