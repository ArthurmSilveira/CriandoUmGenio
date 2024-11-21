<?php
verificarPermissao('admin');

function gerarPDF($html, $nome_arquivo) {
    try {
        // Se não conseguir carregar via Composer, usar alternativa
        if (!file_exists('vendor/autoload.php')) {
            // Gerar arquivo HTML como fallback
            $dir_relatorios = 'relatorios';
            if (!file_exists($dir_relatorios)) {
                mkdir($dir_relatorios, 0777, true);
            }
            
            $nome_arquivo_html = str_replace('.pdf', '.html', $nome_arquivo);
            $caminho_arquivo = $dir_relatorios . '/' . $nome_arquivo_html;
            
            // Adicionar CSS básico ao HTML
            $html = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; }
                        table { border-collapse: collapse; width: 100%; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f5f5f5; }
                    </style>
                </head>
                <body>' . $html . '</body></html>';
            
            file_put_contents($caminho_arquivo, $html);
            return $caminho_arquivo;
        }
        
        // Se tiver Composer, usar mPDF
        require_once 'vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        
        $dir_relatorios = 'relatorios';
        if (!file_exists($dir_relatorios)) {
            mkdir($dir_relatorios, 0777, true);
        }
        
        $caminho_arquivo = $dir_relatorios . '/' . $nome_arquivo;
        $mpdf->Output($caminho_arquivo, 'F');
        
        return $caminho_arquivo;
    } catch (Exception $e) {
        error_log("Erro ao gerar relatório: " . $e->getMessage());
        throw $e;
    }
}

$mensagem = '';
$relatorio_gerado = '';

if (isset($_POST['gerar_relatorio'])) {
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $periodo = isset($_POST['periodo']) ? $_POST['periodo'] : 'atual';
    
    if (!empty($tipo)) {
        try {
            $html = '';
            $nome_arquivo = '';
            
            switch ($tipo) {
                case 'alunos':
                    $stmt = $conn->query("SELECT * FROM alunos ORDER BY nome");
                    $alunos = $stmt->fetchAll();
                    
                    $html = '<h1>Relatório de Alunos</h1>';
                    $html .= '<table border="1" cellpadding="5">';
                    $html .= '<tr><th>Nome</th><th>Série</th><th>Turma</th></tr>';
                    
                    foreach ($alunos as $aluno) {
                        $html .= '<tr>';
                        $html .= '<td>' . htmlspecialchars($aluno['nome']) . '</td>';
                        $html .= '<td>' . $aluno['serie'] . 'º Ano</td>';
                        $html .= '<td>Turma ' . $aluno['turma'] . '</td>';
                        $html .= '</tr>';
                    }
                    
                    $html .= '</table>';
                    $nome_arquivo = 'relatorio_alunos_' . date('Y-m-d') . '.pdf';
                    break;
                    
                case 'professores':
                    $stmt = $conn->query("SELECT * FROM professores ORDER BY nome");
                    $professores = $stmt->fetchAll();
                    
                    $html = '<h1>Relatório de Professores</h1>';
                    $html .= '<table border="1" cellpadding="5">';
                    $html .= '<tr><th>Nome</th><th>Disciplinas</th></tr>';
                    
                    foreach ($professores as $professor) {
                        $html .= '<tr>';
                        $html .= '<td>' . htmlspecialchars($professor['nome']) . '</td>';
                        $html .= '<td>' . htmlspecialchars($professor['disciplinas']) . '</td>';
                        $html .= '</tr>';
                    }
                    
                    $html .= '</table>';
                    $nome_arquivo = 'relatorio_professores_' . date('Y-m-d') . '.pdf';
                    break;
                    
                case 'notas':
                    $stmt = $conn->query("
                        SELECT a.nome as aluno, d.nome as disciplina, n.nota, n.bimestre
                        FROM notas n
                        JOIN alunos a ON n.aluno_id = a.id
                        JOIN disciplinas d ON n.disciplina_id = d.id
                        ORDER BY a.nome, d.nome, n.bimestre
                    ");
                    $notas = $stmt->fetchAll();
                    
                    $html = '<h1>Relatório de Notas</h1>';
                    $html .= '<table border="1" cellpadding="5">';
                    $html .= '<tr><th>Aluno</th><th>Disciplina</th><th>Nota</th><th>Bimestre</th></tr>';
                    
                    foreach ($notas as $nota) {
                        $html .= '<tr>';
                        $html .= '<td>' . htmlspecialchars($nota['aluno']) . '</td>';
                        $html .= '<td>' . htmlspecialchars($nota['disciplina']) . '</td>';
                        $html .= '<td>' . $nota['nota'] . '</td>';
                        $html .= '<td>' . $nota['bimestre'] . 'º Bimestre</td>';
                        $html .= '</tr>';
                    }
                    
                    $html .= '</table>';
                    $nome_arquivo = 'relatorio_notas_' . date('Y-m-d') . '.pdf';
                    break;
            }
            
            if ($html && $nome_arquivo) {
                $caminho_arquivo = gerarPDF($html, $nome_arquivo);
                $mensagem = '<div class="alert alert-success">Relatório gerado com sucesso!</div>';
                $relatorio_gerado = $caminho_arquivo;
            }
        } catch (Exception $e) {
            $mensagem = '<div class="alert alert-danger">Erro ao gerar relatório: ' . $e->getMessage() . '</div>';
        }
    } else {
        $mensagem = '<div class="alert alert-danger">Selecione o tipo de relatório!</div>';
    }
}
?>

<h2 class="mb-4">Relatórios</h2>

<?php echo $mensagem; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Gerar Relatório</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Relatório</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="">Selecione...</option>
                            <option value="alunos">Alunos</option>
                            <option value="professores">Professores</option>
                            <option value="notas">Notas</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="periodo" class="form-label">Período</label>
                        <select class="form-control" id="periodo" name="periodo">
                            <option value="atual">Atual</option>
                            <option value="bimestre1">1º Bimestre</option>
                            <option value="bimestre2">2º Bimestre</option>
                            <option value="bimestre3">3º Bimestre</option>
                            <option value="bimestre4">4º Bimestre</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="gerar_relatorio" class="btn btn-primary">
                        Gerar Relatório
                    </button>
                </form>
            </div>
        </div>
        
        <?php if ($relatorio_gerado): ?>
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Relatório Gerado</h5>
                <div class="d-grid gap-2">
                    <a href="<?php echo $relatorio_gerado; ?>" class="btn btn-success" target="_blank">
                        <i class="fas fa-download"></i> Download do Relatório
                    </a>
                    <button onclick="imprimirRelatorio('<?php echo $relatorio_gerado; ?>')" class="btn btn-info">
                        <i class="fas fa-print"></i> Imprimir Relatório
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Relatórios Gerados</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $dir_relatorios = 'relatorios';
                            if (file_exists($dir_relatorios)) {
                                $arquivos = glob($dir_relatorios . '/*.{pdf,html}', GLOB_BRACE);
                                foreach ($arquivos as $arquivo) {
                                    $nome = basename($arquivo);
                                    $data = date('d/m/Y H:i', filemtime($arquivo));
                                    echo '<tr>';
                                    echo '<td>' . $nome . '</td>';
                                    echo '<td>' . $data . '</td>';
                                    echo '<td>';
                                    echo '<a href="' . $arquivo . '" class="btn btn-sm btn-primary me-2" target="_blank">Visualizar</a>';
                                    echo '<button onclick="imprimirRelatorio(\'' . $arquivo . '\')" class="btn btn-sm btn-info">Imprimir</button>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function imprimirRelatorio(url) {
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);
    
    iframe.onload = function() {
        iframe.contentWindow.print();
        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 1000);
    };
}
</script>