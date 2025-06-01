<?php

require_once '../code/prelude.php';
require_once '../code/professor.php';
require_once '../code/request.php';
require_once '../code/user.php';

$user_id = $_SESSION['id'];
$user = fetch_user_by_id($user_id);

if ($user['rol'] != 'jefatura') {
    header('Location: /asuntos_particulares/index.php');
    exit();
}

$professor_id = $_GET['id'];
$professor = fetch_professor_by_id($professor_id);

if (!empty($_POST)) {
    $op = $_POST['operacion'];
    $request_id = $_POST['id'];

    switch ($op) {
    case 'aceptar':
        accept_request($request_id);
        break;

    case 'denegar':
        reject_request($request_id);
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
    <script>
function submit_with_confirmation(message)
{
    if (confirm(message)) {
        let button = event.srcElement
        let form = button.parentElement
        form.submit()
    }
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

        <table class="professors-table">
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Departamento</th>
                    <th>Teléfono</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $professor['nombre'] ?> <?= $professor['apellidos'] ?></td>
                    <td><?= $professor['departamento'] ?></td>
                    <td><?= $professor['telefono'] ?></td>
                </tr>
            </tbody>
        </table>

        <table class="professors-table">
            <thead>
                <tr>
                    <th>Fecha de registro</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (fetch_requests_by_professor_id($professor['id']) as $request) { ?>
                <tr>
                    <td><?= $request['fecha_registro'] ?></td>
                    <td><?= ucfirst($request['estado']) ?></td>
                    <td>
                        <?php if ($request['estado'] == 'registrada') { ?>
                        <form method="post" style="display: inline-block">
                            <input type="hidden" name="operacion" value="aceptar">
                            <input type="hidden" name="id" value="<?= $request['id'] ?>">
                            <button type="button" class="action-btn-dangerous" onclick="javascript:submit_with_confirmation('¿Está seguro de que quiere aceptar la solicitud?')">Aceptar</button>
                        </form>
                        <form method="post" style="display: inline-block">
                            <input type="hidden" name="operacion" value="denegar">
                            <input type="hidden" name="id" value="<?= $request['id'] ?>">
                            <button type="button" class="action-btn-dangerous" onclick="javascript:submit_with_confirmation('¿Está seguro de que quiere denegar la solicitud?')">Denegar</button>
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
