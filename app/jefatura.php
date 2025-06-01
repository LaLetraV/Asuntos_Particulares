<?php

require_once '../code/prelude.php';
require_once '../code/database.php';
require_once '../code/dates.php';
require_once '../code/professor.php';
require_once '../code/user.php';

$user_id = $_SESSION['id'];
$user = fetch_user_by_id($user_id);
$import_failed = false;

if ($user['rol'] != 'jefatura') {
    header('Location: /asuntos_particulares/index.php');
    exit();
}

if (!empty($_POST)) {
    $op = $_POST['operacion'];

    switch ($op) {
    case 'modificar_dias':
        $addition_string = $_POST['dates_to_add'];
        $deletion_string = $_POST['dates_to_remove'];
        $dates_to_add = explode(',', $addition_string);
        $dates_to_remove = explode(',', $deletion_string);

        $conn->beginTransaction();

        try {
            if ($addition_string) {
                foreach ($dates_to_add as $date) {
                    add_date($date);
                }
            }

            if ($deletion_string) {
                foreach ($dates_to_remove as $date) {
                    delete_date($date);
                }
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
        }

        break;

    case 'alta_masiva':
        $conn = connect_to_database();
        $file = $_FILES['csv'];
        $path = $file['tmp_name'];
        $handle = fopen($path, 'r');

        $conn->beginTransaction();

        try {
            while (true) {
                $row = fgetcsv($handle, null, ';');

                if (!$row) {
                    break;
                }

                [
                    $usuario,
                    $contrasena,
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
                ] = $row;

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
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
        }

        fclose($handle);
        break;

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
    <script>
available_dates = new Set([
<?php foreach (fetch_all_dates() as $date) { ?>
    '<?= $date ?>',
<?php } ?>
])

function submit_with_confirmation(message)
{
    if (confirm(message)) {
        let button = event.srcElement
        let form = button.parentElement
        form.submit()
    }
}

function update_available_dates()
{
    let button = event.srcElement
    let form = button.parentElement
    let input = document.getElementById('dias')

    let entries = input.value
        .split(',')
        .map(str => str.trim())

    let selected_dates = new Set(entries)
    let dates_to_add = selected_dates.difference(available_dates)
    let dates_to_remove = available_dates.difference(selected_dates)

    let addition_input = form.querySelector('[name=dates_to_add]')
    let deletion_input = form.querySelector('[name=dates_to_remove]')

    addition_input.value = [...dates_to_add].join(',')
    deletion_input.value = [...dates_to_remove].join(',')

    form.submit()
}
    </script>
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

        <?php if ($import_failed) { ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span>Error al importar docentes. Por favor, compruebe los datos introducidos.</span>
            </div>
        <?php } ?>

        <details>
            <summary><i class="fas fa-plus-circle"></i> Gestionar días disponibles</summary>
            <form method="post" class="form-grid" style="text-align: center; grid-template-columns: repeat(1, 1fr)">
                <input type="hidden" name="operacion" value="modificar_dias">
                <input type="hidden" name="dates_to_add">
                <input type="hidden" name="dates_to_remove">

                <input type="date" id="dias" style="display: none" required />
                <script>
flatpickr("#dias", {
    dateFormat: "Y-m-d",
    mode: 'multiple',
    allowInput: true,
    locale: 'es',
    inline: true,
    //minDate: 'today',
    defaultDate: [...available_dates],
});
                </script>
                
                <button type="button" class="form-submit" onclick="javascript:update_available_dates()">Registrar cambios</button>
            </form>
        </details>

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

        <details>
            <summary><i class="fas fa-plus-circle"></i> Dar de alta a múltiples docentes a partir de un fichero</summary>
            <form method="post" class="form-grid" enctype="multipart/form-data" style="grid-template-columns: repeat(1, 1fr)">
                <input type="hidden" name="operacion" value="alta_masiva">

                <input type="file" name="csv" accept=".csv, text/csv" />

                <button type="submit" class="form-submit">Importar docentes</button>
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
                        <form method="get" action="solicitudes.php" style="display: inline-block">
                            <input type="hidden" name="id" value="<?= $professor['id'] ?>">
                            <button type="submit" class="action-btn">Solicitudes</button>
                        </form>

                        <?php if ($professor['tipo'] == 'sustituto') { ?>
                        <form method="post" style="display: inline-block">
                            <input type="hidden" name="operacion" value="baja">
                            <input type="hidden" name="id" value="<?= $professor['id'] ?>">
                            <button type="button" class="action-btn-dangerous" onclick="javascript:submit_with_confirmation('¿Está seguro de que quiere eliminar al docente?')">Dar de baja</button>
                        </form>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        function actualizarDiasAsignados() {
            const tipo = document.getElementById('tipo').value;
            const trienios = parseInt(document.getElementById('trienios').value) || 0;
            const diasAsignadosInput = document.getElementById('dias_asignados');
            
            if (tipo === 'plantilla' || tipo === 'vacante') {
                let diasBase = 5;
                let diasAdicionales = Math.floor(trienios / 6);
                diasAsignadosInput.value = diasBase + diasAdicionales;
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('tipo').addEventListener('change', actualizarDiasAsignados);
            document.getElementById('trienios').addEventListener('input', actualizarDiasAsignados);
            actualizarDiasAsignados();
        });
    </script>
</body>
</html>