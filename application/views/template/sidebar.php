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
                <h1 class="text-xl font-bold text-white">LabSy</h1>
                <p class="text-xs text-blue-100 font-medium">Sistem Informasi Laboratorium</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu - Scrollable -->
    <nav class="sidebar-menu p-4 space-y-1 flex-1 overflow-y-auto custom-scrollbar">
        
        <!-- ADMIN ROLE -->
        <?php if($this->session->userdata('role') == 'admin'): ?>
            
            <!-- Dashboard -->
            <a href="<?= base_url('admin/dashboard') ?>" 
               class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'dashboard') ? 'bg-white !text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>"
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
                        Manajemen Sistem Laboratorium
                    </p>
                </div>
                
                <a href="<?= base_url('admin/user_management') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'user_management') ? 'bg-white !text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span class="font-medium">Kelola Pengguna</span>
                    </div>
                </a>
                <a href="<?= base_url('pasien/kelola') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'pasien' && $this->uri->segment(2) == 'kelola') ? 'bg-white !text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="user" class="w-5 h-5"></i>
                        <span class="font-medium">Kelola Pasien</span>
                    </div>
                </a>
                <a href="<?= base_url('inventory/kelola') ?>" 
                    class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'inventory' && $this->uri->segment(2) == 'kelola') ? 'bg-white !text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="box" class="w-5 h-5"></i>
                            <span class="font-medium">Kelola Inventory</span>
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
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'activity_reports') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        <span class="font-medium">Laporan Aktivitas</span>
                    </div>
                </a>
                
                <a href="<?= base_url('admin/examination_reports') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'examination_reports') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                        <span class="font-medium">Laporan Pemeriksaan</span>
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
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'backup') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="database" class="w-5 h-5"></i>
                        <span class="font-medium">Cadangkan & Pulihkan DB</span>
                    </div>
                </a>
            </div>
            
        <!-- ADMINISTRASI ROLE -->
        <?php elseif($this->session->userdata('role') == 'administrasi'): ?>
            
            <!-- Dashboard -->
            <a href="<?= base_url('administrasi/dashboard') ?>" 
               class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'administrasi' && $this->uri->segment(2) == 'dashboard') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>"
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
                        Pasien
                    </p>
                </div>
                
                <a href="<?= base_url('administrasi/patient_management') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'administrasi' && $this->uri->segment(2) == 'patient_management') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span class="font-medium">Kelola Pasien</span>
                    </div>
                </a>

                <a href="<?= base_url('administrasi/examination_request') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'administrasi' && $this->uri->segment(2) == 'examination_request') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="stethoscope" class="w-5 h-5"></i>
                        <span class="font-medium">Permintaan Pemeriksaan</span>
                    </div>
                </a>
                
                <a href="<?= base_url('administrasi/patient_history') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'administrasi' && $this->uri->segment(2) == 'patient_history') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
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
                        Laporan
                    </p>
                </div>
                
                <a href="<?= base_url('Administrasi_laporan/examination_reports') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'Administrasi_laporan' && $this->uri->segment(2) == 'examination_reports') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                        <span class="font-medium">Laporan Pemeriksaan</span>
                    </div>
                </a>
            </div>
            
        <!-- PETUGAS LAB ROLE -->
        <?php elseif($this->session->userdata('role') == 'petugas_lab'): ?>
            
            <!-- Dashboard -->
            <a href="<?= base_url('laboratorium/dashboard') ?>" 
               class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'laboratorium' && $this->uri->segment(2) == 'dashboard') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>"
               title="Dashboard Lab">
                <div class="flex items-center space-x-3">
                    <i data-lucide="microscope" class="w-5 h-5"></i>
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
                   class="nav-link flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'laboratorium' && $this->uri->segment(2) == 'incoming_requests') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
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
                   <a href="<?= base_url('sample_data') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'laboratorium' && $this->uri->segment(2) == 'sample_data') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="test-tube" class="w-5 h-5"></i>
                        <span class="font-medium">Data Sampel</span>
                    </div>
                </a>
                
            <!-- INVENTORY Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Inventori
                    </p>
                </div>
                
                <a href="<?= base_url('inventory_lab/kelola') ?>" 
                   class="nav-link flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'inventory_lab' && $this->uri->segment(2) == 'kelola') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
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
                <a href="<?= base_url('sample_inventory') ?>" 
       class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'sample_inventory') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
        <div class="flex items-center space-x-3">
            <i data-lucide="flask-conical" class="w-5 h-5"></i>
            <span class="font-medium">Inventory Sampel</span>
        </div>
    </a>
                
                <a href="<?= base_url('quality_control') ?>" 
       class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'quality_control') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
        <div class="flex items-center space-x-3">
            <i data-lucide="shield-check" class="w-5 h-5"></i>
            <span class="font-medium">Quality Control</span>
        </div>
    </a>
            </div>
            
        <!-- supevisor ROLE -->

        <?php elseif($this->session->userdata('role') == 'supervisor'): ?>
                <!-- Dashboard -->
                             <a href="<?= base_url('supervisor/dashboard') ?>" 
               class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'administrasi' && $this->uri->segment(2) == 'dashboard') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>"
               title="Dashboard Administrasi">
                <div class="flex items-center space-x-3">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </div>
            </a>

                  <!-- VALIDASI PEMERIKSAAN Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Validasi Pemeriksaan
                    </p>
                </div>

                <a href="<?= base_url('Supervisor/quality_control') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'Supervisor' && $this->uri->segment(2) == 'quality_control') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span class="font-medium">Validasi Lab Results</span>
                    </div>
                </a>
                
                <a href="<?= base_url('Supervisor/histori') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'Supervisor' && $this->uri->segment(2) == 'histori') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="history" class="w-5 h-5"></i>
                        <span class="font-medium">Histori Validasi</span>
                    </div>
                </a>
            </div>

            <!-- QUALITY CONTROL ALAT Section -->
            <div class="pt-4">
                <div class="px-4 py-2 mb-2">
                    <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider">
                        Quality Control Alat
                    </p>
                </div>

                <a href="<?= base_url('Supervisor/qc_alat_validation') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'Supervisor' && $this->uri->segment(2) == 'qc_alat_validation') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                        <span class="font-medium">Validasi QC Alat</span>
                    </div>
                </a>
                
                <a href="<?= base_url('supervisor/qc_alat_history') ?>" 
                   class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?= ($this->uri->segment(1) == 'supervisor' && $this->uri->segment(2) == 'qc_alat_history') ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-white hover:bg-white hover:bg-opacity-10' ?>">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="file-clock" class="w-5 h-5"></i>
                        <span class="font-medium">Histori QC Alat</span>
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
// Enhanced Logout Confirmation with Modal
function confirmLogout() {
    const modal = createLogoutModal();
    document.body.appendChild(modal);
    
    setTimeout(() => {
        const cancelBtn = modal.querySelector('.cancel-btn');
        if (cancelBtn) cancelBtn.focus();
    }, 100);
}

function createLogoutModal() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50 p-4 animate-fade-in';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i data-lucide="log-out" class="w-6 h-6 text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Keluar</h3>
                    <p class="text-sm text-gray-500">Anda akan keluar dari sistem</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-6">Apakah Anda yakin ingin keluar dari sistem?</p>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="this.closest('.fixed').remove()" 
                        class="cancel-btn px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Batal
                </button>
                <a href="<?= base_url('auth/logout') ?>"
                   class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    Ya, Keluar
                </a>
            </div>
        </div>
    `;
    
    modal.addEventListener('click', function(e) {
        if (e.target === this) this.remove();
    });
    
    const handleEscape = function(e) {
        if (e.key === 'Escape') {
            modal.remove();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
    
    setTimeout(() => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }, 0);
    
    return modal;
}

// Add fade-in animation style
if (!document.querySelector('#logout-modal-style')) {
    const style = document.createElement('style');
    style.id = 'logout-modal-style';
    style.textContent = `
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
}
</script>

<!-- Main Content Area with Mobile Menu Button -->
<main class="main-content ml-0 md:ml-64 min-h-screen bg-gray-50 transition-all duration-300 flex flex-col">
    <!-- Mobile Menu Button -->
    <div class="md:hidden sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center justify-between p-4">
            <button id="mobile-menu-btn" 
                    class="flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                    aria-label="Toggle navigation menu"
                    aria-expanded="false">
                <i data-lucide="menu" class="w-6 h-6 text-gray-700"></i>
            </button>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                    <i data-lucide="hospital" class="w-5 h-5 text-white"></i>
                </div>
                <div class="text-right">
                    <h1 class="text-sm font-bold text-gray-900">LabSy</h1>
                    <p class="text-xs text-gray-500">Sistem Informasi Laboratorium</p>
                </div>
            </div>
        </div>
    </div>