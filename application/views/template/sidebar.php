<aside class="fixed inset-y-0 left-0 bg-white shadow-lg w-64">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-center h-20 shadow-md">
            <img src="<?= base_url('assets/img/logo/logo.png') ?>" alt="Logo" class="h-32 w-32 ">
        </div>
        <nav class="flex-grow px-4 pb-4 md:block md:pb-0 md:overflow-y-auto pt-4">
            <ul>
                <li class="mb-3">
                    <a href="<?= base_url('index.php/beranda') ?>" 
                       class="<?= $this->uri->segment(1) == 'beranda' || $this->uri->segment(1) == '' ? 
                              'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-50' ?> 
                              flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-home mr-3"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li class="mb-3">
                    <a href="<?= base_url('index.php/programs') ?>"
                       class="<?= $this->uri->segment(1) == 'programs' ? 
                              'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-50' ?>
                              flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-project-diagram mr-3"></i>
                        <span>Programs</span>
                    </a>
                </li>
                <li class="mb-3">
                    <a href="<?= base_url('index.php/profile') ?>"
                       class="<?= $this->uri->segment(1) == 'profile' ? 
                              'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-50' ?>
                              flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-user mr-3"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li class="mb-3">
                    <a href="<?= base_url('about') ?>"
                       class="<?= $this->uri->segment(1) == 'about' ? 
                              'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-50' ?>
                              flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-info-circle mr-3"></i>
                        <span>About Us</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="mt-auto pt-8">
            <a href="logout.php" 
            class="block w-full text-center text-red-500 bg-red-700/20 hover:bg-red-700/30 p-4 rounded-lg 
                    font-medium hover:scale-105 transition-transform duration-200"
            onclick="return confirm('Apakah Anda yakin ingin logout?');">
                Logout
            </a>
        </div>
    </div>
</aside>