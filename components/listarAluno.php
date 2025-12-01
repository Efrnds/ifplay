<?php
// Se não foi incluído de outro arquivo, faz a conexão
if (!isset($conn)) {
    require_once '../utils/conexao.php';
}

// Configuração da paginação
$registros_por_pagina = 5;
$pagina_atual = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Captura o termo de pesquisa
$pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
$pesquisa_sql = $conn->real_escape_string($pesquisa);

// Condição WHERE para pesquisa
$where = '';
if (!empty($pesquisa_sql)) {
    $where = "WHERE nome LIKE '%$pesquisa_sql%' OR matricula LIKE '%$pesquisa_sql%'";
}

// Conta o total de registros (com filtro de pesquisa)
$sql_count = "SELECT COUNT(*) as total FROM aluno $where";
$result_count = $conn->query($sql_count);
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta com LIMIT e OFFSET para paginação
$sql = "SELECT alunoID, nome, matricula, anoEntrada, status FROM aluno $where ORDER BY status DESC LIMIT $registros_por_pagina OFFSET $offset";

$result = $conn->query($sql);

// Se não for include, inicia o buffer
if (!isset($is_included)) {
    ob_start();
}
?>

<div class="card col-span-1">
    <div class="card-header">
        <div class="card-header-content">
            <h2>Alunos Cadastrados</h2>
            <p style="color: var(--color-muted); font-size: 0.875rem;">Gerencie os alunos cadastrados no sistema</p>
        </div>
        <button class="btn" onclick="openModal('modalCadastro')">Novo Aluno</button>
    </div>

    <!-- Barra de Pesquisa -->
    <div style="margin: 1rem 0;">
        <form method="GET" action="" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <input type="text"
                name="pesquisa"
                placeholder="Pesquisar por nome ou matrícula..."
                value="<?= htmlspecialchars($pesquisa) ?>"
                style="flex: 1; min-width: 200px; padding: 0.5rem 1rem; border: 1px solid var(--color-border); border-radius: 0.375rem; background: var(--color-surface); color: var(--color-text);">
            <button type="submit" class="btn" style="padding: 0.5rem 1rem;">Pesquisar</button>
            <?php if (!empty($pesquisa)): ?>
                <a href="<?= isset($nivel) ? $nivel : '?' ?>" class="btn" style="padding: 0.5rem 1rem; background: var(--color-muted);">Limpar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>Ano de Entrada</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['alunoID'] ?></td>
                            <td><?= htmlspecialchars($row['nome']) ?></td>
                            <td><?= htmlspecialchars($row['matricula']) ?></td>
                            <td><?= date('Y', strtotime($row['anoEntrada'])) ?></td>
                            <td>
                                <span class="status-badge <?= $row['status'] ? 'ativo' : 'inativo' ?>">
                                    <?= $row['status'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= isset($nivel) ? $nivel : '' ?>?editar=<?= $row['alunoID'] ?>"
                                        onclick="event.preventDefault(); openEditModal(<?= $row['alunoID'] ?>, '<?= htmlspecialchars($row['nome'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['matricula'], ENT_QUOTES) ?>', '<?= date('Y', strtotime($row['anoEntrada'])) ?>', <?= $row['status'] ?>)">
                                        Editar
                                    </a>
                                    <a href="#"
                                        class="btn-excluir-aluno"
                                        data-aluno-id="<?= $row['alunoID'] ?>"
                                        data-aluno-nome="<?= htmlspecialchars($row['nome'], ENT_QUOTES) ?>"
                                        style="color: var(--color-error);">
                                        Excluir
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--color-muted);">
                            Nenhum aluno encontrado.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_paginas > 1): ?>
        <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 1.5rem; flex-wrap: wrap;">
            <?php
            // Monta a URL base para paginação (mantém o termo de pesquisa)
            $url_base = isset($nivel) ? $nivel : '?';
            $separador = strpos($url_base, '?') !== false ? '&' : '?';
            $param_pesquisa = !empty($pesquisa) ? 'pesquisa=' . urlencode($pesquisa) . '&' : '';
            ?>

            <!-- Botão Anterior -->
            <?php if ($pagina_atual > 1): ?>
                <a href="<?= $url_base . $separador . $param_pesquisa ?>pagina=<?= $pagina_atual - 1 ?>"
                    class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                    ← Anterior
                </a>
            <?php else: ?>
                <span class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem; opacity: 0.5; cursor: not-allowed;">
                    ← Anterior
                </span>
            <?php endif; ?>

            <!-- Números das páginas -->
            <?php
            $inicio = max(1, $pagina_atual - 2);
            $fim = min($total_paginas, $pagina_atual + 2);

            if ($inicio > 1):
                ?>
                <a href="<?= $url_base . $separador . $param_pesquisa ?>pagina=1"
                    class="btn" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">1</a>
                <?php if ($inicio > 2): ?>
                    <span style="color: var(--color-muted);">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                <?php if ($i == $pagina_atual): ?>
                    <span class="btn" style="padding: 0.5rem 0.75rem; font-size: 0.875rem; background: var(--color-muted); color: white; cursor: default;">
                        <?= $i ?>
                    </span>
                <?php else: ?>
                    <a href="<?= $url_base . $separador . $param_pesquisa ?>pagina=<?= $i ?>"
                        class="btn" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($fim < $total_paginas): ?>
                <?php if ($fim < $total_paginas - 1): ?>
                    <span style="color: var(--color-muted);">...</span>
                <?php endif; ?>
                <a href="<?= $url_base . $separador . $param_pesquisa ?>pagina=<?= $total_paginas ?>"
                    class="btn" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;"><?= $total_paginas ?></a>
            <?php endif; ?>

            <!-- Botão Próximo -->
            <?php if ($pagina_atual < $total_paginas): ?>
                <a href="<?= $url_base . $separador . $param_pesquisa ?>pagina=<?= $pagina_atual + 1 ?>"
                    class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                    Próximo →
                </a>
            <?php else: ?>
                <span class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem; opacity: 0.5; cursor: not-allowed;">
                    Próximo →
                </span>
            <?php endif; ?>
        </div>

        <p style="text-align: center; color: var(--color-muted); font-size: 0.75rem; margin-top: 0.75rem;">
            Mostrando <?= min($offset + 1, $total_registros) ?> - <?= min($offset + $registros_por_pagina, $total_registros) ?> de <?= $total_registros ?> registros
        </p>
    <?php endif; ?>
</div>

<?php
// Se não for include, renderiza o layout
if (!isset($is_included)) {
    $conteudo = ob_get_clean();
    require_once '../layout.php';
    echo renderLayout('Lista de Alunos', $conteudo, '../');
}
?>