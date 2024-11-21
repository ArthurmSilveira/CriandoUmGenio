import db from '../config/bancoDados.js';

export const dashboard = (req, res) => {
    res.render('professor/dashboard', {
        usuario: req.session.usuario,
        titulo: 'Dashboard do Professor'
    });
};

export const listarTurmas = (req, res) => {
    const professorId = req.usuario.id;
    db.all(
        'SELECT * FROM turmas WHERE professor_id = ?',
        [professorId],
        (err, turmas) => {
            if (err) {
                return res.status(500).render('erro', { erro: 'Erro ao buscar turmas' });
            }
            res.render('professor/turmas', {
                usuario: req.session.usuario,
                turmas: turmas,
                titulo: 'Minhas Turmas'
            });
        }
    );
};

export const lancarNotas = async (req, res) => {
    const { alunoId, disciplinaId, nota, bimestre } = req.body;
    // Implementar lógica de lançamento de notas
};

export const registrarFrequencia = async (req, res) => {
    const { alunoId, disciplinaId, data, presente } = req.body;
    // Implementar lógica de registro de frequência
};