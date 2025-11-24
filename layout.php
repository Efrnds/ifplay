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
    <link rel="shortcut icon" href="<?= $nivel ?>assets/img/logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= $nivel ?>assets/css/style.css">
  </head>

  <body class="body">
    <header class="header">
      <div class="logo">
        <img src="<?= $nivel ?>assets/img/logo.svg" alt="Logo IFPlay">
        <h1>IFPlay</h1>
      </div>
      <nav class="nav">
        <a class="btn" href="<?= $nivel ?>/">Início</a>
      </nav>
    </header>

    <main class="main">
      <?= $conteudo ?>

    </main>

    <footer class="footer">
      <p>&copy; <?= date('Y') ?> IFPlay - Sistema de Gerenciamento de Alunos</p>
    </footer>

    <script>
      function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
      }

      function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
      }

      function openEditModal(id, nome, matricula, anoEntrada, status) {
        document.getElementById('editAlunoID').value = id;
        document.getElementById('editNome').value = nome;
        document.getElementById('editMatricula').value = matricula;
        document.getElementById('editAnoEntrada').value = anoEntrada;
        document.getElementById('editStatus').value = status;
        openModal('modalEditar');
      }

      function openEditFreqModal(id, descricao, data, horario, aluno, situacao) {
        document.getElementById('editFreqID').value = id;
        document.getElementById('editFreqDescricao').value = descricao;
        document.getElementById('editFreqData').value = new Date(data + 'T00:00:00').toLocaleDateString('pt-BR');
        document.getElementById('editFreqHorario').value = horario;
        document.getElementById('editFreqAluno').value = aluno;
        document.getElementById('editFreqSituacao').value = situacao;
        openModal('modalEditarFrequencia');
      }

      // Fechar modal ao clicar fora dele
      window.onclick = function (event) {
        if (event.target.classList.contains('modal')) {
          event.target.classList.remove('active');
        }
      }
    </script>
  </body>

  </html>
  <?php
  return ob_get_clean();
}
?>