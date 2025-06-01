<?php

require_once 'database.php';

function fetch_user_by_name($username)
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('
        SELECT *
        FROM usuario
        WHERE usuario = :usuario
    ');

    $stmt->bindValue(':usuario', $username);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        return null;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetch_user_by_id($id)
{
    $conn = connect_to_database();

    $stmt = $conn->prepare('
        SELECT *
        FROM usuario
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        return null;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function add_user($username, $password)
{
    $conn = connect_to_database();

    $stmt = $conn->prepare('
        INSERT INTO usuario (
            usuario,
            contrasena,
            rol
        ) VALUES (
            :username,
            :password,
            :role
        )');

    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
    $stmt->bindValue(':role', 'docente');
    $stmt->execute();

    return $conn->lastInsertId();
}

function delete_user($id)
{
    $conn = connect_to_database();
    $stmt = $conn->prepare('
        DELETE FROM usuario
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id);
    $stmt->execute();
}

function log_in_with_credentials($username, $password)
{
    $user = fetch_user_by_name($username);

    if (!$user) {
        return;
    }

    $hash = $user['contrasena'];
    $role = $user['rol'];

    if (password_verify($password, $hash)) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['token'] = $hash;
        header("Location: /asuntos_particulares/app/$role.php");
        exit();
    }
}

function log_in_with_token($username, $token)
{
    $user = fetch_user_by_name($username);

    if (!$user) {
        return;
    }

    $hash = $user['contrasena'];
    $role = $user['rol'];

    if ($hash == $token) {
        header("Location: /asuntos_particulares/app/$role.php");
        exit();
    }
}

?>
