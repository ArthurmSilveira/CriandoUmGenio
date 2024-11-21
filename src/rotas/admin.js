import express from 'express';
import { autenticarToken, verificarTipo } from '../middleware/autenticacao.js';

const router = express.Router();

router.use(autenticarToken);
router.use(verificarTipo(['admin']));

router.get('/dashboard', (req, res) => {
    res.render('admin/dashboard', { usuario: req.usuario });
});

router.get('/usuarios', (req, res) => {
    res.render('admin/usuarios', { usuario: req.usuario });
});

export default router;