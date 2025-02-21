<div class="card mb-2">
    <div class="card-body card_descricao_body">
        <h6 class="card-title mb-0">Seja bem-vindo(a): <?php echo $buscaUsuario['dados']['usuario_nome']; ?>! </h6>


        <?php


        if ($buscaUsuario['dados']['usuario_aniversario'] == '2000-01-01' || empty($buscaUsuario['dados']['usuario_aniversario'])) {
            echo '<hr><p class="card-text mb-0 mt-2"><i class="bi bi-info-circle"></i> Você precisa informar a data do seu aniversário. <a href="?secao=meu-gabinete">Clique aqui.</a></p>';
        } else {
            $aniversario = $buscaUsuario['dados']['usuario_aniversario'];
            $dataAtual = new DateTime();
            $anoAtual = $dataAtual->format('Y');

            // Extrai o mês e o dia do aniversário
            $dataAniversario = DateTime::createFromFormat('Y-m-d', $aniversario);
            $mesDiaAniversario = $dataAniversario->format('m-d');

            // Define o aniversário deste ano
            $dataAniversario = DateTime::createFromFormat('Y-m-d', $anoAtual . '-' . $mesDiaAniversario);

            // Se o aniversário já passou este ano, considera o próximo ano
            if ($dataAniversario < $dataAtual) {
                $dataAniversario->modify('+1 year');
            }

            // Calcula a diferença em dias
            $diferencaDias = $dataAtual->diff($dataAniversario)->days;

            // Verifica se hoje é o aniversário
            if ($mesDiaAniversario === $dataAtual->format('m-d')) {
                echo '<hr><p class="card-text mb-0 mt-2"><i class="bi bi-gift"></i> 🎉 <b>Parabéns! Hoje é o seu aniversário!</b> 🎂</p>';
            }
            // Exibe a mensagem apenas se estiver dentro do intervalo de 3 meses antes (90 dias)
            elseif ($diferencaDias <= 90) {
                echo '<hr><p class="card-text mb-0 mt-2"><i class="bi bi-cake"></i> Seu aniversário está chegando! Faltam ' . $diferencaDias . ' dias</p>';
            }
        }


        ?>
    </div>
</div>