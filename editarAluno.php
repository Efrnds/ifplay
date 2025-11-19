<?php
require_once "conexao.php";

if (!isset($_GET['id'])) {
    die("ID do aluno não informado.");
}

$alunoID = $_GET['id'];
$mensagem = "";

$sql = "SELECT * FROM aluno WHERE alunoID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $alunoID);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();

if (!$aluno) {
    die("Aluno não encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $novoStatus = $_POST['status'];

    $sqlUpdate = "UPDATE aluno SET status = ? WHERE alunoID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ii", $novoStatus, $alunoID);

    if ($stmtUpdate->execute()) {
        $mensagem = "<p style='color:green'>Status atualizado com sucesso!</p>";
        $aluno['status'] = $novoStatus;
    } else {
        $mensagem = "<p style='color:red'>Erro ao atualizar: " . $stmtUpdate->error . "</p>";
    }
}
?>

<!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Editar Aluno</title>
    </head>
    <body>

        <h2>Editar Aluno</h2>

        <?php if ($mensagem) echo $mensagem; ?>

        <p><strong>Nome:</strong> <?= $aluno['nome'] ?></p>
        <p><strong>Matrícula:</strong> <?= $aluno['matricula'] ?></p>
        <p><strong>Ano de Entrada:</strong> <?= $aluno['anoEntrada'] ?></p>

        <form method="POST">

            <label>Status:</label><br>
            <select name="status">
                <option value="1" <?= $aluno['status'] ? 'selected' : '' ?>>Ativo</option>
                <option value="0" <?= !$aluno['status'] ? 'selected' : '' ?>>Inativo</option>
            </select>
            <br><br>

            <button type="submit">Salvar Alterações</button>
        </form>

        <br>
        <a href="listarAluno.php">Voltar à lista</a>

    </body>
</html>
