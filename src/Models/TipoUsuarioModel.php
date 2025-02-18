<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

/**
 * Classe TipoUsuarioModel
 * 
 * Esta classe fornece métodos para interagir com a tabela usuario_tipo no banco de dados.
 * Permite realizar operações como criar, atualizar, listar, buscar e apagar registros de tipos de usuários.
 * 
 * @package GabineteMvc\Models
 */
class TipoUsuarioModel {

    /**
     * @var PDO Conexão com o banco de dados.
     */
    private $conn;

    /**
     * Construtor da classe.
     * Inicializa a conexão com o banco de dados utilizando o método getConnection da classe Database.
     */
    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Cria um novo tipo de usuário no banco de dados.
     * 
     * @param array $dados Dados do tipo de usuário a ser criado. Deve incluir:
     * - 'usuario_tipo_nome' (string)
     * - 'usuario_tipo_descricao' (string)
     * 
     * @return bool Retorna true se a operação for bem-sucedida, ou false em caso de erro.
     */
    public function criar($dados) {
        $query = 'INSERT INTO usuario_tipo(usuario_tipo_id, usuario_tipo_nome, usuario_tipo_descricao) 
                  VALUES (UUID(), :usuario_tipo_nome, :usuario_tipo_descricao)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_nome', $dados['usuario_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_descricao', $dados['usuario_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Atualiza as informações de um tipo de usuário no banco de dados.
     * 
     * @param array $dados Dados a serem atualizados. Deve incluir:
     * - 'usuario_tipo_id' (string)
     * - 'usuario_tipo_nome' (string)
     * - 'usuario_tipo_descricao' (string)
     * 
     * @return bool Retorna true se a operação for bem-sucedida, ou false em caso de erro.
     */
    public function atualizar($dados) {
        $query = 'UPDATE usuario_tipo 
                  SET usuario_tipo_nome = :usuario_tipo_nome, usuario_tipo_descricao = :usuario_tipo_descricao 
                  WHERE usuario_tipo_id = :usuario_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_id', $dados['usuario_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_nome', $dados['usuario_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_descricao', $dados['usuario_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Lista os tipos de usuários cadastrados no banco de dados.
     * 
     * @return array Retorna um array com os dados dos tipos de usuários ou um array vazio caso não haja registros.
     */
    public function listar() {
        $query = 'SELECT * FROM usuario_tipo ORDER BY usuario_tipo_nome ASC';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um tipo de usuário específico com base em uma coluna e valor.
     * 
     * @param string $coluna Nome da coluna para a busca (ex: 'usuario_tipo_id' ou 'usuario_tipo_nome').
     * @param mixed $valor Valor a ser buscado.
     * 
     * @return array|null Retorna um array associativo com os dados encontrados ou null caso não haja registros.
     */
    public function busca($coluna, $valor) {
        $query = "SELECT * FROM usuario_tipo WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Apaga um tipo de usuário do banco de dados.
     * 
     * @param string $usuario_tipo_id O ID do tipo de usuário a ser apagado.
     * 
     * @return bool Retorna true se a operação for bem-sucedida, ou false em caso de erro.
     */
    public function apagar($usuario_tipo_id) {
        $query = 'DELETE FROM usuario_tipo WHERE usuario_tipo_id = :usuario_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_id', $usuario_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
