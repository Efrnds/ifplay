<?php
// Se não foi incluído de outro arquivo, faz a conexão
if (!isset($conn)) {
  require_once "../utils/conexao.php";
}

$sql = "SELECT f.*, a.nome, a.matricula 
        FROM frequencia_atividade f
        INNER JOIN aluno a ON a.alunoID = f.participante
        ORDER BY f.data DESC";

if (isset($limit)) {
  $sql .= " LIMIT " . (int) $limit;
}
$result = $conn->query($sql);

// Se não for include, inicia o buffer
if (!isset($is_included)) {
  ob_start();
}
?>

<div class="card col-span-1">
  <div class="flex flex-1 justify-between">
    <div class="flex-1">
      <h2>Frequências Cadastradas</h2>
      <p style="color: var(--color-muted); font-size: 0.875rem;">Gerencie as frequências de atividades</p>
    </div>
    <button class="btn" onclick="openModal('modalCadastroFrequencia')">Nova Frequência</button>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Descrição</th>
          <th>Data</th>
          <th>Carga Horária</th>
          <th>Aluno</th>
          <th>Situação</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['ID'] ?></td>
              <td><?= htmlspecialchars($row['descricao']) ?></td>
              <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
              <td><?= $row['horario'] ?></td>
              <td><?= htmlspecialchars($row['nome']) ?></td>
              <td>
                <span class="status-badge <?= $row['situacao'] == 'Validado' ? 'ativo' : 'inativo' ?>">
                  <?= $row['situacao'] == 'Validado' ? 'Validado' : 'Pendente' ?>
                </span>
              </td>
              <td>
                <div class="table-actions">
                  <a href="<?= isset($nivel) ? $nivel : '' ?>?editarFreq=<?= $row['ID'] ?>"
                    onclick="event.preventDefault(); openEditFreqModal(<?= $row['ID'] ?>, '<?= htmlspecialchars($row['descricao'], ENT_QUOTES) ?>', '<?= $row['data'] ?>', '<?= $row['horario'] ?>', '<?= htmlspecialchars($row['nome'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['situacao'], ENT_QUOTES) ?>')">
                    Editar
                  </a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align: center; color: var(--color-muted);">
              Nenhuma frequência encontrada.
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
  echo renderLayout('Lista de Frequências', $conteudo, '../');
}
?>