-- =====================================================================
-- BANCO DE DADOS: Catálogo Fantástico
-- Estilo Quarteto Fantástico (Azul Cósmico & Laranja Flamejante)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `catalogo_fantastico` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `catalogo_fantastico`;

-- 1. TABELA DE USUÁRIOS
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `usuario` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(150) NULL UNIQUE,
    `telefone` VARCHAR(30) NULL,
    `senha` VARCHAR(255) NOT NULL, -- Hash gerado via bcrypt
    `papel` ENUM('admin', 'usuario') DEFAULT 'usuario',
    `consentimento_dados` TINYINT(1) DEFAULT 1,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. TABELA DE FRANQUIAS
CREATE TABLE IF NOT EXISTS `franquias` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(120) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `genero` VARCHAR(60) NOT NULL DEFAULT 'Super-Heróis',
    `descricao` TEXT NULL,
    `banner_url` VARCHAR(500) NULL,
    `icone_url` VARCHAR(500) NULL,
    `criado_por` INT NULL,
    `visibilidade` ENUM('publica', 'privada', 'compartilhada') DEFAULT 'publica',
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`criado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABELA DE FILMES
CREATE TABLE IF NOT EXISTS `filmes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `franquia_id` INT NOT NULL,
    `titulo` VARCHAR(200) NOT NULL,
    `ano_lancamento` INT NOT NULL,
    `ordem_cronologica` INT NOT NULL,
    `ordem_lancamento` INT NOT NULL,
    `streaming` VARCHAR(255) NOT NULL, -- Disney+, Netflix, Max, Prime Video, etc.
    `streaming_url` VARCHAR(500) NULL, -- Redirecionamento direto para assistir
    `duracao` VARCHAR(30) NULL,
    `classificacao_etaria` VARCHAR(20) DEFAULT '12', -- Livre, 10, 12, 14, 16, 18
    `sinopse` TEXT NULL,
    `poster_url` VARCHAR(500) NULL,
    `criado_por` INT NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`franquia_id`) REFERENCES `franquias`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`criado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. TABELA DE PROGRESSO DO USUÁRIO
CREATE TABLE IF NOT EXISTS `progresso_usuario` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `filme_id` INT NOT NULL,
    `assistido` TINYINT(1) DEFAULT 1,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_usuario_filme` (`usuario_id`, `filme_id`),
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`filme_id`) REFERENCES `filmes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. TABELA DE ORDENAÇÃO PERSONALIZADA DO USUÁRIO
CREATE TABLE IF NOT EXISTS `ordem_personalizada` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `franquia_id` INT NOT NULL,
    `filme_id` INT NOT NULL,
    `posicao` INT NOT NULL,
    UNIQUE KEY `uk_usuario_franquia_filme` (`usuario_id`, `franquia_id`, `filme_id`),
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`franquia_id`) REFERENCES `franquias`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`filme_id`) REFERENCES `filmes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. TABELA DE PERMISSÕES DE COMPARTILHAMENTO
CREATE TABLE IF NOT EXISTS `permissoes_listas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `dono_id` INT NOT NULL,
    `franquia_id` INT NOT NULL,
    `tipo_acesso` ENUM('publico', 'privado', 'compartilhado') DEFAULT 'publico',
    `pode_editar` TINYINT(1) DEFAULT 0,
    `pode_excluir` TINYINT(1) DEFAULT 0,
    `pode_administrar` TINYINT(1) DEFAULT 0,
    `codigo_convite` VARCHAR(64) UNIQUE,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`dono_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`franquia_id`) REFERENCES `franquias`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. TABELA DE AUDITORIA E HISTÓRICO
CREATE TABLE IF NOT EXISTS `logs_auditoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NULL,
    `usuario_nome` VARCHAR(100) NOT NULL,
    `acao` VARCHAR(80) NOT NULL,
    `detalhes` TEXT NOT NULL,
    `ip` VARCHAR(45) NOT NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. TABELA DE AVALIAÇÕES E FEEDBACKS
CREATE TABLE IF NOT EXISTS `avaliacoes_feedbacks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NULL,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NULL,
    `nota` TINYINT NOT NULL DEFAULT 5,
    `tipo` ENUM('sugestao', 'elogio', 'critica', 'problema') DEFAULT 'sugestao',
    `mensagem` TEXT NOT NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
