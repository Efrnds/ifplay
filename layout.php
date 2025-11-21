<?php
function renderLayout($titulo, $conteudo, $nivel = '../')
{
  ob_start();
  ?>
  <!DOCTYPE html>
  <html lang="pt-br">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> - IFPlay</title>
    <link rel="shortcut icon" href="./assets/img/logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= $nivel ?>assets/css/style.css">
  </head>

  <body class="body">
    <header class="header">
        <div class="logo">
          <img src="./assets/img/logo.svg" alt="" srcset="">
          <h1>IFPlay</h1>
        </div>
        <nav class="nav">
          <a href="<?= $nivel ?>index.php">Início</a>
          <a href="<?= $nivel ?>components/listarAluno.php">Alunos</a>
          <a href="<?= $nivel ?>components/frequenciaCadastrar.php">Frequências</a>
        </nav>
    </header>

    <main class="container">
      <?= $conteudo ?>
    </main>

    <footer class="footer">
      <div class="container">
        <p>&copy; <?= date('Y') ?> IFPlay - Sistema de Gerenciamento de Alunos</p>
      </div>
    </footer>
  </body>

  </html>
  <?php
  return ob_get_clean();
}
?>