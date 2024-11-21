import jwt from 'jsonwebtoken';

export const autenticarToken = (req, res, next) => {
  const token = req.session.token;

  if (!token) {
    return res.status(401).redirect('/auth/login');
  }

  try {
    const usuario = jwt.verify(token, process.env.JWT_SECRET || 'chave-secreta');
    req.usuario = usuario;
    next();
  } catch (err) {
    return res.status(403).redirect('/auth/login');
  }
};

export const verificarTipo = (tipos) => {
  return (req, res, next) => {
    if (!req.usuario) {
      return res.status(401).redirect('/auth/login');
    }

    if (!tipos.includes(req.usuario.tipo)) {
      return res.status(403).render('erro', { erro: 'Acesso negado' });
    }

    next();
  };
};