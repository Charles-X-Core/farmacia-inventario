<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PharmaTrack')</title>

    <link rel="stylesheet" href="{{ asset('css/Registro.css') }}">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
</head>

<body>

    <div class="container w-50 rounded-5">
        <div class="row justify-content-center">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4">
                        <div class="mb-1 text-center">
                            <i class="fas fa-prescription-bottle-alt fa-3x text-primary mb-2"></i>
                            <h2 class="fw-bold text-gray-800">PharmaTrack</h2>
                            <p class="text-muted">Inventario Empresarial Inteligente</p>
                        </div>

                        <form id="registerForm">
                            <!-- Nombres -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Nombres</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" id="name" class="form-control bg-light" placeholder="Nombres" required>
                                </div>
                            </div>

                            <!-- Apellidos -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Apellidos</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" id="last_name" class="form-control bg-light" placeholder="Apellidos" required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Correo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" id="email" class="form-control bg-light" placeholder="Correo" required>
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" id="password" class="form-control bg-light" placeholder="Contraseña" required>
                                </div>
                            </div>

                            <!-- Empresa -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Empresa</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-industry text-muted"></i>
                                    </span>
                                    <input type="text" id="company_name" class="form-control bg-light" placeholder="Empresa" required>
                                </div>
                            </div>

                            <!-- Documento + tipo -->
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Documento</label>

                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-id-card text-muted"></i>
                                    </span>

                                    <input type="text" id="document" class="form-control bg-light" placeholder="Número de documento" required>

                                    <select id="type_document_id" class="form-select bg-light" required>
                                        <option value="1">RUC</option>
                                        <option value="2">DNI</option>
                                    </select>
                                </div>
                            </div>

                            
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold registro">
                                Registrar
                            </button>

                            <a href="{{ route('login') }}" class="btn btn-info btn-lg fw-bold text-white volver">
                                <i class="fas fa-sign-in-alt ms-2 fa-rotate-180"></i> Volver
                            </a>
                        </div>


                        </form>

                        &nbsp;



                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

<!--El js-->
<script>
    document.getElementById('registerForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: document.getElementById('name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                company_name: document.getElementById('company_name').value,
                document: document.getElementById('document').value,
                type_document_id: document.getElementById('type_document_id').value
            })
        });

        const data = await response.json();

        if (response.ok) {
            alert("Registro exitoso");
            window.location.href = "/login";
        } else {
            console.log(data);
            alert("Error en registro");
        }
    });
</script>

</html>
