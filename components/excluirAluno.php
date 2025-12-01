<?php
if (!isset($conn)) {
    require_once "../utils/conexao.php";
}

$mensagem = "";
$mensagemTipo = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_aluno'])) {
    $alunoID = (int)$_POST['alunoID'];
    
    // Verifica se o aluno existe
    $sqlCheck = "SELECT nome, matricula FROM aluno WHERE alunoID = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $alunoID);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    
    if ($resultCheck->num_rows > 0) {
        $aluno = $resultCheck->fetch_assoc();
        
        // Exclui o aluno (CASCADE vai excluir as frequências relacionadas)
        $sqlDelete = "DELETE FROM aluno WHERE alunoID = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $alunoID);
        
        if ($stmtDelete->execute()) {
            $mensagem = "Aluno '{$aluno['nome']}' excluído com sucesso!";
            $mensagemTipo = "success";
            
            $redirectPath = isset($nivel) && $nivel === './' ? './' : '../';
            
            echo "<script>
                setTimeout(() => {
                    window.location.href = '{$redirectPath}';
                }, 1500);
            </script>";
        } else {
            $mensagem = "Erro ao excluir: " . $stmtDelete->error;
            $mensagemTipo = "error";
        }
    } else {
        $mensagem = "Aluno não encontrado.";
        $mensagemTipo = "error";
    }
}
?>

<div id="modalExcluir" class="modal <?= $mensagem && $mensagemTipo == 'error' ? 'active' : '' ?>">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirmar Exclusão</h2>
            <button class="modal-close" onclick="closeModal('modalExcluir')">&times;</button>
        </div>

        <form method="POST">
            <div class="modal-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= $mensagemTipo ?>">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="alunoID" id="excluirAlunoID">
                
                <p style="margin-bottom: 1rem; color: var(--color-text);">
                    Tem certeza que deseja excluir o aluno <strong id="excluirNomeAluno"></strong>?
                </p>
                <p style="margin-bottom: 1rem; color: var(--color-muted); font-size: 0.875rem;">
                    Esta ação não pode ser desfeita. Todas as frequências relacionadas também serão excluídas.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalExcluir')">Cancelar</button>
                <button type="submit" name="excluir_aluno" class="btn" style="background: var(--color-error);">Excluir</button>
            </div>
        </form>
    </div>
</div>

