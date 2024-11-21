import db from '../config/bancoDados.js';

export const dashboard = (req, res) => {
    res.render('aluno/dashboard', {
        usuario: req.session.usuario,
        titulo: 'Dashboard do Aluno'
    });
};

export const visualizarNotas = (req, res) => {
    const alunoId = req.usuario.id;
    db.all(
        'SELECT n.*, d.nome as disciplina FROM notas n JOIN disciplinas d ON n.disciplina_id = d.id WHERE n.aluno_id = ?',
        [alunoId],
        (err, notas) => {
            if (err) {
                return res.status(500).render('erro', { erro: 'Erro ao buscar notas' });
            }
            res.render('aluno/notas', {
                usuario: req.session.usuario,
                notas: notas,
                titulo: 'Minhas Notas'
            });
        }
    );
};

export const visualizarMateriais = (req, res) => {
    const alunoId = req.usuario.id;
    db.all(
        'SELECT m.* FROM materiais m JOIN disciplinas d ON m.disciplina_id = d.id JOIN alunos_turmas at ON d.turma_id = at.turma_id WHERE at.aluno_id = ?',
        [alunoId],
        (err, materiais) => {
            if (err) {
                return res.status(500).render('erro', { erro: 'Erro ao buscar materiais' });
            }
            res.render('aluno/materiais', {
                usuario: req.session.usuario,
                materiais: materiais,
                titulo: 'Materiais Didáticos'
            });
        }
    );
};