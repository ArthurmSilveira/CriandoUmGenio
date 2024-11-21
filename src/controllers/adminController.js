import db from '../config/bancoDados.js';

export const dashboard = (req, res) => {
    res.render('admin/dashboard', {
        usuario: req.session.usuario,
        titulo: 'Dashboard Administrativo'
    });
};

export const listarUsuarios = (req, res) => {
    db.all('SELECT * FROM usuarios', [], (err, usuarios) => {
        if (err) {
            return res.status(500).render('erro', { erro: 'Erro ao buscar usuários' });
        }
        res.render('admin/usuarios', {
            usuario: req.session.usuario,
            usuarios: usuarios,
            titulo: 'Gerenciar Usuários'
        });
    });
};

export const criarUsuario = async (req, res) => {
    const { nome, email, senha, tipo } = req.body;
    // Implementar lógica de criação de usuário
};

export const editarUsuario = async (req, res) => {
    const { id } = req.params;
    const { nome, email, tipo } = req.body;
    // Implementar lógica de edição de usuário
};

export const excluirUsuario = async (req, res) => {
    const { id } = req.params;
    // Implementar lógica de exclusão de usuário
};