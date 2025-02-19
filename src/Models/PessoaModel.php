<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class PessoaModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // MODELO PESSOA TIPO
    public function criarPessoaTipo($dados) {
        $query = 'INSERT INTO pessoa_tipo(pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) VALUES (UUID(), :pessoa_tipo_nome, :pessoa_tipo_descricao, :pessoa_tipo_criado_por, :pessoa_tipo_gabinete);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_tipo_nome', $dados['pessoa_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_descricao', $dados['pessoa_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_criado_por', $dados['pessoa_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_gabinete', $dados['pessoa_tipo_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarPessoaTipo($dados) {
        $query = 'UPDATE pessoa_tipo SET pessoa_tipo_nome = :pessoa_tipo_nome, pessoa_tipo_descricao = :pessoa_tipo_descricao WHERE pessoa_tipo_id = :pessoa_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_tipo_id', $dados['pessoa_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_nome', $dados['pessoa_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_descricao', $dados['pessoa_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarPessoaTipo() {
        $query = "SELECT * FROM pessoa_tipo ORDER BY pessoa_tipo_nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaPessoaTipo($id) {
        $query = "SELECT * FROM pessoa_tipo WHERE pessoa_tipo_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarPessoaTipo($id) {
        $query = 'DELETE FROM pessoa_tipo WHERE pessoa_tipo_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // MODELO PESSOA GENERO
    public function criarPessoaGenero($dados) {
        $query = 'INSERT INTO pessoa_genero(pessoa_genero_id, pessoa_genero_nome, pessoa_genero_criado_por, pessoa_genero_gabinete) VALUES (UUID(), :pessoa_genero_nome, :pessoa_genero_criado_por, :pessoa_genero_gabinete);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_genero_nome', $dados['pessoa_genero_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_genero_criado_por', $dados['pessoa_genero_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_genero_gabinete', $dados['pessoa_genero_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarPessoaGenero($dados) {
        $query = 'UPDATE pessoa_genero SET pessoa_genero_nome = :pessoa_genero_nome WHERE pessoa_genero_id = :pessoa_genero_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_genero_id', $dados['pessoa_genero_id'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_genero_nome', $dados['pessoa_genero_nome'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarPessoaGenero() {
        $query = "SELECT * FROM pessoa_genero ORDER BY pessoa_genero_nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaPessoaGenero($id) {
        $query = "SELECT * FROM pessoa_genero WHERE pessoa_genero_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarPessoaGenero($id) {
        $query = 'DELETE FROM pessoa_genero WHERE pessoa_genero_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // MODELO PESSOA PROFISSAO
    public function criarPessoaProfissao($dados) {
        $query = 'INSERT INTO pessoa_profissao(pessoa_profissao_id, pessoa_profissao_nome, pessoa_profissao_criado_por, pessoa_profissao_gabinete) VALUES (UUID(), :pessoa_profissao_nome, :pessoa_profissao_criado_por, :pessoa_profissao_gabinete);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_profissao_nome', $dados['pessoa_profissao_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_profissao_criado_por', $dados['pessoa_profissao_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_profissao_gabinete', $dados['pessoa_profissao_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarPessoaProfissao($dados) {
        $query = 'UPDATE pessoa_profissao SET pessoa_profissao_nome = :pessoa_profissao_nome WHERE pessoa_profissao_id = :pessoa_profissao_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_profissao_id', $dados['pessoa_profissao_id'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_profissao_nome', $dados['pessoa_profissao_nome'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarPessoaProfissao() {
        $query = "SELECT * FROM pessoa_profissao ORDER BY pessoa_profissao_nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaPessoaProfissao($id) {
        $query = "SELECT * FROM pessoa_profissao WHERE pessoa_profissao_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarPessoaProfissao($id) {
        $query = 'DELETE FROM pessoa_profissao WHERE pessoa_profissao_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // MODELO PESSOA
    public function criarPessoa($dados) {
        $query = 'INSERT INTO pessoa(pessoa_id, pessoa_nome, pessoa_email, pessoa_tipo, pessoa_profissao, pessoa_genero, pessoa_orgao, pessoa_cargo, pessoa_gabinete, pessoa_estado, pessoa_municipio, pessoa_bairro, pessoa_endereco, pessoa_telefone, pessoa_instagram, pessoa_twitter, pessoa_facebook, pessoa_foto, pessoa_informacoes, pessoa_criada_por) VALUES (UUID(), :pessoa_nome, :pessoa_email, :pessoa_tipo, :pessoa_profissao, :pessoa_genero, :pessoa_orgao, :pessoa_cargo, :pessoa_gabinete, :pessoa_estado, :pessoa_municipio, :pessoa_bairro, :pessoa_endereco, :pessoa_telefone, :pessoa_instagram, :pessoa_twitter, :pessoa_facebook, :pessoa_foto, :pessoa_informacoes, :pessoa_criada_por);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_nome', $dados['pessoa_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_email', $dados['pessoa_email'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo', $dados['pessoa_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_profissao', $dados['pessoa_profissao'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_genero', $dados['pessoa_genero'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_orgao', $dados['pessoa_orgao'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_cargo', $dados['pessoa_cargo'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_gabinete', $dados['pessoa_gabinete'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_estado', $dados['pessoa_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_municipio', $dados['pessoa_municipio'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_bairro', $dados['pessoa_bairro'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_endereco', $dados['pessoa_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_telefone', $dados['pessoa_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_instagram', $dados['pessoa_instagram'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_twitter', $dados['pessoa_twitter'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_facebook', $dados['pessoa_facebook'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_foto', $dados['pessoa_foto'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_informacoes', $dados['pessoa_informacoes'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_criada_por', $dados['pessoa_criada_por'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarPessoa($dados) {
        $query = 'UPDATE pessoa SET pessoa_nome = :pessoa_nome, pessoa_email = :pessoa_email, pessoa_tipo = :pessoa_tipo, pessoa_profissao = :pessoa_profissao, pessoa_genero = :pessoa_genero, pessoa_orgao = :pessoa_orgao, pessoa_cargo = :pessoa_cargo, pessoa_gabinete = :pessoa_gabinete, pessoa_estado = :pessoa_estado, pessoa_municipio = :pessoa_municipio, pessoa_bairro = :pessoa_bairro, pessoa_endereco = :pessoa_endereco, pessoa_telefone = :pessoa_telefone, pessoa_instagram = :pessoa_instagram, pessoa_twitter = :pessoa_twitter, pessoa_facebook = :pessoa_facebook, pessoa_foto = :pessoa_foto, pessoa_informacoes = :pessoa_informacoes WHERE pessoa_id = :pessoa_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_id', $dados['pessoa_id'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_nome', $dados['pessoa_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_email', $dados['pessoa_email'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo', $dados['pessoa_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_profissao', $dados['pessoa_profissao'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_genero', $dados['pessoa_genero'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_orgao', $dados['pessoa_orgao'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_cargo', $dados['pessoa_cargo'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_gabinete', $dados['pessoa_gabinete'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_estado', $dados['pessoa_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_municipio', $dados['pessoa_municipio'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_bairro', $dados['pessoa_bairro'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_endereco', $dados['pessoa_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_telefone', $dados['pessoa_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_instagram', $dados['pessoa_instagram'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_twitter', $dados['pessoa_twitter'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_facebook', $dados['pessoa_facebook'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_foto', $dados['pessoa_foto'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_informacoes', $dados['pessoa_informacoes'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarPessoa($itens, $pagina, $ordem, $ordenarPor) {
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT pessoa.*, (SELECT COUNT(pessoa_id) FROM pessoa) as total_pessoa 
                  FROM pessoa 
                  ORDER BY $ordenarPor $ordem 
                  LIMIT :itens OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaPessoa($id) {
        $query = "SELECT * FROM pessoa WHERE pessoa_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarPessoa($id) {
        $query = 'DELETE FROM pessoa WHERE pessoa_id = :id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
