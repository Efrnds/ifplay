<?php
require_once "conexao.php";

if (!isset($_GET['id'])) {
    die("ID não informado.");
}

$id = $_GET['id'];
$mensagem = "";

$sql = "SELECT f.*, a.nome, a.matricula
        FROM frequencia_atividade f
        INNER JOIN aluno a ON f.participante = a.alunoID
        WHERE f.ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$freq = $result->fetch_assoc();

if (!$freq) {
    die("Registro não encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['situacao'])) {

    $situacao = $_POST['situacao'];

    $sqlUpdate = "UPDATE frequencia_atividade SET situacao = ? WHERE ID = ?";
    $stmt2 = $conn->prepare($sqlUpdate);
    $stmt2->bind_param("si", $situacao, $id);

    if ($stmt2->execute()) {
        $mensagem = "<p style='color:green'>Situação atualizada com sucesso!</p>";
        $freq['situacao'] = $situacao;
    } else {
        $mensagem = "<p style='color:red'>Erro: " . $stmt2->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Editar Frequência</title>
    </head>
    <body>

        <h2>Editar Frequência</h2>

        <?= $mensagem ?>

        <p><strong>Aluno:</strong> <?= $freq['nome'] ?> (<?= $freq['matricula'] ?>)</p>
        <p><strong>Data:</strong> <?= $freq['data'] ?></p>
        <p><strong>Carga Horária:</strong> <?= $freq['horario'] ?></p>
        <p><strong>Descrição:</strong> <?= $freq['descricao'] ?></p>

        <form method="POST">
            <label><strong>Situação:</strong></label><br>
            <select name="situacao" required>
                <option value="Aguardando aprovação do professor"
                    <?= $freq['situacao'] == "Aguardando aprovação do professor" ? "selected" : "" ?>>
                    Aguardando aprovação do professor
                </option>

                <option value="Validado"
                    <?= $freq['situacao'] == "Validado" ? "selected" : "" ?>>
                    Validado
                </option>
            </select>
            <br><br>

            <button type="submit">Salvar Alterações</button>
        </form>

        <br>
        <a href="frequenciaCadastrar.php">Voltar</a>

    </body>
</html>
