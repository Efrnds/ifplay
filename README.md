# IFPlay

Sistema de gerenciamento de alunos e frequências de atividades desenvolvido em PHP com MySQL.

## 📋 Descrição

O IFPlay é um sistema web para controle de cadastro de alunos e registro de frequências em atividades. Permite o gerenciamento completo de informações acadêmicas, incluindo validação de frequências por professores.

## 🚀 Funcionalidades

### Gerenciamento de Alunos
- Cadastro de novos alunos ([cadastroAluno.php](cadastroAluno.php))
- Listagem de alunos cadastrados ([listarAluno.php](listarAluno.php))
- Edição de status (ativo/inativo) ([editarAluno.php](editarAluno.php))
- Campos: nome, matrícula, ano de entrada e status

### Gerenciamento de Frequências
- Cadastro de frequências de atividades ([frequenciaCadastrar.php](frequenciaCadastrar.php))
- Listagem de frequências cadastradas
- Edição de situação da frequência ([editarFrequencia.php](editarFrequencia.php))
- Vinculação de frequências a alunos específicos
- Status de validação: "Aguardando aprovação do professor" ou "Validado"
- Campos: descrição, data, carga horária e participante

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7+
- **Banco de Dados**: MySQL/MariaDB
- **Frontend**: HTML5
- **Servidor**: Apache/Nginx (compatível com qualquer servidor PHP)

## 📦 Estrutura do Projeto

```
ifplay/
├── cadastroAluno.php          # Formulário de cadastro de alunos
├── listarAluno.php            # Lista todos os alunos
├── editarAluno.php            # Edita status do aluno
├── frequenciaCadastrar.php    # Cadastro e lista de frequências
├── editarFrequencia.php       # Edita situação da frequência
├── ../utils/conexao.php                # Configuração de conexão com banco
├── index.php                  # Página inicial
├── IFPlay_db.sql              # Script de criação do banco
├── LICENSE                    # Licença GPL v3
└── README.md                  # Documentação
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
- `situacao` (VARCHAR 25)
- `participante` (INT, FK → aluno.alunoID)

## ⚙️ Instalação

### Pré-requisitos
- PHP 7.0 ou superior
- MySQL/MariaDB 5.7 ou superior
- Servidor web (Apache, Nginx, etc.)
- ou XAMPP

### Passos

1. **Clone o repositório**
   ```bash
   git clone <url-do-repositorio>
   cd ifplay
   ```

2. **Configure o banco de dados**
   
   Execute o script SQL:
   ```bash
   mysql -u root -p < IFPlay_db.sql
   ```

3. **Configure a conexão**
   
   Edite o arquivo [../utils/conexao.php](../utils/conexao.php) com suas credenciais:
   ```php
   $servername = "127.0.0.1";
   $username = "root";
   $password = "sua_senha";
   $dbname = "IFPlay_db";
   ```

4. **Inicie o servidor**
   
   Opção 1 - Servidor PHP embutido:
   ```bash
   php -S localhost:8000
   ```
   
   Opção 2 - Apache/Nginx:
   Coloque os arquivos no diretório web do servidor (ex: `/var/www/html/`)

5. **Acesse o sistema**
   ```
   http://localhost:8000
   ```

## 🖥️ Uso

### Página Inicial
Acesse [index.php](index.php) para navegar entre:
- Ver alunos cadastrados
- Registrar frequências

### Cadastrar Aluno
1. Acesse "Cadastrar novo aluno"
2. Preencha: nome, matrícula e ano de entrada
3. Clique em "Cadastrar"

### Editar Status do Aluno
1. Na lista de alunos, clique em "Editar"
2. Altere o status (Ativo/Inativo)
3. Salve as alterações

### Cadastrar Frequência
1. Acesse "Frequência"
2. Preencha: descrição, data, carga horária
3. Selecione o aluno participante
4. Clique em "Cadastrar"

### Validar Frequência
1. Na lista de frequências, clique em "Editar"
2. Altere a situação para "Validado"
3. Salve as alterações

## 🔒 Segurança

⚠️ **Atenção**: Este sistema é uma versão básica para fins educacionais. Para uso em produção, implemente:

- Validação e sanitização de dados no servidor
- Prepared statements (já implementado em alguns arquivos)
- Sistema de autenticação e autorização
- Proteção contra CSRF
- Criptografia de senhas (se houver login)
- HTTPS para comunicação segura

## 📄 Licença

Este projeto está licenciado sob a GNU General Public License v3.0 - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para:
- Reportar bugs
- Sugerir novas funcionalidades
- Enviar pull requests

## 📧 Contato

Para dúvidas ou sugestões, abra uma issue no repositório.