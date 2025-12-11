<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Admin - Customer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --card-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-primary: #1a1d23;
            --bg-secondary: #2d3139;
            --text-primary: #e9ecef;
            --text-secondary: #adb5bd;
            --border-color: #495057;
            --card-bg: #23272f;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar {
            background-color: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .card {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .card-header {
            background-color: var(--bg-secondary);
            border-bottom-color: var(--border-color);
            color: var(--text-primary);
        }

        .form-control, .form-select {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--card-bg);
            color: var(--text-primary);
            border-color: #667eea;
        }

        .sidebar-link {
            color: var(--text-primary);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            display: block;
            transition: all 0.2s;
        }

        .sidebar-link:hover {
            background-color: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .sidebar-link.active {
            background-color: rgba(102, 126, 234, 0.2);
            color: #667eea;
            font-weight: 600;
        }

        .theme-toggle {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .theme-toggle input {
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
            border-radius: 30px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #667eea;
        }

        input:checked + .slider:before {
            transform: translateX(30px);
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="px-3">
                    <h5 class="mb-4">
                        <i class="fas fa-palette"></i> Theme Admin
                    </h5>
                    <nav>
                        <a href="#" class="sidebar-link active">
                            <i class="fas fa-moon"></i> Dark Mode
                        </a>
                        <a href="/settings" class="sidebar-link">
                            <i class="fas fa-cog"></i> Portal Settings
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Theme Customization</h2>
                    <div>
                        <span class="me-2">Current Theme:</span>
                        <span class="badge badge-status" id="themeStatus" style="background-color: #667eea; color: white;">
                            <i class="fas fa-sun"></i> Light Mode
                        </span>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('restart'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-sync-alt fa-spin"></i> Server Restarting...
                        </h5>
                        <p class="mb-0">{{ session('restart') }}</p>
                        <hr>
                        <p class="mb-0">
                            <small>
                                <i class="fas fa-clock"></i> Estimated time: 1-2 minutes
                            </small>
                        </p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $error)
                            <p class="mb-0">{{ $error }}</p>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- SCXP-BetterPortal Core Functions -->
                <div class="card shadow-sm mb-4" style="border-left: 4px solid #667eea;">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <h5 class="mb-0">
                            <i class="fas fa-rocket"></i> SCXP-BetterPortal Core Functions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="p-3 border rounded h-100">
                                    <h6 class="mb-2">
                                        <i class="fas fa-puzzle-piece text-primary"></i> Plugin Creator
                                    </h6>
                                    <p class="text-muted small mb-3">
                                        Create new plugins through an intuitive UI interface with automatic scaffolding
                                    </p>
                                    <a href="{{ route('theme.admin.plugin-creator') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-magic"></i> Create Plugin
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded h-100">
                                    <h6 class="mb-2">
                                        <i class="fas fa-sync-alt text-warning"></i> Server Restart
                                    </h6>
                                    <p class="text-muted small mb-3">
                                        Restart the portal server (Docker container) for applying updates
                                    </p>
                                    <form method="POST" action="{{ route('theme.admin.restart-server') }}" 
                                          onsubmit="return confirm('Are you sure you want to restart the server? This will temporarily interrupt service.');">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-power-off"></i> Restart Server
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dark Mode Settings Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-adjust"></i> Dark Mode Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('theme.admin.update') }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Enable Dark Mode Feature</h6>
                                            <small class="text-muted">Allow users to toggle dark mode</small>
                                        </div>
                                        <label class="theme-toggle">
                                            <input type="checkbox" name="dark_mode_enabled" 
                                                   {{ $darkModeEnabled ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Default to Dark Mode</h6>
                                            <small class="text-muted">Set dark mode as default theme</small>
                                        </div>
                                        <label class="theme-toggle">
                                            <input type="checkbox" name="dark_mode_default" 
                                                   {{ $darkModeDefault ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Save Settings
                                    </button>
                                    <a href="/settings" class="btn btn-outline-secondary btn-lg ms-2">
                                        <i class="fas fa-cog"></i> Go to Portal Settings
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-eye"></i> Theme Preview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Current Theme</h6>
                                <p class="text-muted">Toggle dark mode to see changes:</p>
                                <label class="theme-toggle">
                                    <input type="checkbox" id="previewToggle">
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2" id="previewLabel">Light Mode</span>
                            </div>
                            <div class="col-md-6">
                                <h6>User Profile Integration</h6>
                                <p class="text-muted">
                                    When enabled, users will see a dark mode toggle in their profile page
                                    at <code>/portal/profile</code>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme preview toggle
        const previewToggle = document.getElementById('previewToggle');
        const previewLabel = document.getElementById('previewLabel');
        const themeStatus = document.getElementById('themeStatus');
        const html = document.documentElement;

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            html.setAttribute('data-theme', 'dark');
            previewToggle.checked = true;
            previewLabel.textContent = 'Dark Mode';
            themeStatus.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
        }

        previewToggle.addEventListener('change', function() {
            if (this.checked) {
                html.setAttribute('data-theme', 'dark');
                previewLabel.textContent = 'Dark Mode';
                themeStatus.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
                localStorage.setItem('theme', 'dark');
            } else {
                html.setAttribute('data-theme', 'light');
                previewLabel.textContent = 'Light Mode';
                themeStatus.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
                localStorage.setItem('theme', 'light');
            }
        });
    </script>
</body>
</html>
