-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS sistema_educacional;
USE sistema_educacional;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    tipo ENUM('admin', 'professor', 'aluno') NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Configurações
CREATE TABLE IF NOT EXISTS configuracoes (
    id INT PRIMARY KEY,
    nome_escola VARCHAR(100) NOT NULL,
    endereco TEXT,
    telefone VARCHAR(20),
    email VARCHAR(100),
    sobre_texto TEXT,
    mapa_url TEXT,
    horario_funcionamento TEXT,
    redes_sociais TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de Apoiadores
CREATE TABLE IF NOT EXISTS apoiadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    logo VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    local_exibicao ENUM('banner', 'rodape', 'geral') NOT NULL,
    posicao INT DEFAULT 0,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Alunos
CREATE TABLE IF NOT EXISTS alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    nome VARCHAR(100) NOT NULL,
    serie VARCHAR(20) NOT NULL,
    turma VARCHAR(10) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de Professores
CREATE TABLE IF NOT EXISTS professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    nome VARCHAR(100) NOT NULL,
    disciplinas TEXT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de Disciplinas
CREATE TABLE IF NOT EXISTS disciplinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT,
    serie VARCHAR(20) NOT NULL,
    professor_id INT,
    FOREIGN KEY (professor_id) REFERENCES professores(id)
);

-- Tabela de Materiais
CREATE TABLE IF NOT EXISTS materiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    arquivo VARCHAR(255) NOT NULL,
    disciplina_id INT,
    enviado_por INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id),
    FOREIGN KEY (enviado_por) REFERENCES usuarios(id)
);

-- Tabela de Downloads
CREATE TABLE IF NOT EXISTS downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    arquivo VARCHAR(255) NOT NULL,
    categoria ENUM('material_didatico', 'atividade', 'jogo') NOT NULL,
    serie VARCHAR(20) NOT NULL,
    contador INT DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Notas
CREATE TABLE IF NOT EXISTS notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT,
    disciplina_id INT,
    nota DECIMAL(4,2) NOT NULL,
    bimestre INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id),
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id)
);

-- Tabela de Frequência
CREATE TABLE IF NOT EXISTS frequencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT,
    disciplina_id INT,
    data DATE NOT NULL,
    presente BOOLEAN NOT NULL,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id),
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id)
);

-- Inserir usuário admin padrão se não existir
INSERT IGNORE INTO usuarios (usuario, senha, email, tipo) 
VALUES ('admin', '$2y$10$XxPJ8xZvZj3rBxjzHXXkCuKQDB3p.U6FHxk7x9E8ksgZSw4YzzBSi', 'admin@escola.com', 'admin');

-- Inserir configurações padrão se não existirem
INSERT IGNORE INTO configuracoes (id, nome_escola, endereco, telefone, email, sobre_texto, horario_funcionamento) 
VALUES (
    1, 
    'Sistema Educacional', 
    'Rua da Escola, 123 - Centro', 
    '(11) 1234-5678', 
    'contato@escola.com',
    'Nossa escola tem como missão proporcionar uma educação de qualidade, formando cidadãos críticos e conscientes.',
    'Segunda a Sexta: 8h às 18h\nSábado: 8h às 12h'
);