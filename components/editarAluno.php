<?php
if (!isset($conn)) {
    require_once "../utils/conexao.php";
}

$mensagem = "";
$mensagemTipo = "";

// Processar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_aluno'])) {
    $alunoID = $_POST['alunoID'];
    $novoStatus = $_POST['status'];

    $sqlUpdate = "UPDATE aluno SET status = ? WHERE alunoID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ii", $novoStatus, $alunoID);

    if ($stmtUpdate->execute()) {
        $mensagem = "Status atualizado com sucesso!";
        $mensagemTipo = "success";

        // Detecta o caminho correto baseado no $nivel
        $redirectPath = isset($nivel) && $nivel === './' ? './' : '../';

        echo "<script>
            setTimeout(() => {
                window.location.href = '{$redirectPath}';
            }, 1500);
        </script>";
    } else {
        $mensagem = "Erro ao atualizar: " . $stmtUpdate->error;
        $mensagemTipo = "error";
    }
}
?>

<div id="modalEditar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Editar Aluno</h2>
            <button class="modal-close" onclick="closeModal('modalEditar')">&times;</button>
        </div>

        <form method="POST">
            <div class="modal-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= $mensagemTipo ?>">
                        <?= $mensagem ?>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="alunoID" id="editAlunoID">

                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" id="editNome" disabled
                        style="background-color: var(--color-surface); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label>Matrícula:</label>
                    <input type="text" id="editMatricula" disabled
                        style="background-color: var(--color-surface); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label>Ano de Entrada:</label>
                    <input type="text" id="editAnoEntrada" disabled
                        style="background-color: var(--color-surface); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="editStatus" name="status">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditar')">Cancelar</button>
                <button type="submit" name="editar_aluno" class="btn">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>