import express from 'express';
import { login, cadastrar, sair } from '../controllers/autenticacaoController.js';

const router = express.Router();

router.post('/login', login);
router.post('/cadastrar', cadastrar);
router.get('/sair', sair);

export default router;