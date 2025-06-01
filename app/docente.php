<?php

require_once '../code/prelude.php';
require_once '../code/dates.php';
require_once '../code/user.php';
require_once '../code/professor.php';
require_once '../code/request.php';

$user_id = $_SESSION['id'];
$user = fetch_user_by_id($user_id);

if ($user['rol'] != 'docente') {
    header('Location: /asuntos_particulares/index.php');
    exit();
}

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
        $professor_id = $professor['id'];
        $days = explode(',', $_POST['dias']);
        add_request($professor_id, $days);
        break;

    case 'cancelar':
        $id = $_POST['id'];
        cancel_request($id);
        break;

    case 'ejecutar':
        $id = $_POST['id'];
        execute_request($id);
        break;
    }
}

?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Panel Docente</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
    <link rel='stylesheet' href='../assets/css/style.css' />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
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
            <form method="post" class="form-grid" style="text-align: center; grid-template-columns: repeat(1, 1fr)">
                <input type="hidden" name="operacion" value="alta">

                <input type="date" name="dias" id="dias" style="display: none" required />
                <script>
let threeMonthsFromNow = new Date();
threeMonthsFromNow.setMonth(threeMonthsFromNow.getMonth() + 3);

flatpickr("#dias", {
    dateFormat: "Y-m-d",
    mode: 'multiple',
    allowInput: true,
    locale: 'es',
    inline: true,
    minDate: threeMonthsFromNow,
    enable: [
<?php foreach (fetch_all_dates() as $date) { ?>
        new Date('<?= $date ?>'),
<?php } ?>
    ],
});
                </script>
                
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
                        <?php if ($request['estado'] == 'registrada' || $request['estado'] == 'aceptada') { ?>
                        <form method="post" style="display: inline-block">
                            <input type="hidden" name="operacion" value="cancelar">
                            <input type="hidden" name="id" value="<?= $request['id'] ?>">
                            <button type="button" class="action-btn-dangerous" onclick="javascript:submit_with_confirmation('¿Está seguro de que quiere cancelar la solicitud?')">Cancelar</button>
                        </form>
                        <?php } ?>
                        <?php if ($request['estado'] == 'aceptada') { ?>
                        <form method="post" style="display: inline-block">
                            <input type="hidden" name="operacion" value="ejecutar">
                            <input type="hidden" name="id" value="<?= $request['id'] ?>">
                            <button type="button" class="action-btn-dangerous" onclick="javascript:submit_with_confirmation('¿Está seguro de que quiere ejecutar la solicitud?')">Ejecutar</button>
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