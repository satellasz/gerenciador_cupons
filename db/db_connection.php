<?php
    // criar conexão

    $conn = new mysqli("localhost", "root", "");

    if($conn->connect_error){
        echo "ERRO na conexao" . $conn->connect_error;
        exit();
    }

    $db = "descontos";

    // cria o banco de dados
    $sql = "CREATE DATABASE IF NOT EXISTS $db";

    if ($conn->query($sql) === FALSE) {
        echo "Erro ao criar banco: " . $conn->error . "<br>";
    }

    // seleciona o banco
    $conn->select_db($db);


    // echo "<a href='../public/index.php'>Menu</a><br>";
?>