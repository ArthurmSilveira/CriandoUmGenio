// Função para validação de formulários
function validarFormulario(form) {
    'use strict';
    
    const forms = document.querySelectorAll(form);
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

// Função para confirmar exclusão
function confirmarExclusao(id, tipo) {
    if (confirm(`Deseja realmente excluir este ${tipo}?`)) {
        document.getElementById(`form-excluir-${id}`).submit();
    }
}

// Inicialização de tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Função para preview de imagem
function previewImagem(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').setAttribute('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}