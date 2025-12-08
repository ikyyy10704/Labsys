<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Dashboard Admin'; ?> - Labsy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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

        /* Chart container */
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        /* Status badges */
        .status-pending { @apply bg-yellow-100 text-yellow-800; }
        .status-progress { @apply bg-blue-100 text-blue-800; }
        .status-selesai { @apply bg-green-100 text-green-800; }
        .status-cancelled { @apply bg-red-100 text-red-800; }
        
        .alert-urgent { @apply bg-red-100 text-red-800; }
        .alert-warning { @apply bg-yellow-100 text-yellow-800; }
        .alert-ok { @apply bg-green-100 text-green-800; }

        /* Enhanced notification styles */
        .notification {
            max-width: 400px;
            min-width: 320px;
            font-size: 14px;
            line-height: 1.5;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Improved animations */
        .toast-enter {
            transform: translateX(100%) scale(0.95);
            opacity: 0;
        }
        
        .toast-enter-active {
            transform: translateX(0) scale(1);
            opacity: 1;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .toast-exit {
            transform: translateX(100%) scale(0.95);
            opacity: 0;
            transition: all 0.3s ease-in;
        }

        /* Progress bar for auto-dismiss */
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.3;
            animation: progressBar 5s linear forwards;
        }

        @keyframes progressBar {
            from { width: 100%; }
            to { width: 0%; }
        }

        /* Notification types */
        .notification-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border-color: #22c55e;
        }
        
        .notification-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fef2f2 100%);
            border-color: #ef4444;
        }
        
        .notification-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fefce8 100%);
            border-color: #f59e0b;
        }
        
        .notification-info {
            background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
            border-color: #3b82f6;
        }

        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        /* Pulse animation for updating data */
        .pulse-update {
            animation: pulse 0.5s ease-in-out;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        /* Connection status */
        .connection-status {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9998;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .connection-online {
            background: #10b981;
            color: white;
        }

        .connection-offline {
            background: #ef4444;
            color: white;
        }

        .connection-reconnecting {
            background: #f59e0b;
            color: white;
        }

        /* Error state */
        .error-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        .error-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        /* Mobile responsiveness */
        @media (max-width: 640px) {
            .notification-container {
                top: 1rem !important;
                left: 1rem !important;
                right: 1rem !important;
                width: auto !important;
            }
            
            .notification {
                min-width: auto;
                max-width: none;
            }
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Pass PHP data to JavaScript -->
<script>
    window.labsysConfig = {
        baseUrl: '<?php echo base_url(); ?>',
        hasInitialData: <?php echo isset($has_data) && $has_data ? 'true' : 'false'; ?>,
        initialData: <?php echo isset($dashboard_data) ? json_encode($dashboard_data) : 'null'; ?>,
        userRole: '<?php echo $this->session->userdata('role'); ?>',
        userId: '<?php echo $this->session->userdata('user_id'); ?>'
    };
</script>

<!-- Notification Container -->
<div id="notification-container" class="notification-container fixed top-4 right-4 z-50 space-y-3"></div>

<!-- Connection Status -->
<div id="connectionStatus" class="connection-status connection-online">
    <i data-lucide="wifi" class="w-3 h-3 inline mr-1"></i>
    <span>Online</span>
</div>

<!-- Header Section -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="shield-check" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
                <p class="text-blue-100">Sistem Manajemen Laboratorium</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="bg-white rounded-lg px-4 py-2 border shadow-sm">
                <p class="text-sm text-gray-500">Last Update</p>
                <p class="text-lg font-semibold text-gray-900" id="last-update">Loading...</p>
            </div>
        </div>
    </div>
</div>

<?php if (isset($error_message)): ?>
<div class="p-4 m-6 bg-red-50 border border-red-200 rounded-lg">
    <div class="flex items-center">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-2"></i>
        <p class="text-red-700"><?php echo $error_message; ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Main Content -->
<div class="p-6 space-y-6">
<!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Total Pemeriksaan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow" id="kpi-card-1">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-2">Total Pemeriksaan</p>
                    <p id="kpi-total-exams" class="text-3xl font-bold text-gray-900 mb-1">
                        <?php if (isset($dashboard_data['kpi']['total_examinations'])): ?>
                            <?php echo number_format($dashboard_data['kpi']['total_examinations']); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-16 h-9 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500">30 hari terakhir</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="w-7 h-7 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Pemeriksaan Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow" id="kpi-card-3">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-2">Pending</p>
                    <p id="kpi-pending" class="text-3xl font-bold text-yellow-600 mb-1">
                        <?php if (isset($dashboard_data['kpi']['pending_today'])): ?>
                            <?php echo number_format($dashboard_data['kpi']['pending_today']); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-12 h-9 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500">Menunggu proses</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-7 h-7 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Pendapatan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow" id="kpi-card-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-2">Pendapatan</p>
                    <p id="kpi-revenue" class="text-2xl font-bold text-emerald-600 mb-1">
                        <?php if (isset($dashboard_data['kpi']['monthly_revenue'])): ?>
                            <?php echo 'Rp ' . number_format($dashboard_data['kpi']['monthly_revenue'], 0, ',', '.'); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-24 h-8 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500">Bulan ini</p>
                </div>
                <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-7 h-7 text-emerald-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Users Aktif -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow" id="kpi-card-5">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-2">Users Aktif</p>
                    <p id="kpi-active-users" class="text-3xl font-bold text-purple-600 mb-1">
                        <?php if (isset($dashboard_data['kpi']['active_users'])): ?>
                            <?php echo number_format($dashboard_data['kpi']['active_users']); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-12 h-9 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500">Total pengguna</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-7 h-7 text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Alert Items -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow" id="kpi-card-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-2">Alert Items</p>
                    <p id="kpi-alerts" class="text-3xl font-bold text-red-600 mb-1">
                        <?php if (isset($dashboard_data['kpi']['alert_items'])): ?>
                            <?php echo number_format($dashboard_data['kpi']['alert_items']); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-10 h-9 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500">Perlu perhatian</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-7 h-7 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
        
        <!-- Examination Trend Chart -->
        <div class="lg:col-span-4 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                    <span>Tren Pemeriksaan (30 Hari)</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="examinationTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="lg:col-span-3 space-y-6">
            <!-- User Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
                        <span>Distribusi User</span>
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4" id="user-distribution">
                        <?php if (isset($dashboard_data['user_distribution']) && !empty($dashboard_data['user_distribution'])): ?>
                            <?php 
                            $roleColors = [
                                'admin' => 'bg-blue-500',
                                'administrasi' => 'bg-green-500',
                                'petugas_lab' => 'bg-purple-500',
                                'dokter' => 'bg-orange-500'
                            ];
                            $roleNames = [
                                'admin' => 'Administrator',
                                'administrasi' => 'Administrasi',
                                'petugas_lab' => 'Petugas Lab',
                                'dokter' => 'Dokter'
                            ];
                            ?>
                            <?php foreach ($dashboard_data['user_distribution'] as $user): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 <?php echo isset($roleColors[$user['role']]) ? $roleColors[$user['role']] : 'bg-gray-500'; ?> rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-900"><?php echo isset($roleNames[$user['role']]) ? $roleNames[$user['role']] : $user['role']; ?></span>
                                </div>
                                <span class="text-sm font-semibold text-gray-600"><?php echo $user['count']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Loading skeleton -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-gray-300 rounded-full skeleton"></div>
                                    <span class="skeleton rounded w-20 h-4"></span>
                                </div>
                                <span class="skeleton rounded w-6 h-4"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="activity" class="w-5 h-5 text-green-600"></i>
                        <span>System Health</span>
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4" id="system-health">
                        <?php if (isset($dashboard_data['system_status'])): ?>
                            <?php 
                            $statusColors = [
                                'online' => 'text-green-600 bg-green-100',
                                'warning' => 'text-yellow-600 bg-yellow-100',
                                'offline' => 'text-red-600 bg-red-100'
                            ];
                            $healthItems = [
                                ['name' => 'Database', 'status' => $dashboard_data['system_status']['database']['status'] ?? 'unknown', 'icon' => 'database', 'message' => $dashboard_data['system_status']['database']['message'] ?? 'Unknown'],
                                ['name' => 'Storage', 'status' => $dashboard_data['system_status']['storage']['status'] ?? 'warning', 'icon' => 'hard-drive', 'message' => $dashboard_data['system_status']['storage']['message'] ?? 'Check needed'],
                                ['name' => 'Backup', 'status' => $dashboard_data['system_status']['backup']['status'] ?? 'warning', 'icon' => 'shield', 'message' => $dashboard_data['system_status']['backup']['message'] ?? 'Check needed']
                            ];
                            ?>
                            <?php foreach ($healthItems as $item): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <i data-lucide="<?php echo $item['icon']; ?>" class="w-4 h-4 text-gray-600"></i>
                                    <span class="text-sm font-medium text-gray-900"><?php echo $item['name']; ?></span>
                                </div>
                                <span class="text-xs font-medium px-2 py-1 rounded-full <?php echo isset($statusColors[$item['status']]) ? $statusColors[$item['status']] : 'text-gray-600 bg-gray-100'; ?>" title="<?php echo $item['message']; ?>">
                                    <?php echo strtoupper($item['status']); ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Loading skeleton -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 bg-gray-300 rounded skeleton"></div>
                                    <span class="skeleton rounded w-16 h-4"></span>
                                </div>
                                <span class="skeleton rounded w-12 h-5"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Pending Examinations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                        <span>Antrian Pemeriksaan</span>
                    </h3>
                    <span id="pending-count" class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-600 rounded-full">
                        <?php echo isset($dashboard_data['pending_examinations']) ? count($dashboard_data['pending_examinations']) . ' pending' : 'Loading...'; ?>
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <th class="pb-3">Nomor</th>
                                <th class="pb-3">Pasien</th>
                                <th class="pb-3">Jenis</th>
                                <th class="pb-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="pending-examinations" class="divide-y divide-gray-200">
                            <?php if (isset($dashboard_data['pending_examinations']) && !empty($dashboard_data['pending_examinations'])): ?>
                                <?php foreach ($dashboard_data['pending_examinations'] as $exam): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 text-sm font-medium text-gray-900"><?php echo $exam['nomor_pemeriksaan']; ?></td>
                                    <td class="py-3 text-sm text-gray-600"><?php echo $exam['nama_pasien']; ?></td>
                                    <td class="py-3 text-sm text-gray-600"><?php echo $exam['jenis_pemeriksaan']; ?></td>
                                    <td class="py-3 text-sm text-gray-500"><?php echo date('d M', strtotime($exam['tanggal_pemeriksaan'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Loading skeleton -->
                                <tr>
                                    <td class="py-3"><span class="skeleton rounded w-20 h-4"></span></td>
                                    <td class="py-3"><span class="skeleton rounded w-24 h-4"></span></td>
                                    <td class="py-3"><span class="skeleton rounded w-16 h-4"></span></td>
                                    <td class="py-3"><span class="skeleton rounded w-12 h-4"></span></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Patients -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="user-plus" class="w-5 h-5 text-blue-600"></i>
                    <span>Pasien Terbaru</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4" id="recent-patients">
                    <?php if (isset($dashboard_data['recent_patients']) && !empty($dashboard_data['recent_patients'])): ?>
                        <?php foreach ($dashboard_data['recent_patients'] as $patient): ?>
                        <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">
                                    <?php 
                                    $names = explode(' ', $patient['nama']);
                                    echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                    ?>
                                </span>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900"><?php echo $patient['nama']; ?></p>
                                <p class="text-sm text-gray-500">NIK: ****<?php echo substr($patient['nik'], -4); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500"><?php echo isset($patient['time_ago']) ? $patient['time_ago'] : 'Baru'; ?></p>
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    <?php echo isset($patient['status']) ? $patient['status'] : 'Baru registrasi'; ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Loading skeleton -->
                        <div class="flex items-center space-x-4 p-3 rounded-lg">
                            <div class="w-10 h-10 bg-gray-300 rounded-full skeleton"></div>
                            <div class="flex-1">
                                <div class="skeleton rounded w-32 h-4 mb-2"></div>
                                <div class="skeleton rounded w-24 h-3"></div>
                            </div>
                            <div class="text-right">
                                <div class="skeleton rounded w-16 h-3 mb-2"></div>
                                <div class="skeleton rounded w-20 h-5"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Alerts and Activity Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Inventory Alerts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="package-x" class="w-5 h-5 text-red-600"></i>
                        <span>Alert Inventory</span>
                    </h3>
                    <span id="alert-count" class="px-2 py-1 text-xs font-medium bg-red-100 text-red-600 rounded-full">
                        <?php echo isset($dashboard_data['inventory_alerts']) ? count($dashboard_data['inventory_alerts']) . ' alerts' : 'Loading...'; ?>
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-80 overflow-y-auto custom-scrollbar" id="inventory-alerts">
                    <?php if (isset($dashboard_data['inventory_alerts']) && !empty($dashboard_data['inventory_alerts'])): ?>
                        <?php 
                        $levelColors = [
                            'Urgent' => 'bg-red-100 text-red-800 border-red-200',
                            'Warning' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'Low Stock' => 'bg-orange-100 text-orange-800 border-orange-200'
                        ];
                        $typeIcons = [
                            'reagen' => 'flask-conical',
                            'alat' => 'wrench'
                        ];
                        ?>
                        <?php foreach ($dashboard_data['inventory_alerts'] as $alert): ?>
                        <div class="p-3 border rounded-lg <?php echo isset($levelColors[$alert['alert_level']]) ? $levelColors[$alert['alert_level']] : 'bg-gray-100 text-gray-800 border-gray-200'; ?> hover:shadow-sm transition-shadow">
                            <div class="flex items-start space-x-3">
                                <i data-lucide="<?php echo isset($typeIcons[$alert['tipe_inventory']]) ? $typeIcons[$alert['tipe_inventory']] : 'package'; ?>" class="w-4 h-4 mt-0.5"></i>
                                <div class="flex-1">
                                    <p class="font-medium text-sm"><?php echo $alert['nama_item']; ?></p>
                                    <p class="text-xs mt-1"><?php echo $alert['message']; ?></p>
                                </div>
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-white">
                                    <?php echo $alert['alert_level']; ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Loading skeleton -->
                        <div class="p-3 border rounded-lg bg-gray-50">
                            <div class="flex items-start space-x-3">
                                <div class="w-4 h-4 bg-gray-300 rounded skeleton mt-0.5"></div>
                                <div class="flex-1">
                                    <div class="skeleton rounded w-40 h-4 mb-1"></div>
                                    <div class="skeleton rounded w-32 h-3"></div>
                                </div>
                                <div class="skeleton rounded w-16 h-5"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity Logs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="activity" class="w-5 h-5 text-gray-600"></i>
                    <span>Aktivitas Terbaru</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-80 overflow-y-auto custom-scrollbar" id="recent-activities">
                    <?php if (isset($dashboard_data['recent_activities']) && !empty($dashboard_data['recent_activities'])): ?>
                        <?php 
                        $typeColors = [
                            'success' => 'text-green-600',
                            'warning' => 'text-yellow-600', 
                            'info' => 'text-blue-600',
                            'error' => 'text-red-600'
                        ];
                        $typeIcons = [
                            'success' => 'check-circle',
                            'warning' => 'alert-circle',
                            'info' => 'info',
                            'error' => 'x-circle'
                        ];
                        ?>
                        <?php foreach ($dashboard_data['recent_activities'] as $activity): ?>
                        <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                            <i data-lucide="<?php echo isset($typeIcons[$activity['type']]) ? $typeIcons[$activity['type']] : 'activity'; ?>" class="w-4 h-4 mt-0.5 <?php echo isset($typeColors[$activity['type']]) ? $typeColors[$activity['type']] : 'text-gray-600'; ?>"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900"><?php echo isset($activity['nama_user']) ? $activity['nama_user'] : 'System'; ?></p>
                                <p class="text-sm text-gray-600"><?php echo $activity['activity']; ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?php echo isset($activity['time_ago']) ? $activity['time_ago'] : 'Baru saja'; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Loading skeleton -->
                        <div class="flex items-start space-x-3 p-3 rounded-lg">
                            <div class="w-4 h-4 bg-gray-300 rounded skeleton mt-0.5"></div>
                            <div class="flex-1">
                                <div class="skeleton rounded w-24 h-4 mb-1"></div>
                                <div class="skeleton rounded w-40 h-4 mb-1"></div>
                                <div class="skeleton rounded w-16 h-3"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let examinationChart = null;
let dashboardData = window.labsysConfig.initialData || {};
let refreshInterval = null;
let isOnline = true;
let wsConnection = null;

// Enhanced notification system
function showNotification(type, message, duration = 5000) {
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'notification-container fixed top-4 right-4 z-50 space-y-3';
        document.body.appendChild(container);
    }
    
    const notificationId = 'notification-' + Date.now() + Math.random().toString(36).substr(2, 9);
    const notification = document.createElement('div');
    
    const config = {
        success: {
            icon: 'check-circle',
            iconColor: 'text-green-600',
            textColor: 'text-green-800',
            className: 'notification-success border-l-4'
        },
        error: {
            icon: 'alert-circle',
            iconColor: 'text-red-600',
            textColor: 'text-red-800',
            className: 'notification-error border-l-4'
        },
        warning: {
            icon: 'alert-triangle',
            iconColor: 'text-yellow-600',
            textColor: 'text-yellow-800',
            className: 'notification-warning border-l-4'
        },
        info: {
            icon: 'info',
            iconColor: 'text-blue-600',
            textColor: 'text-blue-800',
            className: 'notification-info border-l-4'
        }
    };
    
    const currentConfig = config[type] || config.info;
    
    notification.id = notificationId;
    notification.className = `notification ${currentConfig.className} rounded-lg p-4 relative overflow-hidden toast-enter`;
    notification.setAttribute('role', 'alert');
    notification.setAttribute('aria-live', 'polite');
    notification.setAttribute('aria-atomic', 'true');
    
    notification.innerHTML = `
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <i data-lucide="${currentConfig.icon}" class="w-5 h-5 ${currentConfig.iconColor}"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium ${currentConfig.textColor}">${message}</p>
            </div>
            <div class="flex-shrink-0">
                <button onclick="removeNotification('${notificationId}')" 
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        aria-label="Close notification">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div class="toast-progress ${currentConfig.iconColor}"></div>
    `;
    
    container.appendChild(notification);
    lucide.createIcons();
    
    setTimeout(() => {
        notification.classList.remove('toast-enter');
        notification.classList.add('toast-enter-active');
    }, 10);
    
    const timeoutId = setTimeout(() => {
        removeNotification(notificationId);
    }, duration);
    
    notification.dataset.timeoutId = timeoutId;
    
    notification.addEventListener('mouseenter', () => {
        clearTimeout(timeoutId);
        const progressBar = notification.querySelector('.toast-progress');
        if (progressBar) {
            progressBar.style.animationPlayState = 'paused';
        }
    });
    
    notification.addEventListener('mouseleave', () => {
        const newTimeoutId = setTimeout(() => {
            removeNotification(notificationId);
        }, 1000);
        notification.dataset.timeoutId = newTimeoutId;
        
        const progressBar = notification.querySelector('.toast-progress');
        if (progressBar) {
            progressBar.style.animationPlayState = 'running';
        }
    });
}

function removeNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        const timeoutId = notification.dataset.timeoutId;
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        
        notification.classList.add('toast-exit');
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.parentElement.removeChild(notification);
                
                const container = document.getElementById('notification-container');
                if (container && container.children.length === 0) {
                    container.parentElement.removeChild(container);
                }
            }
        }, 300);
    }
}

// Connection status manager
class ConnectionManager {
    constructor() {
        this.statusElement = document.getElementById('connectionStatus');
        this.isOnline = true;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
    }

    setStatus(status) {
        this.isOnline = status === 'online';
        this.statusElement.className = `connection-status connection-${status}`;
        
        const statusMap = {
            online: { icon: 'wifi', text: 'Online' },
            offline: { icon: 'wifi-off', text: 'Offline' },
            reconnecting: { icon: 'loader', text: 'Reconnecting...' }
        };

        const statusInfo = statusMap[status];
        this.statusElement.innerHTML = `
            <i data-lucide="${statusInfo.icon}" class="w-3 h-3 inline mr-1 ${status === 'reconnecting' ? 'loading' : ''}"></i>
            <span>${statusInfo.text}</span>
        `;

        lucide.createIcons({ nameAttr: 'data-lucide' });
    }

    handleOffline() {
        this.setStatus('offline');
        showNotification('error', 'Koneksi terputus, mencoba menyambung kembali...', 3000);
        this.attemptReconnect();
    }

    handleOnline() {
        this.setStatus('online');
        this.reconnectAttempts = 0;
        showNotification('success', 'Koneksi berhasil dipulihkan', 3000);
        loadDashboardData();
    }

    attemptReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            showNotification('error', 'Gagal menyambung kembali. Silakan refresh halaman.', 10000);
            return;
        }

        this.setStatus('reconnecting');
        this.reconnectAttempts++;

        setTimeout(() => {
            fetch(window.labsysConfig.baseUrl + 'admin/ajax_get_dashboard_data', {
                method: 'GET',
                cache: 'no-cache'
            })
            .then(response => {
                if (response.ok) {
                    this.handleOnline();
                } else {
                    this.attemptReconnect();
                }
            })
            .catch(() => {
                this.attemptReconnect();
            });
        }, 2000 * this.reconnectAttempts);
    }
}

// Initialize connection manager
const connectionManager = new ConnectionManager();

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    initializeCharts();
    
    // If we have initial data, update everything
    if (window.labsysConfig.hasInitialData && dashboardData) {
        updateAllComponents();
        updateLastUpdateTime();
    } else {
        // Otherwise load data
        loadDashboardData();
    }
    
    // Auto refresh every 2 minutes
    refreshInterval = setInterval(loadDashboardData, 2 * 60 * 1000);

    // Handle online/offline events
    window.addEventListener('online', () => connectionManager.handleOnline());
    window.addEventListener('offline', () => connectionManager.handleOffline());
    
    // Show welcome notification
    setTimeout(() => {
        showNotification('info', 'Dashboard admin siap digunakan!', 3000);
    }, 1000);
});

// Initialize charts
function initializeCharts() {
    const ctx = document.getElementById('examinationTrendChart').getContext('2d');
    examinationChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Total Pemeriksaan',
                data: [],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Selesai',
                data: [],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.1
            }, {
                label: 'Pending',
                data: [],
                borderColor: 'rgb(251, 191, 36)',
                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    cornerRadius: 8
                }
            }
        }
    });
}

// Load dashboard data
async function loadDashboardData() {
    try {
        showLoadingState(true);
        
        const response = await fetch(window.labsysConfig.baseUrl + 'admin/ajax_get_dashboard_data', {
            method: 'GET',
            headers: {
                'Cache-Control': 'no-cache'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success) {
            dashboardData = result.data;
            updateAllComponents();
            updateLastUpdateTime();
            
            if (!connectionManager.isOnline) {
                showNotification('success', 'Data dashboard berhasil diperbarui');
            }
        } else {
            throw new Error(result.message || 'Gagal memuat data dashboard');
        }
        
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        connectionManager.handleOffline();
        showNotification('error', 'Gagal memperbarui data dashboard');
    } finally {
        showLoadingState(false);
    }
}

// Show/hide loading state
function showLoadingState(loading) {
    const refreshIcon = document.getElementById('refreshIcon');
    if (refreshIcon) {
        if (loading) {
            refreshIcon.classList.add('loading');
        } else {
            refreshIcon.classList.remove('loading');
        }
    }
}

// Update all dashboard components
function updateAllComponents() {
    updateKPICards();
    updateExaminationChart();
    updateUserDistribution();
    updateSystemHealth();
    updatePendingExaminations();
    updateRecentPatients();
    updateInventoryAlerts();
    updateRecentActivities();
}

// Update KPI cards with animation
function updateKPICards() {
    if (!dashboardData.kpi) return;
    
    const { kpi } = dashboardData;
    
    const updates = [
        { id: 'kpi-total-exams', value: kpi.total_examinations?.toLocaleString('id-ID') || '0', card: 'kpi-card-1' },
        { id: 'kpi-completed', value: kpi.completed_today?.toLocaleString('id-ID') || '0', card: 'kpi-card-2' },
        { id: 'kpi-pending', value: kpi.pending_today?.toLocaleString('id-ID') || '0', card: 'kpi-card-3' },
        { id: 'kpi-revenue', value: formatCurrency(kpi.monthly_revenue || 0), card: 'kpi-card-4' },
        { id: 'kpi-active-users', value: kpi.active_users?.toLocaleString('id-ID') || '0', card: 'kpi-card-5' },
        { id: 'kpi-alerts', value: kpi.alert_items?.toLocaleString('id-ID') || '0', card: 'kpi-card-6' }
    ];

    updates.forEach(update => {
        const element = document.getElementById(update.id);
        const card = document.getElementById(update.card);
        
        if (element && card) {
            card.classList.add('pulse-update');
            setTimeout(() => card.classList.remove('pulse-update'), 500);
            element.innerHTML = update.value;
        }
    });
}

// Update examination chart
function updateExaminationChart() {
    if (!dashboardData.examination_trend || !examinationChart) return;
    
    const trendData = dashboardData.examination_trend;
    
    const labels = trendData.map(day => {
        const date = new Date(day.exam_date);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    });
    
    const totalData = trendData.map(day => parseInt(day.total) || 0);
    const completedData = trendData.map(day => parseInt(day.completed) || 0);
    const pendingData = trendData.map(day => parseInt(day.pending) || 0);
    
    examinationChart.data.labels = labels;
    examinationChart.data.datasets[0].data = totalData;
    examinationChart.data.datasets[1].data = completedData;
    examinationChart.data.datasets[2].data = pendingData;
    
    examinationChart.update('active');
}

// Update user distribution
function updateUserDistribution() {
    if (!dashboardData.user_distribution) return;
    
    const container = document.getElementById('user-distribution');
    const distribution = dashboardData.user_distribution;
    
    const roleColors = {
        'admin': 'bg-blue-500',
        'administrasi': 'bg-green-500',
        'petugas_lab': 'bg-purple-500',
        'dokter': 'bg-orange-500'
    };
    
    const roleNames = {
        'admin': 'Administrator',
        'administrasi': 'Administrasi',
        'petugas_lab': 'Petugas Lab',
        'dokter': 'Dokter'
    };
    
    container.innerHTML = distribution.map(user => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 ${roleColors[user.role] || 'bg-gray-500'} rounded-full"></div>
                <span class="text-sm font-medium text-gray-900">${roleNames[user.role] || user.role}</span>
            </div>
            <span class="text-sm font-semibold text-gray-600">${user.count}</span>
        </div>
    `).join('');
}

// Update system health
function updateSystemHealth() {
    if (!dashboardData.system_status) return;
    
    const container = document.getElementById('system-health');
    const status = dashboardData.system_status;
    
    const statusColors = {
        'online': 'text-green-600 bg-green-100',
        'warning': 'text-yellow-600 bg-yellow-100',
        'offline': 'text-red-600 bg-red-100'
    };
    
    const healthItems = [
        { name: 'Database', status: status.database?.status || 'unknown', icon: 'database', message: status.database?.message || 'Unknown' },
        { name: 'Storage', status: status.storage?.status || 'warning', icon: 'hard-drive', message: status.storage?.message || 'Check needed' },
        { name: 'Backup', status: status.backup?.status || 'warning', icon: 'shield', message: status.backup?.message || 'Check needed' }
    ];
    
    container.innerHTML = healthItems.map(item => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="flex items-center space-x-3">
                <i data-lucide="${item.icon}" class="w-4 h-4 text-gray-600"></i>
                <span class="text-sm font-medium text-gray-900">${item.name}</span>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-full ${statusColors[item.status] || 'text-gray-600 bg-gray-100'}" title="${item.message}">
                ${item.status.toUpperCase()}
            </span>
        </div>
    `).join('');
    
    lucide.createIcons();
}

// Update pending examinations
function updatePendingExaminations() {
    if (!dashboardData.pending_examinations) return;
    
    const tbody = document.getElementById('pending-examinations');
    const countElement = document.getElementById('pending-count');
    const examinations = dashboardData.pending_examinations;
    
    countElement.textContent = `${examinations.length} pending`;
    
    if (examinations.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-500">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                    <p>Tidak ada pemeriksaan pending</p>
                </td>
            </tr>
        `;
    } else {
        tbody.innerHTML = examinations.map(exam => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-3 text-sm font-medium text-gray-900">${exam.nomor_pemeriksaan}</td>
                <td class="py-3 text-sm text-gray-600">${exam.nama_pasien}</td>
                <td class="py-3 text-sm text-gray-600">${exam.jenis_pemeriksaan}</td>
                <td class="py-3 text-sm text-gray-500">${formatDate(exam.tanggal_pemeriksaan)}</td>
            </tr>
        `).join('');
    }
    
    lucide.createIcons();
}

// Update recent patients
function updateRecentPatients() {
    if (!dashboardData.recent_patients) return;
    
    const container = document.getElementById('recent-patients');
    const patients = dashboardData.recent_patients;
    
    if (patients.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                <p>Belum ada pasien terbaru</p>
            </div>
        `;
    } else {
        container.innerHTML = patients.map(patient => `
            <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-semibold text-white">
                        ${patient.nama.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()}
                    </span>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-900">${patient.nama}</p>
                    <p class="text-sm text-gray-500">NIK: ****${patient.nik.slice(-4)}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">${patient.time_ago}</p>
                    <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                        ${patient.status}
                    </span>
                </div>
            </div>
        `).join('');
    }
    
    lucide.createIcons();
}

// Update inventory alerts
function updateInventoryAlerts() {
    if (!dashboardData.inventory_alerts) return;
    
    const container = document.getElementById('inventory-alerts');
    const countElement = document.getElementById('alert-count');
    const alerts = dashboardData.inventory_alerts;
    
    countElement.textContent = `${alerts.length} alerts`;
    
    if (alerts.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-400"></i>
                <p>Semua inventory dalam kondisi baik</p>
            </div>
        `;
    } else {
        const levelColors = {
            'Urgent': 'bg-red-100 text-red-800 border-red-200',
            'Warning': 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'Low Stock': 'bg-orange-100 text-orange-800 border-orange-200'
        };
        
        const typeIcons = {
            'reagen': 'flask-conical',
            'alat': 'wrench'
        };
        
        container.innerHTML = alerts.map(alert => `
            <div class="p-3 border rounded-lg ${levelColors[alert.alert_level] || 'bg-gray-100 text-gray-800 border-gray-200'} hover:shadow-sm transition-shadow">
                <div class="flex items-start space-x-3">
                    <i data-lucide="${typeIcons[alert.tipe_inventory] || 'package'}" class="w-4 h-4 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-medium text-sm">${alert.nama_item}</p>
                        <p class="text-xs mt-1">${alert.message}</p>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-white">
                        ${alert.alert_level}
                    </span>
                </div>
            </div>
        `).join('');
    }
    
    lucide.createIcons();
}

// Update recent activities
function updateRecentActivities() {
    if (!dashboardData.recent_activities) return;
    
    const container = document.getElementById('recent-activities');
    const activities = dashboardData.recent_activities;
    
    if (activities.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="activity" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                <p>Belum ada aktivitas terbaru</p>
            </div>
        `;
    } else {
        const typeColors = {
            'success': 'text-green-600',
            'warning': 'text-yellow-600',
            'info': 'text-blue-600',
            'error': 'text-red-600'
        };
        
        const typeIcons = {
            'success': 'check-circle',
            'warning': 'alert-circle',
            'info': 'info',
            'error': 'x-circle'
        };
        
        container.innerHTML = activities.map(activity => `
            <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                <i data-lucide="${typeIcons[activity.type] || 'activity'}" class="w-4 h-4 mt-0.5 ${typeColors[activity.type] || 'text-gray-600'}"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">${activity.nama_user || 'System'}</p>
                    <p class="text-sm text-gray-600">${activity.activity}</p>
                    <p class="text-xs text-gray-500 mt-1">${activity.time_ago}</p>
                </div>
            </div>
        `).join('');
    }
    
    lucide.createIcons();
}

// Update last update time
function updateLastUpdateTime() {
    const now = new Date();
    document.getElementById('last-update').textContent = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Refresh dashboard manually
function refreshDashboard() {
    showNotification('info', 'Memperbarui data dashboard...', 2000);
    loadDashboardData();
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short'
    });
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
    if (wsConnection) {
        wsConnection.close();
    }
});

// Handle network errors
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    if (event.reason instanceof TypeError && event.reason.message.includes('fetch')) {
        connectionManager.handleOffline();
    }
});

// Error handling for uncaught errors
window.addEventListener('error', function(event) {
    console.error('Uncaught error:', event.error);
    showNotification('error', 'Terjadi kesalahan sistem. Silakan refresh halaman.');
});
</script>

</body>
</html>