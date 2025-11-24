<?php
if (!isset($conn)) {
    require_once "../utils/conexao.php";
}

$mensagem = "";
$mensagemTipo = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_aluno'])) {
    try {
        $nome = trim($_POST['nome']);
        $matricula = trim($_POST['matricula']);
        $anoEntrada = $_POST['anoEntrada'];
        $status = isset($_POST['status']) ? 1 : 0;

        $sql = "INSERT INTO aluno (nome, matricula, anoEntrada, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $matricula, $anoEntrada, $status);

        if ($stmt->execute()) {
            $mensagem = "Aluno cadastrado com sucesso!";
            $mensagemTipo = "success";

            $redirectPath = isset($nivel) && $nivel === './' ? './' : '../';

            echo "<script>
                setTimeout(() => {
                    window.location.href = '{$redirectPath}';
                }, 1500);
            </script>";
        } else {
            $mensagem = "Erro ao cadastrar: " . $stmt->error;
            $mensagemTipo = "error";
        }
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            $mensagem = "Erro: Esta matrícula já está cadastrada.";
        } elseif (strpos($e->getMessage(), 'Data too long') !== false) {
            $mensagem = "Erro: Os dados informados excedem o tamanho permitido.";
        } else {
            $mensagem = "Erro ao cadastrar aluno: " . $e->getMessage();
        }
        $mensagemTipo = "error";
    } catch (Exception $e) {
        $mensagem = "Erro inesperado: " . $e->getMessage();
        $mensagemTipo = "error";
    }
}

if (!isset($is_included)) {
    ob_start();
}
?>

<div id="modalCadastro" class="modal <?= $mensagem && $mensagemTipo == 'error' ? 'active' : '' ?>">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Cadastrar Novo Aluno</h2>
            <button class="modal-close" onclick="closeModal('modalCadastro')">&times;</button>
        </div>

        <form method="POST">
            <div class="modal-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= $mensagemTipo ?>">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>

                <div class="form-group">
                    <label for="matricula">Matrícula:</label>
                    <input type="text" id="matricula" name="matricula" required>
                </div>

                <div class="form-group">
                    <label for="anoEntrada">Ano de Entrada:</label>
                    <input type="date" id="anoEntrada" name="anoEntrada" required>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; font-weight: normal;">
                        <input type="checkbox" name="status" checked style="width: auto; margin-right: 0.5rem;">
                        Aluno Ativo
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalCadastro')">Cancelar</button>
                <button type="submit" name="cadastrar_aluno" class="btn">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<?php
if (!isset($is_included)) {
    $conteudo = ob_get_clean();
    require_once '../layout.php';
    echo renderLayout('Cadastrar Aluno', $conteudo, '../');
}
?>