<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Dashboard Laboratorium'; ?> - Labsys</title>
    
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
    </style>
</head>
<body class="bg-gray-50">

<script>
    window.labConfig = {
        baseUrl: '<?php echo base_url(); ?>',
        userId: '<?php echo $this->session->userdata('user_id'); ?>'
    };
</script>

<!-- Header - TEMA HIJAU UNTUK LAB (Beda dari Admin & Administrasi) -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="microscope" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Dashboard Laboratorium</h1>
                <p class="text-blue-100">Monitoring & Analisis Pemeriksaan</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm">
                <p class="text-sm text-gray-500">Last Update</p>
                <p class="text-lg font-semibold text-gray-900" id="last-update">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content - FULL WIDTH -->
<div class="p-6 space-y-6">

    <!-- KPI Cards - 6 Metrik Utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        
        <!-- Pending Requests -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Permintaan Masuk</p>
                    <p class="text-3xl font-bold text-yellow-600">
                        <?php echo $stats['pending_requests'] ?? 0; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Menunggu</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="inbox" class="w-7 h-7 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Sedang Proses</p>
                    <p class="text-3xl font-bold text-blue-600">
                        <?php echo $stats['samples_in_progress'] ?? 0; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Sampel</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="flask-conical" class="w-7 h-7 text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Completed Today -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Selesai Hari Ini</p>
                    <p class="text-3xl font-bold text-blue-600">
                        <?php echo $stats['completed_today'] ?? 0; ?>
                    </p>
                    <p class="text-xs text-blue-600 mt-1 font-medium">
                        <?php echo $stats['completed_this_month'] ?? 0; ?> bulan ini
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-7 h-7 text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Validation Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Perlu Validasi</p>
                    <p class="text-3xl font-bold text-purple-600">
                        <?php echo $qc_stats['pending_validation'] ?? 0; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Hasil</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-7 h-7 text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Stok Rendah</p>
                    <p class="text-3xl font-bold text-orange-600">
                        <?php echo $stats['low_stock_items'] ?? 0; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Item</p>
                </div>
                <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="package" class="w-7 h-7 text-orange-600"></i>
                </div>
            </div>
        </div>

        <!-- Equipment Alert -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Alat Perlu Cek</p>
                    <p class="text-3xl font-bold text-red-600">
                        <?php echo $stats['equipment_maintenance_due'] ?? 0; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Kalibrasi</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="activity" class="w-7 h-7 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Sidebar Row -->
    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
        
        <!-- Completion Trend Chart - 4 cols -->
        <div class="lg:col-span-4 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                    <span>Tren Penyelesaian (7 Hari)</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="completionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution & QC Stats - 3 cols -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Status Distribution Donut -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="pie-chart" class="w-5 h-5 text-blue-600"></i>
                        <span>Status Pemeriksaan</span>
                    </h3>
                </div>
                <div class="p-6">
                    <canvas id="statusChart" style="max-height: 220px;"></canvas>
                </div>
            </div>

            <!-- QC Performance -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="gauge" class="w-5 h-5 text-purple-600"></i>
                        <span>Performa QC</span>
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Avg. Validation Time</span>
                        <span class="text-lg font-bold text-purple-600"><?php echo $qc_stats['avg_validation_time'] ?? 0; ?>h</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Validated Today</span>
                        <span class="text-lg font-bold text-green-600"><?php echo $qc_stats['validated_today'] ?? 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">This Month</span>
                        <span class="text-lg font-bold text-blue-600"><?php echo $qc_stats['validated_this_month'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Required Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Pending Requests List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                        <span>Permintaan Masuk</span>
                    </h3>
                    <a href="<?php echo base_url('laboratorium/incoming_requests'); ?>" class="text-sm text-blue-600 hover:text-vlue-700 font-medium">
                        Lihat Semua →
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                    <?php if (!empty($pending_requests)): ?>
                        <?php foreach ($pending_requests as $request): ?>
                            <?php 
                            $priority = $this->Laboratorium_model->get_priority_level($request['hours_waiting']);
                            ?>
                            <div class="p-3 border priority-<?php echo $priority['level']; ?> rounded-lg">
                                <div class="flex items-start justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-900"><?php echo $request['nomor_pemeriksaan']; ?></span>
                                    <span class="px-2 py-1 text-xs font-medium bg-<?php echo $priority['color']; ?>-100 text-<?php echo $priority['color']; ?>-800 rounded">
                                        <?php echo $priority['label']; ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 mb-1"><?php echo $request['nama_pasien']; ?></p>
                                <p class="text-xs text-gray-500"><?php echo $request['jenis_pemeriksaan']; ?></p>
                                <p class="text-xs text-gray-400 mt-2"><?php echo $request['hours_waiting']; ?> jam menunggu</p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                            <p>Tidak ada permintaan pending</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Validation Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-purple-600"></i>
                        <span>Perlu Validasi</span>
                    </h3>
                    <a href="<?php echo base_url('laboratorium/quality_control'); ?>" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Validasi →
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                    <?php if (!empty($pending_validation)): ?>
                        <?php foreach (array_slice($pending_validation, 0, 5) as $validation): ?>
                            <div class="p-3 border border-purple-200 bg-purple-50 rounded-lg">
                                <span class="text-sm font-semibold text-gray-900"><?php echo $validation['nomor_pemeriksaan']; ?></span>
                                <p class="text-sm text-gray-700 mt-1"><?php echo $validation['nama_pasien']; ?></p>
                                <p class="text-xs text-gray-500"><?php echo $validation['jenis_pemeriksaan']; ?></p>
                                <p class="text-xs text-purple-600 mt-2 font-medium"><?php echo $validation['hours_waiting']; ?> jam tunggu</p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-2 text-green-400"></i>
                            <p>Semua hasil sudah divalidasi!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Inventory Alerts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600"></i>
                        <span>Inventory Alert</span>
                    </h3>
                    <a href="<?php echo base_url('laboratorium/inventory_list'); ?>" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Kelola →
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                    <?php if (!empty($inventory_alerts)): ?>
                        <?php 
                        $alert_count = 0;
                        foreach ($inventory_alerts as $alert): 
                            if ($alert_count >= 5) break;
                            $alert_class = '';
                            switch($alert['severity']) {
                                case 'urgent': $alert_class = 'alert-urgent'; break;
                                case 'warning': $alert_class = 'alert-warning'; break;
                                default: $alert_class = 'alert-info';
                            }
                        ?>
                            <div class="p-3 <?php echo $alert_class; ?> rounded-lg">
                                <div class="flex items-start space-x-2">
                                    <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 flex-shrink-0"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900"><?php echo $alert['item']; ?></p>
                                        <p class="text-xs text-gray-600 mt-1"><?php echo $alert['message']; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            $alert_count++;
                        endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-2 text-green-400"></i>
                            <p>Semua inventory dalam kondisi baik</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities - Full Width -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                <i data-lucide="activity" class="w-5 h-5 text-blue-600"></i>
                <span>Aktivitas Terkini</span>
            </h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            <th class="pb-3">Waktu</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3">Pemeriksaan</th>
                            <th class="pb-3">Pasien</th>
                            <th class="pb-3">Petugas</th>
                            <th class="pb-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($recent_activities)): ?>
                            <?php foreach ($recent_activities as $activity): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4">
                                    <span class="text-xs text-gray-500">
                                        <?php echo date('H:i', strtotime($activity['tanggal_update'])); ?>
                                    </span>
                                </td>
                                <td class="py-4">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                        <?php echo $activity['status']; ?>
                                    </span>
                                </td>
                                <td class="py-4">
                                    <span class="text-sm font-medium text-gray-900">
                                        <?php echo $activity['nomor_pemeriksaan']; ?>
                                    </span>
                                </td>
                                <td class="py-4">
                                    <span class="text-sm text-gray-700"><?php echo $activity['nama_pasien']; ?></span>
                                </td>
                                <td class="py-4">
                                    <span class="text-sm text-gray-600"><?php echo $activity['nama_petugas']; ?></span>
                                </td>
                                <td class="py-4">
                                    <span class="text-sm text-gray-500"><?php echo substr($activity['keterangan'], 0, 50); ?>...</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">
                                    Belum ada aktivitas hari ini
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
lucide.createIcons();

// Initialize Charts
let completionChart = null;
let statusChart = null;

function initializeCharts() {
    // Completion Trend Chart
    const ctx1 = document.getElementById('completionChart').getContext('2d');
    completionChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Selesai',
                data: [],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Status Distribution Chart
    const ctx2 = document.getElementById('statusChart').getContext('2d');
    const statusData = <?php echo json_encode($status_distribution ?? array('pending' => 0, 'progress' => 0, 'selesai' => 0, 'cancelled' => 0)); ?>;
    
    statusChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Progress', 'Selesai', 'Cancelled'],
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
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    loadCompletionData();
}

async function loadCompletionData() {
    try {
        const response = await fetch(window.labConfig.baseUrl + 'laboratorium/ajax_get_completion_trend');
        if (response.ok) {
            const result = await response.json();
            if (result.success && result.data) {
                updateCompletionChart(result.data);
            }
        }
    } catch (error) {
        console.error('Error loading completion data:', error);
    }
}

function updateCompletionChart(data) {
    if (!completionChart || !data) return;
    
    const labels = data.map(day => {
        const date = new Date(day.date);
        return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' });
    });
    
    const amounts = data.map(day => parseInt(day.completed) || 0);
    
    completionChart.data.labels = labels;
    completionChart.data.datasets[0].data = amounts;
    completionChart.update();
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
    
    loadCompletionData();
    
    setTimeout(() => {
        updateLastUpdateTime();
        icon.classList.remove('loading');
    }, 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    updateLastUpdateTime();
    setInterval(updateLastUpdateTime, 60000);
});
</script>

</body>
</html>