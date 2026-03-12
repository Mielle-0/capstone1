<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #1a2530 100%);
            color: white;
            min-height: 100vh;
            padding: 0;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .sidebar .list-group-item {
            background: transparent;
            border: none;
            color: #b8c7ce;
            padding: 0.85rem 1.5rem;
            border-radius: 0;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar .list-group-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: rgba(52, 152, 219, 0.2);
            transition: width 0.3s ease;
        }
        
        .sidebar .list-group-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
            padding-left: 2rem;
        }
        
        .sidebar .list-group-item:hover:before {
            width: 4px;
        }
        
        .sidebar .list-group-item.active {
            color: white;
            background: linear-gradient(90deg, #3498db 0%, #2980b9 100%);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #1abc9c;
        }
        
        .sidebar .list-group-item.active:before {
            display: none;
        }
        
        .sidebar .list-group-item span {
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .sidebar .list-group-item:hover span {
            transform: translateX(3px);
        }
        
        .sidebar-icon {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            transition: transform 0.2s ease;
        }
        
        .sidebar .list-group-item:hover .sidebar-icon {
            transform: scale(1.1);
        }
        
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
            text-align: center;
            font-size: 0.85rem;
            color: #7b8a8b;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                border-radius: 0 0 10px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 p-0">
                <div class="sidebar d-flex flex-column">
                    <div class="sidebar-header">
                        <h3>Admin Panel</h3>
                    </div>
                    
                    <div class="list-group list-group-flush flex-grow-1">
                        <a href="/dashboard"
                           class="list-group-item list-group-item-action d-flex align-items-center
                                  {{ request()->is('dashboard') ? 'active' : '' }}">
                            <span class="sidebar-icon">📊</span>
                            <span class="ms-3">Dashboard</span>
                        </a>
                        
                        <!-- Uncomment when needed
                        <a href="/inbox"
                           class="list-group-item list-group-item-action d-flex align-items-center
                                  {{ request()->is('inbox') ? 'active' : '' }}">
                            <span class="sidebar-icon">📥</span>
                            <span class="ms-3">Inbox</span>
                        </a>
                        -->

                        <a href="/feedbacks"
                           class="list-group-item list-group-item-action d-flex align-items-center
                                  {{ request()->is('feedbacks') ? 'active' : '' }}">
                            <span class="sidebar-icon">📂</span>
                            <span class="ms-3">Feedbacks</span>
                        </a>
                        
                        <a href="/search"
                           class="list-group-item list-group-item-action d-flex align-items-center
                                  {{ request()->is('search') ? 'active' : '' }}">
                            <span class="sidebar-icon">🔎</span>
                            <span class="ms-3">Search</span>
                        </a>
                        
                        <a href="/analytics"
                           class="list-group-item list-group-item-action d-flex align-items-center
                                  {{ request()->is('analytics') ? 'active' : '' }}">
                            <span class="sidebar-icon">📈</span>
                            <span class="ms-3">Analytics</span>
                        </a>
                        
                        <a href="/departments"
                           class="list-group-item list-group-item-action d-flex align-items-center
                                  {{ request()->is('departments') ? 'active' : '' }}">
                            <span class="sidebar-icon">🏢</span>
                            <span class="ms-3">Departments</span>
                        </a>
                        
                        <a href="/audit-log"
                           class="list-group-item list-group-item-action d-flex align-items-center
                                  {{ request()->is('audit-log') ? 'active' : '' }}">
                            <span class="sidebar-icon">📜</span>
                            <span class="ms-3">Audit Log</span>
                        </a>
                        
                        <a href="/settings"
                           class="list-group-item list-group-item-action d-flex align-items-center
                                  {{ request()->is('settings') ? 'active' : '' }}">
                            <span class="sidebar-icon">⚙️</span>
                            <span class="ms-3">Settings</span>
                        </a>
                    </div>
                    
                    <div class="sidebar-footer">
                        v2.1.0
                    </div>
                </div>
            </div>
            
            <div class="col-md-9 col-lg-10 p-4">
                <h1>Main Content Area</h1>
                <p>This is where your main application content would appear.</p>
                <p>Try clicking on the sidebar items to see the active state.</p>
            </div>
        </div>
    </div>

    <script>
        // Simple script to demonstrate active state switching
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarItems = document.querySelectorAll('.sidebar .list-group-item');
            
            sidebarItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Remove active class from all items
                    sidebarItems.forEach(i => i.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>