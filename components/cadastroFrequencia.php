<?php
if (!isset($conn)) {
    require_once '../utils/conexao.php';
}

$mensagem = '';
$mensagemTipo = '';

// Buscar lista de alunos ativos
$sqlAlunos = 'SELECT alunoID, nome, matricula FROM aluno WHERE status = 1 ORDER BY nome ASC';
$resultAlunos = $conn->query($sqlAlunos);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_frequencia'])) {
    try {
        $descricao = trim($_POST['descricao']);
        $data = $_POST['data'];
        $horario = $_POST['cargaHoraria'];
        $situacao = 'Pendente';  // VARCHAR(25) - max 25 caracteres

        // Verificar se há participantes selecionados
        $participantes = isset($_POST['participantes']) ? $_POST['participantes'] : [];

        if (empty($participantes)) {
            $mensagem = 'Erro: Selecione pelo menos um aluno.';
            $mensagemTipo = 'error';
        } else {
            // Inserir na tabela frequencia_atividade
            $sql = 'INSERT INTO frequencia_atividade (descricao, data, horario, situacao) 
                    VALUES (?, ?, ?, ?)';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $descricao, $data, $horario, $situacao);

            if ($stmt->execute()) {
                // Obter o ID da frequência recém-criada
                $frequenciaID = $conn->insert_id;

                // Inserir múltiplos registros na tabela de junção aluno_frequencia
                $sql_relacao = 'INSERT INTO aluno_frequencia (alunoID, frequenciaID) VALUES (?, ?)';
                $stmt_relacao = $conn->prepare($sql_relacao);

                $erro_relacao = false;
                foreach ($participantes as $alunoID) {
                    $alunoID = (int) $alunoID;
                    $stmt_relacao->bind_param('ii', $alunoID, $frequenciaID);
                    if (!$stmt_relacao->execute()) {
                        $erro_relacao = true;
                        $mensagem = 'Erro ao relacionar aluno com frequência: ' . $stmt_relacao->error;
                        $mensagemTipo = 'error';
                        break;
                    }
                }
                $stmt_relacao->close();

                if (!$erro_relacao) {
                    $totalAlunos = count($participantes);
                    $mensagem = "Frequência cadastrada com sucesso para {$totalAlunos} aluno(s)!";
                    $mensagemTipo = 'success';

                    $redirectPath = isset($nivel) && $nivel === './' ? './' : '../';

                    echo "<script>
                        setTimeout(() => {
                            window.location.href = '{$redirectPath}';
                        }, 1500);
                    </script>";
                }
            } else {
                $mensagem = 'Erro ao cadastrar: ' . $stmt->error;
                $mensagemTipo = 'error';
            }
            $stmt->close();
        }
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'Data too long') !== false) {
            preg_match("/column '(\w+)'/", $e->getMessage(), $matches);
            $coluna = $matches[1] ?? 'desconhecida';
            $mensagem = "Erro: O campo '$coluna' excede o tamanho permitido.";
        } else {
            $mensagem = 'Erro ao cadastrar frequência: ' . $e->getMessage();
        }
        $mensagemTipo = 'error';
    } catch (Exception $e) {
        $mensagem = 'Erro inesperado: ' . $e->getMessage();
        $mensagemTipo = 'error';
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
                    <label for="pesquisaAluno">Pesquisar Aluno (nome ou matrícula):</label>
                    <input type="text"
                        id="pesquisaAluno"
                        placeholder="Digite para pesquisar...">
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="margin: 0;">Participantes:</label>
                        <button type="button"
                            id="btnSelecionarTodos"
                            class="btn btn-secondary"
                            style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">
                            Selecionar Todos
                        </button>
                    </div>
                    <div id="listaAlunos"
                        style="max-height: 200px; overflow-y: auto; border: 1px solid var(--color-border); border-radius: 0.375rem; padding: 0.5rem; background: var(--color-surface);">
                        <?php
                        if ($resultAlunos) {
                            $resultAlunos->data_seek(0);
                            while ($aluno = $resultAlunos->fetch_assoc()):
                        ?>
                                <label style="display: flex; align-items: center; padding: 0.5rem; margin-bottom: 0.25rem; border-radius: 0.25rem; cursor: pointer; transition: background-color 0.15s ease;"
                                    class="aluno-item"
                                    data-nome="<?= htmlspecialchars(strtolower($aluno['nome'])) ?>"
                                    data-matricula="<?= htmlspecialchars(strtolower($aluno['matricula'])) ?>"
                                    onmouseover="this.style.backgroundColor='var(--color-hover)'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <input type="checkbox"
                                        name="participantes[]"
                                        value="<?= $aluno['alunoID'] ?>"
                                        style="width: auto; margin-right: 0.75rem; cursor: pointer;"
                                        class="checkbox-aluno">
                                    <span style="flex: 1; font-size: 0.875rem;">
                                        <?= htmlspecialchars($aluno['nome']) ?> - <?= htmlspecialchars($aluno['matricula']) ?>
                                    </span>
                                </label>
                        <?php
                            endwhile;
                        }
                        ?>
                    </div>
                    <small style="color: var(--color-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">
                        <span id="contadorSelecionados">0</span> aluno(s) selecionado(s)
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    onclick="closeModal('modalCadastroFrequencia')">Cancelar</button>
                <button type="submit" name="cadastrar_frequencia" class="btn" id="btnCadastrar">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        function inicializarFrequencia() {
            const pesquisaInput = document.getElementById('pesquisaAluno');
            const listaAlunos = document.getElementById('listaAlunos');
            const contadorSelecionados = document.getElementById('contadorSelecionados');
            const btnCadastrar = document.getElementById('btnCadastrar');
            const modal = document.getElementById('modalCadastroFrequencia');

            if (!pesquisaInput || !listaAlunos || !contadorSelecionados || !btnCadastrar) {
                return;
            }

            // Função para obter itens visíveis
            function obterItensVisiveis() {
                return Array.from(listaAlunos.querySelectorAll('.aluno-item')).filter(item => {
                    return item.style.display !== 'none' && item.offsetParent !== null;
                });
            }

            // Função para atualizar contador de selecionados
            function atualizarContador() {
                const selecionados = listaAlunos.querySelectorAll('.checkbox-aluno:checked').length;
                const itensVisiveis = obterItensVisiveis();
                const visiveis = itensVisiveis.length;
                const selecionadosVisiveis = itensVisiveis.filter(item => {
                    const checkbox = item.querySelector('.checkbox-aluno');
                    return checkbox && checkbox.checked;
                }).length;

                contadorSelecionados.textContent = selecionados;

                // Atualizar texto do botão "Selecionar Todos"
                const btnSelecionarTodos = document.getElementById('btnSelecionarTodos');
                if (btnSelecionarTodos && visiveis > 0) {
                    const todosVisiveisSelecionados = selecionadosVisiveis === visiveis;
                    btnSelecionarTodos.textContent = todosVisiveisSelecionados ? 'Deselecionar Todos' : 'Selecionar Todos';
                }

                // Desabilitar botão se nenhum aluno estiver selecionado
                btnCadastrar.disabled = selecionados === 0;
                btnCadastrar.style.opacity = selecionados === 0 ? '0.5' : '1';
                btnCadastrar.style.cursor = selecionados === 0 ? 'not-allowed' : 'pointer';
            }

            // Função para selecionar/deselecionar todos os alunos visíveis
            function selecionarTodos() {
                const itensVisiveis = obterItensVisiveis();

                if (itensVisiveis.length === 0) return;

                const todosSelecionados = itensVisiveis.every(item => {
                    const checkbox = item.querySelector('.checkbox-aluno');
                    return checkbox && checkbox.checked;
                });

                itensVisiveis.forEach(item => {
                    const checkbox = item.querySelector('.checkbox-aluno');
                    if (checkbox) {
                        checkbox.checked = !todosSelecionados;
                        // Disparar evento change para garantir que outros listeners sejam notificados
                        checkbox.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });

                atualizarContador();
            }

            // Função para filtrar alunos
            function filtrarAlunos() {
                const termo = pesquisaInput.value.toLowerCase().trim();
                const itens = listaAlunos.querySelectorAll('.aluno-item');

                itens.forEach(item => {
                    const nome = item.getAttribute('data-nome');
                    const matricula = item.getAttribute('data-matricula');

                    if (termo === '' || nome.includes(termo) || matricula.includes(termo)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Atualizar contador após filtrar para atualizar o botão
                atualizarContador();
            }

            // Event listeners
            pesquisaInput.addEventListener('input', filtrarAlunos);

            // Botão selecionar todos
            const btnSelecionarTodos = document.getElementById('btnSelecionarTodos');
            if (btnSelecionarTodos) {
                btnSelecionarTodos.addEventListener('click', selecionarTodos);
            }

            // Atualizar contador quando checkboxes mudarem (usando event delegation)
            listaAlunos.addEventListener('change', function(e) {
                if (e.target.classList.contains('checkbox-aluno')) {
                    atualizarContador();
                }
            });

            // Inicializar contador
            atualizarContador();

            // Validação no submit do formulário
            const form = modal.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const selecionados = listaAlunos.querySelectorAll('.checkbox-aluno:checked').length;
                    if (selecionados === 0) {
                        e.preventDefault();
                        alert('Por favor, selecione pelo menos um aluno.');
                        return false;
                    }
                });
            }

            // Limpar pesquisa e resetar quando modal for fechado
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (!modal.classList.contains('active')) {
                            pesquisaInput.value = '';
                            filtrarAlunos();
                            atualizarContador();
                        }
                    }
                });
            });

            observer.observe(modal, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        // Inicializar quando o DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializarFrequencia);
        } else {
            inicializarFrequencia();
        }
    })();
</script>

<?php
if (!isset($is_included)) {
    $conteudo = ob_get_clean();
    require_once '../layout.php';
    echo renderLayout('Cadastrar Frequência', $conteudo, '../');
}
?>