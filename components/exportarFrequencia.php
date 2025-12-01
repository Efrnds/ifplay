<?php
if (!isset($conn)) {
  require_once '../utils/conexao.php';
}

$pesquisa_freq = isset($_GET['pesquisa_freq']) ? trim($_GET['pesquisa_freq']) : '';
$pesquisa_freq_sql = $conn->real_escape_string($pesquisa_freq);

$filtro_situacao = isset($_GET['filtro_situacao']) ? trim($_GET['filtro_situacao']) : '';
$filtro_situacao_sql = $conn->real_escape_string($filtro_situacao);

$where_freq = '';
$condicoes = [];

if (!empty($pesquisa_freq_sql)) {
  $condicoes[] = "(a.nome LIKE '%$pesquisa_freq_sql%' OR a.matricula LIKE '%$pesquisa_freq_sql%' OR f.descricao LIKE '%$pesquisa_freq_sql%')";
}

if (!empty($filtro_situacao_sql) && ($filtro_situacao_sql === 'Pendente' || $filtro_situacao_sql === 'Validado')) {
  $condicoes[] = "f.situacao = '$filtro_situacao_sql'";
}

if (!empty($condicoes)) {
  $where_freq = 'WHERE ' . implode(' AND ', $condicoes);
}

$sql = "SELECT f.ID, f.descricao, f.data, f.horario, a.nome, a.matricula, f.situacao 
        FROM frequencia_atividade f
        INNER JOIN aluno_frequencia af ON f.ID = af.frequenciaID
        INNER JOIN aluno a ON af.alunoID = a.alunoID
        $where_freq
        ORDER BY f.data DESC, a.nome ASC";

$result = $conn->query($sql);

$filename = 'frequencias_' . date('Y-m-d_His') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";

echo '<table border="1">';
echo '<tr>';
echo '<th>ID</th>';
echo '<th>Descrição</th>';
echo '<th>Data</th>';
echo '<th>Carga Horária</th>';
echo '<th>Aluno</th>';
echo '<th>Matrícula</th>';
echo '<th>Situação</th>';
echo '</tr>';

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['ID']) . '</td>';
    echo '<td>' . htmlspecialchars($row['descricao']) . '</td>';
    echo '<td>' . date('d/m/Y', strtotime($row['data'])) . '</td>';
    echo '<td>' . htmlspecialchars($row['horario']) . '</td>';
    echo '<td>' . htmlspecialchars($row['nome']) . '</td>';
    echo '<td>' . htmlspecialchars($row['matricula']) . '</td>';
    echo '<td>' . htmlspecialchars($row['situacao']) . '</td>';
    echo '</tr>';
  }
} else {
  echo '<tr>';
  echo '<td colspan="7" style="text-align: center;">Nenhuma frequência encontrada.</td>';
  echo '</tr>';
}

echo '</table>';
exit;
