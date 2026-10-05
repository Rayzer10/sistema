<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema de Gestión</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: #0A0A0B;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 360px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo svg {
            width: 40px;
            height: 40px;
            margin-bottom: 16px;
        }

        .login-logo h1 {
            color: #FFFFFF;
            font-size: 18px;
            font-weight: 500;
            letter-spacing: -0.01em;
        }

        .login-logo p {
            color: #8A8F98;
            font-size: 14px;
            margin-top: 6px;
        }

        .login-card {
            background: #1B1B1F;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 32px;
        }

        .login-alert {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            display: none;
            align-items: center;
            gap: 8px;
            line-height: 1.4;
        }

        .login-alert.show {
            display: flex;
        }

        .login-alert.alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.25);
            color: #DC3545;
        }

        .login-alert.alert-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.25);
            color: #28A745;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            color: #8A8F98;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            background: #0A0A0B;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            padding: 10px 12px;
            color: #FFFFFF;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input::placeholder {
            color: #4A4B52;
        }

        .form-group input:focus {
            outline: none;
            border-color: #5E6AD2;
            box-shadow: 0 0 0 3px rgba(94, 106, 210, 0.15);
        }

        .form-group input.is-invalid {
            border-color: #DC3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important;
        }

        .invalid-feedback {
            color: #DC3545;
            font-size: 12px;
            margin-top: 6px;
            display: none;
        }

        .btn-login {
            width: 100%;
            background: #5E6AD2;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #545CBA;
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            animation: fadeIn 0.4s ease-out;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="40" height="40" rx="10" fill="#5E6AD2"/>
                <path d="M12 20L18 26L28 14" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h1>Sistema de Gestión</h1>
            <p>Ingresá para continuar</p>
        </div>

        <div class="login-card">
            <div id="loginAlert" class="login-alert"></div>

            <form id="loginForm" novalidate>
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email"
                           id="correo"
                           name="correo"
                           placeholder="tu@email.com"
                           required>
                    <div class="invalid-feedback">Ingrese un correo electrónico válido.</div>
                </div>

                <div class="form-group">
                    <label for="clave">Contraseña</label>
                    <input type="password"
                           id="clave"
                           name="clave"
                           placeholder="••••••••"
                           required>
                    <div class="invalid-feedback">La contraseña es obligatoria.</div>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">Iniciar Sesión</button>
            </form>
        </div>
    </div>

    <script src="<?= BASE_URL ?>resource/js/validaciones.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            FormValidation.init('#loginForm');

            var form = document.getElementById('loginForm');
            var correo = document.getElementById('correo');
            var clave = document.getElementById('clave');
            var btnLogin = document.getElementById('btnLogin');
            var loginAlert = document.getElementById('loginAlert');

            function showAlert(message, type) {
                loginAlert.textContent = message;
                loginAlert.className = 'login-alert alert-' + type + ' show';
            }

            function hideAlert() {
                loginAlert.className = 'login-alert';
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                hideAlert();

                if (!form.validate()) return;

                btnLogin.disabled = true;
                btnLogin.textContent = 'Ingresando...';

                fetch('<?= BASE_URL ?>login/loginAjax', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        correo: correo.value.trim(),
                        clave: clave.value.trim()
                    })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        showAlert('Inicio de sesión exitoso. Redirigiendo...', 'success');
                        setTimeout(function() {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        showAlert(data.message, 'danger');
                        btnLogin.disabled = false;
                        btnLogin.textContent = 'Iniciar Sesión';
                    }
                })
                .catch(function() {
                    showAlert('Error de conexión con el servidor.', 'danger');
                    btnLogin.disabled = false;
                    btnLogin.textContent = 'Iniciar Sesión';
                });
            });
        });
    </script>
</body>
</html>
