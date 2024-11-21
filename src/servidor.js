import express from 'express';
import session from 'express-session';
import helmet from 'helmet';
import cors from 'cors';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';
import dotenv from 'dotenv';

// Importação das rotas
import rotasAutenticacao from './rotas/autenticacao.js';
import rotasAdmin from './rotas/admin.js';
import rotasProfessor from './rotas/professor.js';
import rotasAluno from './rotas/aluno.js';

dotenv.config();

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const app = express();
const PORTA = process.env.PORT || 3000;

// Middlewares
app.use(helmet());
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(join(__dirname, 'public')));

app.use(session({
  secret: process.env.SESSION_SECRET || 'chave-secreta',
  resave: false,
  saveUninitialized: false,
  cookie: { secure: process.env.NODE_ENV === 'production' }
}));

// Configuração do motor de visualização
app.set('view engine', 'ejs');
app.set('views', join(__dirname, 'views'));

// Rotas
app.use('/auth', rotasAutenticacao);
app.use('/admin', rotasAdmin);
app.use('/professor', rotasProfessor);
app.use('/aluno', rotasAluno);

// Rota inicial
app.get('/', (req, res) => {
  res.render('index', { usuario: req.session.usuario });
});

// Tratamento de erros
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).render('erro', { erro: 'Algo deu errado!' });
});

app.listen(PORTA, () => {
  console.log(`Servidor rodando na porta ${PORTA}`);
});