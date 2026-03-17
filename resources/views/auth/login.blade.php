<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="{{ asset('frontend_assets/images/logo/favicon.jpg') }}">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .login-title {
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="card login-card">
        <div class="card-body p-4">

            <h3 class="text-center mb-4 login-title">
                <i class="fa-solid fa-right-to-bracket me-2"></i>Login
            </h3>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('admin.loginAttempt') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-success w-100">
                    Login
                </button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
