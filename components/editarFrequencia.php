<?php
if (!isset($conn)) {
    require_once '../utils/conexao.php';
}

$mensagem = '';
$mensagemTipo = '';

// buscar alunos vinculados a uma frequência
if (isset($_GET['ajax']) && $_GET['ajax'] === 'getAlunos' && isset($_GET['frequenciaID'])) {
    header('Content-Type: application/json');
    $frequenciaID = (int) $_GET['frequenciaID'];
    
    $sqlAlunos = "SELECT a.alunoID, a.nome, a.matricula 
                  FROM aluno a
                  INNER JOIN aluno_frequencia af ON a.alunoID = af.alunoID
                  WHERE af.frequenciaID = ?
                  ORDER BY a.nome ASC";
    $stmtAlunos = $conn->prepare($sqlAlunos);
    $stmtAlunos->bind_param('i', $frequenciaID);
    $stmtAlunos->execute();
    $resultAlunos = $stmtAlunos->get_result();
    
    $alunos = [];
    while ($aluno = $resultAlunos->fetch_assoc()) {
        $alunos[] = $aluno;
    }
    
    $stmtAlunos->close();
    echo json_encode($alunos);
    exit;
}

// Processar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_frequencia'])) {
    $frequenciaID = $_POST['frequenciaID'];
    $novaSituacao = $_POST['situacao'];

    $sqlUpdate = 'UPDATE frequencia_atividade SET situacao = ? WHERE ID = ?';
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param('si', $novaSituacao, $frequenciaID);

    if ($stmtUpdate->execute()) {
        $mensagem = 'Situação atualizada com sucesso!';
        $mensagemTipo = 'success';

        $redirectPath = isset($nivel) && $nivel === './' ? './' : '../';

        echo "<script>
            setTimeout(() => {
                window.location.href = '{$redirectPath}';
            }, 1500);
        </script>";
    } else {
        $mensagem = 'Erro ao atualizar: ' . $stmtUpdate->error;
        $mensagemTipo = 'error';
    }
}
?>

<div id="modalEditarFrequencia" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Editar Frequência</h2>
            <button class="modal-close" onclick="closeModal('modalEditarFrequencia')">&times;</button>
        </div>

        <form method="POST">
            <div class="modal-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= $mensagemTipo ?>">
                        <?= $mensagem ?>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="frequenciaID" id="editFreqID">

                <div class="form-group">
                    <label>Descrição:</label>
                    <textarea id="editFreqDescricao" disabled
                        style="background-color: var(--color-surface); cursor: not-allowed;" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Data:</label>
                    <input type="text" id="editFreqData" disabled
                        style="background-color: var(--color-surface); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label>Carga Horária:</label>
                    <input type="text" id="editFreqHorario" disabled
                        style="background-color: var(--color-surface); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label>Alunos Vinculados:</label>
                    <div id="editFreqAlunos" 
                        style="max-height: 150px; overflow-y: auto; border: 1px solid var(--color-border); border-radius: 0.375rem; padding: 0.75rem; background: var(--color-surface); font-size: 0.875rem;">
                        <span style="color: var(--color-muted);">Carregando...</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="situacao">Situação:</label>
                    <select id="editFreqSituacao" name="situacao">
                        <option value="Pendente">Pendente</option>
                        <option value="Validado">Validado</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    onclick="closeModal('modalEditarFrequencia')">Cancelar</button>
                <button type="submit" name="editar_frequencia" class="btn">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>