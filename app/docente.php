<?php

require_once '../code/prelude.php';
require_once '../code/professor.php';
require_once '../code/request.php';

$user_id = $_SESSION['id'];
$professor = fetch_professor_by_user_id($user_id);

if (!$professor) {
    $_SESSION = array();
    header('Location: /asuntos_particulares/index.php');
    exit();
}

if (!empty($_POST)) {
    $op = $_POST['operacion'];

    switch ($op) {
    case 'alta':
        add_request($professor['id']);
        break;

    case 'baja':
        $id = $_POST['id'];
        delete_request($id);
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
            <h1 class="panel-title">
                Panel de
                <?= $professor['nombre'] ?>
                <?= $professor['apellidos'] ?>
            </h1>
            <form method='post' action='../logout.php'>
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </button>
            </form>
        </div>

        <details>
            <summary><i class="fas fa-plus-circle"></i> Solicitar días</summary>
            <form method="post" class="form-grid">
                <input type="hidden" name="operacion" value="alta">
                
                <button type="submit" class="form-submit">Registrar solicitud</button>
            </form>
        </details>

        <table class="requests-table">
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
                        <form method="post">
                            <input type="hidden" name="operacion" value="baja">
                            <input type="hidden" name="id" value="<?= $request['id'] ?>">
                            <button type="submit" class="action-btn">Cancelar</button>
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