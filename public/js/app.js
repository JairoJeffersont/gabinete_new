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

$('button[name="btn_atualizar_gabinete"]').on('click', function (event) {
    const confirmacao = confirm("Você tem certeza que deseja alterar este gabinete? ⚠️ Atenção! A mudança no tipo e no político do gabinete pode afetar o funcionamento de algumas funções do sistema.");
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


$('button[name="btn_atualizar_usuario"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja atualizar esse usuário?");
    if (!confirmacao) {
        event.preventDefault();
    }
});

$('button[name="btn_desativar_usuario"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja desativar esse usuário?");
    if (!confirmacao) {
        event.preventDefault();
    }
});

$('button[name="btn_ativar_usuario"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja ativar esse usuário?");
    if (!confirmacao) {
        event.preventDefault();
    }
});

$('button[name="btn_apagar_usuario"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja apagar esse usuário?");
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


// Excluir o comportamento do modal para o botão com id "btn_imprimir"
$('button').not('#sidebarToggle, #btn_imprimir').on('click', function () {
    $('.modal').modal('show');

    // Após 10 segundos, o modal vai desaparecer
    setTimeout(function () {
        $('.modal').modal('hide');
    }, 10000); // 10000ms = 10 segundos
});

// Caso o botão específico do menu seja clicado, ele não vai exibir o modal
$('#sidebarToggle').on('click', function (e) {
    e.stopPropagation(); // Impede que o evento de clique se propague e abra o modal
});

// Caso o botão com id "btn_imprimir" seja clicado, ele não vai exibir o modal
$('#btn_imprimir').on('click', function (e) {
    e.stopPropagation(); // Impede que o evento de clique se propague e abra o modal
});

// Ao clicar no link com id "link"
$('#link').on('click', function () {
    $('.modal').modal('show');

    // Após 3 segundos, o modal vai desaparecer
    setTimeout(function () {
        $('.modal').modal('hide');
    }, 10000); // 10000ms = 10 segundos
});

// Excluir o comportamento do modal para links com id "btn_imprimir"
$('a').not('#btn_imprimir').not('#sidebarToggle').not('#navbarDropdown').not('.dropdown-item').on('click', function () {
    // Impede que os links dentro do menu (navbar) abram o modal
    $('.modal').modal('show');

    // Após 3 segundos, o modal vai desaparecer
    setTimeout(function () {
        $('.modal').modal('hide');
    }, 10000); // 10000ms = 10 segundos
});


