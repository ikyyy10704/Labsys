<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Dashboard Administrasi'; ?> - Labsys</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Custom scrollbar - sama dengan admin dashboard */
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

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        .pulse-update {
            animation: pulse 0.5s ease-in-out;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .notification {
            max-width: 400px;
            min-width: 320px;
            font-size: 14px;
            line-height: 1.5;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

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

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Status badges */
        .status-pending { @apply bg-yellow-100 text-yellow-800; }
        .status-progress { @apply bg-blue-100 text-blue-800; }
        .status-selesai { @apply bg-green-100 text-green-800; }
        .status-cancelled { @apply bg-red-100 text-red-800; }
        
        .status-lunas { @apply bg-green-100 text-green-800; }
        .status-belum { @apply bg-red-100 text-red-800; }
        .status-cicilan { @apply bg-yellow-100 text-yellow-800; }
    </style>
</head>
<body class="bg-gray-50">

<!-- Pass PHP data to JavaScript -->
<script>
    window.administrasiConfig = {
        baseUrl: '<?php echo base_url(); ?>',
        hasInitialData: <?php echo isset($financial_summary) ? 'true' : 'false'; ?>,
        userId: '<?php echo $this->session->userdata('user_id'); ?>'
    };
</script>

<!-- Notification Container -->
<div id="notification-container" class="notification-container fixed top-4 right-4 z-50 space-y-3"></div>

<!-- Header Section - MENGIKUTI TEMA BIRU DARI ADMIN -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="clipboard-list" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Dashboard Administrasi</h1>
                <p class="text-blue-100">Manajemen Pasien & Keuangan</p>
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

<!-- Main Content - FULL WIDTH tanpa container -->
<div class="p-6 space-y-6">

    <!-- KPI Cards - 5 Metrik Utama (Ditambah Total Permintaan) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        
        <!-- Total Pasien -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover" id="kpi-card-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pasien</p>
                    <p id="kpi-total-patients" class="text-3xl font-bold text-gray-900">
                        <?php if (isset($registration_stats['total_pasien'])): ?>
                            <?php echo number_format($registration_stats['total_pasien']); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-16 h-9 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-blue-600 mt-1 font-medium">
                        +<?php echo $registration_stats['registrasi_hari_ini'] ?? 0; ?> hari ini
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-7 h-7 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Permintaan (BARU) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover" id="kpi-card-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Permintaan</p>
                    <p id="kpi-total-requests" class="text-3xl font-bold text-orange-600">
                        <?php if (isset($examination_stats['total'])): ?>
                            <?php echo number_format($examination_stats['total']); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-16 h-9 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <?php echo $examination_stats['pending'] ?? 0; ?> pending
                    </p>
                </div>
                <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-7 h-7 text-orange-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Pendapatan Bulan Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover" id="kpi-card-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendapatan Bulan Ini</p>
                    <p id="kpi-monthly-revenue" class="text-3xl font-bold text-emerald-600">
                        <?php if (isset($monthly_revenue['revenue'])): ?>
                            <?php echo 'Rp ' . number_format($monthly_revenue['revenue'], 0, ',', '.'); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-24 h-9 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <?php echo $monthly_revenue['invoice_count'] ?? 0; ?> transaksi
                    </p>
                </div>
                <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-7 h-7 text-emerald-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Invoice Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover" id="kpi-card-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Invoice Pending</p>
                    <p id="kpi-pending-invoices" class="text-3xl font-bold text-red-600">
                        <?php if (isset($financial_summary['unpaid_invoices'])): ?>
                            <?php echo number_format($financial_summary['unpaid_invoices']); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-12 h-9 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Rp <?php echo number_format($financial_summary['pending_revenue'] ?? 0, 0, ',', '.'); ?>
                    </p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-7 h-7 text-red-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Registrasi Hari Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover" id="kpi-card-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Registrasi Hari Ini</p>
                    <p id="kpi-today-registrations" class="text-3xl font-bold text-purple-600">
                        <?php if (isset($registration_stats['registrasi_hari_ini'])): ?>
                            <?php echo number_format($registration_stats['registrasi_hari_ini']); ?>
                        <?php else: ?>
                            <span class="skeleton rounded w-10 h-9 inline-block"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <?php echo $registration_stats['registrasi_minggu_ini'] ?? 0; ?> minggu ini
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-7 h-7 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row - FULL WIDTH -->
    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
        
        <!-- Grafik Pendapatan - 4 kolom -->
        <div class="lg:col-span-4 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-blue-600"></i>
                        <span>Tren Pendapatan (7 Hari Terakhir)</span>
                    </h3>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            Total: Rp <span id="total-revenue-week"><?php echo number_format(($today_revenue ?? 0) + ($weekly_revenue ?? 0), 0, ',', '.'); ?></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Stats Sidebar - 3 kolom -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Metode Pembayaran Stats (DIUBAH DARI "JENIS PEMBAYARAN") -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="credit-card" class="w-5 h-5 text-blue-600"></i>
                        <span>Metode Pembayaran</span>
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4" id="payment-methods-stats">
                        <?php if (isset($payment_method_stats) && !empty($payment_method_stats)): ?>
                            <?php 
                            $methodColors = [
                                'tunai' => ['bg' => 'from-green-50 to-green-100', 'dot' => 'bg-green-500'],
                                'transfer' => ['bg' => 'from-blue-50 to-blue-100', 'dot' => 'bg-blue-500'],
                                'kartu_kredit' => ['bg' => 'from-purple-50 to-purple-100', 'dot' => 'bg-purple-500'],
                                'kartu_debit' => ['bg' => 'from-indigo-50 to-indigo-100', 'dot' => 'bg-indigo-500'],
                                'e-wallet' => ['bg' => 'from-orange-50 to-orange-100', 'dot' => 'bg-orange-500']
                            ];
                            
                            $methodLabels = [
                                'tunai' => 'Tunai',
                                'transfer' => 'Transfer Bank',
                                'kartu_kredit' => 'Kartu Kredit',
                                'kartu_debit' => 'Kartu Debit',
                                'e-wallet' => 'E-Wallet'
                            ];
                            ?>
                            <?php foreach ($payment_method_stats as $method): ?>
                                <?php 
                                $methodKey = strtolower(str_replace([' ', '-'], '_', $method['metode_pembayaran']));
                                $colors = $methodColors[$methodKey] ?? ['bg' => 'from-gray-50 to-gray-100', 'dot' => 'bg-gray-500'];
                                $label = $methodLabels[$methodKey] ?? ucwords(str_replace('_', ' ', $method['metode_pembayaran']));
                                ?>
                                <div class="flex items-center justify-between p-3 bg-gradient-to-r <?php echo $colors['bg']; ?> rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 <?php echo $colors['dot']; ?> rounded-full"></div>
                                        <span class="text-sm font-medium text-gray-900"><?php echo $label; ?></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-gray-900"><?php echo number_format($method['count']); ?></span>
                                        <p class="text-xs text-gray-500">Rp <?php echo number_format($method['total_amount'], 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full skeleton"></div>
                                    <span class="skeleton rounded w-20 h-4"></span>
                                </div>
                                <div class="text-right">
                                    <span class="skeleton rounded w-6 h-4"></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Payment Rate -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="percent" class="w-5 h-5 text-emerald-600"></i>
                        <span>Payment Rate</span>
                    </h3>
                </div>
                <div class="p-6">
                    <?php 
                    $payment_rate = 0;
                    if (isset($financial_summary['total_invoices']) && $financial_summary['total_invoices'] > 0) {
                        $payment_rate = ($financial_summary['paid_invoices'] / $financial_summary['total_invoices']) * 100;
                    }
                    ?>
                    <div class="text-center">
                        <div class="relative inline-flex items-center justify-center w-32 h-32">
                            <svg class="transform -rotate-90 w-32 h-32">
                                <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="8" fill="none" />
                                <circle cx="64" cy="64" r="56" stroke="#10b981" stroke-width="8" fill="none"
                                    stroke-dasharray="<?php echo 2 * 3.14159 * 56; ?>"
                                    stroke-dashoffset="<?php echo 2 * 3.14159 * 56 * (1 - $payment_rate / 100); ?>"
                                    stroke-linecap="round" />
                            </svg>
                            <div class="absolute">
                                <span class="text-3xl font-bold text-gray-900"><?php echo number_format($payment_rate, 1); ?>%</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-4">
                            <?php echo $financial_summary['paid_invoices'] ?? 0; ?> dari <?php echo $financial_summary['total_invoices'] ?? 0; ?> invoice lunas
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row - FULL WIDTH -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Registrasi Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-blue-600"></i>
                        <span>Registrasi Terbaru</span>
                    </h3>
                    <a href="<?php echo base_url('administrasi/patient_management'); ?>" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Lihat Semua →
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <?php if (isset($recent_registrations) && !empty($recent_registrations)): ?>
                        <?php foreach ($recent_registrations as $patient): ?>
                        <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150 border border-gray-100">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-white">
                                    <?php 
                                    $names = explode(' ', $patient['nama']);
                                    echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                    ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 truncate"><?php echo $patient['nama']; ?></p>
                                <p class="text-sm text-gray-500">NIK: <?php echo $patient['nik']; ?></p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                        <?php echo $patient['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?>
                                    </span>
                                    <span class="text-xs text-gray-500"><?php echo $patient['telepon']; ?></span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500"><?php echo date('d M Y', strtotime($patient['created_at'])); ?></p>
                                <p class="text-xs font-medium text-blue-600"><?php echo $patient['nomor_registrasi']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                            <p>Belum ada registrasi hari ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pembayaran Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                        <i data-lucide="dollar-sign" class="w-5 h-5 text-red-600"></i>
                        <span>Pembayaran Pending</span>
                    </h3>
                    <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                        <?php echo isset($pending_payments) ? count($pending_payments) : 0; ?> pending
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <?php if (isset($pending_payments) && !empty($pending_payments)): ?>
                        <?php foreach ($pending_payments as $invoice): ?>
                        <div class="p-4 border border-red-100 bg-red-50 rounded-lg hover:shadow-sm transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="font-semibold text-gray-900"><?php echo $invoice['nomor_invoice']; ?></span>
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                                            <?php echo strtoupper($invoice['jenis_pembayaran']); ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700"><?php echo $invoice['nama_pasien']; ?></p>
                                    <p class="text-xs text-gray-500"><?php echo $invoice['jenis_pemeriksaan']; ?></p>
                                </div>
                                <div class="text-right flex-shrink-0 ml-4">
                                    <p class="text-lg font-bold text-red-600">
                                        Rp <?php echo number_format($invoice['total_biaya'], 0, ',', '.'); ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?php echo date('d M Y', strtotime($invoice['tanggal_invoice'])); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-2 text-green-400"></i>
                            <p>Semua invoice sudah lunas!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pembayaran Terbaru - FULL WIDTH TABLE -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-green-600"></i>
                    <span>Pembayaran Terbaru</span>
                </h3>
                <a href="<?php echo base_url('administrasi/financial_reports'); ?>" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Lihat Semua →
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            <th class="pb-3">Invoice</th>
                            <th class="pb-3">Pasien</th>
                            <th class="pb-3">Jenis</th>
                            <th class="pb-3">Jumlah</th>
                            <th class="pb-3">Metode</th>
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (isset($recent_payments) && !empty($recent_payments)): ?>
                            <?php foreach ($recent_payments as $payment): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4">
                                    <span class="text-sm font-medium text-blue-600"><?php echo $payment['nomor_invoice']; ?></span>
                                </td>
                                <td class="py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo $payment['nama_pasien']; ?></p>
                                        <p class="text-xs text-gray-500"><?php echo $payment['nik']; ?></p>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">
                                        <?php echo strtoupper($payment['jenis_pembayaran']); ?>
                                    </span>
                                </td>
                                <td class="py-4">
                                    <span class="text-sm font-semibold text-gray-900">
                                        Rp <?php echo number_format($payment['total_biaya'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td class="py-4">
                                    <span class="text-sm text-gray-600"><?php echo ucfirst($payment['metode_pembayaran'] ?? '-'); ?></span>
                                </td>
                                <td class="py-4">
                                    <span class="text-sm text-gray-500"><?php echo date('d M Y', strtotime($payment['tanggal_pembayaran'])); ?></span>
                                </td>
                                <td class="py-4">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Lunas
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                    <p>Belum ada pembayaran hari ini</p>
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
// Initialize icons
lucide.createIcons();

// Initialize revenue chart
let revenueChart = null;

function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Data dummy untuk 7 hari terakhir (akan di-replace dengan data real dari AJAX)
    const today = new Date();
    const labels = [];
    const data = [];
    
    for (let i = 6; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString('id-ID', { weekday: 'short' }));
        data.push(0); // Will be updated via AJAX
    }
    
    revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan',
                data: data,
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000) + 'jt';
                            }
                            return 'Rp ' + (value / 1000) + 'k';
                        }
                    }
                }
            }
        }
    });
    
    // Load real data
    loadRevenueData();
}

// Load revenue data via AJAX
async function loadRevenueData() {
    try {
        const response = await fetch(window.administrasiConfig.baseUrl + 'administrasi/ajax_get_revenue_chart_data');
        if (response.ok) {
            const result = await response.json();
            if (result.success && result.data) {
                updateRevenueChart(result.data);
            }
        }
    } catch (error) {
        console.error('Error loading revenue data:', error);
    }
}

// Update revenue chart with real data
function updateRevenueChart(data) {
    if (!revenueChart || !data) return;
    
    const labels = data.map(day => {
        const date = new Date(day.date);
        return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
    });
    
    const amounts = data.map(day => parseFloat(day.total) || 0);
    
    revenueChart.data.labels = labels;
    revenueChart.data.datasets[0].data = amounts;
    revenueChart.update('active');
    
    // Update total
    const total = amounts.reduce((sum, val) => sum + val, 0);
    const totalElement = document.getElementById('total-revenue-week');
    if (totalElement) {
        totalElement.textContent = total.toLocaleString('id-ID');
    }
}

// Update last update time
function updateLastUpdateTime() {
    const now = new Date();
    document.getElementById('last-update').textContent = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Refresh dashboard
function refreshDashboard() {
    const refreshIcon = document.getElementById('refreshIcon');
    refreshIcon.classList.add('loading');
    
    loadRevenueData();
    
    setTimeout(() => {
        updateLastUpdateTime();
        refreshIcon.classList.remove('loading');
        showNotification('success', 'Data dashboard berhasil diperbarui');
    }, 1000);
}

// Notification system (sama dengan admin dashboard)
function showNotification(type, message, duration = 5000) {
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'notification-container fixed top-4 right-4 z-50 space-y-3';
        document.body.appendChild(container);
    }
    
    const config = {
        success: { icon: 'check-circle', iconColor: 'text-green-600', className: 'notification-success border-l-4' },
        error: { icon: 'alert-circle', iconColor: 'text-red-600', className: 'notification-error border-l-4' },
        warning: { icon: 'alert-triangle', iconColor: 'text-yellow-600', className: 'notification-warning border-l-4' },
        info: { icon: 'info', iconColor: 'text-blue-600', className: 'notification-info border-l-4' }
    };
    
    const currentConfig = config[type] || config.info;
    const notificationId = 'notification-' + Date.now();
    
    const notification = document.createElement('div');
    notification.id = notificationId;
    notification.className = `notification ${currentConfig.className} rounded-lg p-4 relative overflow-hidden`;
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

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    initializeRevenueChart();
    updateLastUpdateTime();
    
    // Auto refresh every 5 minutes
    setInterval(updateLastUpdateTime, 60000);
    
    // Welcome notification
    setTimeout(() => {
        showNotification('info', 'Selamat datang di Dashboard Administrasi!', 3000);
    }, 500);
});
</script>

</body>
</html>