<div class="ml-64 p-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Program Management</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-lg overflow-hidden shadow-lg relative">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-white mb-2">Pengelolaan Karyawan</h2>
                    <div class="text-blue-200 mb-4">
                        <span class="inline-block bg-blue-700 rounded px-2 py-1 text-sm">
                            Data Management
                        </span>
                    </div>
                    <p class="text-blue-100 text-sm mb-4">
                        Manage employee data, personal information, and status
                    </p>
                    <div class="flex space-x-2 mt-4">
                        <a href="<?= base_url('karyawan/create') ?>" 
                           class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-plus mr-2"></i>Add
                        </a>
                        <a href="<?= base_url('karyawan') ?>" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-list mr-2"></i>View All
                        </a>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-900 to-purple-800 rounded-lg overflow-hidden shadow-lg relative">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-white mb-2">Pengelolaan Kinerja</h2>
                    <div class="text-purple-200 mb-4">
                        <span class="inline-block bg-purple-700 rounded px-2 py-1 text-sm">
                            Performance
                        </span>
                    </div>
                    <p class="text-purple-100 text-sm mb-4">
                        Track and manage employee performance metrics
                    </p>
                    <div class="flex space-x-2 mt-4">
                        <a href="<?= base_url('kinerja/create') ?>" 
                           class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-plus mr-2"></i>Add
                        </a>
                        <a href="<?= base_url('index.php/kinerja') ?>" 
                           class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-list mr-2"></i>View All
                        </a>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-emerald-900 to-emerald-800 rounded-lg overflow-hidden shadow-lg relative">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-white mb-2">Pengelolaan Gaji</h2>
                    <div class="text-emerald-200 mb-4">
                        <span class="inline-block bg-emerald-700 rounded px-2 py-1 text-sm">
                            Payroll
                        </span>
                    </div>
                    <p class="text-emerald-100 text-sm mb-4">
                        Manage employee salaries and compensations
                    </p>
                    <div class="flex space-x-2 mt-4">
                        <a href="<?= base_url('index.php/Gaji') ?>" 
                           class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-plus mr-2"></i>Add
                        </a>
                        <a href="<?= base_url('gaji') ?>" 
                           class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-list mr-2"></i>View All
                        </a>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-red-900 to-red-800 rounded-lg overflow-hidden shadow-lg relative">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-white mb-2">Pengelolaan Absensi</h2>
                    <div class="text-red-200 mb-4">
                        <span class="inline-block bg-red-700 rounded px-2 py-1 text-sm">
                            Attendance
                        </span>
                    </div>
                    <p class="text-red-100 text-sm mb-4">
                        Track employee attendance and schedules
                    </p>
                    <div class="flex space-x-2 mt-4">
                        <a href="<?= base_url('absensi/create') ?>" 
                           class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-plus mr-2"></i>Add
                        </a>
                        <a href="<?= base_url('absensi') ?>" 
                           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-list mr-2"></i>View All
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>