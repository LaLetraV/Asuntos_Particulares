<?php

require_once '../code/prelude.php';
require_once '../code/professor.php';
require_once '../code/user.php';

if (!empty($_POST)) {
    $op = $_POST['operacion'];

    switch ($op) {
    case 'alta':
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $dni = $_POST['dni'];
        $telefono = $_POST['telefono'];
        $departamento = $_POST['departamento'];
        $trienios = $_POST['trienios'];
        $tipo = $_POST['tipo'];
        $fecha_alta = $_POST['fecha_alta'];
        $fecha_baja = $_POST['fecha_baja'];
        $dias_asignados = $_POST['dias_asignados'];

        $user_id = add_user($usuario, $contrasena);

        add_professor(
            $user_id,
            $nombre,
            $apellidos,
            $dni,
            $telefono,
            $departamento,
            $trienios,
            $tipo,
            $fecha_alta,
            $fecha_baja,
            $dias_asignados,
        );

        break;

    case 'baja':
        $professor_id = $_POST['id'];
        $professor = fetch_professor_by_id($professor_id);

        if (!$professor) {
            break;
        }

        if ($professor['tipo'] != 'sustituto') {
            break;
        }

        $user_id = $professor['usuario_id'];
        delete_user($user_id);
        break;
    }
}

?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Panel Jefatura</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
    <link rel='stylesheet' href='../assets/css/style.css' />
</head>
<body>
    <div class="container">
        <div class="panel-header">
            <h1 class="panel-title">Panel de Jefatura</h1>
            <form method='post' action='../logout.php'>
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </button>
            </form>
        </div>

        <details>
            <summary><i class="fas fa-plus-circle"></i> Dar de alta a un docente</summary>
            <form method="post" class="form-grid">
                <input type="hidden" name="operacion" value="alta">
                
                <div class="input-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                
                <div class="input-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                </div>
                
                <!-- Resto de campos del formulario -->
                <div class="input-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="input-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" required>
                </div>
                
                <div class="input-group">
                    <label for="dni">DNI</label>
                    <input type="text" id="dni" name="dni" required pattern="[0-9]{8}[A-Z]">
                </div>
                
                <div class="input-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" required pattern="[0-9]{9}">
                </div>
                
                <div class="input-group">
                    <label for="departamento">Departamento</label>
                    <input type="text" id="departamento" name="departamento" required>
                </div>
                
                <div class="input-group">
                    <label for="trienios">Trienios</label>
                    <input type="number" id="trienios" name="trienios" required>
                </div>
                
                <div class="input-group">
                    <label for="trienios">Tipo</label>
                    <select id="tipo" name="tipo" required>
                        <option value="plantilla">Plantilla</option>
                        <option value="sustituto">Sustituto</option>
                        <option value="vacante">Vacante</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <label for="fecha_alta">Fecha de alta</label>
                    <input type="date" id="fecha_alta" name="fecha_alta" required>
                </div>
                
                <div class="input-group">
                    <label for="fecha_baja">Fecha de baja</label>
                    <input type="date" id="fecha_baja" name="fecha_baja" required>
                </div>
                
                <div class="input-group">
                    <label for="dias_asignados">Días asignados</label>
                    <input type="number" id="dias_asignados" name="dias_asignados" required>
                </div>
                
                <button type="submit" class="form-submit">Registrar docente</button>
            </form>
        </details>

        <table class="professors-table">
            <thead>
                <tr>
                    <th>Nombre completo</th>
                    <th>Departamento</th>
                    <th>Teléfono</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (fetch_all_professors() as $professor) { ?>
                <tr>
                    <td><?= $professor['nombre'] ?> <?= $professor['apellidos'] ?></td>
                    <td><?= $professor['departamento'] ?></td>
                    <td><?= $professor['telefono'] ?></td>
                    <td><?= ucfirst($professor['tipo']) ?></td>
                    <td>
                        <?php if ($professor['tipo'] == 'sustituto') { ?>
                        <form method="post">
                            <input type="hidden" name="operacion" value="baja">
                            <input type="hidden" name="id" value="<?= $professor['id'] ?>">
                            <button type="submit" class="action-btn">Dar de baja</button>
                        </form>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>