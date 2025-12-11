<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Authentication - Theme Customizer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 3rem;
            max-width: 450px;
            width: 100%;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-header h1 {
            color: #333;
            font-size: 1.75rem;
            font-weight: 600;
        }
        .auth-header p {
            color: #666;
            font-size: 0.95rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 500;
        }
        .btn-primary:hover {
            filter: brightness(1.1);
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h1>🔐 Admin Access</h1>
            <p>Enter your settings key to access the theme admin panel</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('theme.admin.authenticate') }}">
            @csrf
            <div class="mb-4">
                <label for="key" class="form-label">Settings Key</label>
                <input type="password" class="form-control form-control-lg" id="key" name="key" 
                       placeholder="Enter settings key" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                Access Admin Panel
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                This is the same key used for /settings access
            </small>
        </div>
    </div>
</body>
</html>
