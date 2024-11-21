<?php
verificarPermissao('aluno');

$jogo_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM jogos WHERE id = ? AND categoria = 'memoria'");
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
                        <div>Tentativas: <span id="tentativas">0</span></div>
                        <div>Pares encontrados: <span id="pares">0</span></div>
                    </div>
                    
                    <div id="jogo-memoria" class="row g-2">
                        <!-- As cartas serão inseridas aqui via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const cartas = [
    {id: 1, imagem: 'carta1.jpg'},
    {id: 2, imagem: 'carta2.jpg'},
    {id: 3, imagem: 'carta3.jpg'},
    {id: 4, imagem: 'carta4.jpg'},
    {id: 5, imagem: 'carta5.jpg'},
    {id: 6, imagem: 'carta6.jpg'}
];

let cartasViradas = [];
let paresEncontrados = 0;
let tentativas = 0;

function embaralharCartas() {
    const todasCartas = [...cartas, ...cartas];
    return todasCartas.sort(() => Math.random() - 0.5);
}

function criarTabuleiro() {
    const tabuleiro = document.getElementById('jogo-memoria');
    const cartasEmbaralhadas = embaralharCartas();
    
    cartasEmbaralhadas.forEach((carta, index) => {
        const div = document.createElement('div');
        div.className = 'col-3';
        div.innerHTML = `
            <div class="carta" data-id="${carta.id}" data-index="${index}">
                <div class="carta-conteudo">
                    <div class="carta-frente"></div>
                    <div class="carta-verso">
                        <img src="assets/images/jogos/${carta.imagem}" alt="Carta">
                    </div>
                </div>
            </div>
        `;
        tabuleiro.appendChild(div);
        
        div.querySelector('.carta').addEventListener('click', virarCarta);
    });
}

function virarCarta(e) {
    const carta = e.currentTarget;
    if (carta.classList.contains('virada') || cartasViradas.length >= 2) return;
    
    carta.classList.add('virada');
    cartasViradas.push(carta);
    
    if (cartasViradas.length === 2) {
        tentativas++;
        document.getElementById('tentativas').textContent = tentativas;
        
        setTimeout(verificarPar, 1000);
    }
}

function verificarPar() {
    const [carta1, carta2] = cartasViradas;
    const match = carta1.dataset.id === carta2.dataset.id;
    
    if (match) {
        paresEncontrados++;
        document.getElementById('pares').textContent = paresEncontrados;
        
        if (paresEncontrados === cartas.length) {
            alert('Parabéns! Você completou o jogo!');
        }
    } else {
        carta1.classList.remove('virada');
        carta2.classList.remove('virada');
    }
    
    cartasViradas = [];
}

criarTabuleiro();
</script>

<style>
.carta {
    perspective: 1000px;
    cursor: pointer;
    aspect-ratio: 1;
}

.carta-conteudo {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.5s;
}

.carta.virada .carta-conteudo {
    transform: rotateY(180deg);
}

.carta-frente, .carta-verso {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 8px;
}

.carta-frente {
    background-color: #2196f3;
}

.carta-verso {
    transform: rotateY(180deg);
    background-color: white;
}

.carta-verso img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}
</style>