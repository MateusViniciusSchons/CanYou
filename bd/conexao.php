<?php
    $hostname = "localhost";
    $user = "root"; 
    $password = "";
    $database = "dbprojeto";

    $conexao = mysqli_connect($hostname, $user, $password, $database);
    if (!$conexao) {
        echo "Falha na conexão com o Banco de Dados! ";
         die("Connection failed: " . mysqli_connect_error());
    }
?>