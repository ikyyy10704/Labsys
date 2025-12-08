<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Sistem Informasi Laboratorium' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/logo/logo.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/logo/logo.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/logo/logo.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.png') ?>">
    <meta name="theme-color" content="#1E40AF">
    <meta name="msapplication-TileColor" content="#1E40AF">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'med-blue': '#1E40AF',
                        'med-light-blue': '#3B82F6',
                        'med-gray': '#6B7280',
                        'med-green': '#10B981',
                        'med-orange': '#F97316',
                        'med-red': '#EF4444'
                    }
                }
            }
        }
    </script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS - Enhanced with Proper Fullwidth System -->
    <style>
        /* ========================================= */
        /* BASE LAYOUT - Fullwidth Implementation    */
        /* ========================================= */
        body { 
            overflow-y: auto !important; 
            height: auto !important;
            margin: 0;
            padding: 0;
        }
        
        /* Main Container */
        .main-layout {
            width: 100%;
            min-height: 100vh;
            display: flex;
        }
        
        /* ========================================= */
        /* SIDEBAR - Fixed Position                  */
        /* ========================================= */
        .sidebar {
            width: 256px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 50;
        }
        
        /* ========================================= */
        /* MAIN CONTENT - Responsive Width           */
        /* ========================================= */
        .main-content {
            flex: 1;
            margin-left: 256px;
            width: calc(100% - 256px);
            min-height: 100vh;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        
        /* ========================================= */
        /* MOBILE RESPONSIVE - Tablet & Phone        */
        /* ========================================= */
        @media (max-width: 768px) {
            /* Hide sidebar by default */
            .sidebar {
                transform: translateX(-100%);
            }
            
            /* Show sidebar when menu open */
            .sidebar.mobile-open {
                transform: translateX(0) !important;
            }
            
            /* Full width content on mobile */
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            /* Mobile overlay backdrop */
            .mobile-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 45;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }
            
            .mobile-overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }
        
        /* ========================================= */
        /* MENU STYLING - Keep Original Blue Theme  */
        /* ========================================= */
        .sidebar-menu .active {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            color: #FFFFFF;
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
        }
        
        .sidebar-menu .active .menu-count {
            background-color: #FFFFFF;
            color: #1E40AF;
        }
        
        .sidebar-menu a:not(.active):hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
            transform: translateX(4px);
        }
        
        .sidebar-menu a:not(.active) .menu-count {
            background-color: #1E40AF;
            color: #FFFFFF;
        }
        
        .sidebar-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        
        .sidebar-submenu.active {
            max-height: 500px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* ========================================= */
        /* SMOOTH ANIMATIONS                         */
        /* ========================================= */
        .sidebar, .main-content, .mobile-overlay {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Navigation link hover effects */
        .nav-link {
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        
        /* Focus states */
        .nav-link:focus {
            outline: 2px solid rgba(255, 255, 255, 0.5);
            outline-offset: 2px;
        }
        
        /* Touch optimizations */
        @media screen and (max-width: 768px) and (orientation: portrait) {
            .sidebar {
                height: 100vh;
                height: 100dvh;
            }
            .nav-link {
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
                -webkit-tap-highlight-color: transparent;
                min-height: 44px;
            }
        }
    </style>
    
    <?php 
    date_default_timezone_set('Asia/Jakarta');
    ?>
    
    <!-- Mobile Menu JavaScript - FIXED VERSION -->
    <script>
        // Store current page info for active state detection
        window.currentController = '<?= $this->router->class ?>';
        window.currentMethod = '<?= $this->router->method ?>';
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing sidebar...');
            initializeSidebar();
        });
        
        function initializeSidebar() {
            try {
                setupMobileMenu();
                setupNavigationEnhancements();
                setupKeyboardNavigation();
                setupAccessibility();
                setupTouchGestures();
                
                // Initialize Lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                    console.log('Lucide icons initialized');
                }
            } catch (error) {
                console.error('Error initializing sidebar:', error);
            }
        }
        
        function setupMobileMenu() {
            // CRITICAL FIX: Use correct ID from sidebar.php
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            console.log('Mobile menu elements:', {
                button: !!mobileMenuBtn,
                sidebar: !!sidebar,
                overlay: !!overlay
            });
            
            if (!sidebar) {
                console.error('Sidebar element not found!');
                return;
            }
            
            if (!overlay) {
                console.error('Overlay element not found!');
                return;
            }
            
            function toggleSidebar(show = null) {
                console.log('Toggle sidebar called, show:', show);
                
                const isOpen = sidebar.classList.contains('mobile-open');
                const shouldShow = show !== null ? show : !isOpen;
                
                console.log('Current state - isOpen:', isOpen, 'shouldShow:', shouldShow);
                
                if (shouldShow) {
                    // Open sidebar
                    sidebar.classList.add('mobile-open');
                    overlay.classList.add('active');
                    document.body.classList.add('overflow-hidden');
                    
                    if (mobileMenuBtn) {
                        updateMenuIcon('x');
                        mobileMenuBtn.setAttribute('aria-expanded', 'true');
                    }
                    
                    document.addEventListener('keydown', handleEscapeKey);
                    
                    // Focus first nav item
                    const firstNavItem = sidebar.querySelector('.nav-link');
                    if (firstNavItem) setTimeout(() => firstNavItem.focus(), 100);
                    
                    console.log('Sidebar opened');
                } else {
                    // Close sidebar
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                    
                    if (mobileMenuBtn) {
                        updateMenuIcon('menu');
                        mobileMenuBtn.setAttribute('aria-expanded', 'false');
                    }
                    
                    document.removeEventListener('keydown', handleEscapeKey);
                    
                    console.log('Sidebar closed');
                }
            }
            
            function updateMenuIcon(iconType) {
                if (!mobileMenuBtn) return;
                const menuIcon = mobileMenuBtn.querySelector('i');
                if (menuIcon) {
                    menuIcon.setAttribute('data-lucide', iconType);
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            }
            
            function handleEscapeKey(event) {
                if (event.key === 'Escape') {
                    toggleSidebar(false);
                }
            }
            
            // Button click handler
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Mobile menu button clicked');
                    toggleSidebar();
                });
                console.log('Mobile button listener attached');
            } else {
                console.error('Mobile menu button not found!');
            }
            
            // Overlay click handler
            overlay.addEventListener('click', function() {
                console.log('Overlay clicked');
                toggleSidebar(false);
            });
            
            // Close menu when clicking sidebar link
            const menuLinks = sidebar.querySelectorAll('a[href]:not([href="#"])');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        setTimeout(() => toggleSidebar(false), 150);
                    }
                });
            });
            
            // Handle window resize
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    if (window.innerWidth >= 768) {
                        sidebar.classList.remove('mobile-open');
                        overlay.classList.remove('active');
                        document.body.classList.remove('overflow-hidden');
                        if (mobileMenuBtn) {
                            updateMenuIcon('menu');
                            mobileMenuBtn.setAttribute('aria-expanded', 'false');
                        }
                    }
                }, 250);
            });
            
            // Expose function globally for debugging
            window.toggleSidebar = toggleSidebar;
        }
        
        function setupTouchGestures() {
            if (!('ontouchstart' in window)) return;
            
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;
            
            let touchStartX = 0;
            let isSwiping = false;
            
            sidebar.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
                isSwiping = true;
            }, { passive: true });
            
            sidebar.addEventListener('touchmove', function(e) {
                if (!isSwiping) return;
                
                const touchX = e.touches[0].clientX;
                const deltaX = touchX - touchStartX;
                
                // Swipe left to close
                if (deltaX < -50 && window.innerWidth < 768) {
                    if (window.toggleSidebar) window.toggleSidebar(false);
                    isSwiping = false;
                }
            }, { passive: true });
            
            sidebar.addEventListener('touchend', function() {
                isSwiping = false;
            }, { passive: true });
        }
        
        function setupNavigationEnhancements() {
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    createRippleEffect(e, this);
                });
            });
        }
        
        function createRippleEffect(event, element) {
            const ripple = document.createElement('span');
            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(59, 130, 246, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
                z-index: 1;
            `;
            
            if (!document.querySelector('#ripple-style')) {
                const style = document.createElement('style');
                style.id = 'ripple-style';
                style.textContent = `
                    @keyframes ripple {
                        to {
                            transform: scale(2);
                            opacity: 0;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
            
            element.style.position = 'relative';
            element.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        }
        
        function setupKeyboardNavigation() {
            const navLinks = document.querySelectorAll('.nav-link');
            let currentIndex = -1;
            
            document.addEventListener('keydown', function(e) {
                // Alt + Arrow keys to navigate menu
                if (e.altKey && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
                    e.preventDefault();
                    
                    if (e.key === 'ArrowDown') {
                        currentIndex = Math.min(currentIndex + 1, navLinks.length - 1);
                    } else {
                        currentIndex = Math.max(currentIndex - 1, 0);
                    }
                    
                    if (navLinks[currentIndex]) {
                        navLinks[currentIndex].focus();
                    }
                }
                
                // Enter to click focused link
                if (e.key === 'Enter' && document.activeElement.classList.contains('nav-link')) {
                    document.activeElement.click();
                }
            });
        }
        
        function setupAccessibility() {
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach((link, index) => {
                link.setAttribute('role', 'menuitem');
                link.setAttribute('tabindex', index === 0 ? '0' : '-1');
                
                link.addEventListener('focus', function() {
                    navLinks.forEach(l => {
                        l.setAttribute('tabindex', l === this ? '0' : '-1');
                    });
                });
            });
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Mobile Overlay - FIXED ID -->
    <div class="mobile-overlay" id="mobile-overlay"></div>
    
    <!-- Main Layout Container -->
    <div class="main-layout">