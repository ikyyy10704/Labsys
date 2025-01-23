<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="ml-64 p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Main Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-600 bg-opacity-30">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white text-sm">Total Karyawan</p>
                        <p class="text-white text-2xl font-bold"><?= $total_karyawan ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-600 bg-opacity-30">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white text-sm">Total Manajer</p>
                        <p class="text-white text-2xl font-bold"><?= $total_manajer ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-600 bg-opacity-30">
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white text-sm">Total Absensi</p>
                        <p class="text-white text-2xl font-bold"><?= $total_absensi ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-600 bg-opacity-30">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white text-sm">Total Kinerja</p>
                        <p class="text-white text-2xl font-bold"><?= $total_kinerja ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Performance Table -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-trophy mr-3 text-yellow-500"></i>
                    Top Performers
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departemen</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($top_performers as $performer): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?= $performer->nama_krywn ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <?= $performer->nilai_kerja ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $performer->departemen ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Salary Stats -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-money-bill-wave mr-3 text-green-500"></i>
                    Statistik Gaji per Departemen
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departemen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Gaji</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($salary_stats as $stat): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?= $stat->departemen ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $stat->jumlah_karyawan   ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp <?= $stat->total_gaji ? number_format($stat->total_gaji, 0, ',', '.') : 0 ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp <?= $stat->rata_rata_gaji ? number_format($stat->rata_rata_gaji, 0, ',', '.') : 0 ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Pie Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-chart-pie mr-3 text-blue-500"></i>
                    Distribusi Kinerja
                </h2>
                <div class="relative" style="height: 300px;">
                    <canvas id="performancePieChart"></canvas>
                </div>
            </div>

            <!-- Stacked Bar Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-green-500"></i>
                    Statistik Departemen
                </h2>
                <div class="relative" style="height: 300px;">
                    <canvas id="departmentBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Previous tables and other content remains unchanged -->
    </div>
</div>

<!-- Add Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
// Performance Pie Chart
const performanceCtx = document.getElementById('performancePieChart').getContext('2d');
const performanceData = {
    labels: <?= json_encode(array_column($performance_distribution, 'status_pengelolaan')) ?>,
    datasets: [{
        data: <?= json_encode(array_column($performance_distribution, 'total')) ?>,
        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
        borderWidth: 0
    }]
};

new Chart(performanceCtx, {
    type: 'pie',
    data: performanceData,
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

// Department Stacked Bar Chart
const deptCtx = document.getElementById('departmentBarChart').getContext('2d');
const departmentData = {
    labels: <?= json_encode(array_column($salary_stats, 'departemen')) ?>,
    datasets: [{
        label: 'Jumlah Karyawan',
        data: <?= json_encode(array_column($salary_stats, 'jumlah_karyawan')) ?>,
        backgroundColor: '#3B82F6',
        stack: 'Stack 0',
    }, {
        label: 'Rata-rata Gaji (dalam jutaan)',
        data: <?= json_encode(array_map(function($stat) {
            return $stat->rata_rata_gaji ? $stat->rata_rata_gaji / 1000000 : 0;
        }, $salary_stats)) ?>,
        backgroundColor: '#10B981',
        stack: 'Stack 1',
    }]
};

new Chart(deptCtx, {
    type: 'bar',
    data: departmentData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true,
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <i class="fas fa-chart-pie mr-3 text-blue-500"></i>
                Distribusi Status Kinerja
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach($performance_distribution as $dist): ?>
                <div class="bg-gray-50 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-gray-500"><?= $dist->status_pengelolaan ?></div>
                    <div class="mt-1 text-2xl font-bold text-gray-900"><?= $dist->total ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>