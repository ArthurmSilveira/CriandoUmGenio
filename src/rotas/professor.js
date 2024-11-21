import express from 'express';
import { autenticarToken, verificarTipo } from '../middleware/autenticacao.js';

const router = express.Router();

router.use(autenticarToken);
router.use(verificarTipo(['professor']));

router.get('/dashboard', (req, res) => {
    res.render('professor/dashboard', { usuario: req.usuario });
});

router.get('/notas', (req, res) => {
    res.render('professor/notas', { usuario: req.usuario });
});

router.get('/materiais', (req, res) => {
    res.render('professor/materiais', { usuario: req.usuario });
});

export default router;