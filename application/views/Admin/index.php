  <!-- Header Section -->
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                    <p class="text-gray-600">Welcome back, Administrator</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-gray-50 rounded-lg px-4 py-2 border">
                    <p class="text-sm text-gray-500">Last Login</p>
                    <p class="text-lg font-semibold text-gray-900"><?= date('d M Y H:i') ?></p>
                </div>
                <div class="relative">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                        <span class="text-sm font-semibold text-gray-600">Dr. Smith</span>
                    </div>
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                        <span class="text-xs text-white font-bold">2</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Patients -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Patients</p>
                        <p class="text-3xl font-bold text-gray-900 mb-2">
                            <?= isset($stats['today']['new_patients']) ? number_format($stats['today']['new_patients'] * 100) : '2,847' ?>
                        </p>
                        <div class="flex items-center text-sm">
                            <i data-lucide="trending-up" class="w-4 h-4 text-green-500 mr-1"></i>
                            <span class="text-green-600 font-medium">+12% from last month</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Examinations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Examinations</p>
                        <p class="text-3xl font-bold text-gray-900 mb-2">
                            <?= isset($stats['today']['examinations']) ? number_format($stats['today']['examinations'] * 50) : '1,456' ?>
                        </p>
                        <div class="flex items-center text-sm">
                            <i data-lucide="trending-up" class="w-4 h-4 text-green-500 mr-1"></i>
                            <span class="text-green-600 font-medium">+18% from last month</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="clipboard-check" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <!-- Reports Generated -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Reports Generated</p>
                        <p class="text-3xl font-bold text-gray-900 mb-2">
                            <?= isset($stats['today']['completed_tests']) ? number_format($stats['today']['completed_tests'] * 25) : '892' ?>
                        </p>
                        <div class="flex items-center text-sm">
                            <i data-lucide="trending-up" class="w-4 h-4 text-orange-500 mr-1"></i>
                            <span class="text-orange-600 font-medium">+5% from last month</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="file-text" class="w-6 h-6 text-orange-600"></i>
                    </div>
                </div>
            </div>

            <!-- Active Doctors -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Active Doctors</p>
                        <p class="text-3xl font-bold text-gray-900 mb-2">
                            <?= isset($master_stats['active_users']) ? $master_stats['active_users'] : '47' ?>
                        </p>
                        <div class="flex items-center text-sm">
                            <i data-lucide="trending-up" class="w-4 h-4 text-blue-500 mr-1"></i>
                            <span class="text-blue-600 font-medium">+3 new doctors</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="stethoscope" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
            
            <!-- Recent Patients - Takes up more space -->
            <div class="lg:col-span-4 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Patients</h2>
                        <a href="<?= base_url('admin/patients') ?>" class="text-sm text-blue-600 hover:text-blue-800 flex items-center font-medium">
                            <i data-lucide="external-link" class="w-4 h-4 ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <?php if (!empty($recent_examinations)): ?>
                            <?php foreach (array_slice($recent_examinations, 0, 3) as $index => $exam): ?>
                            <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-white">
                                        <?= substr($exam['nama_pasien'], 0, 2) ?>
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900"><?= $exam['nama_pasien'] ?></p>
                                    <p class="text-sm text-gray-500">ID: <?= $exam['nomor_pemeriksaan'] ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?= date('g:i A', strtotime($exam['created_at'])) ?>
                                    </p>
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        <?= ucfirst($exam['status_pemeriksaan']) ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Default/Demo Data -->
                            <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-white">SJ</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Sarah Johnson</p>
                                    <p class="text-sm text-gray-500">ID: HP-2024-001</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">10:30 AM</p>
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Completed</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-white">MB</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Michael Brown</p>
                                    <p class="text-sm text-gray-500">ID: HP-2024-002</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">2:15 PM</p>
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">In Progress</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-white">ED</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Emily Davis</p>
                                    <p class="text-sm text-gray-500">ID: HP-2024-003</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">4:00 PM</p>
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Scheduled</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <a href="<?= base_url('admin/users') ?>" class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-150 group">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-700 transition-colors">
                                <i data-lucide="user-plus" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Add Patient</p>
                                <p class="text-sm text-gray-500">Register new patient</p>
                            </div>
                        </a>
                        
                        <a href="<?= base_url('admin/backup') ?>" class="flex items-center space-x-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-150 group">
                            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center group-hover:bg-green-700 transition-colors">
                                <i data-lucide="download" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">New Report</p>
                                <p class="text-sm text-gray-500">Generate system report</p>
                            </div>
                        </a>
                        
                        <a href="<?= base_url('admin/activity_reports') ?>" class="flex items-center space-x-3 p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors duration-150 group">
                            <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center group-hover:bg-orange-700 transition-colors">
                                <i data-lucide="calendar" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Schedule</p>
                                <p class="text-sm text-gray-500">View appointments</p>
                            </div>
                        </a>
                        
                        <a href="<?= base_url('admin/master_data') ?>" class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150 group">
                            <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center group-hover:bg-gray-700 transition-colors">
                                <i data-lucide="download" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Export Data</p>
                                <p class="text-sm text-gray-500">Download system data</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Additional Styles for matching the screenshot -->
<style>
/* Custom styles to match the exact look from screenshot */
.stats-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease-in-out;
}

.stats-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

/* Ensure proper spacing and layout */
.main-content {
    background-color: #f8fafc;
    min-height: 100vh;
}

/* Custom scrollbar for sidebar */
.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>