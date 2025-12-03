# IFPlay

Sistema de gerenciamento de alunos e frequências de atividades desenvolvido em PHP com MySQL.

## 📋 Descrição

O IFPlay é um sistema web para controle de cadastro de alunos e registro de frequências em atividades. Permite o gerenciamento completo de informações acadêmicas, incluindo validação de frequências por professores e exclusão de alunos.

## 🚀 Funcionalidades

### Gerenciamento de Alunos
- Cadastro de novos alunos ([cadastroAluno.php](cadastroAluno.php))
- Listagem de alunos cadastrados ([listarAluno.php](listarAluno.php))
- Edição de status (ativo/inativo) ([editarAluno.php](editarAluno.php))
- Exclusão de alunos com cascata de frequências ([excluirAluno.php](excluirAluno.php))
- Campos: nome, matrícula, ano de entrada e status
- Paginação e pesquisa por nome ou matrícula

### Gerenciamento de Frequências
- Cadastro de frequências de atividades ([frequenciaCadastrar.php](frequenciaCadastrar.php))
- Listagem de frequências cadastradas ([listarFrequencia.php](listarFrequencia.php))
- Edição de situação da frequência ([editarFrequencia.php](editarFrequencia.php))
- Vinculação de frequências a múltiplos alunos
- Status de validação: "Pendente" ou "Validado"
- Campos: descrição, data, horário/carga horária e participantes
- Exportação da lista de frequência em Excel ([exportarFrequencia.php](exportarFrequencia.php))
- Paginação, pesquisa por descrição/aluno e filtro por situação

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7+
- **Banco de Dados**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3
- **Servidor**: Apache/Nginx (compatível com qualquer servidor PHP)

## 📦 Estrutura do Projeto

```
ifplay/
├── index.php                          # Página inicial
├── layout.php                         # Template de layout
├── assets/
│   ├── css/
│   │   └── style.css                  # Estilos globais
│   └── img/
│       └── logo.svg                   # Logo do sistema
├── components/
│   ├── cadastroAluno.php              # Formulário de cadastro de alunos
│   ├── listarAluno.php                # Listagem de alunos com paginação
│   ├── editarAluno.php                # Edição de status do aluno
│   ├── excluirAluno.php               # Exclusão de alunos
│   ├── frequenciaCadastrar.php        # Formulário de cadastro de frequências
│   ├── listarFrequencia.php           # Listagem de frequências com paginação
│   ├── editarFrequencia.php           # Edição de frequências
│   └── exportarFrequencia.php         # Exportação em Excel
├── utils/
│   ├── conexao.php                    # Configuração de conexão com banco
│   └── IFPlay_db.sql                  # Script de criação do banco
├── LICENSE                            # Licença GPL v3
└── README.md                          # Documentação
```

## 📊 Estrutura do Banco de Dados

### Tabela `aluno`
- `alunoID` (INT, PK, AUTO_INCREMENT)
- `nome` (VARCHAR 150)
- `matricula` (VARCHAR 25, UNIQUE)
- `anoEntrada` (DATE)
- `status` (BOOLEAN, padrão: 1)

### Tabela `frequencia_atividade`
- `ID` (INT, PK, AUTO_INCREMENT)
- `descricao` (VARCHAR 400)
- `data` (DATE)
- `horario` (TIME)
- `situacao` (VARCHAR 25, padrão: 'Pendente')

### Tabela `aluno_frequencia`
- `alunoID` (INT, PK, FK → aluno.alunoID)
- `frequenciaID` (INT, PK, FK → frequencia_atividade.ID)

## ⚙️ Instalação

### Pré-requisitos
- PHP 7.0 ou superior
- MySQL/MariaDB 5.7 ou superior
- Servidor web (Apache, Nginx, etc.)
- XAMPP ou outro servidor local

### Passos

1. **Clone o repositório**
   ```bash
   git clone <url-do-repositorio>
   cd ifplay
   ```

2. **Configure o banco de dados**
   
   Execute o script SQL:
   ```bash
   mysql -u root -p < utils/IFPlay_db.sql
   ```

3. **Configure a conexão**
   
   Edite o arquivo [utils/conexao.php](utils/conexao.php) com suas credenciais:
   ```php
   $servername = "127.0.0.1";
   $username = "root";
   $password = "sua_senha";
   $dbname = "IFPlay_db";
   ```

4. **Inicie o servidor**
   
   **Opção 1 - Servidor PHP embutido:**
   ```bash
   php -S localhost:8000
   ```
   
   **Opção 2 - Apache/Nginx:**
   Coloque os arquivos no diretório web do servidor (ex: `/var/www/html/`)

5. **Acesse o sistema**
   ```
   http://localhost:8000
   ```

## 🖥️ Uso

### Página Inicial
Acesse [index.php](index.php) para visualizar:
- Dashboard com lista de alunos cadastrados
- Dashboard com lista de frequências registradas

### Gerenciar Alunos

#### Cadastrar Aluno
1. Clique em "Novo Aluno" na seção de alunos
2. Preencha os campos: nome, matrícula e ano de entrada
3. Marque "Aluno Ativo" se aplicável
4. Clique em "Cadastrar"

#### Pesquisar Alunos
1. Use a barra de pesquisa por nome ou matrícula
2. Clique em "Pesquisar" ou "Limpar" para resetar

#### Editar Status do Aluno
1. Na lista de alunos, clique em "Editar"
2. Altere o status (Ativo/Inativo)
3. Clique em "Salvar Alterações"

#### Excluir Aluno
1. Na lista de alunos, clique em "Excluir"
2. Confirme a exclusão no modal
⚠️ **Nota:** A exclusão de um aluno também excluirá todas as suas frequências associadas

### Gerenciar Frequências

#### Cadastrar Frequência
1. Clique em "Nova Frequência" na seção de frequências
2. Preencha os campos:
   - **Descrição:** descrição da atividade
   - **Data:** data da atividade
   - **Carga Horária:** duração/horário da atividade
3. Pesquise e selecione os alunos participantes
4. Use "Selecionar Todos" para marcar todos os alunos visíveis
5. Clique em "Cadastrar"

#### Pesquisar e Filtrar Frequências
1. Use a barra de pesquisa (descrição, nome ou matrícula do aluno)
2. Filtre por situação (Pendente/Validado)
3. Clique em "Filtrar" ou "Limpar" para resetar

#### Editar Situação da Frequência
1. Na lista de frequências, clique em "Editar"
2. Altere a situação para "Pendente" ou "Validado"
3. Visualize os alunos vinculados
4. Clique em "Salvar Alterações"

#### Exportar Frequências
1. Na lista de frequências, clique em "Exportar Excel"
2. O arquivo será baixado com os dados filtrados
3. Planilha contém: ID, Descrição, Data, Carga Horária, Aluno, Matrícula e Situação

## 🎨 Interface

- **Design responsivo**: Funciona em desktop, tablet e mobile
- **Paginação**: 5 registros por página com navegação intuitiva
- **Modais interativos**: Cadastro e edição em pop-ups sem recarregar a página
- **Feedback visual**: Mensagens de sucesso/erro em tempo real

## 🔒 Segurança

⚠️ **Atenção**: Este sistema é uma versão básica para fins educacionais. Para uso em produção, implemente:

- Prepared statements (já implementado)
- Validação e sanitização avançada de dados no servidor
- Sistema de autenticação e autorização
- Proteção contra CSRF (tokens CSRF)
- Proteção contra SQL Injection (use prepared statements)
- Criptografia de senhas (se houver login)
- HTTPS para comunicação segura
- Rate limiting
- Logs de auditoria

## 📄 Licença

Este projeto está licenciado sob a GNU General Public License v3.0 - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para:
- Reportar bugs
- Sugerir novas funcionalidades
- Enviar pull requests

## 📧 Contato

Para dúvidas ou sugestões, abra uma issue no repositório.

---

**Última atualização:** Dezembro 2025
**Versão:** 1.1.0