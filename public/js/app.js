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