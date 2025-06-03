<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - @yield('title', 'App')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        :root {
            --psu-primary: #1e3a8a;
            --psu-secondary: #3b82f6;
            --psu-accent: #fbbf24;
            --sidebar-width: 280px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: linear-gradient(135deg, var(--psu-primary) 0%, var(--psu-secondary) 100%);
            box-shadow: 0 4px 20px rgba(30, 58, 138, 0.15);
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 2rem;
        }

        .header .logo-section {
            display: flex;
            align-items: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .header .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--psu-accent);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: white;
            font-size: 1.2rem;
            border: 20;
        }

        .header .user-section {
            margin-left: auto;
            position: relative;
        }

        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-trigger {
            display: flex;
            align-items: center;
            color: white;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 12px;
            transition: all 0.2s ease;
            border: none;
            background: transparent;
        }

        .user-trigger:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .user-info {
            text-align: left;
        }

        .user-welcome {
            font-size: 0.9rem;
            margin-bottom: 2px;
        }

        .user-role {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        .dropdown-arrow {
            margin-left: 8px;
            font-size: 0.8rem;
            transition: transform 0.2s ease;
        }

        .user-dropdown.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-menu-custom {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(226, 232, 240, 0.8);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1001;
            margin-top: 8px;
        }

        .user-dropdown.active .dropdown-menu-custom {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 1rem 1.25rem 0.5rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
        }

        .dropdown-user-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .dropdown-user-role {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 2px;
        }

        .dropdown-item-custom {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-item-custom:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .dropdown-item-custom i {
            width: 18px;
            margin-right: 10px;
            font-size: 0.9rem;
        }

        .dropdown-item-custom.logout {
            color: #dc2626;
            border-top: 1px solid #e2e8f0;
            margin-top: 0.5rem;
        }

        .dropdown-item-custom.logout:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            background: white;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-nav {
            padding: 2rem 0;
            height: 100%;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.1) 0%, transparent 100%);
            color: var(--psu-secondary);
            border-left-color: var(--psu-secondary);
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(30, 58, 138, 0.1) 0%, transparent 100%);
            color: var(--psu-primary);
            border-left-color: var(--psu-primary);
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            min-height: calc(100vh - var(--header-height));
        }

        .content-header {
            margin-bottom: 2rem;
        }

        .content-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .content-subtitle {
            color: #64748b;
            font-size: 1.1rem;
        }

        /* Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: between;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--psu-primary), var(--psu-secondary));
            color: white;
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #64748b;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header {
                padding: 0 1rem;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        /* Overlay for dropdown */
        .dropdown-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .dropdown-overlay.active {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo-section">
            <div class="logo-icon">
            <img src="{{ asset('images/Pangasinan_State_University_logo.png') }}" alt="University Logo" style="width: 40px; height: 40px;">
            </div>
            <div>
                <div>Property Stock Card</div>
                <div style="font-size: 0.8rem; opacity: 0.8;">Pangasinan State University</div>
            </div>
        </div>
        <div class="user-section">
            <div class="user-dropdown" id="userDropdown">
                <button class="user-trigger" type="button">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <div class="user-welcome">Welcome back</div>
                        <div class="user-role">Administrator</div>
                    </div>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="dropdown-menu-custom">
                    <div class="dropdown-header">
                        <div class="dropdown-user-name">Administrator</div>
                        <div class="dropdown-user-role">System Administrator</div>
                    </div>
                  
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item-custom logout">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Dropdown Overlay -->
    <div class="dropdown-overlay" id="dropdownOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main Menu</div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </div>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('entities.*') ? 'active' : '' }}" href="{{ route('entities.index') }}">
                        <i class="fas fa-building"></i>
                        Entities
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}" href="{{ route('branches.index') }}">
                        <i class="fas fa-code-branch"></i>
                        Branches
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('fund_clusters.*') ? 'active' : '' }}" href="{{ route('fund_clusters.index') }}">
                        <i class="fas fa-layer-group"></i>
                        Fund Clusters
                    </a>
                </div>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Equipment</div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('received_equipment.*') ? 'active' : '' }}" href="{{ route('received_equipment.index') }}">
                        <i class="fas fa-inbox"></i>
                        Received Equipment
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                        <i class="fas fa-clipboard-list"></i>
                        Inventory
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('property_cards.*') ? 'active' : '' }}" href="{{ route('property_cards.index') }}">
                        <i class="fas fa-clipboard-list"></i>
                        Property Cards
                    </a>
                </div>
           
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
    
    <script>
        // User dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdown = document.getElementById('userDropdown');
            const dropdownOverlay = document.getElementById('dropdownOverlay');
            const userTrigger = userDropdown.querySelector('.user-trigger');

            // Toggle dropdown
            userTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
                dropdownOverlay.classList.toggle('active');
            });

            // Close dropdown when clicking overlay
            dropdownOverlay.addEventListener('click', function() {
                userDropdown.classList.remove('active');
                dropdownOverlay.classList.remove('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('active');
                    dropdownOverlay.classList.remove('active');
                }
            });

            // Prevent dropdown from closing when clicking inside
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Animate stats on load
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const finalNumber = stat.textContent;
                stat.textContent = '0';
                animateNumber(stat, finalNumber, 2000);
            });
        });

        function animateNumber(element, target, duration) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current);
                }
            }, 16);
        }
    </script>
</body>
</html>