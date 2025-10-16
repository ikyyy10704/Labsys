<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Labsys</title>
    
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
                            <?php if ($sample['status_pemeriksaan'] == 'progress'): ?>
                                bg-gradient-to-br from-orange-500 to-orange-600
                            <?php elseif ($sample['status_pemeriksaan'] == 'selesai'): ?>
                                bg-gradient-to-br from-green-500 to-green-600
                            <?php else: ?>
                                bg-gradient-to-br from-gray-500 to-gray-600
                            <?php endif; ?>">
                            <?php if ($sample['status_pemeriksaan'] == 'progress'): ?>
                            <i data-lucide="loader" class="w-6 h-6 text-white"></i>
                            <?php elseif ($sample['status_pemeriksaan'] == 'selesai'): ?>
                            <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                            <?php else: ?>
                            <i data-lucide="x-circle" class="w-6 h-6 text-white"></i>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Sample Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900"><?= $sample['jenis_pemeriksaan'] ?></h3>
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    <?php if ($sample['status_pemeriksaan'] == 'progress'): ?>
                                        bg-orange-100 text-orange-800
                                    <?php elseif ($sample['status_pemeriksaan'] == 'selesai'): ?>
                                        bg-green-100 text-green-800
                                    <?php else: ?>
                                        bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?= strtoupper($sample['status_pemeriksaan']) ?>
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-x-8 gap-y-2 text-xs text-gray-600 mt-3">
                                <div class="flex items-center gap-1">
                                    <i data-lucide="user" class="w-3 h-3 text-gray-500"></i>
                                    <span class="font-medium text-gray-700">Pasien:</span> 
                                    <span><?= $sample['nama_pasien'] ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="credit-card" class="w-3 h-3 text-gray-500"></i>
                                    <span class="font-medium text-gray-700">NIK:</span> 
                                    <span><?= $sample['nik'] ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="hash" class="w-3 h-3 text-gray-500"></i>
                                    <span class="font-medium text-gray-700">No. Pemeriksaan:</span> 
                                    <span class="font-mono"><?= $sample['nomor_pemeriksaan'] ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3 text-gray-500"></i>
                                    <span class="font-medium text-gray-700">Tanggal:</span> 
                                    <span><?= date('d/m/Y', strtotime($sample['tanggal_pemeriksaan'])) ?></span>
                                </div>
                                <?php if ($sample['nama_petugas']): ?>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="user-round" class="w-3 h-3 text-gray-500"></i>
                                    <span class="font-medium text-gray-700">Petugas:</span> 
                                    <span class="truncate"><?= $sample['nama_petugas'] ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3 text-orange-500"></i>
                                    <span class="font-medium text-orange-600">Proses:</span> 
                                    <span class="font-semibold"><?= $sample['processing_hours'] ?> jam</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="activity" class="w-3 h-3 text-gray-500"></i>
                                    <span class="font-medium text-gray-700">Update:</span> 
                                    <span><?= $sample['timeline_count'] ?> kejadian</span>
                                </div>
                            </div>
                            
                            <?php if ($sample['keterangan']): ?>
                            <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                <p class="text-xs text-gray-700"><strong>Keterangan:</strong> <?= $sample['keterangan'] ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($sample['latest_status']['keterangan'])): ?>
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-xs text-blue-700"><strong>Update Terakhir:</strong> <?= $sample['latest_status']['keterangan'] ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col space-y-2 ml-4">
                        <button type="button" onclick="viewTimeline(<?= $sample['pemeriksaan_id'] ?>)" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                            <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                            <span>Timeline</span>
                        </button>
                        
                        <?php if ($sample['status_pemeriksaan'] == 'progress'): ?>
                        <button type="button" onclick="updateStatus(<?= $sample['pemeriksaan_id'] ?>)" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 transition-colors duration-200">
                            <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                            <span>Update</span>
                        </button>
                        
                        <button type="button" onclick="inputResults(<?= $sample['pemeriksaan_id'] ?>, '<?= $sample['jenis_pemeriksaan'] ?>')" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                            <i data-lucide="plus-circle" class="w-3 h-3 mr-1"></i>
                            <span>Input Hasil</span>
                        </button>
                        <?php elseif ($sample['status_pemeriksaan'] == 'selesai'): ?>
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
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
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
    <?php endif; ?>
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
            populateModal(data.examination, data.existing_results, examinationType);
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

function populateModal(examination, existingResults = null, examinationType = null) {
    const subtitle = `${examination.nomor_pemeriksaan} - ${examination.nama_pasien} (${examination.jenis_pemeriksaan})`;
    document.getElementById('modalSubtitle').textContent = subtitle;
    document.getElementById('modalExamId').value = examination.pemeriksaan_id;
    
    const examType = examinationType || examination.jenis_pemeriksaan;
    const resultType = getResultTypeFromExamination(examType);
    document.getElementById('modalResultType').value = resultType;
    currentExaminationType = examType;
    
    generateFormFields(examType, existingResults);
    
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

function generateFormFields(jenisType, existingResults) {
    const container = document.getElementById('dynamicFormContent');
    let html = '';
    
    switch (jenisType.toLowerCase()) {
        case 'kimia darah':
            html = generateKimiaDarahForm(existingResults);
            break;
        case 'hematologi':
            html = generateHematologiForm(existingResults);
            break;
        case 'urinologi':
            html = generateUrinologiForm(existingResults);
            break;
        case 'serologi':
        case 'serologi imunologi':
            html = generateSerologiForm(existingResults);
            break;
        case 'tbc':
            html = generateTbcForm(existingResults);
            break;
        case 'ims':
            html = generateImsForm(existingResults);
            break;
        default:
            html = generateMlsForm(existingResults);
            break;
    }
    
    container.innerHTML = html;
}

// Generate form functions
function generateKimiaDarahForm(existingResults) {
    const values = existingResults || {};
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <h4 class="col-span-full text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="droplet" class="w-5 h-5 mr-2 text-red-600"></i>
                Parameter Kimia Darah
            </h4>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Gula Darah Sewaktu (mg/dL)</label>
            <input type="number" name="gula_darah_sewaktu" value="${values.gula_darah_sewaktu || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="70-200" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Gula Darah Puasa (mg/dL)</label>
            <input type="number" name="gula_darah_puasa" value="${values.gula_darah_puasa || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="70-110" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Gula Darah 2 Jam PP (mg/dL)</label>
            <input type="number" name="gula_darah_2jam_pp" value="${values.gula_darah_2jam_pp || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="< 140" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Kolesterol Total (mg/dL)</label>
            <input type="number" name="cholesterol_total" value="${values.cholesterol_total || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="< 200" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Kolesterol HDL (mg/dL)</label>
            <input type="number" name="cholesterol_hdl" value="${values.cholesterol_hdl || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="> 40" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Kolesterol LDL (mg/dL)</label>
            <input type="number" name="cholesterol_ldl" value="${values.cholesterol_ldl || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="< 130" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Trigliserida (mg/dL)</label>
            <input type="number" name="trigliserida" value="${values.trigliserida || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="< 150" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Asam Urat (mg/dL)</label>
            <input type="number" name="asam_urat" value="${values.asam_urat || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="L: 3.5-7.0, P: 2.5-6.0" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Ureum (mg/dL)</label>
            <input type="number" name="ureum" value="${values.ureum || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="10-50" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Kreatinin (mg/dL)</label>
            <input type="number" name="creatinin" value="${values.creatinin || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="L: 0.7-1.3, P: 0.6-1.1" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">SGPT (U/L)</label>
            <input type="number" name="sgpt" value="${values.sgpt || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="< 41" step="0.01"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">SGOT (U/L)</label>
            <input type="number" name="sgot" value="${values.sgot || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="< 37" step="0.01"></div>
        </div>`;
}

function generateHematologiForm(existingResults) {
    const values = existingResults || {};
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <h4 class="col-span-full text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="heart" class="w-5 h-5 mr-2 text-red-600"></i>Parameter Hematologi
            </h4>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hemoglobin (g/dL)</label>
                <input type="number" name="hemoglobin" value="${values.hemoglobin || ''}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="L: 12-16, P: 11-15" step="0.01">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hematokrit (%)</label>
                <input type="number" name="hematokrit" value="${values.hematokrit || ''}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="L: 37-48, P: 35-45" step="0.01">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Laju Endap Darah (mm/jam)</label>
                <input type="number" name="laju_endap_darah" value="${values.laju_endap_darah || ''}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="L: < 15, P: < 20" step="0.01">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Clotting Time (detik)</label>
                <input type="number" name="clotting_time" value="${values.clotting_time || ''}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Normal: 300-900 detik (5-15 menit)" step="1" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bleeding Time (detik)</label>
                <input type="number" name="bleeding_time" value="${values.bleeding_time || ''}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Normal: 60-360 detik (1-6 menit)" step="1" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Golongan Darah</label>
                <select name="golongan_darah" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Golongan Darah</option>
                    <option value="A" ${values.golongan_darah === 'A' ? 'selected' : ''}>A</option>
                    <option value="B" ${values.golongan_darah === 'B' ? 'selected' : ''}>B</option>
                    <option value="AB" ${values.golongan_darah === 'AB' ? 'selected' : ''}>AB</option>
                    <option value="O" ${values.golongan_darah === 'O' ? 'selected' : ''}>O</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rhesus</label>
                <select name="rhesus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Rhesus</option>
                    <option value="+" ${values.rhesus === '+' ? 'selected' : ''}>Positif (+)</option>
                    <option value="-" ${values.rhesus === '-' ? 'selected' : ''}>Negatif (-)</option>
                </select>
            </div>
            <div class="col-span-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">Malaria</label>
                <textarea name="malaria" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                          placeholder="Hasil pemeriksaan malaria...">${values.malaria || ''}</textarea>
            </div>
        </div>`;
}

function generateUrinologiForm(existingResults) {
    const values = existingResults || {};
    return `
        <div class="space-y-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="beaker" class="w-5 h-5 mr-2 text-yellow-600"></i>Parameter Urinologi
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">pH Kimia</label>
                <input type="number" name="kimia_ph" value="${values.kimia_ph || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="4.5-8.0" step="0.1" min="1" max="14"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Tes Kehamilan</label>
                <select name="tes_kehamilan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Hasil</option>
                    <option value="Positif" ${values.tes_kehamilan === 'Positif' ? 'selected' : ''}>Positif</option>
                    <option value="Negatif" ${values.tes_kehamilan === 'Negatif' ? 'selected' : ''}>Negatif</option>
                </select></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Makroskopis</label>
            <textarea name="makroskopis" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Deskripsi makroskopis urin (warna, kejernihan, bau, dll)...">${values.makroskopis || ''}</textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Mikroskopis</label>
            <textarea name="mikroskopis" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Hasil mikroskopis (eritrosit, leukosit, epitel, dll)...">${values.mikroskopis || ''}</textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Protein</label>
            <textarea name="protein" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Hasil tes protein...">${values.protein || ''}</textarea></div>
        </div>`;
}

function generateSerologiForm(existingResults) {
    const values = existingResults || {};
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <h4 class="col-span-full text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="shield" class="w-5 h-5 mr-2 text-green-600"></i>Parameter Serologi & Imunologi
            </h4>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">RDT Antigen</label>
            <select name="rdt_antigen" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Pilih Hasil</option>
                <option value="Positif" ${values.rdt_antigen === 'Positif' ? 'selected' : ''}>Positif</option>
                <option value="Negatif" ${values.rdt_antigen === 'Negatif' ? 'selected' : ''}>Negatif</option>
            </select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">HbsAg</label>
            <select name="hbsag" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Pilih Hasil</option>
                <option value="Reaktif" ${values.hbsag === 'Reaktif' ? 'selected' : ''}>Reaktif</option>
                <option value="Non-Reaktif" ${values.hbsag === 'Non-Reaktif' ? 'selected' : ''}>Non-Reaktif</option>
            </select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">NS1</label>
            <select name="ns1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Pilih Hasil</option>
                <option value="Positif" ${values.ns1 === 'Positif' ? 'selected' : ''}>Positif</option>
                <option value="Negatif" ${values.ns1 === 'Negatif' ? 'selected' : ''}>Negatif</option>
            </select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">HIV</label>
            <select name="hiv" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Pilih Hasil</option>
                <option value="Reaktif" ${values.hiv === 'Reaktif' ? 'selected' : ''}>Reaktif</option>
                <option value="Non-Reaktif" ${values.hiv === 'Non-Reaktif' ? 'selected' : ''}>Non-Reaktif</option>
            </select></div>
            <div class="col-span-full"><label class="block text-sm font-medium text-gray-700 mb-2">Widal</label>
            <textarea name="widal" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Hasil tes Widal...">${values.widal || ''}</textarea></div>
        </div>`;
}

function generateTbcForm(existingResults) {
    const values = existingResults || {};
    return `
        <div class="space-y-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="lungs" class="w-5 h-5 mr-2 text-purple-600"></i>Parameter TBC
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Dahak (BTA)</label>
                <select name="dahak" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Hasil</option>
                    <option value="Negatif" ${values.dahak === 'Negatif' ? 'selected' : ''}>Negatif</option>
                    <option value="Scanty" ${values.dahak === 'Scanty' ? 'selected' : ''}>Scanty</option>
                    <option value="+1" ${values.dahak === '+1' ? 'selected' : ''}>+1</option>
                    <option value="+2" ${values.dahak === '+2' ? 'selected' : ''}>+2</option>
                    <option value="+3" ${values.dahak === '+3' ? 'selected' : ''}>+3</option>
                </select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">TCM (GeneXpert)</label>
                <select name="tcm" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Hasil</option>
                    <option value="Detected" ${values.tcm === 'Detected' ? 'selected' : ''}>Detected</option>
                    <option value="Not Detected" ${values.tcm === 'Not Detected' ? 'selected' : ''}>Not Detected</option>
                </select></div>
            </div>
        </div>`;
}

function generateImsForm(existingResults) {
    const values = existingResults || {};
    return `
        <div class="space-y-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 mr-2 text-red-600"></i>Parameter IMS
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Sifilis</label>
                <select name="sifilis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Hasil</option>
                    <option value="Reaktif" ${values.sifilis === 'Reaktif' ? 'selected' : ''}>Reaktif</option>
                    <option value="Non-Reaktif" ${values.sifilis === 'Non-Reaktif' ? 'selected' : ''}>Non-Reaktif</option>
                </select></div>
                <div class="col-span-full"><label class="block text-sm font-medium text-gray-700 mb-2">Duh Tubuh</label>
                <textarea name="duh_tubuh" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Hasil pemeriksaan duh tubuh...">${values.duh_tubuh || ''}</textarea></div>
            </div>
        </div>`;
}

function generateMlsForm(existingResults) {
    const values = existingResults || {};
    return `
        <div class="space-y-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="flask" class="w-5 h-5 mr-2 text-indigo-600"></i>Parameter Laboratorium Umum
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Jenis Tes</label>
                <input type="text" name="jenis_tes" value="${values.jenis_tes || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Nama jenis tes/parameter" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                <input type="text" name="satuan" value="${values.satuan || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="mg/dL, mmol/L, dll"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Nilai Rujukan</label>
                <input type="text" name="nilai_rujukan" value="${values.nilai_rujukan || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Rentang nilai normal"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Metode</label>
                <input type="text" name="metode" value="${values.metode || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Metode pemeriksaan"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Hasil</label>
            <textarea name="hasil" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan hasil pemeriksaan..." required>${values.hasil || ''}</textarea></div>
        </div>`;
}

function submitResults(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const submitBtn = event.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 loading"></i>Menyimpan...';
    
    fetch('<?= base_url('laboratorium/save_examination_results') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Hasil berhasil disimpan!');
            closeInputModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan hasil');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-lucide="save" class="w-4 h-4 mr-2"></i>Simpan Hasil';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
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
</script>

</body>
</html>