import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import db from '../config/database.js';

export const login = async (req, res) => {
  const { username, password } = req.body;

  db.get('SELECT * FROM users WHERE username = ?', [username], async (err, user) => {
    if (err) {
      return res.status(500).json({ error: 'Database error' });
    }

    if (!user) {
      return res.status(401).json({ error: 'User not found' });
    }

    const validPassword = await bcrypt.compare(password, user.password);
    if (!validPassword) {
      return res.status(401).json({ error: 'Invalid password' });
    }

    const token = jwt.sign(
      { id: user.id, role: user.role },
      process.env.JWT_SECRET || 'your-secret-key',
      { expiresIn: '24h' }
    );

    req.session.token = token;
    req.session.user = { id: user.id, role: user.role };

    res.json({ token });
  });
};

export const register = async (req, res) => {
  const { username, password, email, role } = req.body;

  try {
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash(password, salt);

    db.run(
      'INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)',
      [username, hashedPassword, email, role],
      function(err) {
        if (err) {
          return res.status(500).json({ error: 'Error creating user' });
        }
        res.status(201).json({ message: 'User created successfully' });
      }
    );
  } catch (err) {
    res.status(500).json({ error: 'Error creating user' });
  }
};

export const logout = (req, res) => {
  req.session.destroy();
  res.redirect('/');
};