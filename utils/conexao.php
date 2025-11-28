<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "IFPlay_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
} else {
    //echo "Conexão bem-sucedida com o banco de dados.";
}

?>