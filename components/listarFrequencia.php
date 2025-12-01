<?php
// Se não foi incluído de outro arquivo, faz a conexão
if (!isset($conn)) {
  require_once '../utils/conexao.php';
}

// Configuração da paginação
$registros_por_pagina_freq = 5;
$pagina_atual_freq = isset($_GET['pagina_freq']) ? max(1, (int) $_GET['pagina_freq']) : 1;
$offset_freq = ($pagina_atual_freq - 1) * $registros_por_pagina_freq;

// Captura o termo de pesquisa
$pesquisa_freq = isset($_GET['pesquisa_freq']) ? trim($_GET['pesquisa_freq']) : '';
$pesquisa_freq_sql = $conn->real_escape_string($pesquisa_freq);

// Captura o filtro de situação
$filtro_situacao = isset($_GET['filtro_situacao']) ? trim($_GET['filtro_situacao']) : '';
$filtro_situacao_sql = $conn->real_escape_string($filtro_situacao);

// Condição WHERE para pesquisa e filtro
$where_freq = '';
$condicoes = [];

if (!empty($pesquisa_freq_sql)) {
  $condicoes[] = "(EXISTS (
    SELECT 1 FROM aluno_frequencia af2 
    INNER JOIN aluno a2 ON af2.alunoID = a2.alunoID 
    WHERE af2.frequenciaID = f.ID 
    AND (a2.nome LIKE '%$pesquisa_freq_sql%' OR a2.matricula LIKE '%$pesquisa_freq_sql%')
  ) OR f.descricao LIKE '%$pesquisa_freq_sql%')";
}

if (!empty($filtro_situacao_sql) && ($filtro_situacao_sql === 'Pendente' || $filtro_situacao_sql === 'Validado')) {
  $condicoes[] = "f.situacao = '$filtro_situacao_sql'";
}

if (!empty($condicoes)) {
  $where_freq = 'WHERE ' . implode(' AND ', $condicoes);
}

// Conta o total de registros (com filtro de pesquisa) - agora conta frequências únicas
$sql_count_freq = "SELECT COUNT(DISTINCT f.ID) as total 
                   FROM frequencia_atividade f 
                   $where_freq";
$result_count_freq = $conn->query($sql_count_freq);
$total_registros_freq = $result_count_freq->fetch_assoc()['total'];
$total_paginas_freq = ceil($total_registros_freq / $registros_por_pagina_freq);

// Consulta com LIMIT e OFFSET para paginação - agrupa por frequência
$sql = "SELECT f.ID, f.descricao, f.data, f.horario, f.situacao,
        COUNT(af.alunoID) as total_alunos,
        GROUP_CONCAT(CONCAT(a.nome, ' (', a.matricula, ')') ORDER BY a.nome SEPARATOR ', ') as alunos
        FROM frequencia_atividade f
        LEFT JOIN aluno_frequencia af ON f.ID = af.frequenciaID
        LEFT JOIN aluno a ON af.alunoID = a.alunoID
        $where_freq
        GROUP BY f.ID, f.descricao, f.data, f.horario, f.situacao
        ORDER BY f.data DESC
        LIMIT $registros_por_pagina_freq OFFSET $offset_freq";

$result = $conn->query($sql);

// Se não for include, inicia o buffer
if (!isset($is_included)) {
  ob_start();
}
?>

<div class="card col-span-1">
  <div class="card-header">
    <div class="card-header-content">
      <h2>Frequências Cadastradas</h2>
      <p style="color: var(--color-muted); font-size: 0.875rem;">Gerencie as frequências de atividades</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
      <?php
      // Monta a URL para exportação (mantém os filtros)
      if (isset($nivel)) {
        $url_export = $nivel . 'components/exportarFrequencia.php';
      } else {
        $url_export = 'exportarFrequencia.php';
      }
      $params_export = [];
      if (!empty($pesquisa_freq)) {
        $params_export[] = 'pesquisa_freq=' . urlencode($pesquisa_freq);
      }
      if (!empty($filtro_situacao)) {
        $params_export[] = 'filtro_situacao=' . urlencode($filtro_situacao);
      }
      $param_string_export = !empty($params_export) ? '?' . implode('&', $params_export) : '';
      ?>
      <a href="<?= $url_export . $param_string_export ?>" class="btn btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
        Exportar Excel
      </a>
      <button class="btn" onclick="openModal('modalCadastroFrequencia')">Nova Frequência</button>
    </div>
  </div>

  <!-- Barra de Pesquisa e Filtros -->
  <div style="margin: 1rem 0;">
    <form method="GET" action="" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
      <input type="text"
        name="pesquisa_freq"
        placeholder="Pesquisar por descrição, nome ou matrícula..."
        value="<?= htmlspecialchars($pesquisa_freq) ?>"
        style="flex: 1; min-width: 200px; padding: 0.5rem 1rem; border: 1px solid var(--color-border); border-radius: 0.375rem; background: var(--color-surface); color: var(--color-text);">

      <select name="filtro_situacao"
        style="padding: 0.5rem 1rem; border: 1px solid var(--color-border); border-radius: 0.375rem; background: var(--color-surface); color: var(--color-text); cursor: pointer;">
        <option value="">Todas as situações</option>
        <option value="Pendente" <?= $filtro_situacao === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
        <option value="Validado" <?= $filtro_situacao === 'Validado' ? 'selected' : '' ?>>Validado</option>
      </select>

      <button type="submit" class="btn" style="padding: 0.5rem 1rem;">Filtrar</button>
      <?php if (!empty($pesquisa_freq) || !empty($filtro_situacao)): ?>
        <a href="<?= isset($nivel) ? $nivel : '?' ?>" class="btn" style="padding: 0.5rem 1rem; background: var(--color-muted);">Limpar</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Descrição</th>
          <th>Data</th>
          <th>Carga Horária</th>
          <th>Participantes</th>
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
              <td>
                <?php if ($row['total_alunos'] > 0): ?>
                  <span style="font-size: 0.875rem;" title="<?= htmlspecialchars($row['alunos']) ?>">
                    <?= $row['total_alunos'] ?> aluno(s)
                    <?php if (strlen($row['alunos']) > 50): ?>
                      <br><small style="color: var(--color-muted);"><?= htmlspecialchars(substr($row['alunos'], 0, 50)) ?>...</small>
                    <?php else: ?>
                      <br><small style="color: var(--color-muted);"><?= htmlspecialchars($row['alunos']) ?></small>
                    <?php endif; ?>
                  </span>
                <?php else: ?>
                  <span style="color: var(--color-muted); font-size: 0.875rem;">Nenhum participante</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="status-badge <?= $row['situacao'] == 'Validado' ? 'ativo' : 'inativo' ?>">
                  <?= $row['situacao'] == 'Validado' ? 'Validado' : 'Pendente' ?>
                </span>
              </td>
              <td>
                <div class="table-actions">
                  <a href="<?= isset($nivel) ? $nivel : '' ?>?editarFreq=<?= $row['ID'] ?>"
                    onclick="event.preventDefault(); openEditFreqModal(<?= $row['ID'] ?>, '<?= htmlspecialchars($row['descricao'], ENT_QUOTES) ?>', '<?= $row['data'] ?>', '<?= $row['horario'] ?>', '<?= htmlspecialchars($row['situacao'], ENT_QUOTES) ?>')">
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

  <?php if ($total_paginas_freq > 1): ?>
    <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 1.5rem; flex-wrap: wrap;">
      <?php
      // Monta a URL base para paginação (mantém o termo de pesquisa e filtro de situação)
      $url_base_freq = isset($nivel) ? $nivel : '?';
      $separador_freq = strpos($url_base_freq, '?') !== false ? '&' : '?';
      $params = [];
      if (!empty($pesquisa_freq)) {
        $params[] = 'pesquisa_freq=' . urlencode($pesquisa_freq);
      }
      if (!empty($filtro_situacao)) {
        $params[] = 'filtro_situacao=' . urlencode($filtro_situacao);
      }
      $param_string = !empty($params) ? implode('&', $params) . '&' : '';
      ?>

      <!-- Botão Anterior -->
      <?php if ($pagina_atual_freq > 1): ?>
        <a href="<?= $url_base_freq . $separador_freq . $param_string ?>pagina_freq=<?= $pagina_atual_freq - 1 ?>"
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
      $inicio_freq = max(1, $pagina_atual_freq - 2);
      $fim_freq = min($total_paginas_freq, $pagina_atual_freq + 2);

      if ($inicio_freq > 1):
      ?>
        <a href="<?= $url_base_freq . $separador_freq . $param_string ?>pagina_freq=1"
          class="btn" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">1</a>
        <?php if ($inicio_freq > 2): ?>
          <span style="color: var(--color-muted);">...</span>
        <?php endif; ?>
      <?php endif; ?>

      <?php for ($i = $inicio_freq; $i <= $fim_freq; $i++): ?>
        <?php if ($i == $pagina_atual_freq): ?>
          <span class="btn" style="padding: 0.5rem 0.75rem; font-size: 0.875rem; background: var(--color-muted); color: white; cursor: default;">
            <?= $i ?>
          </span>
        <?php else: ?>
          <a href="<?= $url_base_freq . $separador_freq . $param_string ?>pagina_freq=<?= $i ?>"
            class="btn" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
            <?= $i ?>
          </a>
        <?php endif; ?>
      <?php endfor; ?>

      <?php if ($fim_freq < $total_paginas_freq): ?>
        <?php if ($fim_freq < $total_paginas_freq - 1): ?>
          <span style="color: var(--color-muted);">...</span>
        <?php endif; ?>
        <a href="<?= $url_base_freq . $separador_freq . $param_string ?>pagina_freq=<?= $total_paginas_freq ?>"
          class="btn" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;"><?= $total_paginas_freq ?></a>
      <?php endif; ?>

      <!-- Botão Próximo -->
      <?php if ($pagina_atual_freq < $total_paginas_freq): ?>
        <a href="<?= $url_base_freq . $separador_freq . $param_string ?>pagina_freq=<?= $pagina_atual_freq + 1 ?>"
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
      Mostrando <?= min($offset_freq + 1, $total_registros_freq) ?> - <?= min($offset_freq + $registros_por_pagina_freq, $total_registros_freq) ?> de <?= $total_registros_freq ?> registros
    </p>
  <?php endif; ?>
</div>

<?php
// Se não for include, renderiza o layout
if (!isset($is_included)) {
  $conteudo = ob_get_clean();
  require_once '../layout.php';
  echo renderLayout('Lista de Frequências', $conteudo, '../');
}
?>