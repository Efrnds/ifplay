<?php
if (!isset($conn)) {
    require_once "../utils/conexao.php";
}

$mensagem = "";
$mensagemTipo = "";

// Buscar lista de alunos ativos
$sqlAlunos = "SELECT alunoID, nome, matricula FROM aluno WHERE status = 1 ORDER BY nome ASC";
$resultAlunos = $conn->query($sqlAlunos);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_frequencia'])) {
    try {
        $descricao = trim($_POST['descricao']);
        $data = $_POST['data'];
        $horario = $_POST['cargaHoraria'];
        $participante = $_POST['participante'];
        $situacao = "Pendente"; // VARCHAR(25) - max 25 caracteres

        $sql = "INSERT INTO frequencia_atividade (descricao, data, horario, situacao, participante) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $descricao, $data, $horario, $situacao, $participante);

        if ($stmt->execute()) {
            $mensagem = "Frequência cadastrada com sucesso!";
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
        if (strpos($e->getMessage(), 'Data too long') !== false) {
            preg_match("/column '(\w+)'/", $e->getMessage(), $matches);
            $coluna = $matches[1] ?? 'desconhecida';
            $mensagem = "Erro: O campo '$coluna' excede o tamanho permitido.";
        } else {
            $mensagem = "Erro ao cadastrar frequência: " . $e->getMessage();
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

<div id="modalCadastroFrequencia" class="modal <?= $mensagem && $mensagemTipo == 'error' ? 'active' : '' ?>">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Cadastrar Nova Frequência</h2>
            <button class="modal-close" onclick="closeModal('modalCadastroFrequencia')">&times;</button>
        </div>
        
        <form method="POST">
            <div class="modal-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= $mensagemTipo ?>">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="data">Data:</label>
                    <input type="date" id="data" name="data" required>
                </div>

                <div class="form-group">
                    <label for="cargaHoraria">Carga Horária:</label>
                    <input type="time" id="cargaHoraria" name="cargaHoraria" required>
                </div>

                <div class="form-group">
                    <label for="participante">Participante:</label>
                    <select id="participante" name="participante" required>
                        <option value="">Selecione um aluno...</option>
                        <?php 
                        if ($resultAlunos) {
                            $resultAlunos->data_seek(0);
                            while ($aluno = $resultAlunos->fetch_assoc()): 
                        ?>
                            <option value="<?= $aluno['alunoID'] ?>">
                                <?= htmlspecialchars($aluno['nome']) ?> - <?= htmlspecialchars($aluno['matricula']) ?>
                            </option>
                        <?php 
                            endwhile;
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalCadastroFrequencia')">Cancelar</button>
                <button type="submit" name="cadastrar_frequencia" class="btn">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<?php
if (!isset($is_included)) {
    $conteudo = ob_get_clean();
    require_once '../layout.php';
    echo renderLayout('Cadastrar Frequência', $conteudo, '../');
}
?>