<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaTrack - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #74b9ff, #a29bfe);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
        }
        .btn-custom {
            background-color: #6c5ce7;
            color: white;
            border-radius: 8px;
        }
        .btn-custom:hover {
            background-color: #5b4cc4;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card p-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-prescription-bottle-alt fa-3x text-primary mb-2"></i>
                        <h2 class="fw-bold text-gray-800">PharmaTrack</h2>
                        <p class="text-muted">Inventario Empresarial Inteligente</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 fs-6">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="loginForm">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold text-muted">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 bg-light" id="email" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold text-muted">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 bg-light" id="password" required>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ url('/registro') }}">
                                ¿No tienes cuenta? Regístrate
                            </a>

                        </div>
                        &nbsp;

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-custom btn-lg fw-bold"><i class="fas fa-sign-in-alt me-2"></i>Ingresar al Sistema</button>
                        </div>
                    </form>

                    <form id="webLoginForm" action="{{ route('login') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="email" id="webEmail">
                        <input type="hidden" name="password" id="webPassword">
                    </form>

                    <script>
                        document.getElementById('loginForm').addEventListener('submit', async function(e) {
                            e.preventDefault();

                            const email = document.getElementById('email').value;
                            const password = document.getElementById('password').value;

                            try {
                                // First get JWT token
                                const response = await fetch('/api/login', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ email, password })
                                });

                                const data = await response.json();

                                if (data.token) {
                                    // Store token in localStorage
                                    localStorage.setItem('token', data.token);

                                    // Submit web login form
                                    document.getElementById('webEmail').value = email;
                                    document.getElementById('webPassword').value = password;
                                    document.getElementById('webLoginForm').submit();
                                } else {
                                    alert('Credenciales inválidas');
                                }
                            } catch (error) {
                                alert('Error: ' + error.message);
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
