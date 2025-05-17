<?php

$conn = null;

function connect_to_database()
{
    global $conn;

    if ($conn) {
        return $conn;
    }

    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'gestion_asuntos';

    $conn = new PDO(
        "mysql:host=$host;dbname=$database",
        $username,
        $password,
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

?>