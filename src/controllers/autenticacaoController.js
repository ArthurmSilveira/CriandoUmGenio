import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import db from '../config/bancoDados.js';

export const login = async (req, res) => {
  const { usuario, senha } = req.body;

  db.get('SELECT * FROM usuarios WHERE usuario = ?', [usuario], async (err, user) => {
    if (err) {
      return res.status(500).json({ erro: 'Erro no banco de dados' });
    }

    if (!user) {
      return res.status(401).json({ erro: 'Usuário não encontrado' });
    }

    const senhaValida = await bcrypt.compare(senha, user.senha);
    if (!senhaValida) {
      return res.status(401).json({ erro: 'Senha inválida' });
    }

    const token = jwt.sign(
      { id: user.id, tipo: user.tipo },
      process.env.JWT_SECRET || 'chave-secreta',
      { expiresIn: '24h' }
    );

    req.session.token = token;
    req.session.usuario = { id: user.id, tipo: user.tipo };

    res.json({ token });
  });
};

export const cadastrar = async (req, res) => {
  const { usuario, senha, email, tipo } = req.body;

  try {
    const salt = await bcrypt.genSalt(10);
    const senhaCriptografada = await bcrypt.hash(senha, salt);

    db.run(
      'INSERT INTO usuarios (usuario, senha, email, tipo) VALUES (?, ?, ?, ?)',
      [usuario, senhaCriptografada, email, tipo],
      function(err) {
        if (err) {
          return res.status(500).json({ erro: 'Erro ao criar usuário' });
        }
        res.status(201).json({ mensagem: 'Usuário criado com sucesso' });
      }
    );
  } catch (err) {
    res.status(500).json({ erro: 'Erro ao criar usuário' });
  }
};

export const sair = (req, res) => {
  req.session.destroy();
  res.redirect('/');
};