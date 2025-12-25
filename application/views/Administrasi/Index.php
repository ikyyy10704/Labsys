<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Dashboard Administrasi'; ?> - LabSy</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .loading { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .chart-container { position: relative; height: 320px; }
        
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-progress { background: #dbeafe; color: #1e40af; }
        .status-selesai { background: #d1fae5; color: #065f46; }
        .status-lunas { background: #d1fae5; color: #065f46; }
        .status-belum { background: #fee2e2; color: #991b1b; }
        .status-cicilan { background: #fef3c7; color: #92400e; }

        .notification {
            max-width: 400px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .notification-success { background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border-color: #22c55e; }
        .notification-error { background: linear-gradient(135deg, #fef2f2 0%, #fef2f2 100%); border-color: #ef4444; }
        .notification-info { background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%); border-color: #3b82f6; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<script>
    window.administrasiConfig = {
        baseUrl: '<?php echo base_url(); ?>',
        hasInitialData: <?php echo isset($financial_summary) ? 'true' : 'false'; ?>,
        userId: '<?php echo $this->session->userdata('user_id'); ?>'
    };
</script>

<div id="notification-container" class="fixed top-4 right-4 z-50 space-y-3"></div>

<!-- Header -->
<div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="w-full px-6 py-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="clipboard-list" class="w-7 h-7 text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Dashboard Administrasi</h1>
                    <p class="text-blue-100 text-sm">Manajemen Pasien & Pemeriksaan</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2 border border-white/20">
                    <p class="text-xs text-blue-100">Terakhir Update</p>
                    <p class="text-sm font-semibold text-white" id="last-update">Loading...</p>
                </div>
                <button onclick="refreshDashboard()" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white p-2.5 rounded-lg border border-white/20 transition-colors">
                    <i data-lucide="refresh-cw" id="refreshIcon" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="w-full px-6 py-6 space-y-6">

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Pasien -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                </div>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                    +<?php echo $registration_stats['registrasi_hari_ini'] ?? 0; ?>
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Total Pasien</p>
                <p class="text-2xl font-bold text-gray-900">
                    <?php echo number_format($registration_stats['total_pasien'] ?? 0); ?>
                </p>
                <p class="text-xs text-gray-500 mt-1.5">
                    <?php echo number_format($registration_stats['registrasi_minggu_ini'] ?? 0); ?> minggu ini
                </p>
            </div>
        </div>

        <!-- Total Permintaan -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-orange-600"></i>
                </div>
                <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">
                    <?php echo $examination_stats['pending'] ?? 0; ?> pending
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Total Permintaan</p>
                <p class="text-2xl font-bold text-gray-900">
                    <?php echo number_format($examination_stats['total'] ?? 0); ?>
                </p>
                <p class="text-xs text-gray-500 mt-1.5">
                    <?php echo $examination_stats['progress'] ?? 0; ?> diproses
                </p>
            </div>
        </div>

        <!-- Pemeriksaan Selesai Hari Ini -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                    Hari ini
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Selesai Hari Ini</p>
                <p class="text-2xl font-bold text-gray-900">
                    <?php echo number_format($today_completed_exams ?? 0); ?>
                </p>
                <p class="text-xs text-gray-500 mt-1.5">
                    <?php echo $examination_stats['selesai'] ?? 0; ?> total
                </p>
            </div>
        </div>

        <!-- Registrasi Bulan Ini -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-5 h-5 text-purple-600"></i>
                </div>
                <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">
                    Bulan ini
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Registrasi Bulan Ini</p>
                <p class="text-2xl font-bold text-gray-900">
                    <?php echo number_format($registration_stats['registrasi_bulan_ini'] ?? 0); ?>
                </p>
                <p class="text-xs text-gray-500 mt-1.5">
                    Target: 100/bulan
                </p>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Tren Pendaftaran (2 columns) -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                            <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                            <span>Tren Pendaftaran Pasien</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">14 hari terakhir</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="registrationTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pemeriksaan Populer (1 column) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="activity" class="w-5 h-5 text-orange-600"></i>
                    <span>Pemeriksaan Populer</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-80 overflow-y-auto custom-scrollbar">
                    <?php if (isset($popular_exams) && !empty($popular_exams)): ?>
                        <?php 
                        $max_count = max(array_column($popular_exams, 'request_count'));
                        foreach ($popular_exams as $index => $exam): 
                        $percentage = $max_count > 0 ? ($exam['request_count'] / $max_count) * 100 : 0;
                        ?>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-xs font-bold">
                                        <?php echo $index + 1; ?>
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">
                                        <?php echo $exam['jenis_pemeriksaan']; ?>
                                    </span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">
                                    <?php echo $exam['request_count']; ?>
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-500" 
                                     style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-400">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2"></i>
                            <p class="text-sm">Belum ada data</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Registrasi Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="user-check" class="w-5 h-5 text-blue-600"></i>
                        <span>Registrasi Terbaru</span>
                    </h3>
                    <a href="<?php echo base_url('administrasi/patient_management'); ?>" 
                       class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center space-x-1">
                        <span>Lihat Semua</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                    <?php if (isset($recent_registrations) && !empty($recent_registrations)): ?>
                        <?php foreach ($recent_registrations as $patient): ?>
                        <div class="flex items-center space-x-3 p-4 hover:bg-gray-50 rounded-lg transition-colors border border-gray-100">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-white">
                                    <?php 
                                    $names = explode(' ', $patient['nama']);
                                    echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                    ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900 truncate"><?php echo $patient['nama']; ?></p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-xs text-gray-500"><?php echo $patient['nomor_registrasi']; ?></span>
                                    <span class="text-xs text-gray-300">•</span>
                                    <span class="text-xs text-gray-500"><?php echo $patient['jenis_kelamin'] == 'L' ? 'L' : 'P'; ?></span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500"><?php echo date('d/m/Y', strtotime($patient['created_at'])); ?></p>
                                <p class="text-xs text-gray-400"><?php echo date('H:i', strtotime($patient['created_at'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-400">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2"></i>
                            <p class="text-sm">Belum ada registrasi hari ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Permintaan Pemeriksaan Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                        <span>Permintaan Pending</span>
                    </h3>
                    <a href="<?php echo base_url('administrasi/examination_request'); ?>" 
                       class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center space-x-1">
                        <span>Lihat Semua</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                    <?php if (isset($pending_examinations) && !empty($pending_examinations)): ?>
                        <?php foreach ($pending_examinations as $exam): ?>
                        <div class="flex items-center space-x-3 p-4 hover:bg-gray-50 rounded-lg transition-colors border border-gray-100">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="file-text" class="w-5 h-5 text-yellow-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900 truncate"><?php echo $exam['nama_pasien']; ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?php echo $exam['jenis_pemeriksaan']; ?></p>
                                <p class="text-xs text-gray-400 mt-0.5"><?php echo $exam['nomor_pemeriksaan']; ?></p>
                            </div>
                            <div class="flex flex-col items-end space-y-1 flex-shrink-0">
                                <span class="status-badge status-<?php echo $exam['status_pemeriksaan']; ?>">
                                    <?php echo ucfirst($exam['status_pemeriksaan']); ?>
                                </span>
                                <p class="text-xs text-gray-400"><?php echo date('d/m H:i', strtotime($exam['created_at'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-400">
                            <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-2"></i>
                            <p class="text-sm">Tidak ada pemeriksaan pending</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
            <i data-lucide="zap" class="w-5 h-5 text-blue-600"></i>
            <span>Quick Actions</span>
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?php echo base_url('administrasi/add_patient_data'); ?>" 
               class="flex flex-col items-center justify-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                <i data-lucide="user-plus" class="w-8 h-8 text-blue-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Daftar Pasien Baru</span>
            </a>
            <a href="<?php echo base_url('administrasi/examination_request'); ?>" 
               class="flex flex-col items-center justify-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors group">
                <i data-lucide="clipboard-plus" class="w-8 h-8 text-orange-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Buat Permintaan</span>
            </a>
            <a href="<?php echo base_url('administrasi/patient_management'); ?>" 
               class="flex flex-col items-center justify-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                <i data-lucide="search" class="w-8 h-8 text-green-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Cari Pasien</span>
            </a>
            <a href="<?php echo base_url('administrasi/patient_history'); ?>" 
               class="flex flex-col items-center justify-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                <i data-lucide="file-text" class="w-8 h-8 text-purple-600 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Riwayat Pasien</span>
            </a>
        </div>
    </div>

</div>

<script>
lucide.createIcons();

let regChart = null;

function initializeRegistrationChart() {
    const ctx = document.getElementById('registrationTrendChart').getContext('2d');
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    regChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Pasien Baru',
                data: [],
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1e293b',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Pasien: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { 
                        color: '#f1f5f9',
                        drawBorder: false 
                    },
                    ticks: { 
                        precision: 0,
                        color: '#64748b',
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        color: '#64748b',
                        font: { size: 11 }
                    }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            }
        }
    });
    
    loadRegistrationData();
}

async function loadRegistrationData() {
    try {
        const response = await fetch(`${window.administrasiConfig.baseUrl}administrasi/ajax_get_registration_trend_data`);
        const result = await response.json();
        
        if (result.success && result.data) {
            regChart.data.labels = result.data.map(item => item.date);
            regChart.data.datasets[0].data = result.data.map(item => item.count);
            regChart.update('none');
        }
    } catch (error) {
        console.error('Error loading registration data:', error);
    }
}

function updateLastUpdateTime() {
    const now = new Date();
    document.getElementById('last-update').textContent = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function refreshDashboard() {
    const refreshIcon = document.getElementById('refreshIcon');
    refreshIcon.classList.add('loading');
    
    loadRegistrationData();
    
    setTimeout(() => {
        updateLastUpdateTime();
        refreshIcon.classList.remove('loading');
        showNotification('success', 'Data dashboard berhasil diperbarui');
    }, 1000);
}

function showNotification(type, message, duration = 5000) {
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-3';
        document.body.appendChild(container);
    }
    
    const config = {
        success: { icon: 'check-circle', iconColor: 'text-green-600', className: 'notification-success border-l-4' },
        error: { icon: 'alert-circle', iconColor: 'text-red-600', className: 'notification-error border-l-4' },
        info: { icon: 'info', iconColor: 'text-blue-600', className: 'notification-info border-l-4' }
    };
    
    const currentConfig = config[type] || config.info;
    const notificationId = 'notification-' + Date.now();
    
    const notification = document.createElement('div');
    notification.id = notificationId;
    notification.className = `notification ${currentConfig.className} rounded-lg p-4 relative`;
    notification.style.transition = 'all 0.3s ease';
    notification.innerHTML = `
        <div class="flex items-start space-x-3">
            <i data-lucide="${currentConfig.icon}" class="w-5 h-5 ${currentConfig.iconColor}"></i>
            <p class="text-sm font-medium flex-1">${message}</p>
            <button onclick="removeNotification('${notificationId}')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    
    container.appendChild(notification);
    lucide.createIcons();
    
    setTimeout(() => removeNotification(notificationId), duration);
}

function removeNotification(id) {
    const notification = document.getElementById(id);
    if (notification) {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initializeRegistrationChart();
    updateLastUpdateTime();
    
    setInterval(() => {
        updateLastUpdateTime();
    }, 60000);
    
    setTimeout(() => {
        showNotification('info', 'Selamat datang di Dashboard Administrasi!', 3000);
    }, 500);
});
</script>

</body>
</html>