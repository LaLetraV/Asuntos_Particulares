<?php

require_once 'database.php';

function fetch_all_dates()
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('SELECT * FROM calendariodisponible');
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function add_date($date)
{
    $conn = connect_to_database();

    $stmt = $conn->prepare('
        INSERT INTO calendariodisponible (fecha)
        VALUES (:date)
    ');

    $stmt->bindValue(':date', $date);
    $stmt->execute();

    return $conn->lastInsertId();
}

function delete_date($date)
{
    $conn = connect_to_database();

    $stmt = $conn->prepare('
        DELETE FROM calendariodisponible
        WHERE fecha = :date
    ');

    $stmt->bindValue(':date', $date);
    $stmt->execute();
}

?>
