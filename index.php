<?php
ob_start();
?>

<div class="card text-center">
  <h2>Bem-vindo ao IFPlay</h2>
  <p>Sistema de Gerenciamento de Alunos e Frequências</p>
  <div class="mt-2">
    <a href="components/listarAluno.php" class="btn">Ver Alunos</a>
    <a href="components/frequenciaCadastrar.php" class="btn btn-secondary">Frequências</a>
  </div>
</div>

<?php
$conteudo = ob_get_clean();
require_once 'layout.php';
echo renderLayout('Início', $conteudo, './');
?>