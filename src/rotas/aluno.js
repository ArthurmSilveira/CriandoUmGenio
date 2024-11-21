import express from 'express';
import { autenticarToken, verificarTipo } from '../middleware/autenticacao.js';

const router = express.Router();

router.use(autenticarToken);
router.use(verificarTipo(['aluno']));

router.get('/dashboard', (req, res) => {
    res.render('aluno/dashboard', { usuario: req.usuario });
});

router.get('/notas', (req, res) => {
    res.render('aluno/notas', { usuario: req.usuario });
});

router.get('/materiais', (req, res) => {
    res.render('aluno/materiais', { usuario: req.usuario });
});

export default router;