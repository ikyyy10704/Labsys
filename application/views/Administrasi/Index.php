 <!-- Header Section -->
    <div class="p-6 bg-gradient-to-r from-med-blue to-med-light-blue">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="clipboard-list" class="w-8 h-8 text-med-blue"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Administration Dashboard</h1>
                    <p class="text-blue-100">Patient Registration & Financial Services</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                    <p class="text-sm text-white opacity-90">Today's Registrations</p>
                    <p class="text-lg font-semibold text-white">15 Patients</p>
                </div>
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-6 h-6 text-med-blue"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- New Registrations -->
            <div class="stats-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">New Registrations Today</p>
                        <p class="text-3xl font-bold text-med-blue">15</p>
                        <p class="text-sm text-blue-600 mt-1">
                            <i data-lucide="trending-up" class="w-4 h-4 inline mr-1"></i>
                            +3 from yesterday
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-6 h-6 text-med-blue"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="stats-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending Payments</p>
                        <p class="text-3xl font-bold text-med-orange">8</p>
                        <p class="text-sm text-orange-600 mt-1">
                            <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                            Awaiting payment
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-6 h-6 text-med-orange"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue Today -->
            <div class="stats-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Revenue Today</p>
                        <p class="text-3xl font-bold text-med-green">Rp 12.5M</p>
                        <p class="text-sm text-green-600 mt-1">
                            <i data-lucide="trending-up" class="w-4 h-4 inline mr-1"></i>
                            +15% from avg.
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-med-green"></i>
                    </div>
                </div>
            </div>

            <!-- BPJS vs Umum -->
            <div class="stats-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">BPJS Patients</p>
                        <p class="text-3xl font-bold text-purple-600">60%</p>
                        <p class="text-sm text-purple-600 mt-1">
                            <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                            40% General
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="pie-chart" class="w-6 h-6 text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Recent Registrations -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Patient Registrations</h2>
                    <a href="<?= base_url('administrasi/add_patient_data') ?>" class="px-4 py-2 bg-med-blue text-white rounded-lg hover:bg-med-light-blue transition-colors text-sm">
                        <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                        New Patient
                    </a>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">SL</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Sari Lestari</p>
                                <p class="text-sm text-gray-500">NIK: 3374************</p>
                                <p class="text-xs text-gray-400">Registration: REG20250001</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">10:30 AM</p>
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">BPJS</span>
                            <p class="text-xs text-gray-400 mt-1">Kimia Darah</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">BH</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Budi Hartono</p>
                                <p class="text-sm text-gray-500">NIK: 3374************</p>
                                <p class="text-xs text-gray-400">Registration: REG20250002</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">11:15 AM</p>
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Umum</span>
                            <p class="text-xs text-gray-400 mt-1">Hematologi</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">DP</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Dewi Permata</p>
                                <p class="text-sm text-gray-500">NIK: 3374************</p>
                                <p class="text-xs text-gray-400">Registration: REG20250003</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">12:00 PM</p>
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">BPJS</span>
                            <p class="text-xs text-gray-400 mt-1">Urinologi</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">AS</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Ahmad Syahril</p>
                                <p class="text-sm text-gray-500">NIK: 3374************</p>
                                <p class="text-xs text-gray-400">Registration: REG20250004</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">13:30 PM</p>
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                            <p class="text-xs text-gray-400 mt-1">Serologi</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-6">
                    <a href="<?= base_url('administrasi/patient_history') ?>" class="text-med-blue hover:text-med-light-blue font-medium">
                        View All Patient Records →
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Quick Actions</h2>
                
                <div class="space-y-4">
                    <a href="<?= base_url('administrasi/add_patient_data') ?>" class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="w-10 h-10 bg-med-blue rounded-lg flex items-center justify-center">
                            <i data-lucide="user-plus" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Add Patient</p>
                            <p class="text-sm text-gray-500">Register new patient</p>
                        </div>
                    </a>
                    
                    <a href="<?= base_url('administrasi/schedule') ?>" class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="w-10 h-10 bg-med-green rounded-lg flex items-center justify-center">
                            <i data-lucide="calendar" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Schedule Appointment</p>
                            <p class="text-sm text-gray-500">Book lab appointment</p>
                        </div>
                    </a>
                    
                    <a href="<?= base_url('administrasi/invoice_umum') ?>" class="flex items-center space-x-3 p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Create Invoice</p>
                            <p class="text-sm text-gray-500">General payment</p>
                        </div>
                    </a>
                    
                    <a href="<?= base_url('administrasi/invoice_bpjs') ?>" class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">BPJS Invoice</p>
                            <p class="text-sm text-gray-500">BPJS claims</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Financial Overview and Pending Tasks -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Daily Revenue Chart -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Daily Revenue</h2>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-sm bg-med-blue text-white rounded-lg">7 Days</button>
                        <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">30 Days</button>
                    </div>
                </div>
                
                <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                    <canvas id="revenueChart"></canvas>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Total This Week</p>
                        <p class="text-xl font-bold text-med-blue">Rp 87.5M</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Average Per Day</p>
                        <p class="text-xl font-bold text-med-green">Rp 12.5M</p>
                    </div>
                </div>
            </div>

            <!-- Payment Status Overview -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Payment Status Overview</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div>
                                <p class="font-medium text-gray-900">Completed Payments</p>
                                <p class="text-sm text-gray-500">25 transactions today</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-600">Rp 11.2M</p>
                            <p class="text-sm text-green-600">89.6%</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <div>
                                <p class="font-medium text-gray-900">Pending Payments</p>
                                <p class="text-sm text-gray-500">8 transactions waiting</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-yellow-600">Rp 1.3M</p>
                            <p class="text-sm text-yellow-600">10.4%</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <div>
                                <p class="font-medium text-gray-900">BPJS Claims</p>
                                <p class="text-sm text-gray-500">15 claims processed</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-600">Rp 7.5M</p>
                            <p class="text-sm text-blue-600">60%</p>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-900">Today's Total Revenue</span>
                            <span class="text-xl font-bold text-med-blue">Rp 12.5M</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Chart Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue (Million IDR)',
                    data: [10.2, 11.5, 9.8, 12.3, 13.1, 14.2, 12.5],
                    backgroundColor: 'rgba(30, 64, 175, 0.8)',
                    borderColor: '#1E40AF',
                    borderWidth: 1,
                    borderRadius: 4
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
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value + 'M';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
