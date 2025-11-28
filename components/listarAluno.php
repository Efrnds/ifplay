<?php
// Se não foi incluído de outro arquivo, faz a conexão
if (!isset($conn)) {
    require_once "../utils/conexao.php";
}

$sql = "SELECT alunoID, nome, matricula, anoEntrada, status FROM aluno ORDER BY status desc ";

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
</div>

<?php
// Se não for include, renderiza o layout
if (!isset($is_included)) {
    $conteudo = ob_get_clean();
    require_once '../layout.php';
    echo renderLayout('Lista de Alunos', $conteudo, '../');
}
?>