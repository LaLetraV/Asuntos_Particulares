<?php

require_once 'database.php';

function fetch_requests_by_professor_id($professor_id)
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('
        SELECT *
        FROM solicitud
        WHERE docente_id = :id
    ');

    $stmt->bindValue(':id', $professor_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_request($professor_id)
{
    $conn = connect_to_database();

    $stmt = $conn->prepare("
        INSERT INTO solicitud (
            docente_id,
            estado
        ) VALUES (
            :professor_id,
            :status
        )");

    $stmt->bindValue(':professor_id', $professor_id);
    $stmt->bindValue(':status', 'registrada');
    $stmt->execute();

    return $conn->lastInsertId();
}

function delete_request($id)
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('
        DELETE FROM solicitud
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id);
    $stmt->execute();
}

?>