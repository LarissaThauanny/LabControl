-- LabControl 3.0 | Hospedagem: crie e selecione um banco no painel/ phpMyAdmin.
-- Nao inclui CREATE DATABASE/USE para funcionar com prefixos obrigatorios do provedor.
-- LabControl 3.0 | Instalacao local | Executar uma vez em MySQL 8.0+ ou MariaDB 10.5+

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  tipo ENUM('aluno','admin') NOT NULL DEFAULT 'aluno',
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS equipamentos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  descricao TEXT NULL,
  codigo VARCHAR(80) NULL UNIQUE,
  categoria VARCHAR(80) NOT NULL DEFAULT 'Outros',
  status ENUM('disponivel','emprestado','manutencao') NOT NULL DEFAULT 'disponivel',
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS emprestimos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  equipamento_id INT UNSIGNED NOT NULL,
  status ENUM('pendente','aprovado','recusado','cancelado','devolvido') NOT NULL DEFAULT 'pendente',
  justificativa VARCHAR(255) NULL,
  observacao_admin VARCHAR(255) NULL,
  data_solicitacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  data_aprovacao DATETIME NULL,
  data_devolucao_prevista DATE NOT NULL,
  data_devolucao DATETIME NULL,
  CONSTRAINT fk_emprestimos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_emprestimos_equipamento FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_emprestimos_usuario (usuario_id),
  INDEX idx_emprestimos_equipamento (equipamento_id),
  INDEX idx_emprestimos_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Equipamentos ficticios para facilitar avaliacao sem inserir usuarios ou senhas predefinidas.
INSERT INTO equipamentos (nome,descricao,codigo,categoria,status) VALUES
 ('Notebook de demonstração','Patrimônio fictício para testes de empréstimo','DEMO-LAB-001','Computadores','disponivel'),
 ('Projetor de demonstração','Equipamento fictício','DEMO-LAB-002','Audiovisual','disponivel'),
 ('Kit de periféricos','Mouse e teclado fictícios','DEMO-LAB-003','Periféricos','disponivel')
ON DUPLICATE KEY UPDATE codigo=VALUES(codigo);
