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

function fetch_earliest_request_date_by_request_id($request_id)
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('
        SELECT fecha
        FROM solicituddia
        WHERE solicitud_id = :id
    ');

    $stmt->bindValue(':id', $request_id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        return null;
    }

    return $stmt->fetch(PDO::FETCH_COLUMN);
}

function add_request($professor_id, $days)
{
    sort($days);

    $three_months_from_now = date('Y-m-d', strtotime("+3 months"));
    $earliest_request_date = $days[0];

    if ($earliest_request_date < $three_months_from_now) {
        return null;
    }

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

    $request_id = $conn->lastInsertId();

    foreach ($days as $day) {
        $stmt = $conn->prepare("
            INSERT INTO solicituddia (
                solicitud_id,
                fecha
            ) VALUES (
                :request_id,
                :date
            )");

        $stmt->bindValue(':request_id', $request_id);
        $stmt->bindValue(':date', $day);
        $stmt->execute();
    }

    return $request_id;
}

function change_request_state($id, $state)
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('
        UPDATE solicitud
        SET estado = :estado
        WHERE id = :id
    ');

    $stmt->bindValue(':estado', $state);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
}

function accept_request($id)
{
    change_request_state($id, 'aceptada');
}

function reject_request($id)
{
    change_request_state($id, 'denegada');
}

function cancel_request($id)
{
    change_request_state($id, 'cancelada');
}

function execute_request($id)
{
    $fifteen_days_from_now = date('Y-m-d', strtotime("+15 days"));
    $earliest_request_date = fetch_earliest_request_date_by_request_id($id);

    if ($earliest_request_date >= $fifteen_days_from_now) {
        change_request_state($id, 'ejecutada');
    }
}

?>