<!-- Footer -->
    <footer class="bg-white border-t border-gray-200 p-6 mt-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="text-sm text-gray-600 mb-2 md:mb-0">
                © 2025 LabSy - Sistem Informasi Laboratorium. All rights reserved.
            </div>
            <div class="text-sm text-gray-500">
                Version 1.0.0 | Last Login: 
                <?php if($this->session->userdata('user_id')): ?>
                    <?= date('d M Y H:i') ?>
                <?php endif; ?>
            </div>
        </div>
    </footer>
</main>

</div> <!-- End of main layout -->

<!-- Global JavaScript Functions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Mobile menu functionality
    initializeMobileMenu();
    
    // Initialize notification system
    initializeNotifications();
    
    // Initialize form handlers
    initializeFormHandlers();
    
    // Initialize tooltips and other UI enhancements
    initializeUIEnhancements();
});

function initializeMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const mainContent = document.querySelector('.main-content');
    
    if (!mobileMenuBtn || !sidebar || !overlay) return;
    
    function toggleSidebar() {
        const isHidden = sidebar.classList.contains('-translate-x-full');
        
        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
    
    mobileMenuBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
    
    // Close sidebar on menu item click (mobile only)
    const menuLinks = sidebar.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                setTimeout(() => {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }, 150);
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });
}

function initializeNotifications() {
    // Global notification function
    window.showNotification = function(message, type = 'success') {
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 
                        type === 'error' ? 'bg-red-500' : 
                        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
        
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${bgColor} text-white transform translate-x-full transition-transform duration-300`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : type === 'warning' ? 'alert-triangle' : 'info'}" class="w-5 h-5"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 p-1 hover:bg-black hover:bg-opacity-20 rounded">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Refresh icons and animate in
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    };
}

function initializeFormHandlers() {
    // Handle form submissions with loading states
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn && !form.hasAttribute('data-no-loading')) {
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Processing...';
            
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Re-enable after 30 seconds as fallback
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }, 30000);
        }
    });
    
    // Handle AJAX forms
    window.submitAjaxForm = function(form, successCallback, errorCallback) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.textContent : '';
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Processing...';
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (successCallback) successCallback(data);
                else showNotification(data.message || 'Operation successful', 'success');
            } else {
                if (errorCallback) errorCallback(data);
                else showNotification(data.message || 'Operation failed', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (errorCallback) errorCallback(error);
            else showNotification('Network error occurred', 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    };
}

function initializeUIEnhancements() {
    // Initialize tooltips (simple implementation)
    const elementsWithTitle = document.querySelectorAll('[title]');
    elementsWithTitle.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const title = this.getAttribute('title');
            if (title && title.trim()) {
                this.setAttribute('data-original-title', title);
                this.removeAttribute('title');
                
                const tooltip = document.createElement('div');
                tooltip.className = 'fixed z-50 px-2 py-1 text-xs bg-gray-900 text-white rounded shadow-lg pointer-events-none';
                tooltip.textContent = title;
                tooltip.id = 'tooltip-' + Math.random().toString(36).substr(2, 9);
                
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
                
                this.addEventListener('mouseleave', function() {
                    tooltip.remove();
                    this.setAttribute('title', this.getAttribute('data-original-title'));
                    this.removeAttribute('data-original-title');
                }, { once: true });
            }
        });
    });
    
    // Initialize smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Initialize lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    if (images.length && 'IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('opacity-0');
                    img.classList.add('opacity-100');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => {
            img.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            imageObserver.observe(img);
        });
    }
}

// Global utility functions
window.refreshIcons = function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
};

window.formatNumber = function(num) {
    return new Intl.NumberFormat('id-ID').format(num);
};

window.formatCurrency = function(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

window.formatDate = function(dateString) {
    return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(dateString));
};

window.formatDateTime = function(dateString) {
    return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(dateString));
};

// Enhanced logout function
window.confirmLogout = function() {
    const modal = createLogoutModal();
    document.body.appendChild(modal);
    
    setTimeout(() => {
        const cancelBtn = modal.querySelector('.cancel-btn');
        if (cancelBtn) cancelBtn.focus();
    }, 100);
};

function createLogoutModal() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-xl transform transition-all">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i data-lucide="log-out" class="w-6 h-6 text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Keluar</h3>
                    <p class="text-sm text-gray-500">Keluar dari sistem</p>
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
    
    // Close modal when clicking overlay
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.remove();
        }
    });
    
    // Handle escape key
    const handleEscape = function(e) {
        if (e.key === 'Escape') {
            modal.remove();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
    
    // Refresh icons
    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 0);
    
    return modal;
}
</script>

</body>
</html>