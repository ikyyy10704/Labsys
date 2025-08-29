<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'MedSystem - Hospital Info System' ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        body { 
            overflow-y: auto !important; 
            height: auto !important; 
        }
        .main-wrapper { 
            min-height: 100vh; 
            height: auto; 
        }
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
            background-color: #EFF6FF;
            color: #1E40AF;
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
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen"><?php // Div akan ditutup di footer ?>