<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Dashboard Supervisor'; ?> - LabSy</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .loading { animation: spin 1s linear infinite; }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .chart-container { position: relative; height: 300px; margin-top: 1rem; }
        
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .status-pending { @apply bg-yellow-100 text-yellow-800; }
        .status-progress { @apply bg-blue-100 text-blue-800; }
        .status-selesai { @apply bg-green-100 text-green-800; }
        .status-cancelled { @apply bg-red-100 text-red-800; }

        .priority-urgent { @apply bg-red-100 border-red-300; }
        .priority-high { @apply bg-orange-100 border-orange-300; }
        .priority-normal { @apply bg-blue-100 border-blue-300; }

        .alert-urgent { @apply bg-red-50 border-l-4 border-red-500; }
        .alert-warning { @apply bg-yellow-50 border-l-4 border-yellow-500; }
        .alert-info { @apply bg-blue-50 border-l-4 border-blue-500; }

        .metric-card {
            background: linear-gradient(135deg, var(--tw-gradient-from) 0%, var(--tw-gradient-to) 100%);
        }
    </style>
</head>
<body class="bg-gray-50">

<script>
    window.labConfig = {
        baseUrl: '<?php echo base_url(); ?>',
        userId: '<?php echo $this->session->userdata('user_id'); ?>',
        role: 'supervisor'
    };
</script>

<!-- Header - TEMA PURPLE UNTUK SUPERVISOR (Quality Control Focus) -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="shield-check" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Dashboard Supervisor</h1>
                <p class="text-blue-100">Quality Control & Validation Management</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="refreshDashboard()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                <i data-lucide="refresh-cw" class="w-4 h-4" id="refreshIcon"></i>
                <span class="text-sm font-medium">Refresh</span>
            </button>
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm">
                <p class="text-sm text-gray-500">Last Update</p>
                <p class="text-lg font-semibold text-gray-900" id="last-update">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content - FULL WIDTH -->
<div class="p-6 space-y-6">

    <!-- KPI Cards - Quality Control Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Pending Validation - Priority Metric -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-orange-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Perlu Validasi</p>
                    <p class="text-4xl font-bold text-orange-600 my-2">
                        <?php echo $qc_stats['pending_validation'] ?? 0; ?>
                    </p>
                    <a href="<?php echo base_url('supervisor/quality_control'); ?>" class="text-sm text-orange-600 hover:text-orange-700 font-medium inline-flex items-center">
                        Validasi Sekarang <i data-lucide="arrow-right" class="w-3 h-3 ml-1"></i>
                    </a>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="clock-alert" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Validated Today -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Divalidasi Hari Ini</p>
                    <p class="text-4xl font-bold text-green-600 my-2">
                        <?php echo $qc_stats['validated_today'] ?? 0; ?>
                    </p>
                    <p class="text-xs text-gray-500">Target: 20/hari</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="check-circle-2" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bulan Ini</p>
                    <p class="text-4xl font-bold text-blue-600 my-2">
                        <?php echo $qc_stats['validated_this_month'] ?? 0; ?>
                    </p>
                    <p class="text-xs text-gray-500">Validasi selesai</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="bar-chart-3" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Avg Validation Time -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata Waktu</p>
                    <p class="text-4xl font-bold text-blue-600 my-2">
                        <?php echo $qc_stats['avg_validation_time'] ?? 0; ?><span class="text-2xl">h</span>
                    </p>
                    <p class="text-xs text-gray-500">Validasi per hasil</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="timer" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Validation Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                        <span>Tren Validasi (7 Hari Terakhir)</span>
                    </h3>
                    <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500">
                        <option value="7">7 Hari</option>
                        <option value="14">14 Hari</option>
                        <option value="30">30 Hari</option>
                    </select>
                </div>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="validationTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Performance Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="gauge" class="w-5 h-5 text-blue-600"></i>
                    <span>Performa QC</span>
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Completion Rate -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Completion Rate</span>
                        <span class="text-sm font-bold text-green-600">
                            <?php 
                            $total = ($qc_stats['validated_today'] ?? 0) + ($qc_stats['pending_validation'] ?? 1);
                            $rate = round((($qc_stats['validated_today'] ?? 0) / $total) * 100);
                            echo $rate; 
                            ?>%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-2.5 rounded-full" style="width: <?php echo $rate; ?>%"></div>
                    </div>
                </div>

                <!-- Quality Score -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Quality Score</span>
                        <span class="text-sm font-bold text-blue-600">98%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full" style="width: 98%"></div>
                    </div>
                </div>

                <!-- Speed Performance -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Speed Performance</span>
                        <span class="text-sm font-bold text-blue-600">
                            <?php 
                            $target_hours = 4;
                            $actual = $qc_stats['avg_validation_time'] ?? 0;
                            $speed = max(0, min(100, round((($target_hours - $actual) / $target_hours) * 100 + 50)));
                            echo $speed;
                            ?>%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full" style="width: <?php echo $speed; ?>%"></div>
                    </div>
                </div>

                <!-- Status Overview -->
                <div class="grid grid-cols-2 gap-3 mt-6 pt-6 border-t">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Approved</p>
                        <p class="text-2xl font-bold text-green-600"><?php echo $qc_stats['validated_today'] ?? 0; ?></p>
                    </div>
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-3">
                        <p class="text-xs text-gray-600 mb-1">Rejected</p>
                        <p class="text-2xl font-bold text-red-600">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Action Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Priority Validations -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-orange-200">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-orange-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600"></i>
                        <span>Prioritas Tinggi - Perlu Validasi</span>
                        <span class="bg-orange-500 text-white px-2.5 py-1 rounded-full text-xs font-bold">
                            <?php echo count(array_filter($pending_validation ?? array(), function($v) { return $v['hours_waiting'] > 24; })); ?>
                        </span>
                    </h3>
                    <a href="<?php echo base_url('supervisor/quality_control'); ?>" class="text-sm text-orange-600 hover:text-orange-700 font-medium flex items-center">
                        Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                    <?php if (!empty($pending_validation)): ?>
                        <?php 
                        $urgent_items = array_filter($pending_validation, function($v) { return $v['hours_waiting'] > 24; });
                        $shown = 0;
                        foreach ($urgent_items as $validation): 
                            if ($shown >= 5) break;
                            $shown++;
                        ?>
                            <div class="p-4 border-l-4 border-red-500 bg-red-50 rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-sm font-bold text-gray-900"><?php echo $validation['nomor_pemeriksaan']; ?></span>
                                            <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">URGENT</span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-800"><?php echo $validation['nama_pasien']; ?></p>
                                        <p class="text-xs text-gray-600 mt-1"><?php echo $validation['jenis_pemeriksaan']; ?></p>
                                        <div class="flex items-center space-x-3 mt-2">
                                            <span class="text-xs text-red-600 font-semibold">
                                                <i data-lucide="clock" class="w-3 h-3 inline"></i>
                                                <?php echo $validation['hours_waiting']; ?> jam
                                            </span>
                                            <?php if (!empty($validation['nama_petugas'])): ?>
                                            <span class="text-xs text-gray-500">
                                                <i data-lucide="user" class="w-3 h-3 inline"></i>
                                                <?php echo $validation['nama_petugas']; ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <button onclick="quickValidate(<?php echo $validation['pemeriksaan_id']; ?>)" 
                                            class="ml-3 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors flex items-center space-x-1">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        <span>Validasi</span>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if ($shown === 0): ?>
                            <div class="text-center py-8 text-gray-500">
                                <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-2 text-green-400"></i>
                                <p class="font-medium">Tidak ada validasi urgent!</p>
                                <p class="text-sm mt-1">Semua prioritas tinggi sudah ditangani</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                            <p>Tidak ada hasil pending validasi</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Validations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                        <span>Validasi Terbaru</span>
                    </h3>
                    <a href="<?php echo base_url('supervisor/validated_results'); ?>" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Riwayat →
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                    <?php if (!empty($recent_validations)): ?>
                        <?php foreach (array_slice($recent_validations, 0, 5) as $validation): ?>
                            <div class="p-3 border border-green-200 bg-green-50 rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="text-sm font-semibold text-gray-900"><?php echo $validation['nomor_pemeriksaan']; ?></span>
                                            <span class="px-2 py-0.5 bg-green-500 text-white text-xs font-medium rounded">VALIDATED</span>
                                        </div>
                                        <p class="text-sm text-gray-700"><?php echo $validation['nama_pasien']; ?></p>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo $validation['jenis_pemeriksaan']; ?></p>
                                        <div class="flex items-center space-x-3 mt-2">
                                            <span class="text-xs text-green-600 font-medium">
                                                <i data-lucide="clock" class="w-3 h-3 inline"></i>
                                                <?php echo date('H:i', strtotime($validation['completed_at'])); ?> WIB
                                            </span>
                                            <?php if (!empty($validation['nama_petugas'])): ?>
                                            <span class="text-xs text-gray-500">
                                                <i data-lucide="user-check" class="w-3 h-3 inline"></i>
                                                <?php echo $validation['nama_petugas']; ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <button onclick="viewValidation(<?php echo $validation['pemeriksaan_id']; ?>)" 
                                            class="ml-3 text-blue-600 hover:text-blue-700">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-lucide="calendar" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                            <p>Belum ada validasi hari ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-blue-600"></i>
                <span>Distribusi Status Pemeriksaan</span>
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <canvas id="statusChart" style="max-height: 300px;"></canvas>
                </div>
                <div class="flex items-center">
                    <div class="w-full space-y-3">
                        <?php 
                        $status_data = $status_distribution ?? array('pending' => 0, 'progress' => 0, 'selesai' => 0, 'cancelled' => 0);
                        $total = array_sum($status_data);
                        $statuses = [
                            ['key' => 'pending', 'label' => 'Pending', 'color' => 'yellow', 'icon' => 'clock'],
                            ['key' => 'progress', 'label' => 'Progress', 'color' => 'blue', 'icon' => 'loader'],
                            ['key' => 'selesai', 'label' => 'Selesai', 'color' => 'green', 'icon' => 'check-circle'],
                            ['key' => 'cancelled', 'label' => 'Dibatalkan', 'color' => 'red', 'icon' => 'x-circle']
                        ];
                        
                        foreach ($statuses as $status):
                            $count = $status_data[$status['key']] ?? 0;
                            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                        ?>
                        <div class="flex items-center justify-between p-3 bg-<?php echo $status['color']; ?>-50 rounded-lg border border-<?php echo $status['color']; ?>-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-<?php echo $status['color']; ?>-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="<?php echo $status['icon']; ?>" class="w-5 h-5 text-<?php echo $status['color']; ?>-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?php echo $status['label']; ?></p>
                                    <p class="text-xs text-gray-500"><?php echo $percentage; ?>% dari total</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-<?php echo $status['color']; ?>-600"><?php echo $count; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
lucide.createIcons();

let validationTrendChart = null;
let statusChart = null;

function initializeCharts() {
    // Validation Trend Chart
    const ctx1 = document.getElementById('validationTrendChart').getContext('2d');
    validationTrendChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Divalidasi',
                data: [],
                borderColor: 'rgb(147, 51, 234)',
                backgroundColor: 'rgba(147, 51, 234, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(147, 51, 234)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // Status Distribution Chart
    const ctx2 = document.getElementById('statusChart').getContext('2d');
    const statusData = <?php echo json_encode($status_distribution ?? array('pending' => 0, 'progress' => 0, 'selesai' => 0, 'cancelled' => 0)); ?>;
    
    statusChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Progress', 'Selesai', 'Dibatalkan'],
            datasets: [{
                data: [
                    statusData.pending || 0,
                    statusData.progress || 0,
                    statusData.selesai || 0,
                    statusData.cancelled || 0
                ],
                backgroundColor: [
                    'rgb(234, 179, 8)',
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12
                }
            }
        }
    });

    loadValidationTrendData();
}

async function loadValidationTrendData() {
    try {
        const response = await fetch(window.labConfig.baseUrl + 'supervisor/ajax_get_validation_trend');
        if (response.ok) {
            const result = await response.json();
            if (result.success && result.data) {
                updateValidationChart(result.data);
            }
        }
    } catch (error) {
        console.error('Error loading validation trend:', error);
        // Use mock data if API fails
        const mockData = generateMockTrendData();
        updateValidationChart(mockData);
    }
}

function generateMockTrendData() {
    const days = 7;
    const data = [];
    const today = new Date();
    
    for (let i = days - 1; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(date.getDate() - i);
        data.push({
            date: date.toISOString().split('T')[0],
            validated: Math.floor(Math.random() * 15) + 10
        });
    }
    return data;
}

function updateValidationChart(data) {
    if (!validationTrendChart || !data) return;
    
    const labels = data.map(day => {
        const date = new Date(day.date);
        return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' });
    });
    
    const amounts = data.map(day => parseInt(day.validated) || 0);
    
    validationTrendChart.data.labels = labels;
    validationTrendChart.data.datasets[0].data = amounts;
    validationTrendChart.update();
}

function updateLastUpdateTime() {
    document.getElementById('last-update').textContent = new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function refreshDashboard() {
    const icon = document.getElementById('refreshIcon');
    icon.classList.add('loading');
    
    loadValidationTrendData();
    
    setTimeout(() => {
        updateLastUpdateTime();
        icon.classList.remove('loading');
        showNotification('success', 'Dashboard berhasil diperbarui');
    }, 1000);
}

function quickValidate(examId) {
    if (!confirm('Validasi hasil pemeriksaan ini sekarang?')) return;
    
    fetch(window.labConfig.baseUrl + 'supervisor/validate_result/' + examId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat validasi');
    });
}

function viewValidation(examId) {
    window.location.href = window.labConfig.baseUrl + 'supervisor/quality_control?exam=' + examId;
}

function showNotification(type, message) {
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(notif => notif.remove());
    
    const notification = document.createElement('div');
    notification.className = `custom-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 
        'bg-red-50 border-red-200 text-red-800'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" 
               class="w-5 h-5 ${type === 'success' ? 'text-green-600' : 'text-red-600'}"></i>
            <div>
                <p class="font-medium">${type === 'success' ? 'Berhasil' : 'Error'}</p>
                <p class="text-sm">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    lucide.createIcons();
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    updateLastUpdateTime();
    setInterval(updateLastUpdateTime, 60000);
    
    // Auto refresh setiap 5 menit
    setInterval(() => {
        loadValidationTrendData();
    }, 300000);
});
</script>

</body>
</html>