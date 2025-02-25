$('.custom-alert').each(function () {
    var timeout = $(this).data('timeout') * 1000;
    if (timeout > 0) {
        $(this).delay(timeout).fadeOut();
    }
});

$('#file-button').on('click', function () {
    $('#file-input').click();
});

$('#file-input').on('change', function () {
    var fileName = $(this).val().split('\\').pop();
    $('#file-button').html(fileName ? '<i class="bi bi-check-circle"></i> Ok!' : 'Nada foi enviado');
});

$('button[name="btn_apagar"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja apagar esse registro?");
    if (!confirmacao) {
        event.preventDefault();
    }
});


$('button[name="btn_apagar_arquivo"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja apagar esse arquivo?");
    if (!confirmacao) {
        event.preventDefault();
    }
});


$('button[name="btn_apagar_post"]').on('click', function (event) {
    
    const confirmacao = confirm("⚠️ Atenção! Ao apagar essa postagem, todos os arquivos associados a ela serão permanentemente excluídos e não poderão ser recuperados!");
    if (!confirmacao) {
        event.preventDefault();
    }
});


$('button[name="btn_upload"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja fazer upload desse arquivo?");
    if (!confirmacao) {
        event.preventDefault();
    }
});

$('button[name="btn_salvar"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja inserir esse registro?");
    if (!confirmacao) {
        event.preventDefault();
    }
});

$('button[name="btn_atualizar"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja atualizar esse registro?");
    if (!confirmacao) {
        event.preventDefault();
    }
});



function copyToClipboard() {
    // Pega o link do elemento com o id 'link-cadastro'
    var link = document.getElementById('link-cadastro').innerText;

    // Cria um elemento de input para copiar o texto para a área de transferência
    var tempInput = document.createElement('input');
    tempInput.value = link;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    // Opcional: pode adicionar um feedback visual aqui, como um alert ou tooltip
    alert('Link copiado para a área de transferência!');
}