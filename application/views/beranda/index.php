<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-lg">
        <div class="flex flex-col h-full">
            <div class="p-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Sistem Penggajian</h2>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4">
                <div class="px-4 space-y-2">
                    <a href="<?= base_url('dashboard') ?>" class="block px-4 py-2 text-blue-600 bg-blue-50 rounded-md">
                        Dashboard
                    </a>
                    <a href="<?= base_url('karyawan') ?>" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-md">
                        Data Karyawan
                    </a>
                    <a href="<?= base_url('manajer') ?>" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-md">
                        Data Manajer
                    </a>
                    <a href="<?= base_url('laporan') ?>" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-md">
                        Laporan
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm">
            <div class="px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Total Karyawan -->
                <div class="bg-blue-100 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-500 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Karyawan</h2>
                            <p class="text-2xl font-semibold text-gray-800"><?= $total_karyawan ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Manajer -->
                <div class="bg-pink-100 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-pink-500 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Manajer</h2>
                            <p class="text-2xl font-semibold text-gray-800"><?= $total_manajer ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Departemen -->
                <div class="bg-yellow-100 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-500 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Departemen</h2>
                            <p class="text-2xl font-semibold text-gray-800"><?= $total_departemen ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Absensi -->
                <div class="bg-green-100 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-500 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Absensi</h2>
                            <p class="text-2xl font-semibold text-gray-800"><?= $total_absensi ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Kinerja -->
                <div class="bg-purple-100 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-500 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Kinerja</h2>
                            <p class="text-2xl font-semibold text-gray-800"><?= $total_kinerja ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Gaji -->
                <div class="bg-red-100 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-500 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Transaksi Gaji</h2>
                            <p class="text-2xl font-semibold text-gray-800"><?= $total_gaji ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>