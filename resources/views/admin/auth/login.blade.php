<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bayeng Putra Helm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --dark: #0f172a;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 50%, #0f172a 100%);
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #1e293b;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-brand .brand-icon {
            width: 64px;
            height: 64px;
            background: var(--primary);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .login-brand .brand-icon i {
            font-size: 1.75rem;
            color: #fff;
        }

        .login-brand h4 {
            color: #f1f5f9;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .login-brand p {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .form-floating > .form-control {
            background: #0f172a;
            border: 1px solid #334155;
            color: #f1f5f9;
            border-radius: 10px;
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            background: #0f172a;
            color: #f1f5f9;
        }

        .form-floating > label {
            color: #94a3b8;
        }

        .btn-login {
            background: var(--primary);
            border: none;
            padding: 0.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            color: #fff;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 10px;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon">
                <i class="fas fa-helmet-safety"></i>
            </div>
            <h4>Bayeng Putra Helm</h4>
            <p>Sistem Manajemen</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <i class="fas fa-exclamation-circle me-1"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-3">
                <i class="fas fa-exclamation-circle me-1"></i>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="{{ old('username') }}" required autofocus>
                <label for="username"><i class="fas fa-user me-1"></i> Username</label>
            </div>
            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password"><i class="fas fa-lock me-1"></i> Password</label>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-1"></i> Masuk
            </button>
        </form>
    </div>
</body>
</html>
