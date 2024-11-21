import sqlite3 from 'sqlite3';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const dbPath = join(__dirname, '../../database.sqlite');

const db = new sqlite3.Database(dbPath, (err) => {
  if (err) {
    console.error('Erro ao conectar ao banco de dados:', err);
    return;
  }
  console.log('Conectado ao banco de dados SQLite');
  inicializarBancoDados();
});

function inicializarBancoDados() {
  db.serialize(() => {
    // Tabela de Usuários
    db.run(`CREATE TABLE IF NOT EXISTS usuarios (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      usuario TEXT UNIQUE NOT NULL,
      senha TEXT NOT NULL,
      email TEXT UNIQUE NOT NULL,
      tipo TEXT NOT NULL,
      criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    )`);

    // Tabela de Alunos
    db.run(`CREATE TABLE IF NOT EXISTS alunos (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      usuario_id INTEGER,
      nome TEXT NOT NULL,
      serie TEXT NOT NULL,
      turma TEXT NOT NULL,
      FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )`);

    // Tabela de Professores
    db.run(`CREATE TABLE IF NOT EXISTS professores (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      usuario_id INTEGER,
      nome TEXT NOT NULL,
      disciplinas TEXT NOT NULL,
      FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )`);

    // Tabela de Disciplinas
    db.run(`CREATE TABLE IF NOT EXISTS disciplinas (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      nome TEXT NOT NULL,
      descricao TEXT,
      serie TEXT NOT NULL,
      professor_id INTEGER,
      FOREIGN KEY (professor_id) REFERENCES professores(id)
    )`);

    // Tabela de Materiais
    db.run(`CREATE TABLE IF NOT EXISTS materiais (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      titulo TEXT NOT NULL,
      descricao TEXT,
      caminho_arquivo TEXT NOT NULL,
      disciplina_id INTEGER,
      enviado_por INTEGER,
      criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id),
      FOREIGN KEY (enviado_por) REFERENCES usuarios(id)
    )`);

    // Tabela de Notas
    db.run(`CREATE TABLE IF NOT EXISTS notas (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      aluno_id INTEGER,
      disciplina_id INTEGER,
      nota REAL NOT NULL,
      bimestre TEXT NOT NULL,
      criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (aluno_id) REFERENCES alunos(id),
      FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id)
    )`);

    // Tabela de Frequência
    db.run(`CREATE TABLE IF NOT EXISTS frequencia (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      aluno_id INTEGER,
      disciplina_id INTEGER,
      data DATE NOT NULL,
      presente BOOLEAN NOT NULL,
      FOREIGN KEY (aluno_id) REFERENCES alunos(id),
      FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id)
    )`);
  });
}

export default db;