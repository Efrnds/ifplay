<?php
require_once "conexao.php";

$sql = "SELECT alunoID, nome, matricula, anoEntrada, status FROM aluno ORDER BY nome";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Lista de Alunos</title>
    </head>
    <body>

        <h2>Alunos Cadastrados</h2>

        <a href="cadastroAluno.php">Cadastrar novo aluno</a>
        <br><br>

        <table border="1" cellpadding="7">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Ano de Entrada</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['alunoID'] ?></td>
                        <td><?= $row['nome'] ?></td>
                        <td><?= $row['matricula'] ?></td>
                        <td><?= $row['anoEntrada'] ?></td>
                        <td><?= $row['status'] ? 'Ativo' : 'Inativo' ?></td>
                        <td>
                            <a href="editarAluno.php?id=<?= $row['alunoID'] ?>">Editar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Nenhum aluno encontrado.</td>
                </tr>
            <?php endif; ?>
        </table>

    </body>
</html>
