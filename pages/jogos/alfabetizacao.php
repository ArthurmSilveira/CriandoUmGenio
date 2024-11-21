<?php
verificarPermissao('aluno');

$jogo_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM jogos WHERE id = ? AND categoria = 'alfabetizacao'");
$stmt->execute([$jogo_id]);
$jogo = $stmt->fetch();

if (!$jogo) {
    header('Location: index.php?pagina=jogos');
    exit;
}
?>

<div class="container-fluid">
    <h2 class="mb-4"><?php echo htmlspecialchars($jogo['nome']); ?></h2>
    
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>Pontos: <span id="pontos">0</span></div>
                        <div>Nível: <span id="nivel">1</span></div>
                    </div>
                    
                    <div id="jogo-alfabetizacao" class="text-center">
                        <div id="palavra-container" class="mb-4">
                            <h3 id="palavra-atual" class="display-4"></h3>
                        </div>
                        
                        <div id="opcoes-container" class="row g-3">
                            <!-- As opções serão inseridas aqui via JavaScript -->
                        </div>
                        
                        <div id="feedback" class="mt-4">
                            <div class="alert d-none" role="alert"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const palavras = {
    1: [ // Nível 1 - Palavras simples
        {palavra: 'BOLA', imagem: 'bola.jpg'},
        {palavra: 'CASA', imagem: 'casa.jpg'},
        {palavra: 'GATO', imagem: 'gato.jpg'},
        {palavra: 'PATO', imagem: 'pato.jpg'}
    ],
    2: [ // Nível 2 - Palavras com dígrafos
        {palavra: 'CARRO', imagem: 'carro.jpg'},
        {palavra: 'CHAVE', imagem: 'chave.jpg'},
        {palavra: 'QUEIJO', imagem: 'queijo.jpg'},
        {palavra: 'GUARDA', imagem: 'guarda.jpg'}
    ],
    3: [ // Nível 3 - Palavras complexas
        {palavra: 'BORBOLETA', imagem: 'borboleta.jpg'},
        {palavra: 'CHOCOLATE', imagem: 'chocolate.jpg'},
        {palavra: 'BRINQUEDO', imagem: 'brinquedo.jpg'},
        {palavra: 'PRESENTE', imagem: 'presente.jpg'}
    ]
};

let nivelAtual = 1;
let pontos = 0;
let palavraAtual = null;

function embaralharArray(array) {
    return array.sort(() => Math.random() - 0.5);
}

function mostrarFeedback(mensagem, tipo) {
    const feedback = document.getElementById('feedback').querySelector('.alert');
    feedback.textContent = mensagem;
    feedback.className = `alert alert-${tipo}`;
    feedback.classList.remove('d-none');
    
    setTimeout(() => {
        feedback.classList.add('d-none');
    }, 2000);
}

function verificarResposta(resposta) {
    if (resposta === palavraAtual.palavra) {
        pontos += 10;
        document.getElementById('pontos').textContent = pontos;
        mostrarFeedback('Muito bem!', 'success');
        
        if (pontos >= nivelAtual * 40) {
            if (nivelAtual < 3) {
                nivelAtual++;
                document.getElementById('nivel').textContent = nivelAtual;
                mostrarFeedback('Parabéns! Você passou de nível!', 'success');
            } else if (pontos >= 120) {
                mostrarFeedback('Parabéns! Você completou o jogo!', 'success');
                setTimeout(() => {
                    if (confirm('Você quer jogar novamente?')) {
                        reiniciarJogo();
                    }
                }, 2000);
                return;
            }
        }
    } else {
        mostrarFeedback('Tente novamente!', 'danger');
    }
    
    proximaPalavra();
}

function criarOpcoes(palavraCorreta) {
    const opcoesContainer = document.getElementById('opcoes-container');
    opcoesContainer.innerHTML = '';
    
    let opcoes = [palavraCorreta.palavra];
    const palavrasNivel = palavras[nivelAtual].filter(p => p !== palavraCorreta);
    opcoes = opcoes.concat(embaralharArray(palavrasNivel).slice(0, 3).map(p => p.palavra));
    opcoes = embaralharArray(opcoes);
    
    opcoes.forEach(opcao => {
        const div = document.createElement('div');
        div.className = 'col-6';
        div.innerHTML = `
            <button class="btn btn-lg btn-outline-primary w-100" onclick="verificarResposta('${opcao}')">
                ${opcao}
            </button>
        `;
        opcoesContainer.appendChild(div);
    });
}

function proximaPalavra() {
    const palavrasNivel = palavras[nivelAtual];
    palavraAtual = palavrasNivel[Math.floor(Math.random() * palavrasNivel.length)];
    
    document.getElementById('palavra-atual').innerHTML = `
        <img src="assets/images/jogos/${palavraAtual.imagem}" 
             alt="${palavraAtual.palavra}"
             class="img-fluid mb-3"
             style="max-height: 200px;">
    `;
    
    criarOpcoes(palavraAtual);
}

function reiniciarJogo() {
    nivelAtual = 1;
    pontos = 0;
    document.getElementById('pontos').textContent = pontos;
    document.getElementById('nivel').textContent = nivelAtual;
    proximaPalavra();
}

// Iniciar o jogo
proximaPalavra();
</script>

<style>
#jogo-alfabetizacao {
    min-height: 400px;
}

.btn-outline-primary {
    font-size: 1.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.btn-outline-primary:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
}

#palavra-atual img {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>