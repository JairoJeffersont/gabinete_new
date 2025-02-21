$('.custom-alert').each(function () {
    var timeout = $(this).data('timeout') * 1000;
    if (timeout > 0) {
        $(this).delay(timeout).fadeOut();
    }
});


$('button[name="btn_salvar"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja inserir esse registro?");
    if (!confirmacao) {
        event.preventDefault();
    }
});

$('button[name="btn_apagar_gabinete"]').on('click', function (event) {
    const confirmacao = confirm(
        "⚠️ ATENÇÃO! ⚠️\n\n" +
        "Você tem CERTEZA que deseja APAGAR ESTE GABINETE? 😱\n\n" +
        "⚠️ Tudo relacionado a este gabinete será APAGADO! ⚠️\n\n" +
        "❗Esta ação NÃO PODE SER DESFEITA!❗"
    );
    if (!confirmacao) {
        event.preventDefault();
    }
});


$('button[name="btn_salvar_usuario"]').on('click', function (event) {
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



$('button[name="btn_apagar"]').on('click', function (event) {
    const confirmacao = confirm("Tem certeza que deseja apagar esse registro?");
    if (!confirmacao) {
        event.preventDefault();
    }
});