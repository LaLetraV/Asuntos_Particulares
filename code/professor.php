<?php

require_once 'database.php';

function fetch_all_professors()
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('SELECT * FROM docente');
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_professor_by_id($id)
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('
        SELECT *
        FROM docente
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        return null;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetch_professor_by_user_id($user_id)
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('
        SELECT *
        FROM docente
        WHERE usuario_id = :id
    ');

    $stmt->bindValue(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        return null;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function add_professor(
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
)
{
    $conn = connect_to_database();

    $stmt = $conn->prepare('
        INSERT INTO docente (
            usuario_id,
            nombre,
            apellidos,
            dni,
            telefono,
            departamento,
            trienios,
            tipo,
            fecha_alta,
            fecha_baja,
            dias_asignados
        ) VALUES (
            :user_id,
            :nombre,
            :apellidos,
            :dni,
            :telefono,
            :departamento,
            :trienios,
            :tipo,
            :fecha_alta,
            :fecha_baja,
            :dias_asignados
        )
    ');

    $stmt->bindValue(':user_id', $user_id);
    $stmt->bindValue(':nombre', $nombre);
    $stmt->bindValue(':apellidos', $apellidos);
    $stmt->bindValue(':dni', $dni);
    $stmt->bindValue(':telefono', $telefono);
    $stmt->bindValue(':departamento', $departamento);
    $stmt->bindValue(':trienios', $trienios);
    $stmt->bindValue(':tipo', $tipo);
    $stmt->bindValue(':fecha_alta', $fecha_alta);
    $stmt->bindValue(':fecha_baja', $fecha_baja);
    $stmt->bindValue(':dias_asignados', $dias_asignados);
    $stmt->execute();

    return $conn->lastInsertId();
}

?>