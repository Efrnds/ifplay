<?php
require_once './utils/conexao.php';

ob_start();
?>

<div class="card col-span-2">
  <h2>Bem-vindo ao IFPlay</h2>
  <p>Sistema de Gerenciamento de Alunos e Frequências</p>
</div>


<?php
// Configura variáveis para o include
$is_included = true;
$nivel = './';

// Inclui a lista de alunos
include './components/listarAluno.php';

// Inclui a lista de frequências
include './components/listarFrequencia.php';

// Inclui os modais
include './components/cadastroAluno.php';
include './components/editarAluno.php';
include './components/excluirAluno.php';
include './components/cadastroFrequencia.php';
include './components/editarFrequencia.php';
?>

<?php
$conteudo = ob_get_clean();
require_once 'layout.php';
echo renderLayout('Início', $conteudo, './');
?>