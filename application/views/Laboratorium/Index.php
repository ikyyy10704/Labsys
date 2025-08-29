    <!-- Header Section -->
    <div class="p-6 bg-gradient-to-r from-med-blue to-med-light-blue">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="flask-conical" class="w-8 h-8 text-med-blue"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Dashboard Laboratorium</h1>
                    <p class="text-blue-100">Pemrosesan Sampel & Kontrol Kualitas</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                    <p class="text-sm text-white opacity-90">Sampel Hari Ini</p>
                    <p class="text-lg font-semibold text-white">24 Menunggu</p>
                </div>
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                    <i data-lucide="test-tube" class="w-6 h-6 text-med-blue"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Incoming Requests -->
            <div class="stats-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Permintaan Masuk</p>
                        <p class="text-3xl font-bold text-med-blue">24</p>
                        <p class="text-sm text-blue-600 mt-1">
                            <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                            8 prioritas mendesak
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="inbox" class="w-6 h-6 text-med-blue"></i>
                    </div>
                </div>
            </div>

            <!-- Samples Processing -->
            <div class="stats-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sampel Diproses</p>
                        <p class="text-3xl font-bold text-med-orange">12</p>
                        <p class="text-sm text-orange-600 mt-1">
                            <i data-lucide="loader" class="w-4 h-4 inline mr-1"></i>
                            Dalam tahap analisis
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="test-tube" class="w-6 h-6 text-med-orange"></i>
                    </div>
                </div>
            </div>

            <!-- Completed Tests -->
            <div class="stats-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tes Selesai</p>
                        <p class="text-3xl font-bold text-med-green">45</p>
                        <p class="text-sm text-green-600 mt-1">
                            <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                            Siap untuk ditinjau
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6 text-med-green"></i>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="stats-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Stok Menipis</p>
                        <p class="text-3xl font-bold text-red-600">3</p>
                        <p class="text-sm text-red-600 mt-1">
                            <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                            Perlu pemesanan ulang
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="package" class="w-6 h-6 text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Pending Lab Requests -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Permintaan Lab Menunggu</h2>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200">Mendesak</button>
                        <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200">Semua</button>
                    </div>
                </div>
                
                <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <!-- High Priority Sample -->
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border-l-4 border-red-500 hover:shadow-sm transition-shadow duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-sm">
                                <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Darurat - Pemeriksaan Darah</p>
                                <p class="text-sm text-gray-500">Pasien: John Anderson (HP-2024-089)</p>
                                <p class="text-xs text-gray-400">Diminta oleh: Dr. Smith | 30 menit lalu</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">MENDESAK</span>
                            <p class="text-sm font-medium text-gray-900 mt-1">Kimia Darah</p>
                            <p class="text-xs text-gray-400">ID Sampel: S24-001</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg border-l-4 border-orange-500 hover:shadow-sm transition-shadow duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center shadow-sm">
                                <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Pemeriksaan Lab Rutin</p>
                                <p class="text-sm text-gray-500">Pasien: Maria Santos (HP-2024-090)</p>
                                <p class="text-xs text-gray-400">Diminta oleh: Dr. Johnson | 1 jam lalu</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">TINGGI</span>
                            <p class="text-sm font-medium text-gray-900 mt-1">Hematologi</p>
                            <p class="text-xs text-gray-400">ID Sampel: S24-002</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500 hover:shadow-sm transition-shadow duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                <i data-lucide="test-tube" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Analisis Urin</p>
                                <p class="text-sm text-gray-500">Pasien: Robert Kim (HP-2024-091)</p>
                                <p class="text-xs text-gray-400">Diminta oleh: Dr. Brown | 2 jam lalu</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">NORMAL</span>
                            <p class="text-sm font-medium text-gray-900 mt-1">Urinologi</p>
                            <p class="text-xs text-gray-400">ID Sampel: S24-003</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border-l-4 border-green-500 hover:shadow-sm transition-shadow duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-sm">
                                <i data-lucide="microscope" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Pemeriksaan Mikrobiologi</p>
                                <p class="text-sm text-gray-500">Pasien: Lisa Wong (HP-2024-092)</p>
                                <p class="text-xs text-gray-400">Diminta oleh: Dr. Davis | 3 jam lalu</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">RENDAH</span>
                            <p class="text-sm font-medium text-gray-900 mt-1">Mikrobiologi</p>
                            <p class="text-xs text-gray-400">ID Sampel: S24-004</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200">
                    <button class="w-full px-4 py-2 text-med-blue border border-med-blue rounded-lg hover:bg-med-blue hover:text-white transition-all duration-200 font-medium">
                        Lihat Semua Permintaan
                    </button>
                </div>
            </div>

            <!-- Quick Actions & Today's Summary -->
            <div class="space-y-6">
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                    <div class="space-y-3">
                        <button class="w-full flex items-center justify-between p-3 text-left border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 group">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-200">
                                    <i data-lucide="plus-circle" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <span class="font-medium text-gray-900">Input Hasil Baru</span>
                            </div>
                            <i data-lucide="arrow-right" class="w-5 h-5 text-gray-400 group-hover:text-blue-600"></i>
                        </button>
                        
                        <button class="w-full flex items-center justify-between p-3 text-left border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-all duration-200 group">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors duration-200">
                                    <i data-lucide="search" class="w-5 h-5 text-green-600"></i>
                                </div>
                                <span class="font-medium text-gray-900">Cari Sampel</span>
                            </div>
                            <i data-lucide="arrow-right" class="w-5 h-5 text-gray-400 group-hover:text-green-600"></i>
                        </button>
                        
                        <button class="w-full flex items-center justify-between p-3 text-left border border-gray-200 rounded-lg hover:bg-orange-50 hover:border-orange-300 transition-all duration-200 group">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors duration-200">
                                    <i data-lucide="shield-check" class="w-5 h-5 text-orange-600"></i>
                                </div>
                                <span class="font-medium text-gray-900">Kontrol Kualitas</span>
                            </div>
                            <i data-lucide="arrow-right" class="w-5 h-5 text-gray-400 group-hover:text-orange-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Today's Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Hari Ini</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Total Sampel Diterima</span>
                            <span class="text-sm font-semibold text-gray-900">36</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Hasil Selesai</span>
                            <span class="text-sm font-semibold text-green-600">28</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Masih Diproses</span>
                            <span class="text-sm font-semibold text-orange-600">8</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm font-medium text-gray-900">Efisiensi Hari Ini</span>
                            <span class="text-sm font-semibold text-blue-600">78%</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terakhir</h3>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3 p-2 rounded-lg hover:bg-gray-50">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">Hasil S24-001 telah disetujui</p>
                                <p class="text-xs text-gray-500">5 menit lalu</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-2 rounded-lg hover:bg-gray-50">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="edit" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">Sampel baru ditambahkan</p>
                                <p class="text-xs text-gray-500">10 menit lalu</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-2 rounded-lg hover:bg-gray-50">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">Stok reagen rendah</p>
                                <p class="text-xs text-gray-500">15 menit lalu</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <button class="text-sm text-med-blue hover:text-blue-700 font-medium">
                            Lihat Semua Aktivitas
                        </button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

<style>
/* Custom variables untuk konsistensi warna */
:root {
    --med-blue: #2563eb;
    --med-light-blue: #3b82f6;
    --med-orange: #f59e0b;
    --med-green: #10b981;
}

.text-med-blue { color: var(--med-blue); }
.text-med-orange { color: var(--med-orange); }
.text-med-green { color: var(--med-green); }
.bg-med-blue { background-color: var(--med-blue); }
.border-med-blue { border-color: var(--med-blue); }

/* Custom scrollbar untuk konsistensi */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 6px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 6px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation untuk loading */
@keyframes pulse-soft {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.animate-pulse-soft {
    animation: pulse-soft 2s infinite;
}
</style>

