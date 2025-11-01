<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'پنل مدیریت' ?> - ربات تلگرام</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', 'Vazir', Tahoma, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
            padding: 0;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid #e9ecef;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .sidebar-header h1 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            font-size: 12px;
            opacity: 0.9;
            margin: 0;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            margin: 5px 15px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(-3px);
        }
        
        .nav-link i {
            width: 20px;
            margin-left: 12px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        
        .main-header {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        /* Cards */
        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .card h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .card h2 i {
            margin-left: 10px;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 2px;
        }
        
        .btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn i { margin-left: 8px; }
        
        .btn-danger { 
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        .btn-danger:hover { 
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }
        
        .btn-success { 
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        }
        .btn-success:hover { 
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        
        .btn-warning { 
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #212529;
        }
        .btn-warning:hover { 
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
        }
        
        .btn-info { 
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        .btn-info:hover { 
            box-shadow: 0 8px 25px rgba(23, 162, 184, 0.4);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        /* Tables */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        th, td { 
            padding: 15px 12px; 
            text-align: right; 
            border-bottom: 1px solid #e9ecef;
        }
        
        th { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
            color: #495057;
        }
        
        tr:hover { 
            background: #f8f9fa;
        }
        
        /* Forms */
        .form-group { 
            margin-bottom: 20px; 
        }
        
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 500; 
            color: #495057;
        }
        
        input[type="text"], input[type="password"], input[type="email"], textarea, select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.9);
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }
        
        textarea { 
            min-height: 120px; 
            font-family: 'Courier New', monospace;
            resize: vertical;
        }
        
        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-left: 10px;
        }
        
        .alert-success { 
            background: rgba(40, 167, 69, 0.1);
            color: #155724; 
            border-color: rgba(40, 167, 69, 0.3);
        }
        
        .alert-danger { 
            background: rgba(220, 53, 69, 0.1);
            color: #721c24; 
            border-color: rgba(220, 53, 69, 0.3);
        }
        
        .alert-warning { 
            background: rgba(255, 193, 7, 0.1);
            color: #856404; 
            border-color: rgba(255, 193, 7, 0.3);
        }
        
        .alert-info { 
            background: rgba(23, 162, 184, 0.1);
            color: #0c5460; 
            border-color: rgba(23, 162, 184, 0.3);
        }
        
        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success { 
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white; 
        }
        
        .badge-danger { 
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white; 
        }
        
        .badge-warning { 
            background: linear-gradient(135deg, #ffc107 0%, #ffed4e 100%);
            color: #333; 
        }
        
        .badge-info { 
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white; 
        }
        
        .badge-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
        }
        
        /* Stats Cards */
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .stat-card h3 { 
            font-size: 36px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .stat-card p { 
            color: #6c757d; 
            font-size: 14px;
            font-weight: 500;
        }
        
        .stat-card i {
            font-size: 24px;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                order: 2;
            }
            
            .main-content {
                order: 1;
                padding: 15px;
            }
            
            .main-header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Custom Scrollbar */
        .main-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .main-content::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }
        
        .main-content::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 4px;
        }
        
        .main-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h1><i class="fas fa-robot"></i> پنل مدیریت ربات</h1>
                <p>مدیریت کانال تلگرام</p>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="/admin/" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/index') === false && basename($_SERVER['REQUEST_URI']) === 'admin') ? 'active' : '' ?>">
                        <i class="fas fa-chart-line"></i>
                        داشبورد
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/settings.php" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'settings') !== false ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i>
                        تنظیمات
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/channels.php" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'channels') !== false ? 'active' : '' ?>">
                        <i class="fas fa-broadcast-tower"></i>
                        کانال‌ها
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/tags.php" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'tags') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tags"></i>
                        تگ‌ها و ID
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/template.php" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'template') !== false ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i>
                        قالب پیام
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/update.php" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'update') !== false ? 'active' : '' ?>">
                        <i class="fas fa-cloud-download-alt"></i>
                        مدیریت آپدیت
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/health.php" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'health') !== false ? 'active' : '' ?>">
                        <i class="fas fa-heartbeat"></i>
                        وضعیت سیستم
                    </a>
                </div>
                <div class="nav-item" style="margin-top: 20px; border-top: 1px solid #e9ecef; padding-top: 20px;">
                    <a href="/admin/logout.php" class="nav-link" style="color: #dc3545;">
                        <i class="fas fa-sign-out-alt"></i>
                        خروج
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="main-header">
                <h1 class="page-title"><?= $pageTitle ?? 'داشبورد' ?></h1>
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'مدیر') ?></div>
                        <div style="font-size: 12px; color: #6c757d;"><?= htmlspecialchars($_SESSION['admin_role'] ?? 'admin') ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Alerts -->
            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    تنظیمات با موفقیت ذخیره شد!
                </div>
            <?php endif; ?>
            
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- Page Content -->
            <?= $content ?? '' ?>
        </div>
    </div>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let hasError = false;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = '#dc3545';
                        hasError = true;
                    } else {
                        field.style.borderColor = '#e9ecef';
                    }
                });
                
                if (hasError) {
                    e.preventDefault();
                    alert('لطفاً تمام فیلدهای اجباری را پر کنید');
                }
            });
        });
    </script>
</body>
</html>
