<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  header('Location: ?secao=home');

$rotas = [
    'login' => './src/Views/login/login.php',
    'cadastro' => './src/Views/cadastro/novo-gabinete.php',
    'novo-usuario' => './src/Views/cadastro/novo-usuario.php',
    'sair' => './src/Views/login/sair.php',
    'fatal-error' => './src/Views/erros/fatal-error.php',
    'home' => './src/Views/home/home.php',
    'meu-gabinete' => './src/Views/meu-gabinete/meu-gabinete.php',
    'usuario' => './src/Views/meu-gabinete/editar-usuario.php',
    'minhas-mensagens' => './src/Views/minhas-mensagens/mensagens.php',
    'mensagem' => './src/Views/minhas-mensagens/mensagem.php',
    'tipos-orgaos' => './src/Views/orgao/tipos-orgaos.php',
    'tipo-orgao' => './src/Views/orgao/tipo-orgao.php',
    'orgaos' => './src/Views/orgao/orgaos.php',
    'orgao' => './src/Views/orgao/orgao.php',
    'imprimir-orgaos' => './src/Views/orgao/imprimir-orgaos.php',
    'tipos-pessoas' => './src/Views/pessoa/tipos-pessoas.php',
    'tipo-pessoa' => './src/Views/pessoa/tipo-pessoa.php',
    'profissoes' => './src/Views/pessoa/profissoes.php',
    'profissao' => './src/Views/pessoa/profissao.php',
    'pessoas' => './src/Views/pessoa/pessoas.php',
    'pessoa' => './src/Views/pessoa/pessoa.php',
    'imprimir-pessoa' => './src/Views/pessoa/imprimir-pessoa.php',
    'imprimir-pessoas' => './src/Views/pessoa/imprimir-pessoas.php',
    'aniversariantes' => './src/Views/pessoa/aniversariantes.php',
    'tipos-documentos' => './src/Views/documento/tipos-documentos.php',
    'tipo-documento' => './src/Views/documento/tipo-documento.php',
    'documentos' => './src/Views/documento/documentos.php',
    'documento' => './src/Views/documento/documento.php',
    'emendas-status' => './src/Views/emenda/emendas-status.php',
    'emenda-status' => './src/Views/emenda/emenda-status.php',
    'emendas-objetivos' => './src/Views/emenda/emendas-objetivos.php',
    'emenda-objetivo' => './src/Views/emenda/emenda-objetivo.php',
    'emendas' => './src/Views/emenda/emendas.php',
    'emenda' => './src/Views/emenda/emenda.php',
    'imprimir-emendas' => './src/Views/emenda/imprimir-emendas.php',
    'postagens-status' => './src/Views/postagem/postagens-status.php',
    'postagem-status' => './src/Views/postagem/postagem-status.php',
    'postagens' => './src/Views/postagem/postagens.php',
    'postagem' => './src/Views/postagem/postagem.php',
    'tipos-clipping' => './src/Views/clipping/tipos-clipping.php',
    'tipo-clipping' => './src/Views/clipping/tipo-clipping.php',
    'clippings' => './src/Views/clipping/clippings.php',
    'clipping' => './src/Views/clipping/clipping.php',
    'contatos-imprensa' => './src/Views/pessoa/contatos-imprensa.php',
    'tipos-agenda' => './src/Views/agenda/tipos-agenda.php',
    'tipo-agenda' => './src/Views/agenda/tipo-agenda.php',
    'situacoes-agenda' => './src/Views/agenda/situacoes-agenda.php',
    'situacao-agenda' => './src/Views/agenda/situacao-agenda.php',
    'agendas' => './src/Views/agenda/agendas.php',
    'agenda' => './src/Views/agenda/agenda.php',
    'imprimir-agenda' => './src/Views/agenda/imprimir-agenda.php',
    'sobre' => './src/Views/sobre/sobre.php',
    'proposicoes' => './src/Views/proposicao/proposicoes.php',
    'proposicaoCD' => './src/Views/proposicao/proposicaoCD.php',
    'proposicoes-temas' => './src/Views/proposicao/proposicoesTema.php',
    'imprimir-proposicaoCD' => './src/Views/proposicao/imprimirProposicaoCD.php',



];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/erros/404.php';
}
