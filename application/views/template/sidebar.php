<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden hidden transition-opacity duration-300"></div>

<!-- Sidebar -->
<aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-blue-600 via-blue-700 to-blue-800 shadow-xl transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
    <!-- Sidebar Header -->
    <div class="p-6 border-b border-blue-500 border-opacity-30 flex-shrink-0">
    <div class="flex items-center space-x-3">
        <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
            <img src="<?= base_url('assets/logo/logo.png') ?>" 
                 alt="Hospital Logo" 
                 class="w-12 h-12 object-contain">
        </div>
        <div>
            <h1 class="text-xl font-bold text-white">Labsys</h1>
            <p class="text-xs text-blue-100 font-medium">Sistem Informasi Rumah Sakit</p>
        </div>
    </div>
</div>
    <!-- Navigation Menu - Scrollable -->
    <nav class="sidebar-menu p-4 space-y-1 flex-1 overflow-y-auto custom-scrollbar">
        
        <!-- ADMIN ROLE -->
        <?php if($this->session->userdata('role') == 'admin'): ?>
            
            <!-- Dashboard -->
            <a href="<?= base_url('admin/dashboard') ?>" 
               class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 <?= ($this->router->class == 'admin' && $this->router->method == 'dashboard') ? 'bg-white bg-opacity-20 shadow-sm' : '' ?>"
               title="Dashboard Admin">
                <div class="flex items-center space-x-3">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard Admin</span>
                </div>
            </a>

            <!-- USER MANAGEMENT Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Manajemen Pengguna
                    </p>
                </div>
                
                <a href="<?= base_url('admin/users') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 <?= ($this->router->class == 'admin' && $this->router->method == 'users') ? 'bg-white bg-opacity-20 shadow-sm' : '' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        <span class="font-medium">Tambah Pengguna</span>
                    </div>
                </a>
                
                <a href="<?= base_url('admin/edit_user') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 <?= ($this->router->class == 'admin' && $this->router->method == 'edit_user') ? 'bg-white bg-opacity-20 shadow-sm' : '' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="user-cog" class="w-5 h-5"></i>
                        <span class="font-medium">Edit Pengguna</span>
                    </div>
                </a>
            </div>

            <!-- MONITORING Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Pemantauan
                    </p>
                </div>
                
                <a href="<?= base_url('admin/activity_reports') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        <span class="font-medium">Laporan Aktivitas</span>
                    </div>
                </a>
                
                <a href="<?= base_url('admin/examination_reports') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                        <span class="font-medium">Laporan Pemeriksaan</span>
                    </div>
                </a>
                
                <a href="<?= base_url('admin/financial_reports') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                        <span class="font-medium">Laporan Keuangan</span>
                    </div>
                </a>
            </div>

            <!-- DATA MANAGEMENT Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Manajemen Data
                    </p>
                </div>
                
                <a href="<?= base_url('admin/backup') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="database" class="w-5 h-5"></i>
                        <span class="font-medium">Cadangkan & Pulihkan DB</span>
                    </div>
                </a>
                
                <a href="<?= base_url('admin/operational_data') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="folder" class="w-5 h-5"></i>
                        <span class="font-medium">Data Operasional</span>
                    </div>
                </a>
                
                <a href="<?= base_url('admin/master_data') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="database" class="w-5 h-5"></i>
                        <span class="font-medium">Data Master</span>
                    </div>
                </a>
            </div>
            
        <!-- ADMINISTRASI ROLE -->
        <?php elseif($this->session->userdata('role') == 'administrasi'): ?>
            
            <!-- Dashboard -->
            <a href="<?= base_url('administrasi/dashboard') ?>" 
               class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 <?= ($this->router->class == 'administrasi' && $this->router->method == 'dashboard') ? 'bg-white bg-opacity-20 shadow-sm' : '' ?>"
               title="Dashboard Administrasi">
                <div class="flex items-center space-x-3">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </div>
            </a>

            <!-- PATIENT MANAGEMENT Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Manajemen Pasien
                    </p>
                </div>
                
                <a href="<?= base_url('administrasi/add_patient_data') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        <span class="font-medium">Tambah Data Pasien</span>
                    </div>
                </a>
                
                <a href="<?= base_url('administrasi/patient_history') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="history" class="w-5 h-5"></i>
                        <span class="font-medium">Riwayat Pasien</span>
                    </div>
                </a>
            </div>

            <!-- DATA MANAGEMENT Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Manajemen Data
                    </p>
                </div>
                
                <a href="<?= base_url('administrasi/financial_reports') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                        <span class="font-medium">Laporan Keuangan</span>
                    </div>
                </a>
                
                <a href="<?= base_url('administrasi/schedule') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                        <span class="font-medium">Jadwal</span>
                    </div>
                </a>
                
                <a href="<?= base_url('administrasi/export_data') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="download" class="w-5 h-5"></i>
                        <span class="font-medium">Ekspor Data</span>
                    </div>
                </a>
            </div>
            
        <!-- PETUGAS LAB ROLE -->
        <?php else: ?>
            
            <!-- Dashboard -->
            <a href="<?= base_url('laboratorium/dashboard') ?>" 
               class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200 <?= ($this->router->class == 'laboratorium' && $this->router->method == 'dashboard') ? 'bg-white bg-opacity-20 shadow-sm' : '' ?>"
               title="Dashboard Lab">
                <div class="flex items-center space-x-3">
                    <i data-lucide="flask-conical" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </div>
            </a>

            <!-- LAB OPERATIONS Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Operasi Laboratorium
                    </p>
                </div>
                
                <a href="<?= base_url('laboratorium/incoming_requests') ?>" 
                   class="nav-link flex items-center justify-between px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="inbox" class="w-5 h-5"></i>
                        <span class="font-medium">Permintaan Masuk</span>
                    </div>
                    <?php if(isset($stats['pending_requests']) && $stats['pending_requests'] > 0): ?>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-500 text-white">
                        <?= $stats['pending_requests'] ?>
                    </span>
                    <?php endif; ?>
                </a>
                
                <a href="<?= base_url('laboratorium/sample_data') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="test-tube" class="w-5 h-5"></i>
                        <span class="font-medium">Data Sampel</span>
                    </div>
                </a>
            </div>

            <!-- RESULT MANAGEMENT Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Manajemen Hasil
                    </p>
                </div>
                
                <a href="<?= base_url('laboratorium/input_results') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="edit" class="w-5 h-5"></i>
                        <span class="font-medium">Input Hasil</span>
                    </div>
                </a>
                
                <a href="<?= base_url('laboratorium/quality_control') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                        <span class="font-medium">Kontrol Kualitas</span>
                    </div>
                </a>
            </div>

            <!-- INVENTORY Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Inventori
                    </p>
                </div>
                
                <a href="<?= base_url('laboratorium/inventory_list') ?>" 
                   class="nav-link flex items-center justify-between px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        <span class="font-medium">Daftar Inventori</span>
                    </div>
                    <?php if(isset($stats['low_stock_items']) && $stats['low_stock_items'] > 0): ?>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500 text-white">
                        <?= $stats['low_stock_items'] ?>
                    </span>
                    <?php endif; ?>
                </a>
                
                <a href="<?= base_url('laboratorium/inventory_edit') ?>" 
                   class="nav-link flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="edit-3" class="w-5 h-5"></i>
                        <span class="font-medium">Edit Inventori</span>
                    </div>
                </a>
            </div>
            
        <?php endif; ?>
    </nav>

    <!-- User Info & Logout - Fixed at Bottom -->
    <div class="flex-shrink-0 border-t border-blue-500 border-opacity-30">
        <!-- User Info -->
        <div class="p-4">
            <div class="flex items-center space-x-3 p-3 bg-white bg-opacity-10 rounded-lg">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate" title="<?= $this->session->userdata('nama_lengkap') ?: $this->session->userdata('username') ?>">
                        <?= $this->session->userdata('nama_lengkap') ?: $this->session->userdata('username') ?>
                    </p>
                    <div class="flex items-center space-x-2 mt-1">
                        <p class="text-xs text-blue-100 capitalize font-medium">
                            <?= $this->session->userdata('role') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="p-4 pt-0">
            <button onclick="confirmLogout()" 
               class="flex items-center justify-center space-x-3 px-4 py-2 text-white rounded-lg hover:bg-white hover:bg-opacity-10 transition-all duration-200 w-full border border-white border-opacity-20 hover:border-opacity-30 group text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50"
               title="Keluar dari sistem">
                <i data-lucide="log-out" class="w-4 h-4 group-hover:animate-pulse"></i>
                <span class="font-medium">Keluar</span>
            </button>
        </div>
    </div>
</aside>

<script>
function confirmLogout() {
    if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
        window.location.href = '<?= base_url('auth/logout') ?>';
    }
}

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
    }
    
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    }
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

// Custom scrollbar styles
const style = document.createElement('style');
style.textContent = `
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
`;
document.head.appendChild(style);
</script>

<!-- Main Content Area with Mobile Menu Button -->
<main class="main-content ml-0 md:ml-64 min-h-screen bg-gray-50 transition-all duration-300">
    <!-- Mobile Menu Button -->
    <div class="md:hidden sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center justify-between p-4">
            <button id="mobile-menu-btn" 
                    class="flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                    aria-label="Toggle navigation menu">
                <i data-lucide="menu" class="w-6 h-6 text-gray-700"></i>
            </button>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i data-lucide="hospital" class="w-5 h-5 text-white"></i>
                </div>
                <div class="text-right">
                    <h1 class="text-sm font-bold text-gray-900">Labsys</h1>
                    <p class="text-xs text-gray-500">Sistem Informasi Rumah Sakit</p>
                </div>
            </div>
        </div>
    </div>