-- CREATE DATABASE IFPlay_db;
-- USE IFPlay_db;
CREATE TABLE aluno (
    alunoID INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    matricula VARCHAR(25) NOT NULL UNIQUE,
    anoEntrada DATE NOT NULL,
    status BOOLEAN DEFAULT 1
);

CREATE TABLE frequencia_atividade (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(400),
    data DATE NOT NULL,
    horario TIME NOT NULL,
    situacao VARCHAR(25) DEFAULT 'Pendente'
);

CREATE TABLE aluno_frequencia (
    alunoID INT NOT NULL,
    frequenciaID INT NOT NULL,
    PRIMARY KEY (alunoID, frequenciaID),
    FOREIGN KEY (alunoID) REFERENCES aluno (alunoID) ON DELETE CASCADE,
    FOREIGN KEY (frequenciaID) REFERENCES frequencia_atividade (ID) ON DELETE CASCADE
);