<?php
// Buscar informações da escola
$stmt = $conn->prepare("SELECT * FROM configuracoes WHERE id = 1");
$stmt->execute();
$config = $stmt->fetch();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Sobre Nossa Escola</h2>
                    
                    <div class="mb-4">
                        <h4>Nossa Missão</h4>
                        <p>Proporcionar uma educação de qualidade, formando cidadãos críticos e conscientes, preparados para os desafios do futuro através de um ensino inovador e inclusivo.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Nossa Visão</h4>
                        <p>Ser referência em educação, reconhecida pela excelência no ensino e na formação integral dos alunos, contribuindo para o desenvolvimento da sociedade.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Nossos Valores</h4>
                        <ul>
                            <li>Excelência acadêmica</li>
                            <li>Inovação pedagógica</li>
                            <li>Respeito à diversidade</li>
                            <li>Compromisso com a educação</li>
                            <li>Responsabilidade social</li>
                            <li>Ética e transparência</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Estrutura</h4>
                        <p>Nossa escola conta com:</p>
                        <ul>
                            <li>Salas de aula climatizadas</li>
                            <li>Laboratório de informática</li>
                            <li>Biblioteca completa</li>
                            <li>Quadra poliesportiva</li>
                            <li>Refeitório</li>
                            <li>Área de lazer</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Metodologia</h4>
                        <p>Utilizamos metodologias ativas de aprendizagem, combinando:</p>
                        <ul>
                            <li>Ensino tradicional de qualidade</li>
                            <li>Tecnologia educacional</li>
                            <li>Projetos interdisciplinares</li>
                            <li>Atividades práticas</li>
                            <li>Desenvolvimento socioemocional</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Diferenciais</h4>
                        <ul>
                            <li>Professores altamente qualificados</li>
                            <li>Material didático atualizado</li>
                            <li>Acompanhamento pedagógico personalizado</li>
                            <li>Atividades extracurriculares</li>
                            <li>Eventos culturais e esportivos</li>
                            <li>Parceria família-escola</li>
                        </ul>
                    </div>
                    
                    <?php if (isset($config['sobre_texto']) && !empty($config['sobre_texto'])): ?>
                        <div class="mb-4">
                            <h4>Mais Informações</h4>
                            <?php echo nl2br(htmlspecialchars($config['sobre_texto'])); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <h4>Entre em Contato</h4>
                        <p>Para mais informações, visite nossa escola ou entre em contato:</p>
                        <?php if (isset($config['endereco'])): ?>
                            <p><strong>Endereço:</strong> <?php echo htmlspecialchars($config['endereco']); ?></p>
                        <?php endif; ?>
                        <?php if (isset($config['telefone'])): ?>
                            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($config['telefone']); ?></p>
                        <?php endif; ?>
                        <?php if (isset($config['email'])): ?>
                            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($config['email']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>