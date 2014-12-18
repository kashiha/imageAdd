<?php
$pdo = new PDO(
        'mysql:host=localhost;dbname=test;charset=utf8',
        'root',
        's1413109db',
        array(
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        )
    );
?>