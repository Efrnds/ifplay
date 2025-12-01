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

      function openDeleteModal(id, nome) {
        try {
          var alunoIDField = document.getElementById('excluirAlunoID');
          var nomeField = document.getElementById('excluirNomeAluno');
          var modal = document.getElementById('modalExcluir');

          if (!alunoIDField || !nomeField || !modal) {
            console.error('Elementos do modal não encontrados:', {
              alunoIDField: !!alunoIDField,
              nomeField: !!nomeField,
              modal: !!modal
            });
            alert('Erro: Modal de exclusão não encontrado. Recarregue a página.');
            return;
          }

          alunoIDField.value = id;
          nomeField.textContent = nome;
          openModal('modalExcluir');
        } catch (error) {
          console.error('Erro ao abrir modal de exclusão:', error);
          alert('Erro ao abrir modal de exclusão. Verifique o console para mais detalhes.');
        }
      }

      // Fechar modal ao clicar fora dele
      window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
          event.target.classList.remove('active');
        }
      }

      // Event delegation para botões de exclusão (funciona mesmo com conteúdo dinâmico)
      document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-excluir-aluno')) {
          e.preventDefault();
          var alunoID = e.target.getAttribute('data-aluno-id');
          var alunoNome = e.target.getAttribute('data-aluno-nome');
          openDeleteModal(alunoID, alunoNome);
        }
      });
    </script>
  </body>

  </html>
<?php
  return ob_get_clean();
}
?>