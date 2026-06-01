-- ============================================================
--  Sistema de Agendamento — banco.sql
--  Compatível com MySQL 5.7+
-- ============================================================

CREATE DATABASE IF NOT EXISTS sistema_agendamento
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sistema_agendamento;

-- ------------------------------------------------------------
-- Usuários
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100)  NOT NULL,
    email      VARCHAR(100)  NOT NULL UNIQUE,
    senha      VARCHAR(255)  NOT NULL,           -- password_hash (bcrypt)
    tipo       ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
    criado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Espaços (genérico: quadra, laboratório, sala, etc.)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS espacos (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(100)  NOT NULL,
    tipo         VARCHAR(60)   NOT NULL DEFAULT 'Espaço',   -- ex: Quadra, Laboratório
    descricao    TEXT,
    capacidade   INT           DEFAULT NULL,
    preco_hora   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ativo        TINYINT(1)    NOT NULL DEFAULT 1,
    criado_em    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Reservas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reservas (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id   INT  NOT NULL,
    espaco_id    INT  NOT NULL,
    data_reserva DATE NOT NULL,
    hora_inicio  TIME NOT NULL,
    hora_fim     TIME NOT NULL,
    status       ENUM('ativa','cancelada') NOT NULL DEFAULT 'ativa',
    criado_em    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (espaco_id)  REFERENCES espacos(id)  ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Admin padrão  (senha: 123456)
-- Gere um novo hash em produção: password_hash('suaSenha', PASSWORD_BCRYPT)
-- ------------------------------------------------------------
INSERT INTO usuarios (nome, email, senha, tipo) VALUES
(
    'Administrador',
    'admin@admin.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- 123456
    'admin'
);

-- Espaços de exemplo
INSERT INTO espacos (nome, tipo, descricao, capacidade, preco_hora) VALUES
('Quadra A',        'Quadra',       'Quadra poliesportiva coberta.',       20, 80.00),
('Lab. Informática','Laboratório',  'Laboratório com 30 computadores.',    30, 50.00),
('Sala de Reunião', 'Sala',         'Sala equipada para 10 pessoas.',      10, 30.00);