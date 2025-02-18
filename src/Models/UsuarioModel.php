<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

/**
 * Classe UsuarioModel
 * 
 * Esta classe fornece métodos para interagir com a tabela `usuario` no banco de dados.
 * Ela permite realizar operações como criar, atualizar, listar, buscar e apagar registros de usuários.
 * Utiliza PDO para interação com o banco de dados, e cada método foi projetado para realizar
 * uma operação específica sobre os dados dos usuários.
 * 
 * @package GabineteMvc\Models
 */
class UsuarioModel {

    /**
     * @var PDO Conexão com o banco de dados.
     */
    private $conn;

    /**
     * Construtor da classe.
     * 
     * Inicializa a conexão com o banco de dados utilizando o método `getConnection` da classe `Database`.
     */
    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Cria um novo usuário no banco de dados.
     * 
     * Este método insere um novo registro de usuário na tabela `usuario`. Ele utiliza UUID para garantir
     * que cada usuário tenha um identificador único.
     * 
     * @param array $dados Array contendo os dados do usuário a ser criado. Deve incluir as chaves:
     * - 'usuario_nome' (string)
     * - 'usuario_email' (string)
     * - 'usuario_telefone' (string)
     * - 'usuario_senha' (string)
     * - 'usuario_tipo' (string)
     * - 'usuario_ativo' (bool)
     * - 'usuario_gabinete' (string)
     * - 'usuario_aniversario' (string)
     * 
     * @return bool Retorna `true` se a operação for bem-sucedida, ou `false` em caso de erro.
     */
    public function criar($dados) {
        $query = 'INSERT INTO usuario(usuario_id, usuario_nome, usuario_email, usuario_telefone, usuario_senha, usuario_tipo, usuario_ativo, usuario_gabinete, usuario_aniversario) 
                  VALUES (UUID(), :usuario_nome, :usuario_email, :usuario_telefone, :usuario_senha, :usuario_tipo, :usuario_ativo, :usuario_gabinete, :usuario_aniversario);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_senha', $dados['usuario_senha'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo', $dados['usuario_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_BOOL);
        $stmt->bindValue(':usuario_gabinete', $dados['usuario_gabinete'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Atualiza as informações de um usuário no banco de dados.
     * 
     * Este método atualiza o registro de um usuário existente na tabela `usuario`. O usuário é identificado
     * pelo `usuario_id`, e os dados fornecidos são atualizados de acordo com as informações fornecidas no array `$dados`.
     * 
     * @param array $dados Array contendo os dados a serem atualizados do usuário. Deve incluir as chaves:
     * - 'usuario_id' (string)
     * - 'usuario_nome' (string)
     * - 'usuario_email' (string)
     * - 'usuario_telefone' (string)
     * - 'usuario_senha' (string)
     * - 'usuario_tipo' (string)
     * - 'usuario_ativo' (bool)
     * - 'usuario_gabinete' (string)
     * - 'usuario_aniversario' (string)
     * 
     * @return bool Retorna `true` se a operação for bem-sucedida, ou `false` em caso de erro.
     */
    public function atualizar($dados) {
        $query = 'UPDATE usuario 
                  SET usuario_nome = :usuario_nome, usuario_email = :usuario_email, usuario_telefone = :usuario_telefone, usuario_senha = :usuario_senha,
                      usuario_tipo = :usuario_tipo, usuario_ativo = :usuario_ativo, usuario_gabinete = :usuario_gabinete, usuario_aniversario = :usuario_aniversario 
                  WHERE usuario_id = :usuario_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':usuario_id', $dados['usuario_id'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_senha', $dados['usuario_senha'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo', $dados['usuario_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_BOOL);
        $stmt->bindValue(':usuario_gabinete', $dados['usuario_gabinete'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Lista os usuários cadastrados no banco de dados com suporte a paginação e ordenação.
     * 
     * Este método retorna os registros de usuários da tabela `usuario`, permitindo a limitação dos resultados 
     * com base na página atual, número de itens por página, e ordenação dos resultados por uma coluna específica.
     * 
     * @param int $itens Número de itens a serem retornados por página.
     * @param int $pagina Número da página a ser retornada.
     * @param string $ordem Direção da ordenação: 'ASC' para crescente ou 'DESC' para decrescente.
     * @param string $ordenarPor Nome da coluna para ordenação.
     * 
     * @return array Retorna um array com os dados dos usuários, ordenados e paginados. Se não houver usuários, 
     * retorna um array vazio.
     */
    public function listar($itens, $pagina, $ordem, $ordenarPor) {
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT usuario.*, (SELECT COUNT(usuario_id) FROM usuario) as total_usuario 
                  FROM usuario 
                  ORDER BY $ordenarPor $ordem 
                  LIMIT :itens OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um usuário específico com base em uma coluna e valor.
     * 
     * Este método permite buscar um usuário específico utilizando uma coluna e valor para a filtragem. 
     * O nome da coluna e o valor são passados como parâmetros, e a consulta retorna o primeiro usuário encontrado 
     * que corresponde ao valor da coluna especificada.
     * 
     * @param string $coluna Nome da coluna para a busca, como 'usuario_id' ou 'usuario_email'.
     * @param mixed $valor Valor a ser buscado na coluna especificada.
     * 
     * @return array|null Retorna um array associativo com os dados do usuário encontrado, ou `null` caso nenhum usuário seja encontrado.
     */
    public function busca($coluna, $valor) {
        $query = "SELECT * FROM usuario WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Apaga um usuário do banco de dados.
     * 
     * Este método apaga o registro de um usuário da tabela `usuario`, utilizando o `usuario_id` como referência.
     * 
     * @param string $usuario_id O ID do usuário a ser apagado.
     * 
     * @return bool Retorna `true` se o usuário for apagado com sucesso, ou `false` em caso de erro.
     */
    public function apagar($usuario_id) {
        $query = 'DELETE FROM usuario WHERE usuario_id = :usuario_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
