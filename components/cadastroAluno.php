<?php
require_once "../utils/conexao.php";

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $matricula = trim($_POST['matricula']);
    $anoEntrada = $_POST['anoEntrada'];
    $status = isset($_POST['status']) ? 1 : 0;

    $sql = "INSERT INTO aluno (nome, matricula, anoEntrada, status)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome, $matricula, $anoEntrada, $status);

    if ($stmt->execute()) {
        $mensagem = "<p style='color:green;'>Aluno cadastrado com sucesso!</p>";
    } else {
        $mensagem = "<p style='color:red;'>Erro: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Cadastrar Aluno</title>
    </head>
    <body>

        <h2>Cadastro de Alunos</h2>

        <?php if ($mensagem) echo $mensagem; ?>

        <form method="POST">
            <label>Nome:</label><br>
            <input type="text" name="nome" required><br><br>

            <label>Matrícula:</label><br>
            <input type="text" name="matricula" required><br><br>

            <label>Ano de Entrada:</label><br>
            <input type="date" name="anoEntrada" required><br><br>

            <button type="submit">Cadastrar</button>
        </form>

        <br>
        <a href="listarAluno.php">Ver alunos cadastrados</a>

    </body>
</html>
