CREATE DATABASE IF NOT EXISTS sistema_agendamento;
USE sistema_agendamento;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin','cliente') DEFAULT 'cliente'
);

CREATE TABLE quadras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco_hora DECIMAL(10,2)
);

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    quadra_id INT,
    data_reserva DATE,
    hora_inicio TIME,
    hora_fim TIME
);

INSERT INTO usuarios(nome,email,senha,tipo)
VALUES(
'Administrador',
'admin@admin.com',
MD5('123456'),
'admin'
);
