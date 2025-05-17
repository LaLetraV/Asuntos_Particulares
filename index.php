<?php
require_once 'code/prelude.php';
require_once 'code/user.php';

$login_mistake = false;

if (!empty($_POST)) {
    $username = $_POST['usuario'];
    $password = $_POST['contrasena'];

    if (!log_in_with_credentials($username, $password)) {
        $login_mistake = true;
    }
} else if (!empty($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $token = $_SESSION['token'];
    log_in_with_token($username, $token);
}
?>
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Iniciar Sesión</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url("assets/img/augustobriga.jpg");
            background-size: cover;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #2d3748;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        input {
            width: 100%;
            padding: 12px 20px 12px 40px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }

        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-message i {
            font-size: 1.2rem;
        }

        .additional-links {
            margin-top: 25px;
            text-align: center;
        }

        .additional-links a {
            color: #4a5568;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .additional-links a:hover {
            color: #667eea;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Bienvenido</h1>
            <p>Inicia sesión en tu cuenta</p>
        </div>

        <form method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input name="usuario" type="text" placeholder="Nombre de usuario" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input name="contrasena" type="password" placeholder="Contraseña" required>
            </div>

            <button type="submit">Ingresar</button>
        </form>

        <?php if ($login_mistake) { ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span>Usuario o contraseña incorrectos. Por favor, compruebe los datos introducidos.</span>
            </div>
        <?php } ?>
    </div>
</body>
</html>