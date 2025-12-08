<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aktivitas - LabSy</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Custom scrollbar - konsisten dengan user_manajemen */
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

        /* Slide animation */
        .slide-down {
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; max-height: 0; }
            to { opacity: 1; max-height: 1000px; }
        }

        /* Fullwidth container - ENHANCED */
        .fullwidth-container {
            min-height: 100vh;
            width: 100vw;
            min-width: 100vw;
            max-width: 100vw;
            overflow-x: auto;
        }

        /* Force fullwidth for all containers */
        .force-fullwidth {
            width: 100% !important;
            min-width: 100% !important;
            max-width: none !important;
        }

        /* Responsive table - ENHANCED */
        .table-container {
            min-width: 100%;
            width: 100%;
            overflow-x: auto;
        }

        /* Table wrapper untuk memastikan fullwidth */
        .table-wrapper {
            width: 100%;
            min-width: 100%;
            overflow-x: auto;
        }

        /* Ensure table takes full width */
        .activity-table {
            width: 100%;
            min-width: 100%;
            table-layout: auto;
        }

        /* Responsive columns */
        .col-date { min-width: 100px; }
        .col-time { min-width: 80px; }
        .col-user { min-width: 150px; }
        .col-patient { min-width: 150px; }
        .col-activity { min-width: 200px; }
        .col-detail { min-width: 250px; }
        .col-ip { min-width: 120px; }
        .col-action { min-width: 100px; }

        /* Empty state container */
        .empty-state-container {
            width: 100% !important;
            min-width: 100% !important;
            padding: 3rem 1rem;
        }
    </style>
</head>
<body class="bg-gray-50 fullwidth-container">

<!-- Header Section - konsisten dengan user_manajemen -->
<div class="p-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 border-b border-blue-500 force-fullwidth">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <i data-lucide="bar-chart-3" class="w-8 h-8 text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Laporan Aktivitas</h1>
                <p class="text-blue-100">Monitor dan analisis aktivitas sistem</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content - Full Width -->
<div class="p-6 space-y-6 fullwidth-container force-fullwidth">
    
    <!-- Flash Messages Container -->
    <div id="flash-messages" class="force-fullwidth"></div>

    <!-- Statistics Cards - konsisten dengan user_manajemen -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 force-fullwidth">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Aktivitas</p>
                    <p id="stat-total" class="text-2xl font-bold text-gray-900">
                        <?= isset($statistics['total_activities']) ? number_format($statistics['total_activities']) : '-' ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">7 hari terakhir</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="activity" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Login/Logout</p>
                    <p id="stat-login" class="text-2xl font-bold text-green-600">
                        <?= isset($statistics['by_type']['Login/Logout']) ? number_format($statistics['by_type']['Login/Logout']) : '0' ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Aktivitas autentikasi</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="log-in" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Operasi Data</p>
                    <p id="stat-operations" class="text-2xl font-bold text-orange-600">
                        <?= isset($statistics['by_type']['Create']) ? 
                            number_format($statistics['by_type']['Create'] + ($statistics['by_type']['Update'] ?? 0) + ($statistics['by_type']['Delete'] ?? 0)) : '0' ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Create, Update, Delete</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="database" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pengguna Aktif</p>
                    <p id="stat-users" class="text-2xl font-bold text-purple-600">
                        <?= isset($statistics['most_active_users']) ? count($statistics['most_active_users']) : '0' ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Beraktivitas hari ini</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 force-fullwidth">
        <!-- Daily Activity Trend -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                <span>Tren Aktivitas Harian</span>
            </h3>
            <div class="h-64">
                <canvas id="dailyTrendChart"></canvas>
            </div>
        </div>

        <!-- Activity by Type -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-blue-600"></i>
                <span>Aktivitas berdasarkan Tipe</span>
            </h3>
            <div class="h-64">
                <canvas id="activityTypeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div id="filters-section" class="bg-white rounded-xl shadow-sm border border-gray-200 hidden force-fullwidth">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                    <span>Filter Laporan</span>
                </h2>
                <button onclick="closeFilters()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <form id="filter-form" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- Date Range -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="<?= $this->input->get('start_date') ?>">
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="<?= $this->input->get('end_date') ?>">
                    </div>
                    
                    <!-- User Filter -->
                    <div>
                        <label for="user_filter" class="block text-sm font-medium text-gray-700 mb-2">Pengguna</label>
                        <select id="user_filter" name="user_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Pengguna</option>
                            <?php if (isset($users)): ?>
                            <?php foreach($users as $user): ?>
                            <option value="<?= $user['user_id'] ?>" <?= $this->input->get('user_id') == $user['user_id'] ? 'selected' : '' ?>>
                                <?= $user['nama_lengkap'] ?: $user['username'] ?> (<?= ucfirst($user['role']) ?>)
                            </option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Activity Type Filter -->
                    <div>
                        <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Aktivitas</label>
                        <select id="activity_type" name="activity_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Aktivitas</option>
                            <option value="masuk" <?= $this->input->get('activity_type') == 'masuk' ? 'selected' : '' ?>>Masuk/Keluar Sistem</option>
                            <option value="ditambahkan" <?= $this->input->get('activity_type') == 'ditambahkan' ? 'selected' : '' ?>>Menambahkan Data</option>
                            <option value="diperbarui" <?= $this->input->get('activity_type') == 'diperbarui' ? 'selected' : '' ?>>Memperbarui Data</option>
                            <option value="dihapus" <?= $this->input->get('activity_type') == 'dihapus' ? 'selected' : '' ?>>Menghapus Data</option>
                            <option value="lab" <?= $this->input->get('activity_type') == 'lab' ? 'selected' : '' ?>>Aktivitas Lab</option>
                            <option value="diakses" <?= $this->input->get('activity_type') == 'diakses' ? 'selected' : '' ?>>Mengakses</option>
                        </select>
                    </div>
                    
                    <!-- Table Filter -->
                    <div>
                        <label for="table_filter" class="block text-sm font-medium text-gray-700 mb-2">Tabel</label>
                        <select id="table_filter" name="table_affected" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Tabel</option>
                            <?php if (isset($tables)): ?>
                            <?php foreach($tables as $table): ?>
                            <option value="<?= $table ?>" <?= $this->input->get('table_affected') == $table ? 'selected' : '' ?>>
                                <?= $table ?>
                            </option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                        <div class="relative">
                            <input type="text" id="search" name="search" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Cari aktivitas..."
                                   value="<?= $this->input->get('search') ?>">
                            <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
                    <button type="button" onclick="resetFilters()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Reset
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-colors duration-200">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table - Full Width with Nama Pasien Column -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 fullwidth-container force-fullwidth">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                    <i data-lucide="list" class="w-5 h-5 text-blue-600"></i>
                    <span>Log Aktivitas</span>
                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                        <?= isset($total_records) ? number_format($total_records) : '0' ?> records
                    </span>
                </h2>
                <div class="flex items-center space-x-2">
                    <button onclick="refreshData()" 
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            title="Refresh Data">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    </button>
                    <button onclick="confirmClearOldLogs()" 
                            class="px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                            title="Hapus Log Lama">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                        Hapus Log Lama
                    </button>
                </div>
            </div>
        </div>
        
        <div class="table-wrapper">
            <div class="table-container">
                <table class="activity-table min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="col-time px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="col-user px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th class="col-patient px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                            <th class="col-activity px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                            <th class="col-detail px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                            <th class="col-ip px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            <th class="col-action px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="activity-table-body" class="bg-white divide-y divide-gray-200">
                        <?php if (isset($activity_logs) && !empty($activity_logs)): ?>
                            <?php foreach($activity_logs as $log): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <!-- Waktu -->
                                <td class="col-time px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($log['created_at'])) ?></div>
                                    <div class="text-xs text-gray-500"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                                </td>
                                <!-- Pengguna -->
                                <td class="col-user px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-semibold text-xs">
                                            <?= substr($log['nama_lengkap'] ?: $log['username'], 0, 2) ?>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900"><?= $log['nama_lengkap'] ?: $log['username'] ?></div>
                                            <div class="text-xs text-gray-500"><?= ucfirst($log['role']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Nama Pasien - NEW COLUMN -->
                                <td class="col-patient px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php
                                        // Logic to determine patient name based on activity and record_id
                                        $patient_name = '-';
                                        $patient_info = '';
                                        
                                        // Check if activity is related to patient or examination
                                        if ($log['table_affected'] == 'pemeriksaan_lab' && $log['record_id']) {
                                            // Get patient name from examination record
                                            $this->db->select('p.nama, p.nik');
                                            $this->db->from('pemeriksaan_lab pl');
                                            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
                                            $this->db->where('pl.pemeriksaan_id', $log['record_id']);
                                            $patient_query = $this->db->get();
                                            
                                            if ($patient_query->num_rows() > 0) {
                                                $patient = $patient_query->row_array();
                                                $patient_name = $patient['nama'];
                                                $patient_info = $patient['nik'];
                                            }
                                        } elseif ($log['table_affected'] == 'pasien' && $log['record_id']) {
                                            // Get patient name directly from patient record
                                            $this->db->select('nama, nik');
                                            $this->db->from('pasien');
                                            $this->db->where('pasien_id', $log['record_id']);
                                            $patient_query = $this->db->get();
                                            
                                            if ($patient_query->num_rows() > 0) {
                                                $patient = $patient_query->row_array();
                                                $patient_name = $patient['nama'];
                                                $patient_info = $patient['nik'];
                                            }
                                        } elseif (strpos(strtolower($log['activity']), 'hasil') !== false && $log['record_id']) {
                                            // For result-related activities
                                            $this->db->select('p.nama, p.nik');
                                            $this->db->from('pemeriksaan_lab pl');
                                            $this->db->join('pasien p', 'pl.pasien_id = p.pasien_id');
                                            $this->db->where('pl.pemeriksaan_id', $log['record_id']);
                                            $patient_query = $this->db->get();
                                            
                                            if ($patient_query->num_rows() > 0) {
                                                $patient = $patient_query->row_array();
                                                $patient_name = $patient['nama'];
                                                $patient_info = $patient['nik'];
                                            }
                                        }
                                        
                                        echo $patient_name;
                                        ?>
                                    </div>
                                    <?php if ($patient_info): ?>
                                    <div class="text-xs text-gray-500"><?= $patient_info ?></div>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Aktivitas -->
                                <td class="col-activity px-6 py-4">
                                    <div class="flex items-start space-x-2">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <?php 
                                            $activity_icon = 'activity';
                                            $activity_color = 'text-gray-600';
                                            
                                            if (strpos(strtolower($log['activity']), 'login') !== false || strpos(strtolower($log['activity']), 'logout') !== false || strpos(strtolower($log['activity']), 'masuk') !== false || strpos(strtolower($log['activity']), 'keluar') !== false) {
                                                $activity_icon = 'log-in';
                                                $activity_color = 'text-green-600';
                                            } elseif (strpos(strtolower($log['activity']), 'created') !== false || strpos(strtolower($log['activity']), 'added') !== false || strpos(strtolower($log['activity']), 'ditambahkan') !== false || strpos(strtolower($log['activity']), 'baru') !== false) {
                                                $activity_icon = 'plus-circle';
                                                $activity_color = 'text-blue-600';
                                            } elseif (strpos(strtolower($log['activity']), 'updated') !== false || strpos(strtolower($log['activity']), 'modified') !== false || strpos(strtolower($log['activity']), 'diperbarui') !== false || strpos(strtolower($log['activity']), 'edit') !== false) {
                                                $activity_icon = 'edit';
                                                $activity_color = 'text-orange-600';
                                            } elseif (strpos(strtolower($log['activity']), 'deleted') !== false || strpos(strtolower($log['activity']), 'removed') !== false || strpos(strtolower($log['activity']), 'dihapus') !== false || strpos(strtolower($log['activity']), 'hapus') !== false) {
                                                $activity_icon = 'trash-2';
                                                $activity_color = 'text-red-600';
                                            } elseif (strpos(strtolower($log['activity']), 'lab') !== false || strpos(strtolower($log['activity']), 'results') !== false || strpos(strtolower($log['activity']), 'sample') !== false || strpos(strtolower($log['activity']), 'pemeriksaan') !== false || strpos(strtolower($log['activity']), 'hasil') !== false) {
                                                $activity_icon = 'flask-conical';
                                                $activity_color = 'text-purple-600';
                                            } elseif (strpos(strtolower($log['activity']), 'dashboard') !== false || strpos(strtolower($log['activity']), 'mengakses') !== false || strpos(strtolower($log['activity']), 'accessed') !== false) {
                                                $activity_icon = 'eye';
                                                $activity_color = 'text-indigo-600';
                                            }
                                            ?>
                                            <i data-lucide="<?= $activity_icon ?>" class="w-4 h-4 <?= $activity_color ?>"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-900"><?= $log['activity'] ?></div>
                                            <?php if ($log['table_affected']): ?>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Tabel: <span class="font-medium"><?= $log['table_affected'] ?></span>
                                                <?php if ($log['record_id']): ?>
                                                    | ID: <span class="font-medium"><?= $log['record_id'] ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Detail -->
                                <td class="col-detail px-6 py-4">
                                    <div class="space-y-1">
                                        <?php 
                                        // Generate context based on activity type
                                        $detail_context = '';
                                        $activity_lower = strtolower($log['activity']);
                                        
                                        if (strpos($activity_lower, 'dashboard') !== false || strpos($activity_lower, 'mengakses dashboard') !== false) {
                                            $detail_context = '<span class="text-blue-600"><i data-lucide="monitor" class="w-3 h-3 inline mr-1"></i>Mengakses halaman dashboard sistem</span>';
                                        } elseif (strpos($activity_lower, 'masuk') !== false || strpos($activity_lower, 'login') !== false) {
                                            $detail_context = '<span class="text-green-600"><i data-lucide="log-in" class="w-3 h-3 inline mr-1"></i>Login ke sistem laboratorium</span>';
                                        } elseif (strpos($activity_lower, 'keluar') !== false || strpos($activity_lower, 'logout') !== false) {
                                            $detail_context = '<span class="text-orange-600"><i data-lucide="log-out" class="w-3 h-3 inline mr-1"></i>Logout dari sistem</span>';
                                        } elseif (strpos($activity_lower, 'pengguna') !== false && strpos($activity_lower, 'baru') !== false) {
                                            $detail_context = '<span class="text-blue-600"><i data-lucide="user-plus" class="w-3 h-3 inline mr-1"></i>Menambahkan akun pengguna baru</span>';
                                        } elseif (strpos($activity_lower, 'diperbarui') !== false || strpos($activity_lower, 'updated') !== false) {
                                            $detail_context = '<span class="text-orange-600"><i data-lucide="edit" class="w-3 h-3 inline mr-1"></i>Memperbarui data sistem</span>';
                                        } elseif (strpos($activity_lower, 'dihapus') !== false || strpos($activity_lower, 'deleted') !== false) {
                                            $detail_context = '<span class="text-red-600"><i data-lucide="trash-2" class="w-3 h-3 inline mr-1"></i>Menghapus data dari sistem</span>';
                                        } elseif (strpos($activity_lower, 'aktivitas') !== false && strpos($activity_lower, 'diekspor') !== false) {
                                            $detail_context = '<span class="text-purple-600"><i data-lucide="download" class="w-3 h-3 inline mr-1"></i>Mengekspor data log aktivitas</span>';
                                        } elseif (strpos($activity_lower, 'laporan') !== false && strpos($activity_lower, 'mengakses') !== false) {
                                            $detail_context = '<span class="text-indigo-600"><i data-lucide="bar-chart" class="w-3 h-3 inline mr-1"></i>Mengakses halaman laporan</span>';
                                        } elseif (strpos($activity_lower, 'pemeriksaan') !== false || strpos($activity_lower, 'lab') !== false) {
                                            $detail_context = '<span class="text-purple-600"><i data-lucide="flask-conical" class="w-3 h-3 inline mr-1"></i>Aktivitas laboratorium</span>';
                                        } else {
                                            $detail_context = '<span class="text-gray-600"><i data-lucide="activity" class="w-3 h-3 inline mr-1"></i>Aktivitas sistem umum</span>';
                                        }
                                        ?>
                                        
                                        <div class="text-sm">
                                            <?= $detail_context ?>
                                        </div>
                                        
                                        <?php if ($log['table_affected'] && $log['record_id']): ?>
                                        <div class="text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded">
                                            <i data-lucide="database" class="w-3 h-3 inline mr-1"></i>
                                            Tabel: <strong><?= $log['table_affected'] ?></strong> | Record ID: <strong><?= $log['record_id'] ?></strong>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <!-- IP Address -->
                                <td class="col-ip px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 flex items-center">
                                        <i data-lucide="globe" class="w-3 h-3 mr-1 text-gray-400"></i>
                                        <?= $log['ip_address'] ?>
                                    </div>
                                </td>
                                
                                <!-- Aksi -->
                                <td class="col-action px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="deleteLog(<?= $log['log_id'] ?>)" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 transition-colors duration-200">
                                        <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Empty State with Full Width -->
                            <tr class="fullwidth-container">
                                <td colspan="7" class="px-6 py-12 text-center empty-state-container">
                                    <div class="flex flex-col items-center space-y-4 w-full">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada log aktivitas</h3>
                                            <p class="text-gray-500 mb-4">Belum ada data aktivitas yang tersedia</p>
                                            <button onclick="refreshData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 mx-auto">
                                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                                <span>Refresh Data</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination - konsisten dengan user_manajemen -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="px-6 py-4 border-t border-gray-100 force-fullwidth">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> 
                    sampai <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_records']) ?> 
                    dari <?= number_format($pagination['total_records']) ?> hasil
                </div>
                
                <div class="flex items-center space-x-2">
                    <?php if ($pagination['current_page'] > 1): ?>
                    <a href="?<?= http_build_query(array_merge($pagination['filters'], array('page' => $pagination['current_page'] - 1))) ?>" 
                       class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Sebelumnya
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                    <a href="?<?= http_build_query(array_merge($pagination['filters'], array('page' => $i))) ?>" 
                       class="px-3 py-1 text-sm border rounded-lg transition-colors duration-200 <?= $i == $pagination['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <a href="?<?= http_build_query(array_merge($pagination['filters'], array('page' => $pagination['current_page'] + 1))) ?>" 
                       class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Selanjutnya
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Clear Old Logs Modal -->
<div id="clear-logs-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Hapus Log Aktivitas Lama</h3>
                <button onclick="closeClearLogsModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <p class="text-gray-600 mb-4">Hapus log aktivitas yang lebih lama dari:</p>
            <select id="clear-days" class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-4">
                <option value="30">30 hari</option>
                <option value="60">60 hari</option>
                <option value="90">90 hari</option>
                <option value="180">6 bulan</option>
                <option value="365">1 tahun</option>
            </select>
            <div class="flex items-center justify-end space-x-4">
                <button onclick="closeClearLogsModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Batal
                </button>
                <button onclick="clearOldLogs()" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let currentFilters = {};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    initializeCharts();
    ensureFullwidthLayout();
    
    // Initialize filter form
    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });
});

// ENHANCED: Ensure fullwidth layout in all conditions
function ensureFullwidthLayout() {
    // Force fullwidth for main containers
    const containers = document.querySelectorAll('.fullwidth-container, .force-fullwidth');
    containers.forEach(container => {
        container.style.width = '100%';
        container.style.minWidth = '100%';
        container.style.maxWidth = 'none';
    });
    
    // Ensure table takes full width
    const tableContainers = document.querySelectorAll('.table-container, .table-wrapper');
    tableContainers.forEach(container => {
        container.style.width = '100%';
        container.style.minWidth = '100%';
    });
    
    const tables = document.querySelectorAll('.activity-table');
    tables.forEach(table => {
        table.style.width = '100%';
        table.style.minWidth = '100%';
        table.style.tableLayout = 'auto';
    });
    
    // Ensure empty state takes full width
    const emptyStates = document.querySelectorAll('.empty-state-container');
    emptyStates.forEach(container => {
        container.style.width = '100%';
        container.style.minWidth = '100%';
    });
    
    // Force body to be fullwidth
    document.body.style.width = '100%';
    document.body.style.minWidth = '100%';
    document.body.style.maxWidth = 'none';
    document.body.style.overflowX = 'auto';
}

// Chart initialization
function initializeCharts() {
    const dailyTrendData = <?= isset($statistics['daily_trend']) ? json_encode($statistics['daily_trend']) : '[]' ?>;
    const activityTypeData = <?= isset($statistics['by_type']) ? json_encode($statistics['by_type']) : '{}' ?>;
    
    // Daily Trend Chart
    const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyTrendData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Aktivitas',
                data: dailyTrendData.map(item => item.count),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // Activity Type Chart
    const typeCtx = document.getElementById('activityTypeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(activityTypeData),
            datasets: [{
                data: Object.values(activityTypeData),
                backgroundColor: [
                    'rgb(59, 130, 246)',   // Blue
                    'rgb(16, 185, 129)',   // Green
                    'rgb(245, 158, 11)',   // Orange
                    'rgb(239, 68, 68)',    // Red
                    'rgb(139, 92, 246)',   // Purple
                    'rgb(236, 72, 153)'    // Pink
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Toggle filters
function toggleFilters() {
    const filtersSection = document.getElementById('filters-section');
    if (filtersSection.classList.contains('hidden')) {
        filtersSection.classList.remove('hidden');
        filtersSection.classList.add('slide-down');
    } else {
        filtersSection.classList.add('hidden');
    }
    
    // Re-ensure fullwidth after toggle
    setTimeout(() => {
        ensureFullwidthLayout();
    }, 100);
}

function closeFilters() {
    const filtersSection = document.getElementById('filters-section');
    filtersSection.classList.add('hidden');
    ensureFullwidthLayout();
}

// Apply filters
function applyFilters() {
    const formData = new FormData(document.getElementById('filter-form'));
    const params = new URLSearchParams();
    
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    window.location.href = '?' + params.toString();
}

// Reset filters
function resetFilters() {
    document.getElementById('filter-form').reset();
    window.location.href = window.location.pathname;
}

// Export data
function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '<?= base_url("admin/export_activity_logs") ?>?' + params.toString();
}

// Refresh data
function refreshData() {
    window.location.reload();
}

// Delete log
async function deleteLog(logId) {
    if (!confirm('Apakah Anda yakin ingin menghapus log aktivitas ini?')) {
        return;
    }
    
    try {
        const response = await fetch(`<?= base_url("admin/ajax_delete_activity_log") ?>/${logId}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showFlashMessage('success', data.message);
            refreshData();
        } else {
            showFlashMessage('error', data.message);
        }
    } catch (error) {
        console.error('Error deleting log:', error);
        showFlashMessage('error', 'Gagal menghapus log aktivitas');
    }
}

// Clear old logs
function confirmClearOldLogs() {
    document.getElementById('clear-logs-modal').classList.remove('hidden');
}

function closeClearLogsModal() {
    document.getElementById('clear-logs-modal').classList.add('hidden');
    ensureFullwidthLayout();
}

async function clearOldLogs() {
    const days = document.getElementById('clear-days').value;
    
    if (!confirm(`Apakah Anda yakin ingin menghapus semua log aktivitas yang lebih lama dari ${days} hari?`)) {
        return;
    }
    
    try {
        const response = await fetch('<?= base_url("admin/ajax_clear_old_activity_logs") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `days=${days}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            showFlashMessage('success', data.message);
            closeClearLogsModal();
            refreshData();
        } else {
            showFlashMessage('error', data.message);
        }
    } catch (error) {
        console.error('Error clearing old logs:', error);
        showFlashMessage('error', 'Gagal menghapus log aktivitas lama');
    }
}

// Utility function - konsisten dengan user_manajemen
function showFlashMessage(type, message) {
    const container = document.getElementById('flash-messages');
    const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const iconName = type === 'success' ? 'check-circle' : 'alert-circle';
    const iconClass = type === 'success' ? 'text-green-600' : 'text-red-600';
    
    const alert = document.createElement('div');
    alert.className = `${alertClass} border rounded-lg p-4 flex items-center space-x-3 fade-in force-fullwidth`;
    alert.innerHTML = `
        <i data-lucide="${iconName}" class="w-5 h-5 ${iconClass}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    `;
    
    container.appendChild(alert);
    lucide.createIcons();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 5000);
    
    ensureFullwidthLayout();
}

// Window resize handler to maintain fullwidth
window.addEventListener('resize', function() {
    ensureFullwidthLayout();
});

// Page load handler
window.addEventListener('load', function() {
    ensureFullwidthLayout();
});

// Mutation observer to ensure fullwidth is maintained
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'childList') {
            setTimeout(ensureFullwidthLayout, 50);
        }
    });
});

// Start observing
observer.observe(document.body, {
    childList: true,
    subtree: true
});
</script>

</body>
</html>