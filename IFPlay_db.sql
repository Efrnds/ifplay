CREATE DATABASE IFPlay_db;
USE IFPlay_db;
CREATE TABLE aluno(
    alunoID INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL UNIQUE,
    matricula VARCHAR(25) NOT NULL UNIQUE,
    anoEntrada DATE NOT NULL UNIQUE,
    status BOOLEAN DEFAULT 0
);

CREATE TABLE frequencia_atividade(
    ID INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(400),
    data DATE NOT NULL,
    horario TIME NOT NULL,
    situacao VARCHAR(25),
    participante INT NOT NULL,
    FOREIGN KEY (participante) REFERENCES aluno(alunoID)
);