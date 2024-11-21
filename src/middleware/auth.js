import jwt from 'jsonwebtoken';

export const authenticateToken = (req, res, next) => {
  const token = req.session.token;

  if (!token) {
    return res.status(401).redirect('/auth/login');
  }

  try {
    const user = jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');
    req.user = user;
    next();
  } catch (err) {
    return res.status(403).redirect('/auth/login');
  }
};

export const checkRole = (roles) => {
  return (req, res, next) => {
    if (!req.user) {
      return res.status(401).redirect('/auth/login');
    }

    if (!roles.includes(req.user.role)) {
      return res.status(403).render('error', { error: 'Access forbidden' });
    }

    next();
  };
};