-- Adicionar à estrutura existente

CREATE TABLE jogos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria ENUM('alfabetizacao', 'matematica', 'ciencias', 'memoria') NOT NULL,
    serie VARCHAR(20) NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);