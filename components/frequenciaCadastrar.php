<?php
require("../utils/conexao.php");

function listarAlunos($conn) {
    $sql = "SELECT alunoID, nome, matricula FROM aluno ORDER BY nome ASC";
    return $conn->query($sql);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["acao"]) && $_POST["acao"] == "cadastrar") {
    $descricao = $_POST["descricao"];
    $data = $_POST["data"];
    $carga = $_POST["cargaHoraria"];
    $situacao = "Aguardando aprovação do professor"; // padrão
    $participante = $_POST["participante"];

    $sql = "INSERT INTO frequencia_atividade (descricao, data, horario, situacao, participante)
            VALUES ('$descricao', '$data', '$carga', '$situacao', $participante)";

    if ($conn->query($sql)) {
        $msg = "Frequência cadastrada com sucesso!";
    } else {
        $msg = "Erro: " . $conn->error;
    }
}

$editando = false;
if (isset($_GET["editar"])) {
    $idEditar = $_GET["editar"];
    $editando = true;

    $sql = "SELECT * FROM frequencia_atividade WHERE ID = $idEditar";
    $resultado = $conn->query($sql);
    $freq = $resultado->fetch_assoc();
}

$lista = $conn->query("SELECT f.*, a.nome
                        FROM frequencia_atividade f
                        INNER JOIN aluno a ON a.alunoID = f.participante
                        ORDER BY f.data DESC");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Cadastrar Frequência</title>
    </head>
    <body>

        <h2><?php echo $editando ? "Editar Frequência" : "Cadastrar Frequência"; ?></h2>

        <?php if (isset($msg)) echo "<p><strong>$msg</strong></p>"; ?>

        <form method="POST">
            <input type="hidden" name="acao" value="<?php echo $editando ? "editar" : "cadastrar"; ?>">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?php echo $freq["ID"]; ?>">
            <?php endif; ?>

            <label>Descrição:</label><br>
            <textarea name="descricao" required><?php echo $editando ? $freq["descricao"] : ""; ?></textarea><br><br>

            <label>Data:</label><br>
            <input type="date" name="data" required value="<?php echo $editando ? $freq["data"] : ""; ?>"><br><br>

            <label>Carga Horária:</label><br>
            <input type="time" name="cargaHoraria" required value="<?php echo $editando ? $freq["horario"] : ""; ?>"><br><br>

            <label>Participante:</label><br>
            <select name="participante" required>
                <option value="">Selecione...</option>
                <?php
                    $alunos = listarAlunos($conn);
                    while ($a = $alunos->fetch_assoc()):
                ?>
                    <option value="<?php echo $a["alunoID"]; ?>"
                        <?php if ($editando && $a["alunoID"] == $freq["participante"]) echo "selected"; ?>>
                        <?php echo $a["nome"] . " - " . $a["matricula"]; ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>

            <?php if ($editando): ?>
                <label>Situação:</label><br>
                <select name="situacao">
                    <option value="Aguardando aprovação do professor" 
                        <?php if ($freq["situacao"] == "Aguardando aprovação do professor") echo "selected"; ?>>
                        Aguardando aprovação do professor
                    </option>

                    <option value="Validado"
                        <?php if ($freq["situacao"] == "Validado") echo "selected"; ?>>
                        Validado
                    </option>
                </select><br><br>
            <?php endif; ?>

            <button type="submit"><?php echo $editando ? "Salvar Alterações" : "Cadastrar"; ?></button>
        </form>

        <hr>

        <h2>Frequências Cadastradas</h2>

        <table border="1" cellpadding="7">
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Data</th>
                <th>Carga Horária</th>
                <th>Situação</th>
                <th>Aluno</th>
                <th>Ações</th>
            </tr>

            <?php while ($row = $lista->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["ID"]; ?></td>
                    <td><?php echo $row["descricao"]; ?></td>
                    <td><?php echo $row["data"]; ?></td>
                    <td><?php echo $row["horario"]; ?></td>
                    <td><?php echo $row["situacao"]; ?></td>
                    <td><?php echo $row["nome"]; ?></td>
                    <td>
                        <a href="editarFrequencia.php?id=<?= $row['ID'] ?>">Editar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    </body>
</html>
