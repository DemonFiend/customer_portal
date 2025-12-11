<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plugin Creator - SCXP-BetterPortal</title>
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

        .form-control, .form-select, textarea {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .form-control:focus, .form-select:focus, textarea:focus {
            background-color: var(--card-bg);
            color: var(--text-primary);
            border-color: #667eea;
        }

        .form-check-input {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="mb-4">
                    <a href="/admin" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Admin
                    </a>
                </div>

                <div class="card shadow-lg">
                    <div class="card-header">
                        <h3 class="mb-0">
                            <i class="fas fa-puzzle-piece"></i> Plugin Creator
                            <span class="badge bg-primary ms-2">SCXP-BetterPortal</span>
                        </h3>
                        <p class="mb-0 mt-2 text-muted">Create a new plugin for your customer portal</p>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $error)
                                    <p class="mb-0">{{ $error }}</p>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('theme.admin.create-plugin') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold">
                                    <i class="fas fa-tag"></i> Plugin Name *
                                </label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                       placeholder="e.g., My Custom Plugin" 
                                       pattern="^[a-zA-Z0-9\s]+$" 
                                       title="Plugin name can only contain letters, numbers, and spaces"
                                       required>
                                <small class="form-text text-muted">
                                    The name will be converted to PascalCase for the class name (spaces will be removed). Only letters, numbers, and spaces allowed.
                                </small>
                            </div>

                            <div class="mb-4">
                                <label for="author" class="form-label fw-bold">
                                    <i class="fas fa-user"></i> Author Name *
                                </label>
                                <input type="text" class="form-control form-control-lg" id="author" name="author" 
                                       placeholder="Your Name" required>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label fw-bold">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Brief description of what your plugin does..."></textarea>
                                <small class="form-text text-muted">
                                    Optional: Describe your plugin's purpose and features
                                </small>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="restart_after" 
                                           name="restart_after" style="width: 3rem; height: 1.5rem;">
                                    <label class="form-check-label ms-2" for="restart_after">
                                        <strong>Restart Server After Installing</strong>
                                        <br>
                                        <small class="text-muted">
                                            Automatically restart the portal after plugin creation (recommended)
                                        </small>
                                    </label>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle"></i> What will be created:
                                </h6>
                                <ul class="mb-0">
                                    <li>Complete plugin directory structure</li>
                                    <li>Service provider with auto-loading features</li>
                                    <li>Controllers, models, and routes scaffolding</li>
                                    <li>View templates and configuration files</li>
                                    <li>Database migration template</li>
                                    <li>README documentation</li>
                                </ul>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                                <a href="/admin" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-magic"></i> Create Plugin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <h6><i class="fas fa-lightbulb"></i> After Creation:</h6>
                        <ol class="mb-0">
                            <li>Your plugin will be created in <code>plugins/your-plugin-name/</code></li>
                            <li>The autoloader will be automatically regenerated</li>
                            <li>Edit <code>plugin.json</code> to configure your plugin</li>
                            <li>Modify the service provider in <code>src/</code> to add functionality</li>
                            <li>Plugin will be automatically discovered and loaded</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Apply theme from localStorage
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', currentTheme);
    </script>
</body>
</html>
